<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Auth;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\State;
use App\County;
use App\MdInOutCycle;
use App\UserSearch;
use App\PfAddress;
use App\Helpers\Helper;
use Redirect;
use App\Http\Controllers\CustomerController;
use Session;
use Mail;
class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    //protected $redirectTo = '/';
	// Changed since login functionality will be only for admin 9th Jun 2017. Meena Sharan
	//protected $redirectTo = '/admin';
	protected $redirectAfterLogout = '/login';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware($this->guestMiddleware(), ['except' => 'logout']);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
    }
	

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }
	
	// Added the following function to check if user is customer or admin. Jan 2018
	protected function authenticated($user)
    {
		if(Auth::User()->user_type == 1) {
            return redirect('/admin');
        }

        return redirect('/');
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm($view_status=0)
    {
        try {
            $active = 'User Registration';
            $all_states = State::all();
            $states = [];
            foreach($all_states as $state) {
                $states[$state->state_abbr] = $state->state_name;
            }
            
            $all_counties = County::orderBy('county_name', 'ASC')->where('end_date',null)->get();
            $counties = [];
            foreach($all_counties as $county) {
                $counties[$county->county_name] = $county->county_name;
            }

            return view('auth.register',compact('active', 'states', 'counties'));
        }
        catch (\Exception $e) 
        {
            $result = ['exception_message' => $e->getMessage()];
            return view('errors.error', $result);
        }
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Requests\Registration $request)
    {
        try {
            //echo "<pre>"; print_r($request->all()); die;
            $first_name = $request->first_name;
            $last_name = $request->last_name;
            $email = $request->email;
            $mobile_number = $request->mobile_number;
            $password = $request->password;
            $street_number = $request->street_number;
            $route = $request->route;
            $locality = $request->locality;
            $administrative_area_level_1 = $request->administrative_area_level_1; //state
            $postal_code = $request->postal_code;
            $administrative_area_level_2 = $request->administrative_area_level_2;
            $receive_notification = $request->receive_notification;

            //check user existance 
            $user = User::where(array('email' => $email, 'user_type' => 0, 'end_date' => null) )->get();
            if(count($user) > 0) {

                return Redirect::back()->withInput()->withErrors(array('email' => 'Sorry, the email address you have entered is already registered. Would you like to login or try registering with a different email address'));

            } else {
                //register new user here
                $create_user = User::create([
                    'email' => $email,
                    'password' => bcrypt($password),
                    'user_type' => '0',
                    'ip_address' => $_SERVER['REMOTE_ADDR'],
                ]);

                $user_id = $create_user->id;
                
                if($user_id != "" && !empty($user_id) && $user_id != null){
                    // Send mail to new user
                    $customer_link = url('/search-address');
                    $mail_data['username'] = $email;
                    $mail_data['user_name'] = ucfirst($first_name)." ".ucfirst($last_name);
                    $mail_data['fname'] = ucfirst($first_name);
                    $mail_data['lname'] = ucfirst($last_name);
                    $mail_data['phone'] = $mobile_number;
                    $mail_data['str_n_route'] = $street_number." ".$route;
                    $mail_data['locality'] = $locality;
                    $mail_data['state'] = $administrative_area_level_2;
                    $mail_data['county'] = $administrative_area_level_1;
                    $mail_data['postal'] = $postal_code;

                    $mail_data['content'] = 'CONGRATULATIONS!<p>You are now a member of a growing number of homeowners who want more control of their annual real estate taxes. Follow this link and login to get Tax Appeal. <a href="'.$customer_link.'" target="_blank" style="color:#1570C3;">click to login</a> <br>';
                    $mail_data['subject'] = 'Congratulations on registering your property!';

                    $mail_to = array($email);
                    //$mail_sent = Helper::SendMail($mail_data, $mail_to);
                    // send mail code end here
                    Mail::send('emails.signup', $mail_data, function($message) use ($mail_data, $mail_to)
                    {
                        $message->to($mail_to)->subject('Congratulations on registering your property!');
                         
                    });
                    $state_abbr = $administrative_area_level_1;

                    $state_details = State::where('state_abbr', $state_abbr)->where('end_date',null)->lists('state_id');
                    $county_name = $administrative_area_level_2;
                    $county_details = County::where('state_id', $state_details[0])->where('county_name', $county_name)->where('end_date',null)->get();
                    
                    // start save search details here
                     
                    $user_search_high_key = Helper::getHighKey('user_searches', 'user_search_id', $user_id);

                    /* 31 Jan 2018 Rb */
                    $user_search_token = encrypt($user_id).'-'.Helper::getCustomerToken(10); 
                    
                     /* End 31 Jan 2018 Rb */
                    $create_user_search = UserSearch::create([
                        'user_search_id' => (empty($user_search_high_key)) ? null : $user_search_high_key,
                        'system_user_id' => $user_id,
                        'search_date'    => date('Y-m-d H:i:s'),
                        'first_name'     => $first_name,
                        'last_name'      => $last_name,
                        'land_assessment_value' => '0',
                        'improvement_assessment_value' => '0',
                        'total_assessment_value' => '0',
                        'created_by' => $user_id,
                        'updated_by' => $user_id,
                        'token'      => $user_search_token,
                        'phase2_token' => null,
                        'active_page' => '0',
                    ]);

                    // start add address details here
                    if($create_user_search) {                        
                        $system_object_type_id = Helper::toGenerateCommunicationObjectTypes('pf_lookups', 'name', 'address', $user_id);

                        if(!empty($system_object_type_id)) {
                            $lookup_type = Helper::getLookupTypeIdFromName(config('constants.constants.lookup_type.address'));
                            $where_condition['lookup_type_id'] = $lookup_type[0]->lookup_type_id;

                            // Billing address save here
                            $address_type = Helper::getLookupIdFromName(config('constants.constants.lookup.address.billing_address'), $where_condition);
                            $address_high_key = Helper::getHighKey('pf_addresses', 'address_id', $user_id);
                            $create_search_address = PfAddress::create([
                                'address_id' => (empty($address_high_key)) ? null : $address_high_key,
                                'system_object_type_id' => $system_object_type_id,
                                'ref_object_id' => $create_user_search->user_search_id,
                                'address_type' => $address_type[0]->lookup_id,
                                'mobile_number' => $mobile_number,
                                'receive_notification' => (isset($receive_notification)) ? $receive_notification : '0',
                                'address_line_1' => $street_number,
                                'address_line_2' => $route, 
                                'city' => $locality,
                                'postal_code' => $postal_code,
                                'state' => (isset($state_details[0]) ? $state_details[0] : ""),
                                'county' => (isset($county_details[0]) ? $county_details[0]->county_id : "" ),
                                'created_by' => $user_id,
                                'updated_by' => $user_id,
                            ]);

                            // Search address save here
                            $address_type = Helper::getLookupIdFromName(config('constants.constants.lookup.address.search_address'), $where_condition);
                            
                            $address_primary_key = Helper::getHighKey('pf_addresses', 'address_id', $user_id);
                            $search_address = PfAddress::create([
                                'address_id' => (empty($address_primary_key)) ? null : $address_primary_key,
                                'system_object_type_id' => $system_object_type_id,
                                'ref_object_id' => $create_user_search->user_search_id,
                                'address_type' => $address_type[0]->lookup_id,
                                'mobile_number' => $mobile_number,
                                'receive_notification' => (isset($receive_notification)) ? $receive_notification : '0',
                                'address_line_1' => $street_number,
                                'address_line_2' => $route, 
                                'city' => $locality,
                                'postal_code' => $postal_code,
                                'state' => (isset($state_details[0]) ? $state_details[0] : ""),
                                'county' => (isset($county_details[0]) ? $county_details[0]->county_id : "" ),
                                'created_by' => $user_id,
                                'updated_by' => $user_id,
                            ]);
                        }
                        // end add address details here
                        

                        if(($state_abbr == 'VA' || $state_abbr == 'DC' || $state_abbr == 'MD')) {
                            //MD In-Out 9 Feb 2018  
                            if($request->in_out_case==1 && count($county_details)){
                                $dataInout['latest_assesement_year']=$request->assessment_year;
                                $dataInout['county_id']=$county_details[0]->county_id;
                                $dataInout['latest_search_id']=$create_user_search->user_search_id;
                                $InOutStatus=self::checkInOutStatus($dataInout);

                                if($InOutStatus['success']){
                                    if($InOutStatus['redirect_url']=='/search-address'){
                                        $InOutStatus['redirect_url']='/search-address/'.$create_user_search->token;
                                    }

                                    if(isset($InOutStatus['status']) && $InOutStatus['status']==2){
                                        $search_update_detail['status'] = 2;
                                        $update_search_comp_detail = UserSearch::where('user_search_id', $create_user_search->user_search_id)->where('end_date',null)->update($search_update_detail);
                                    }
                                    $checkUser = Auth::loginUsingId($user_id);
                                    if(Auth::check()){
                                        Session::put('success_msg','Congratulations! you are now a member of a growing number of homeowners who want more control of their annual real estate taxes.');
                                        Session::put('firstTime', '1');
                                        Session::put('user_search_id', $create_user_search->user_search_id);
                                    }else{
                                        return Redirect::back()->withInput()->withErrors(array('server_error_msg' => 'User have created successfully but login attemtation has been failed, Please go to login page.'));
                                    }                                         
                                    return redirect($InOutStatus['redirect_url'])->with('success', $InOutStatus['message']);    
                                }
                            }
                            //End MD CASE 

                            if(count($county_details) &&  strtotime(date('Y-m-d H:i:s')) >= strtotime($county_details[0]->notice_date) && strtotime(date('Y-m-d H:i:s')) <= strtotime($county_details[0]->appeal_deadline_date)) {
                                //SAVING APPEAL YEAR
                                $appeal_year_data['appeal_year'] = date('Y');
                                $update_search_comp_detail = UserSearch::where('user_search_id', $create_user_search->user_search_id)->where('end_date',null)->update($appeal_year_data);

                                //END
                                $checkUser = Auth::loginUsingId($user_id);
                                if(Auth::check()){
                                    

                                    Session::put('success_msg','Congratulations! you are now a member of a growing number of homeowners who want more control of their annual real estate taxes.');

                                    Session::put('firstTime', '1');
                                    Session::put('user_search_id', $create_user_search->user_search_id);

                                    
                                    
                                    return redirect()->to(url('search-address'));
                                } else {
                                    return Redirect::back()->withInput()->withErrors(array('server_error_msg' => 'Your email address '.$email.' has been successfully registered but we could not log you in. Please try logging in using <a class="_login" href="http://tax.solutionsgroup.us/prelive/login">login</a>.'));
                                }

                            } else {

                                $search_update_detail['status'] = 2;
                                $update_search_comp_detail = UserSearch::where('user_search_id', $create_user_search->user_search_id)->where('end_date',null)->update($search_update_detail);

                                return redirect()->to(url('assessment_not_ready'));
                            }
                        } else { 
                            $search_update_detail['status'] = 1;
                            $update_search_comp_detail = UserSearch::where('user_search_id', $create_user_search->user_search_id)->where('end_date',null)->update($search_update_detail);

                            return redirect()->to(url('no_address'));
                        }
                    }
                    // end save search details here
                } else {
                    return Redirect::back()->withInput()->withErrors(array('server_error_msg' => 'Sorry, your registration with <'.$email.'> could not go through. Please try again later.'));
                }
            }

        } catch (Exception $e) {
            return response()->json(['success'=>false, 'message' => $e->getMessage()]);
        }

    }

    /**
      * Check In-Out Case.
      * @param County_id, Latest_assesment_year            
      * @return Response
    **/
    public function checkInOutStatus($dataInout){
       // echo "<pre>";print_r($dataInout);exit;
        try{
            $MdInOutDetail = MdInOutCycle::where('county_id', $dataInout['county_id'])->where('end_date',null)->get();
            $response=array();
            if(count($MdInOutDetail)){
                $currentDate=date('Y-m-d');
                $currentYear=date('Y');
                $latest_assesement_year = $dataInout['latest_assesement_year'];
                //In-Cycle Case
                if($latest_assesement_year == $currentYear){
                    
                    if((strtotime($currentDate) <= strtotime($MdInOutDetail[0]->incycle_deadline_date)) && (strtotime($currentDate) >= strtotime($MdInOutDetail[0]->incycle_notice_date))){
                    //Case 1A                 
                        $message='You can appeal for year 1 before '.date('F d, Y',strtotime($MdInOutDetail[0]->incycle_deadline_date));
                        Session::put('In_OUT_Message',$message);
                        //Update User Search
                        $UpdateUserSearch = UserSearch::where('user_search_id',$dataInout['latest_search_id'])->update(['cycle_type' => 'in', 'appeal_type' => 'Year-1','appeal_deadline_date'=>$MdInOutDetail[0]->incycle_deadline_date,'latest_assesement_year'=>$latest_assesement_year,'appeal_year'=>$currentYear]);
                        $response['success']=1;
                        $response['redirect_url']='/search-address';
                        $response['message']=$message;
                        return $response;
                        echo "Case 1A";exit;

                    }else if((strtotime($currentDate) <= strtotime($MdInOutDetail[0]->outcycle_deadline_date)) && (strtotime($currentDate) >= strtotime($MdInOutDetail[0]->outcycle_notice_date))){
                    //Case 1B
                        $message='You can appeal for year 2 before '.date('F d, Y',strtotime($MdInOutDetail[0]->outcycle_deadline_date));
                        Session::put('In_OUT_Message',$message);
                        //Update User Search
                        $UpdateUserSearch = UserSearch::where('user_search_id',$dataInout['latest_search_id'])->update(['cycle_type' => 'in', 'appeal_type' => 'Year-2','appeal_deadline_date'=>$MdInOutDetail[0]->outcycle_deadline_date,'latest_assesement_year'=>$latest_assesement_year,'appeal_year'=>($currentYear+1)]);
                        $response['success']=1;
                        $response['redirect_url']='/search-address';
                        $response['message']=$message;
                        return $response;
                        echo "Case 1B";exit;
                    }else{
                    //Case 1C
                        $message='We will be sending you automated updates when your new assessment is issued so we can review your taxes and determine if you will be able to save money on your taxes.';
                        Session::put('In_OUT_Message',$message);
                        $UpdateUserSearch = UserSearch::where('user_search_id',$dataInout['latest_search_id'])->update(['cycle_type' => 'in', 'appeal_type' => 'Year-2','appeal_deadline_date'=>$MdInOutDetail[0]->outcycle_deadline_date,'latest_assesement_year'=>$latest_assesement_year,'appeal_year'=>($currentYear+1)]);
                        //$response['redirect_url']='/';
                        $response['success']=1;
                        $response['redirect_url']='/assessment_not_ready';
                        $response['message']=$message;
                        $response['status']=2;
                        return $response;
                        echo "Case 1C";exit;
                    }
                }

                //Out-Cycle Case
                if($latest_assesement_year == ($currentYear-1)){
                    if((strtotime($currentDate) <= strtotime($MdInOutDetail[0]->outcycle_deadline_date)) && (strtotime($currentDate) >= strtotime($MdInOutDetail[0]->outcycle_notice_date))){
                        //Case 2A            
                        $message='You can appeal for year 3 before '.date('F d, Y',strtotime($MdInOutDetail[0]->outcycle_deadline_date));
                        Session::put('In_OUT_Message',$message);
                        //Update User Search
                        $UpdateUserSearch = UserSearch::where('user_search_id',$dataInout['latest_search_id'])->update(['cycle_type' => 'out', 'appeal_type' => 'Year-3','appeal_deadline_date'=>$MdInOutDetail[0]->outcycle_deadline_date,'latest_assesement_year'=>$latest_assesement_year,'appeal_year'=>($currentYear+1)]);  
                        //echo "Case 2A";exit;
                        //$response['redirect_url']='/make-payment';
                        $response['success']=1;
                        $response['redirect_url']='/search-address';
                        $response['message']=$message;
                        return $response;
                    }else{
                        //Case 2B
                        $message='We will be sending you automated updates when your new assessment is issued so we can review your taxes and determine if you will be able to save money on your taxes.';
                        Session::put('In_OUT_Message',$message);
                        //echo "Case 2B";exit;
                        $UpdateUserSearch = UserSearch::where('user_search_id',$dataInout['latest_search_id'])->update(['cycle_type' => 'out', 'appeal_type' => 'Year-3','appeal_deadline_date'=>$MdInOutDetail[0]->outcycle_deadline_date,'latest_assesement_year'=>$latest_assesement_year,'appeal_year'=>($currentYear+1)]);  
                        $response['success']=1;
                        $response['redirect_url']='/assessment_not_ready';
                        $response['message']=$message;
                        $response['status']=2;
                        return $response;
                    }
                }

                if($latest_assesement_year <= ($currentYear-2)){
                    /*if((strtotime($currentDate) <= strtotime($MdInOutDetail[0]->outcycle_deadline_date)) && (strtotime($currentDate) >= strtotime($MdInOutDetail[0]->outcycle_notice_date))){    */ 
                    $message='We will be sending you automated updates when your new assessment is issued so we can review your taxes and determine if you will be able to save money on your taxes. You will be able to Appeal your upcoming tax assessment scheduled for '.date('F d',strtotime($MdInOutDetail[0]->incycle_notice_date)).', '.($currentYear+1).' between'.date('F d',strtotime($MdInOutDetail[0]->incycle_notice_date)).', '.($currentYear+1).'-'.date('F d',strtotime($MdInOutDetail[0]->incycle_deadline_date)).', '.($currentYear+1);
                                     
                    Session::put('In_OUT_Message',$message);
                    $UpdateUserSearch = UserSearch::where('user_search_id',$dataInout['latest_search_id'])->update(['cycle_type' => 'out', 'appeal_type' => 'Year-3','appeal_deadline_date'=>$appealDeadline,'latest_assesement_year'=>$latest_assesement_year,'appeal_year'=>($currentYear+1)]);

                    $response['success']=1;
                    $response['redirect_url']='/assessment_not_ready';
                    $response['message']=$message;
                    $response['status']=2;
                    return $response;
                        echo "Case 2A";exit;
                   // }
                }
            }
        } 
        catch (\Exception $e) {
            return response()->json(['success'=>false, 'message' => $e->getMessage()]);
        }
        
    }
}
