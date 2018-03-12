<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Redirect;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
		// Added the following code for graceful expiration of Session, 12th Jan, 2018
		if ($e instanceof TokenMismatchException) {
 
			return redirect('login')->withErrors(['session_expiry_message' =>'Your session has expired. Please log in again.']);
		}
		
		if ($this->isHttpException($e)) {
			switch ($e->getStatusCode()) {

				// not authorized
				case '403':
					return \Response::view('errors.403',array(),403);
					break;

				// not found
				case '404':
                    
                    return redirect('/')->withErrors('Sorry, your request could not be completed. Please use the view link in the table below to continue reviewing your assessment. If the problem persists, please <a target="_blank" href="http://hometaxsavings.com/wp_prelive/enquiry">Contact us</a>.');
					//return \Response::view('errors.404',array(),404);
					break;

			
				default:
					return $this->renderHttpException($e);
					break;
			}
		}
		
        return parent::render($request, $e);
    }
}
