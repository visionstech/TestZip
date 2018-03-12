<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/* All routes that don't need authentication are here */

Route::group(['middleware' => ['web']], function () {

//Route::auth();

// Authentication Routes...
    Route::get('login', 'Auth\AuthController@showLoginForm');
    Route::post('login', 'Auth\AuthController@login');
    Route::get('logout', 'Auth\AuthController@logout');
	
    Route::get('register', 'Auth\AuthController@showRegistrationForm');
    Route::post('registerUser', 'Auth\AuthController@register');
    
	// Password Reset Routes...
	Route::get('password/reset/{token?}', 'Auth\PasswordController@showResetForm');
	Route::post('password/email', 'Auth\PasswordController@sendResetLinkEmail');
	Route::post('password/reset', 'Auth\PasswordController@reset');	
	
	
	// Admin routes
	/* Configurable items related routes */
	

	// get state counties
    Route::post('/state-counties', 'CommonController@postCounty');
    // get county link
    Route::post('/county-link', 'CommonController@postCountyLink');
      // get county link
    Route::post('/county-link-md', 'CommonController@postCountyLinkMd');
	
    // Address not found
    Route::get('/no_address/{search_id?}', 'CustomerController@getAddressNotFound');
    Route::get('/assessment_not_ready/{search_id?}', 'CustomerController@getAssessmentNotReady');

    Route::get('/test-api', 'CustomerController@getTestApi');
    Route::post('/test-api', 'CustomerController@postTestApi');
    Route::get('/test-api-comparables', 'CustomerController@getTestApiComparables');
    Route::post('/test-api-comparables', 'CustomerController@postTestApiComparables');

    Route::get('/adjustment-test-api', 'AdjustmentController@getTestApi');
    Route::post('/adjustment-test-api', 'AdjustmentController@postTestApi');
    Route::get('/adjustment-test-api-comparables', 'AdjustmentController@getTestApiComparables');
    Route::post('/adjustment-test-api-comparables', 'AdjustmentController@postTestApiComparables');
    Route::get('/download-all-adjusted-sales', 'AdjustmentController@downloadAllAdjustedComps');
	Route::get('/download-top-adjusted-sales', 'AdjustmentController@downloadTopAdjustedComps');
	Route::get('/generate-excelsheet', 'AdjustmentController@getGenerateSheet');
    Route::post('/generate-excelsheet', 'AdjustmentController@postGenerateSheet');

    Route::post('/get-state-counties', 'CustomerController@postStateCounties');

    #spreadsheet
    Route::get('/generate-sheet', 'CustomerController@getGenerateSheet');
    Route::post('/generate-sheet', 'CustomerController@postGenerateSheet');
	
	Route::get('/download-all-adjusted-comps', 'CustomerController@downloadAllAdjustedComps');
	Route::get('/download-top-adjusted-comps', 'CustomerController@downloadTopAdjustedComps');
	
	Route::get('/pdf_testing/{view}', 'CustomerController@getPdfTesting');

	/* 13 Feb Cron job Notifications Routing */
	Route::controller('cron', 'CronController');
	/* End 13 Feb Cron job Notifications Routing */

});

// All member routes listed here
Route::group(['middleware' => ['web','auth','prevent-back-history','is-member']], function () {
    	
        // search address
        Route::get('/search-address/{view?}', 'CustomerController@getSearchAddress');
        Route::post('/search-address', 'CustomerController@postSearchAddress');
        
        // thankyou page
        Route::get('/thankyou/{view?}', 'CustomerController@getThankyou');

    
        // payment routes
        Route::get('/make-payment/{view?}', 'CustomerController@getMakePayment');
        Route::post('/make-payment', 'CustomerController@postMakePayment');
		
		//Phase 2 payment routes
	   	//Route::post('/make-phase2-payment', 'CustomerPhase2Controller@makePhase2Payment');
	   	Route::get('/make-phase2-payment/{encryptedToken}', 'CustomerPhase2Controller@makePhase2Payment');
	   	Route::post('/phase2-payment', 'CustomerPhase2Controller@postPhase2MakePayment');
		
		Route::get('/phase2-token-status/{view?}', 'CustomerPhase2Controller@getPhase2TokenStatus');

	   	//Phase 2 subject top 5 comparables list
	   	Route::get('/top_comparables_list/{view?}', 'CustomerPhase2Controller@getTopComparablesList');
        
        // address routes
        Route::get('/address/{view?}', 'CustomerController@getAddress');
        Route::post('/address', 'CustomerController@postAddress');
        
        // verify address routes
        Route::get('/verify-address/{view?}', 'CustomerController@getVerifyAddress');
        Route::post('/verify-address', 'CustomerController@postVerifyAddress');
        
        // assessment review routes
        Route::get('/assessment-review/{view?}', 'CustomerController@getAssessmentReview');
        Route::post('/assessment-review', 'CustomerController@postAssessmentReview');
        
        // invalid token
        Route::get('/invalid-token', 'CustomerController@getInvalidToken');
        Route::get('/token-status/{view?}', 'CustomerController@getTokenStatus');
        
        // get token status from mail link
        Route::get('/token/{token}', 'CustomerController@getToken');
        
        // test mail
        Route::get('/test-mail', 'CustomerController@getTestMail');
        
        // start new session
        Route::get('/start-new-session', 'CustomerController@getDeleteSessionToken');
        
        // get additional homeowner question description
        Route::post('/question-description', 'CommonController@postQuestionDescription');
        
        // check valid address api 
        Route::post('/check-valid-address', 'CustomerController@postCheckValidAddress');
        
        Route::get('/test-pdf1', 'CustomerController@getTestPdf1');
        Route::get('/test-pdf2', 'CustomerController@getTestPdf2');
        Route::get('/test-pdf3', 'CustomerController@getTestPdf3');
        Route::get('/test-pdf4', 'CustomerController@getTestPdf4');
        
        Route::get('/arlington-pdf/{token}', 'CustomerController@getArlingtonPdf');
        Route::get('/fairfax-pdf/{token}', 'CustomerController@getFairfaxPdf');
        Route::get('/dc-pdf/{token}', 'CustomerController@getDcPdf');
        Route::get('/md-pdf/{token}', 'CustomerController@getMdPdf');
        
		
		//Routes for Change Password
		Route::get('/changePassword','AdminUserController@showChangePasswordForm');
		Route::post('/changePassword','AdminUserController@changePassword')->name('changePassword');
		
		// Routes for Edit Profile
		Route::get('/editProfile','AdminUserController@showEditProfileForm');
		Route::post('/updateProfile','AdminUserController@updateProfile');
		Route::post('/getProfileCounties', 'AdminUserController@postProfileStateCounties');

		// Address not found after login
	    Route::get('/no_address_after_login/{search_id?}', 'CustomerController@getAddressNotFoundAfterLogin');
	    // Assessment not ready after login
	    Route::get('/assessment_not_ready_after_login/{search_id?}', 'CustomerController@getAssessmentNotReadyAfterLogin');

        
       
    
	   	/*Route::get('/', function () {
	            return view('home');
	            //return view('welcome');
		});*/
		Route::get('/', 'CustomerController@getDashboard');
	
	
});

// All admin routes listed here

Route::group(['middleware' => ['web','auth','prevent-back-history','is-admin']], function () {
	
	Route::get('/home', 'HomeController@index');
	/*Route::get('/admin', function () {
		return view('welcome');
	}); Commented for round 1*/
	Route::get('/admin','AdminUserController@listMember');
	
	Route::get('settings', 'HomeController@settings');
	Route::post('saveItem', 'HomeController@saveItem');
	Route::post('updateItem', 'HomeController@updateItem');
	Route::get('/item/add', array('as'=>'addItem','uses'=>'HomeController@addItem'));
	Route::get('/item/{itemId}/edit', array('as'=>'editItem','uses'=>'HomeController@editItem'))->where('id', '[0-9]+');
	Route::get('/item/{itemId}/delete', array('as'=>'deleteItem','uses'=>'HomeController@deleteItem'))->where('id', '[0-9]+');

	/* User related routes */
	Route::get('users','AdminUserController@listUser');
	Route::get('/user/add', 'AdminUserController@addUser');
	Route::post('saveUser', 'AdminUserController@saveUser');
	Route::post('updateUser', 'AdminUserController@updateUser');
	Route::get('/user/{userId}/edit', array('as'=>'editUser','uses'=>'AdminUserController@editUser'))->where('id', '[0-9]+');
	Route::get('/user/{userId}/delete', array('as'=>'deleteUser','uses'=>'AdminUserController@deleteUser'))->where('id', '[0-9]+');
	
	
	
	
	
	// Added routes for lookups and jurisdictions for get
	
	Route::get('/lookup/add', array('as'=>'addLookup','uses'=>'HomeController@addLookup'));
	Route::get('/lookup/{lookupId}/edit', array('as'=>'editLookup','uses'=>'HomeController@editLookup'))->where('lookupId', '[0-9]+');
	Route::get('/lookup/{lookupId}/delete', array('as'=>'deleteLookup','uses'=>'HomeController@deleteLookup'))->where('lookupId', '[0-9]+');
	Route::post('saveLookup', 'HomeController@saveLookup');
	Route::post('updateLookup', 'HomeController@updateLookup');
	Route::match(['get', 'post'],'jurisdiction', 'HomeController@jurisdiction');
	Route::match(['get', 'post'],'lookups', 'HomeController@lookups');

	// Routes for State and County CRUD

	Route::get('/county/{countyId}/edit', array('as'=>'editCounty','uses'=>'HomeController@editCounty'))->where('id', '[0-9]+');
	Route::post('updateCounty', 'HomeController@updateCounty');
	
	/* Member related routes */
	Route::get('members','AdminUserController@listMember');
	Route::get('/member/{memberId}', array('as'=>'viewMember','uses'=>'AdminUserController@viewMemberDetails'))->where('id', '[0-9]+');

	
	
	
});

/* Paypal related routes */
Route::get('paywithpaypal', array('as' => 'customer.paywithpaypal','uses' => 'CustomerController@payWithPaypal',));
Route::post('paypal', array('as' => 'customer.paypal','uses' => 'CustomerController@postPaymentWithpaypal',));


