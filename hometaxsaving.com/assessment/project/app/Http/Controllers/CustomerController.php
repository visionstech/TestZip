<?php 
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use Input;
use App\User;
use Illuminate\Contracts\Auth\Guard;
use Redirect;
use Mail;
use Session;
use View;
use App\State;
use App\County;
use App\MdInOutCycle;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use PDF;
use App\Helpers\Payment;
use App\Helpers\Helper;
use App\PfAddress;
use App\PfLookup;
use DB;
use App\UserSearch;
use App\SubjectCompsDetail;
use App\PfLookupType;
use Config;
use App\SearchComparable;
use Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\Filesystem;
use Auth;
class CustomerController extends Controller {
     // use PaymentTrait;

    /*
    |--------------------------------------------------------------------------
    | Customer Controller
    |--------------------------------------------------------------------------
    |
    | This controller manages customer's profile.
    |
    */
    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct(Guard $auth)
    {
        $token_status = 0;
        if(Session::has('token')) { //echo "<pre>const "; print_r(Session::all());
            $session_token = UserSearch::where('token', Session::get('token'))->where('end_date',null)->get();
            //echo "<pre>"; print_r($session_token);
            if(count($session_token)) { //echo 'if'; exit;
                $token_status = $session_token[0]->active_page;
            }
            else { //echo 'else'; exit;
                Session::flush();
                return redirect('/');
            }
        }
        
        View::share('token_status', $token_status);
    }
    
    /**
      * Return get Add User Form.
      * @param   
      * @return Response
    **/
    public function getDeleteSessionToken()
    {
        Session::flush();
        return redirect('/');
    }
    
    /**
      * Return get Add User Form.
      * @param   
      * @return Response
    **/
    public function getToken($token=null)
    {
        //echo $token; exit;
        if($token == null) {
            return redirect('/');
        }
        else { 
            Session::put('token', $token);
            return redirect('/token-status');
        }
    }
    
    
	
	/**
      * Return get Search Address Form.
      * @param   
      * @return Response
    **/
    public function getSearchAddress($view_status = 0)
    {
        try {
            /*$clientId = config('constants.clientId');
            $secret = config('constants.secret');
            $amt1 = config('constants.phase1Amt');
            $amt2 = config('constants.phase2Amt');
            
            
            echo $clientId.'<br/>'.$secret.'<br/>'.$amt1.'<br/>'.$amt2;exit;*/
            $userDetail=array();
            $addressDetail=array();
            $countyName = array();
            $stateName = array();
            $newSearch = 0;
            if( !empty(Session::get('firstTime')) && Session::get('firstTime') == 1 && !empty(Session::get('user_search_id')) ){
               $userDetail = UserSearch::where('user_search_id', Session::get('user_search_id') )->first();
               Session::put('fromsignup','1');
            } elseif ($view_status != "" && $view_status != '0' && $view_status != '1' ) {
                Session::put('fromsignup','0');
                $tokenId =  $view_status;
                $searchData = UserSearch::where('token', $tokenId)->first();
                if (!empty($searchData)) {
                    $userDetail = $searchData;
                } else {
                    $userDetail = UserSearch::where('system_user_id', Auth::user()->id)->first();
                    $newSearch = 1;
                }
            } else {
                Session::put('fromsignup','0');
                $userDetail = UserSearch::where('system_user_id', Auth::user()->id)->first();
                $newSearch = 1;
            }
            //echo "<pre>e";print_r($userDetail);exit;
            
            if($newSearch == 0 && !empty($userDetail) && $userDetail->count()){
                $system_object_type_id = Helper::toGenerateCommunicationObjectTypes('pf_lookups', 'name', 'address', Auth::user()->id);

                if(!empty($system_object_type_id)) {
                    $lookup_type = Helper::getLookupTypeIdFromName(config('constants.constants.lookup_type.address'));
                    $where_condition['lookup_type_id'] = $lookup_type[0]->lookup_type_id;
                    $address_type = Helper::getLookupIdFromName(config('constants.constants.lookup.address.search_address'), $where_condition);

                    $addressDetail = PfAddress::where(array('ref_object_id' => $userDetail->user_search_id, 'address_type' => $address_type[0]->lookup_id, 'system_object_type_id' => '1' ) )->first();
                }

                if($addressDetail->count()){
                    $countyName = County::where('county_id',$addressDetail->county)->get()->toArray();
                    $stateName = State::where('state_id',$addressDetail->state)->get()->toArray();
                }
            }

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
            return view('customer.search_address',compact('states', 'counties','userDetail','addressDetail','countyName','stateName', 'newSearch'));
        }
        catch (\Exception $e) 
        {
            $result = ['exception_message' => $e->getMessage()];
            return view('errors.error', $result);
        }
    }
    
    /**
      * Check search address details of user.
      * @param Request $request            
      * @return Response
    **/
    public function postSearchAddress(Requests\SearchAddress $request)
    {
        try {   
            $state_abbr = $request->administrative_area_level_1;
            $state_details = State::where('state_abbr', $state_abbr)->where('end_date',null)->lists('state_id');
            $county_name = $request->administrative_area_level_2;
            $county_details = County::where('state_id', $state_details[0])->where('county_name', $county_name)->where('end_date',null)->get();
            $first_name = $request->first_name; 
            $last_name = $request->last_name; 
            $email = $request->email; 
            $mobile_number = $request->mobile_number;
            $search_street_number = $request->street_number;
            $search_street = $request->route;
            $search_city = $request->locality;
            $search_state = $request->administrative_area_level_1; 
            $search_zipcode = $request->postal_code; 
            $search_county = $request->administrative_area_level_2; 
            $land_assessment_value = null;//$request->land_assessment_value;
            $improvement_assessment_value = null;//$request->improvement_assessment_value;
            $total_assessment_value = null;//$request->total_assessment_value;
            $autocomplete_search = $request->autocomplete_search;
            $user_id = Auth::user()->id;
            $latest_search_id = '';          
            

            if (!empty($request->search_id)) {
                //update exiting search
                $update_detail['land_assessment_value'] = $land_assessment_value;
                $update_detail['improvement_assessment_value'] = $improvement_assessment_value;
                $update_detail['total_assessment_value'] = $total_assessment_value;
                $update_detail['updated_by'] = $user_id;

                $update_search_detail = UserSearch::where('user_search_id', $request->search_id)->where('end_date',null)->update($update_detail);
                $latest_search_id = $request->search_id;
            } else {
                
                $where_condition['pf_addresses.address_line_1'] = $search_street_number;
                $where_condition['pf_addresses.address_line_2'] = $search_street;
                $where_condition['pf_addresses.city'] = $search_city;
                $where_condition['pf_addresses.state'] = (isset($state_details[0]) ? $state_details[0] : "");
                $where_condition['pf_addresses.postal_code'] = $search_zipcode;
                $where_condition['pf_addresses.county'] = (isset($county_details[0]) ? $county_details[0]->county_id : "" );
                $where_condition['pf_addresses.address_type'] = 1;
                //echo "<pre>eee";print_r($state_details);exit;
                if(isset($state_details[0]) && ($state_details[0] ==24)){
                    //MD CASE
                   // echo "1";exit;
                    $where_condition['user_searches.latest_assesement_year']=$request->assessment_year;
                    $where_condition['users.id']=$user_id;
            
                    $InOutAddressStatus=self::checkInOutAddressStatus($where_condition);
                    //echo "<pre>eee11";print_r($InOutAddressStatus);exit;
                    if($InOutAddressStatus['status']==1){
                        //You have already searched for Year 1 for latest assessment year 2018.
                       return Redirect::back()->withErrors(['You have already searched for '.$InOutAddressStatus['type'].' for latest assessment year '.$request->assessment_year ]); 
                    }
                    //echo "<pre>eee11";print_r($InOutAddressStatus);exit;
                }else{
                    //echo "2";exit;
                    $where_condition['user_searches.appeal_year'] = (date('Y') + 1);
                    $where_condition['users.id'] = $user_id;
                    $getAddress = User::join('user_searches', 'users.id', '=', 'user_searches.system_user_id')->join('pf_addresses', 'pf_addresses.ref_object_id', '=', 'user_searches.user_search_id')
                        ->select('users.id','user_searches.user_search_id','pf_addresses.address_id')
                        ->where($where_condition)
                        ->get();
                }                
                
                //echo "<pre>";print_r($getAddress);exit;
                /*$user_searches_list = UserSearch::where('system_user_id', $user_id)->lists('user_search_id');
                $user_address_exist = PfAddress::where($where_condition)->whereIn('ref_object_id', $user_searches_list)->count();
                if($user_address_exist > 0) {*/
                if(isset($getAddress) && count($getAddress)){
                    //Address already exist
                   // echo "Aaa";exit;
                   // return Redirect::back()->withErrors(['Search address already exists for appeal year '.date('Y').'. Please go to dashboard to view details.']);
                    return Redirect::back()->withErrors(['The address you have searched for already exists in your account for the current year.']);
                } else {
                    //echo "Aaa111";exit;
                    $user_search_high_key = Helper::getHighKey('user_searches', 'user_search_id', $user_id);
                    $user_search_token = encrypt($user_id).'-'.Helper::getCustomerToken(10); 
                        
                    $create_user_search = UserSearch::create([
                        'user_search_id' => (empty($user_search_high_key)) ? null : $user_search_high_key,
                        'system_user_id' => $user_id,
                        'search_date'    => date('Y-m-d H:i:s'),
                        'first_name'     => $first_name,
                        'last_name'      => $last_name,
                        'land_assessment_value' => $land_assessment_value,
                        'improvement_assessment_value' => $improvement_assessment_value,
                        'total_assessment_value' => $total_assessment_value,
                        'created_by' => $user_id,
                        'updated_by' => $user_id,
                        'token'      => $user_search_token,
                        'phase2_token' => null,
                        'active_page' => '0',
                    ]);

                    if($create_user_search) {                        
                        $latest_search_id = $create_user_search->user_search_id;
                        
                        $system_object_type_id = Helper::toGenerateCommunicationObjectTypes('pf_lookups', 'name', 'address', $user_id);

                        if(!empty($system_object_type_id)) {
                            $lookup_type = Helper::getLookupTypeIdFromName(config('constants.constants.lookup_type.address'));
                            $whereCondition['lookup_type_id'] = $lookup_type[0]->lookup_type_id;

                            // Billing address save here
                            $address_type = Helper::getLookupIdFromName(config('constants.constants.lookup.address.search_address'), $whereCondition);
                            $address_high_key = Helper::getHighKey('pf_addresses', 'address_id', $user_id);
                            $create_search_address = PfAddress::create([
                                'address_id' => (empty($address_high_key)) ? null : $address_high_key,
                                'system_object_type_id' => $system_object_type_id,
                                'ref_object_id' => $create_user_search->user_search_id,
                                'address_type' => $address_type[0]->lookup_id,
                                'mobile_number' => $mobile_number,
                                'receive_notification' => '0',
                                'address_line_1' => $search_street_number,
                                'address_line_2' => $search_street, 
                                'city' => $search_city,
                                'postal_code' => $search_zipcode,
                                'state' => (isset($state_details[0]) ? $state_details[0] : ""),
                                'county' => (isset($county_details[0]) ? $county_details[0]->county_id : "" ),
                                'created_by' => $user_id,
                                'updated_by' => $user_id,
                            ]);
                            
                        } else {
                            DB::rollback();
                            return Redirect::back()->withErrors(['Search address doesn\'t added, please try again.']);
                        }
                    } else {
                        return Redirect::back()->withErrors(['When address details could not be found (error from Corelogic?).']);
                    }
                }
            }
            //MD In-Out 9 Feb 2018 
            if($request->in_out_case==1 && count($county_details) && Session::get('fromsignup')=='0'){
                $dataInout['latest_assesement_year']=$request->assessment_year;
                $dataInout['county_id']=$county_details[0]->county_id;
                $dataInout['latest_search_id']=$latest_search_id;
                $InOutStatus=self::checkInOutStatus($dataInout);

                if($InOutStatus['success']){ 

                    if($InOutStatus['redirect_url']=='/make-payment'){
                        if($request->search_id){
                            $user_search_token = UserSearch::where('user_search_id', $request->search_id)->lists('token'); 
                             $InOutStatus['redirect_url']='/make-payment/'.$user_search_token[0];   
                        }else{
                            $InOutStatus['redirect_url']='/make-payment/'.$user_search_token;
                        }
                        if( isset($InOutStatus['status']) && $InOutStatus['status']==2){
                            $search_update_detail['status'] = 2;
                            $update_search_comp_detail = UserSearch::where('user_search_id', $latest_search_id)->where('end_date',null)->update($search_update_detail);    
                        }
                        
                       //echo "<pre>e";print_r($InOutStatus);exit;
                    }
                    return redirect($InOutStatus['redirect_url'])->with('success', $InOutStatus['message']);    
                }
            }
            //End MD In-Out

            if(($state_abbr == 'VA' || $state_abbr == 'DC' || $state_abbr == 'MD')) {


                if(count($county_details) &&  strtotime(date('Y-m-d H:i:s')) >= strtotime($county_details[0]->notice_date) && strtotime(date('Y-m-d H:i:s')) <= strtotime($county_details[0]->appeal_deadline_date)) {

                    $update_sear_detail['active_page'] = '1';
                    //SAVING APPEAL YEAR
                    $update_sear_detail['appeal_year'] = (date('Y') + 1);
                    //END
                    $update_search_page = UserSearch::where('user_search_id', $latest_search_id)->where('end_date',null)->update($update_sear_detail);

                    $getSearchInfo = Helper::getSearchDetail($latest_search_id);
                    
                    if(!empty($getSearchInfo) ){
                        $searchToken = $getSearchInfo->token;
                        return redirect('/make-payment/'.$searchToken);  
                    } else {
                        DB::rollback();
                        return Redirect::back()->withErrors(['Search address added, please make payment.']);
                    }
                } else {
                    $search_update_detail['status'] = 2;
                    $search_update_detail['appeal_year'] = (date('Y') + 1);
                    $update_search_comp_detail = UserSearch::where('user_search_id', $latest_search_id)->where('end_date',null)->update($search_update_detail);
                    return redirect()->to(url('/'))->withErrors(['We will be sending you automated updates when your new assessment is issued so we can review your taxes and determine if you will be able to save money on your taxes.']);
                }
            } else { 
                $search_update_detail['status'] = 1;
                $update_search_comp_detail = UserSearch::where('user_search_id', $latest_search_id)->where('end_date',null)->update($search_update_detail);
                //return redirect()->to(url('no_address_after_login'));
                return redirect()->to(url('/'))->withErrors(['Unfortunately, your home address is not located in our current coverage area. We will notify you when this changes.']);
            }
                    
        } catch (\Exception $e) {   
            $result = ['exception_message' => $e->getMessage()];
            return view('errors.error', $result);
        }    
        
    }


    /**
      * Check Address Already Exists
      * @param  POST
      * @return Response
    **/
    public function checkInOutAddressStatus($whereCondition){

        //return 1;
        //echo "<pre>dd3454";print_r($whereCondition);exit;
        
        //echo "<pre>";print_r($dataInout['latest_search_id']);exit;
        try{

            $MdInOutDetail = MdInOutCycle::where('county_id', $whereCondition['pf_addresses.county'])->where('end_date',null)->get();
            $response=array();
            if(count($MdInOutDetail)){
                $currentDate=date('Y-m-d');
                $currentYear=date('Y');
                $latest_assesement_year = $whereCondition['user_searches.latest_assesement_year'];
                //In-Cycle Case
                if($latest_assesement_year == $currentYear){
                    
                    if((strtotime($currentDate) <= strtotime($MdInOutDetail[0]->incycle_deadline_date)) && (strtotime($currentDate) >= strtotime($MdInOutDetail[0]->incycle_notice_date))){
                        //CHECK appeal Year Apply for 2018 For Y1.
                        $whereCondition['user_searches.appeal_year']=$currentYear;
                        $getAddress = User::join('user_searches', 'users.id', '=', 'user_searches.system_user_id')->join('pf_addresses', 'pf_addresses.ref_object_id', '=', 'user_searches.user_search_id')
                            ->select('users.id','user_searches.user_search_id','pf_addresses.address_id')
                            ->where($whereCondition)
                            ->get();
                        if(count($getAddress)){
                            $response['status']=1;
                            $response['type']='year-1';
                        }else{
                            $response['status']=0;
                            $response['type']='year-1';

                        }
                        return $response;
                        //echo "<pre>e1";print_r($getAddress);exit;

                    }else if((strtotime($currentDate) <= strtotime($MdInOutDetail[0]->outcycle_deadline_date)) && (strtotime($currentDate) >= strtotime($MdInOutDetail[0]->outcycle_notice_date))){
                        //CHECK appeal Year Apply for 2019 For Y2.
                        $whereCondition['user_searches.appeal_year']=($currentYear+1);
                        $getAddress = User::join('user_searches', 'users.id', '=', 'user_searches.system_user_id')->join('pf_addresses', 'pf_addresses.ref_object_id', '=', 'user_searches.user_search_id')
                            ->select('users.id','user_searches.user_search_id','pf_addresses.address_id')
                            ->where($whereCondition)
                            ->get();
                        if(count($getAddress)){
                            $response['status']=1;
                            $response['type']='year-1';
                        }else{
                            $response['status']=0;
                            $response['type']='year-1';

                        }
                        return $response;
                        echo "<pre>e";print_r($getAddress);exit;

                    }else{
                        //Case 1C
                        $whereCondition['user_searches.appeal_year']=($currentYear+1);
                        $getAddress = User::join('user_searches', 'users.id', '=', 'user_searches.system_user_id')->join('pf_addresses', 'pf_addresses.ref_object_id', '=', 'user_searches.user_search_id')
                            ->select('users.id','user_searches.user_search_id','pf_addresses.address_id')
                            ->where($whereCondition)
                            ->get();
                        if(count($getAddress)){
                            $response['status']=1;
                            $response['type']='year-1';
                        }else{
                            $response['status']=0;
                            $response['type']='year-1';

                        }    
                       // $response['status']=0;
                        //$response['type']='assesment_not_ready';
                        return $response;
                    }
                }

                //Out-Cycle Case
                if($latest_assesement_year == ($currentYear-1)){
                    
                    if((strtotime($currentDate) <= strtotime($MdInOutDetail[0]->outcycle_deadline_date)) && (strtotime($currentDate) >= strtotime($MdInOutDetail[0]->outcycle_notice_date))){
                        //Check For Year-3 appeal year         
                        $whereCondition['user_searches.appeal_year']=($currentYear+1);
                        $getAddress = User::join('user_searches', 'users.id', '=', 'user_searches.system_user_id')->join('pf_addresses', 'pf_addresses.ref_object_id', '=', 'user_searches.user_search_id')
                            ->select('users.id','user_searches.user_search_id','pf_addresses.address_id')
                            ->where($whereCondition)
                            ->get();
                        if(count($getAddress)){
                            $response['status']=1;
                            $response['type']='year-3';
                        }else{
                            $response['status']=0;
                            $response['type']='year-3';

                        }
                        return $response;
                    }else{
                        //Case 2B
                        $whereCondition['user_searches.appeal_year']=($currentYear+1);
                        $getAddress = User::join('user_searches', 'users.id', '=', 'user_searches.system_user_id')->join('pf_addresses', 'pf_addresses.ref_object_id', '=', 'user_searches.user_search_id')
                            ->select('users.id','user_searches.user_search_id','pf_addresses.address_id')
                            ->where($whereCondition)
                            ->get();
                        if(count($getAddress)){
                            $response['status']=1;
                            $response['type']='year-3';
                        }else{
                            $response['status']=0;
                            $response['type']='year-3  assesment_not_ready';

                        }
                        //$response['status']=0;
                        //$response['type']='year-3 assesment_not_ready';
                        return $response;
                    }
                }

                if($latest_assesement_year <= ($currentYear-2)){
                        
                    $response['status']=0;
                    $response['type']='year > 2 assesment_not_ready';

                    $message='You will be able to Appeal your upcoming tax assessment scheduled for '.date('F d',strtotime($MdInOutDetail[0]->incycle_notice_date)).', '.($currentYear+1).' between'.date('F d',strtotime($MdInOutDetail[0]->incycle_notice_date)).', '.($currentYear+1).'-'.date('F d',strtotime($MdInOutDetail[0]->incycle_deadline_date)).', '.($currentYear+1);                    
                   return $response;
                }

            }
        } 
        catch (\Exception $e) {
            return response()->json(['success'=>false, 'message' => $e->getMessage()]);
        }

    }
    
    /**
      * Check In-Out Case.
      * @param County_id, Latest_assesment_year            
      * @return Response
    **/
    public function checkInOutStatus($dataInout){
        //echo "<pre>";print_r($dataInout['latest_search_id']);exit;
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
                        $response['redirect_url']='/make-payment';
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
                        $response['redirect_url']='/make-payment';
                        $response['message']=$message;
                        return $response;
                        echo "Case 1B";exit;
                    }else{
                    //Case 1C
                        //$message='You Will get Notification when appeal date is available.';
                        $message='We will be sending you automated updates when your new assessment is issued so we can review your taxes and determine if you will be able to save money on your taxes.';
                        Session::put('In_OUT_Message',$message);
                        $UpdateUserSearch = UserSearch::where('user_search_id',$dataInout['latest_search_id'])->update(['cycle_type' => 'in', 'appeal_type' => 'Year-2','appeal_deadline_date'=>$MdInOutDetail[0]->outcycle_deadline_date,'latest_assesement_year'=>$latest_assesement_year,'appeal_year'=>($currentYear+1),'status'=>2]);
                        //$response['redirect_url']='/';
                        $response['success']=1;
                        $response['status']=2;
                        $response['redirect_url']='/';
                        $response['message']=$message;
                        return $response;
                        echo "Case 1C";exit;
                    }
                }

                //Out-Cycle Case
                if($latest_assesement_year == ($currentYear-1)){
                   // echo "dfgdfgdfg";exit;
                    if((strtotime($currentDate) <= strtotime($MdInOutDetail[0]->outcycle_deadline_date)) && (strtotime($currentDate) >= strtotime($MdInOutDetail[0]->outcycle_notice_date))){
                        //Case 2A
                        $message='You can appeal for year 3 before '.date('F d, Y',strtotime($MdInOutDetail[0]->outcycle_deadline_date));
                        Session::put('In_OUT_Message',$message);
                        //Update User Search
                        $UpdateUserSearch = UserSearch::where('user_search_id',$dataInout['latest_search_id'])->update(['cycle_type' => 'out', 'appeal_type' => 'Year-3','appeal_deadline_date'=>$MdInOutDetail[0]->outcycle_deadline_date,'latest_assesement_year'=>$latest_assesement_year,'appeal_year'=>($currentYear+1)]);  
                        //echo "Case 2A";exit;
                        //$response['redirect_url']='/make-payment';
                        $response['success']=1;
                        $response['redirect_url']='/make-payment';
                        $response['message']=$message;
                        return $response;
                    }else{
                        //Case 2B
                        //$message='You Will get Notification when appeal date is available for year 3.';
                        $message='We will be sending you automated updates when your new assessment is issued so we can review your taxes and determine if you will be able to save money on your taxes.';
                        Session::put('In_OUT_Message',$message);
                        $UpdateUserSearch = UserSearch::where('user_search_id',$dataInout['latest_search_id'])->update(['cycle_type' => 'out', 'appeal_type' => 'Year-3','appeal_deadline_date'=>$MdInOutDetail[0]->outcycle_deadline_date,'latest_assesement_year'=>$latest_assesement_year,'appeal_year'=>($currentYear+1),'status'=>2]);  
                        //echo "Case 2B";exit;
                        $response['status']=2;
                        $response['success']=1;
                        $response['redirect_url']='/';
                        $response['message']=$message;
                        return $response;
                    }
                }

                if($latest_assesement_year <= ($currentYear-2)){
                    /*if((strtotime($currentDate) <= strtotime($MdInOutDetail[0]->outcycle_deadline_date)) && (strtotime($currentDate) >= strtotime($MdInOutDetail[0]->outcycle_notice_date))){    */ 
                    $message='We will be sending you automated updates when your new assessment is issued so we can review your taxes and determine if you will be able to save money on your taxes. You will be able to Appeal your upcoming tax assessment scheduled for '.date('F d',strtotime($MdInOutDetail[0]->incycle_notice_date)).', '.($currentYear+1).' between '.date('F d',strtotime($MdInOutDetail[0]->incycle_notice_date)).', '.($currentYear+1).' - '.date('F d',strtotime($MdInOutDetail[0]->incycle_deadline_date)).', '.($currentYear+1);

                    $appealDeadline=date('F d',strtotime($MdInOutDetail[0]->incycle_deadline_date)).', '.($currentYear+1);

                    $UpdateUserSearch = UserSearch::where('user_search_id',$dataInout['latest_search_id'])->update(['cycle_type' => 'out', 'appeal_type' => 'Year-3','appeal_deadline_date'=>$appealDeadline,'latest_assesement_year'=>$latest_assesement_year,'appeal_year'=>($currentYear+1),'appeal_year'=>($currentYear+1),'status'=>2]);

                    Session::put('In_OUT_Message',$message);
                    $response['success']=1;
                    $response['status']=2;
                    $response['redirect_url']='/';
                    $response['message']=$message;
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
    
    /**
      * Save search address details of user.
      * @param Request $request            
      * @return Response
    **/
    public function postExistSearchAddress($search_details)
    {
        try {  
            
            $user = User::where('email', $search_details['email'])->get();
            if(count($user) > 0) {
                
                $state_details = State::where('state_abbr', $search_details['administrative_area_level_1'])->where('end_date',null)->lists('state_id');
                $search_state = $state_details[0];

                $county_details = County::where('state_id', $state_details[0])->where('county_name', $search_details['administrative_area_level_2'])->where('end_date',null)->lists('county_id');
                $search_county = $county_details[0];
                
                $user_searches_list = UserSearch::where('system_user_id', $user[0]->id)->lists('user_search_id');

                $user_searches_token = UserSearch::where('system_user_id', $user[0]->id)->get();
                //echo "<pre>"; print_r($user_searches_list); exit;
                
                //$where_condition['user_search_id'] = $user_searches_list;
                $where_condition['address_line_1'] = $search_details['street_number'];
                $where_condition['address_line_2'] = $search_details['route'];
                $where_condition['city'] = $search_details['locality'];
                $where_condition['state'] = $search_state;
                $where_condition['postal_code'] = $search_details['postal_code'];
                $where_condition['county'] = $search_county;
                $where_condition['address_type'] = 1;
                
                $user_address_exist = PfAddress::where($where_condition)->whereIn('ref_object_id', $user_searches_list)->count();
                
                if($user_address_exist > 0) {
                //if(($user_searches_token->count() && $user_searches_token[0]->token != null)) {
                    $response['success'] = false;
                    $response['message'] = 'Sorry, you already have a token for this address. Please use that token.';
                }else {
                    $response['success'] = true;
                    $response['message'] = '';
                }
            }
            else {
                $response['success'] = true;
                $response['message'] = '';
            }
            
            return $response;
            
        
        } 
        catch (\Exception $e) {
            return response()->json(['success'=>false, 'message' => $e->getMessage()]);
        }
        
    }
    
    
    /**
      * Save search address details of user.
      * @param Request $request            
      * @return Response
    **/
    public function postSaveSearchAddress()
    {
        try {  

            DB::beginTransaction();
            
            $first_name = Session::get('first_name'); 
            $last_name = Session::get('last_name'); 
            $email = Session::get('email'); 
            $mobile_number = Session::get('mobile_number');            
            $search_street_number = Session::get('search_street_number');
            $search_street = Session::get('search_street');
            
            $api_values['saleDateFromDate'] = "";
            $api_values['saleDateToDate'] = "";

            $api_values['street'] = $search_street_number.' '.$search_street;
            $api_values['city'] = $search_city = Session::get('search_city');
            //$api_values = Session::get('search_state'); 
            $api_values['zipcode'] = $search_zipcode = Session::get('search_zipcode'); 
            //$search_county = Session::get('search_county'); 
            $land_assessment_value = Session::get('land_assessment_value');
            $improvement_assessment_value = Session::get('improvement_assessment_value');
            $total_assessment_value = Session::get('total_assessment_value');
            if(Session::has('make_payment')) {
                $make_payment = Session::get('make_payment');
                $receive_notification = Session::get('receive_notification');
            }            
            
            $api_values['state'] = Session::get('search_state');
            $state_details = State::where('state_abbr', Session::get('search_state'))->where('end_date',null)->lists('state_id');
            //echo "<pre>";print_r( $state_details);exit;
            $search_state = $state_details[0];
            //echo Session::get('search_state').'<br/>';
            //echo Session::get('search_county').'<br/>';
            $county_details = County::where('state_id', $state_details[0])->where('county_name', Session::get('search_county'))->where('end_date',null)->lists('county_id');
            $search_county = $county_details[0];
            $api_values['DistanceFromSubjectNumber'] = $api_values['LandUse'] = $api_values['LivingAreaVariancePercent'] = $api_values['NumCompsReturned'] = $api_values['MonthsBackNumber'] = '';
            $search_criteria_lookup = PfLookupType::where('name', 'Search_criteria')->get();
            if(count($search_criteria_lookup)) {
                $search_criteria_conditions = PfLookup::where('lookup_type_id', $search_criteria_lookup[0]->lookup_type_id)->get();
                if(count($search_criteria_conditions)) {
                    foreach($search_criteria_conditions as $search_criteria_condition) {
                        if($search_criteria_condition->name == 'DistanceFromSubjectNumber') {
                            $api_values['DistanceFromSubjectNumber'] = $search_criteria_condition->value;
                        }
                        if($search_criteria_condition->name == 'LandUse') {
                            //$LandUse = $search_criteria_condition->value;
                            $api_values['LandUse'] = '<_LAND_USE _SameAsSubjectType = "Yes" />';
                        }
                        if($search_criteria_condition->name == 'LivingAreaVariancePercent') {
                            $api_values['LivingAreaVariancePercent'] = $search_criteria_condition->value;
                        }
                        if($search_criteria_condition->name == 'NumCompsReturned') {
                            $api_values['NumCompsReturned'] = $search_criteria_condition->value;
                        }
                        /*if($search_criteria_condition->name == 'MonthsBackNumber') {
                            $api_values['MonthsBackNumber'] = $search_criteria_condition->value;
                        }*/
                    }
                }
            }
            
            if(isset($make_payment) && $make_payment == '1') {
                //$check_valid_address = self::postCheckValidAddress($api_values);
                //echo "<pre>";print_r($api_values);exit;
                $check_valid_address = $this->postCheckValidAddress($api_values);
                //echo "<pre>";print_r($check_valid_address);exit;
                if($check_valid_address['success'] == '1') {
                    $subject_property = $check_valid_address['result_array'];            
                    $api_street_address = $subject_property['PROPERTY']['@attributes']['_StreetAddress'];
                    $api_city = $subject_property['PROPERTY']['@attributes']['_City'];
                    $api_state = $subject_property['PROPERTY']['@attributes']['_State'];
                    $api_zipcode = $subject_property['PROPERTY']['@attributes']['_PostalCode'];
                    $api_county = $subject_property['PROPERTY']['@attributes']['_County'];

                    $type_of_house = trim($subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_SITE']['_CHARACTERISTICS']['@attributes']['_LandUseDescription']);
                    $square_footage = trim($subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_ROOM_COUNT']['@attributes']['_GrossLivingAreaSquareFeetNumber']);
                    $square_footage = ($square_footage != '') ? $square_footage : '0';
                    $total_bedrooms = trim($subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_ROOM_COUNT']['@attributes']['_TotalBedroomsCount']);
                    $total_bedrooms = ($total_bedrooms != '') ? $total_bedrooms : '0';
                    $total_bathrooms = trim($subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_ROOM_COUNT']['@attributes']['_TotalBathsCount']);
                    $total_bathrooms = ($total_bathrooms != '') ? $total_bathrooms : '0';
                    $total_basement_space = trim($subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_BASEMENT']['@attributes']['_AreaSquareFeet']);
                    $total_basement_space = ($total_basement_space != '') ? $total_basement_space : '0';
                    $finished_space = trim($subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_BASEMENT']['@attributes']['_FinishedAreaSquareFeet']);
                    $finished_space = ($finished_space != '') ? $finished_space : '0';
                    
                    $unfinished_space = $total_basement_space - $finished_space;

                    //$garage_exist = (strpos($subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_PARKING']['@attributes']['_TypeDescription'], 'are') !== false) ? '1' : '0';
                    //$carport_exist = (!empty($subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_PARKING']['@attributes']['_TypeDescription'])) ? '1' : '0';
                    //$garage_exist = (strpos(strtolower($subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_PARKING']['@attributes']['_TypeDescription']), 'garage') !== false) ? '1' : '0';
                    /*
                    $garage_exist = (!empty($subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_PARKING']['@attributes']['_GarageSquareFeetNumber'])) ? '1' : '0';
                    if($garage_exist == '1') {
                        //$garage_count = (!empty($subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_PARKING']['@attributes']['_GarageTwoArea'])) ? '2' : '1';
                        $garage_count = round($subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_PARKING']['@attributes']['_GarageSquareFeetNumber'] / 400, 0);
                    }
                    else {
                        $garage_count = '0';
                    }
                     */
                    
                    if(!empty($subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_PARKING']['@attributes']['_GarageTotalCarCount'])) {
                        $garage_count = $subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_PARKING']['@attributes']['_GarageTotalCarCount'];
                    }
                    else {
                        $garage_area_total = $subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_PARKING']['@attributes']['_GarageSquareFeetNumber'] + $subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_PARKING']['@attributes']['_GarageTwoArea'];
                        if(!empty($garage_area_total) && $garage_area_total < 200) {
                            $garage_count = '1';
                        }
                        else {
                            $garage_count = round(($garage_area_total) / 400, 0);
                        }
                    }
                    
                    
                    //$garage_count = round(($subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_PARKING']['@attributes']['_GarageSquareFeetNumber'] + $subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_PARKING']['@attributes']['_GarageTwoArea']) / 400, 0);
                    
                    $carport_exist = (!empty($subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_PARKING']['@attributes']['_CarportArea'])) ? '1' : '0';

                    $porch_deck_exist = '0';
                    $patio_exist = '0';
                    foreach($subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_XTRA_FEATURES'] as $extra_features) {
                        if(isset($extra_features['@attributes'])) {
                            if(((strpos($extra_features['@attributes']['_Xtra_Features_Code'], 'porch') !== false) || (strpos($extra_features['@attributes']['_Xtra_Features_Code'], 'deck') !== false)) && $porch_deck_exist == '0') {
                                $porch_deck_exist = '1';
                            }
                            if(strpos($extra_features['@attributes']['_Xtra_Features_Code'], 'patio') !== false && $patio_exist == '0') {
                                $patio_exist = '1';
                            }
                        }                    
                    }

                    $fireplace_exist = '0';
                    $fireplace_count = '0';
                    $pool_exist = '0';
                    foreach($subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_FEATURES'] as $feature_key => $features) {
                        if($feature_key == '_FIREPLACES') {
                            $fireplace_exist = ($features['@attributes']['_HasFeatureIndicator'] == 'Y') ? '1' : '0';
                            $fireplace_count = ($fireplace_exist == '1') ? $features['@attributes']['_CountNumber'] : '0';
                        }
                        if($feature_key == '_POOL') {
                            $pool_exist = ($features['@attributes']['_HasFeatureIndicator'] == 'Y') ? '1' : '0';
                        }
                    }                       
                }     
                else {
                    $response['success'] = false;
                    $response['message'] = 'Sorry, your address is invalid.';
                    return $response;
                }
            }
            
            if(isset($make_payment) && $make_payment == '1') {
                $billing_street_number = Session::get('billing_street_number');
                $billing_street = Session::get('billing_street');
                $billing_city = Session::get('billing_city');
                //$billing_state = Session::get('billing_state');
                $billing_zipcode = Session::get('billing_zipcode');
                //$billing_county = Session::get('billing_county');
                
                $billing_state_details = State::where('state_abbr', Session::get('billing_state'))->where('end_date',null)->lists('state_id');
                $billing_state = $billing_state_details[0];
                //echo 'here'.Session::get('billing_county');
                $billing_county_details = County::where('state_id', $billing_state_details[0])->where('county_name', Session::get('billing_county'))->where('end_date',null)->lists('county_id');
                $billing_county = $billing_county_details[0];                
            }
            
            $user_id = '';
            $user = User::where('email', $email)->get();
            if(count($user)) {
                $user_id = $user[0]->id;
                //$user_name = $user[0]->name;
                $user_email = $user[0]->email;
            }
            else {
                $create_user = User::create([
                    //'name' => $name,
                    'email' => $email,
                    'ip_address' => $_SERVER['REMOTE_ADDR'],
                ]);
                $user_id = $create_user->id;
                //$user_name = $create_user->name;
                $user_email = $create_user->email;
            }
            
            $user_name = $first_name.' '.$last_name;
 
            if($user_id != '') {                   
                $user_search_high_key = Helper::getHighKey('user_searches', 'user_search_id', $user_id);
                //$user_search_token = encrypt($user_id).'-'.$this->getCustomerToken(10); 
				if(Session::has('token')){
                    $user_search_token=Session::get('token');
                }else{
                    $user_search_token = encrypt($user_id).'-'.Helper::getCustomerToken(10); 
                    Session::put('token',$user_search_token);
                    
                }
    
				
				// Added value 0 for phase2 token. This will be populated when phase2 payment is // done. 25th Oct 2017
				$phase2_token = '0';
                $create_user_search = UserSearch::create([
                    'user_search_id' => (empty($user_search_high_key)) ? null : $user_search_high_key,
                    'system_user_id' => $user_id,
                    'search_date'    => date('Y-m-d H:i:s'),
                    'first_name'     => $first_name,
                    'last_name'      => $last_name,
                    'land_assessment_value' => $land_assessment_value,
                    'improvement_assessment_value' => $improvement_assessment_value,
                    'total_assessment_value' => $total_assessment_value,
                    'created_by' => $user_id,
                    'updated_by' => $user_id,
                    'token'      => $user_search_token,
					'phase2_token' => $phase2_token,
                    'active_page' => '1',
                ]);
                
                if($create_user_search) {                        
                    $system_object_type_id = Helper::toGenerateCommunicationObjectTypes('pf_lookups', 'name', 'address', $user_id);
                    if(!empty($system_object_type_id)) {

                        //$lookup_type = PfLookupType::where('name', config('constants.constants.lookup_type.address'))->where('end_date',null)->get(['lookup_type_id']);
                        //$address_type = PfLookup::where('name', config('constants.constants.lookup.address.search_address'))->where('lookup_type_id', $lookup_type[0]->lookup_type_id)->where('end_date',null)->get(['lookup_id']);
                        
                        $lookup_type = Helper::getLookupTypeIdFromName(config('constants.constants.lookup_type.address'));
                        $where_condition['lookup_type_id'] = $lookup_type[0]->lookup_type_id;
                        $address_type = Helper::getLookupIdFromName(config('constants.constants.lookup.address.search_address'), $where_condition);
                        
                        $address_high_key = Helper::getHighKey('pf_addresses', 'address_id', $user_id);
                        $create_search_address = PfAddress::create([
                            'address_id' => (empty($address_high_key)) ? null : $address_high_key,
                            'system_object_type_id' => $system_object_type_id,
                            'ref_object_id' => $create_user_search->user_search_id,
                            'address_type' => $address_type[0]->lookup_id,
                            'mobile_number' => $mobile_number,
                            'receive_notification' => (isset($receive_notification)) ? $receive_notification : '0',
                            'address_line_1' => $search_street_number,
                            'address_line_2' => $search_street, //(isset($api_street_address)) ? $api_street_address : $search_street,
                            'city' => (isset($api_city)) ? $api_city : $search_city,
                            'postal_code' => (isset($api_zipcode)) ? $api_zipcode : $search_zipcode,
                            'state' => $search_state, //(isset($api_state)) ? $api_state : $search_state,
                            'county' => $search_county, //(isset($api_county)) ? $api_county : $search_county,
                            'created_by' => $user_id,
                            'updated_by' => $user_id,
                        ]);

                        if(isset($make_payment) && $make_payment == '1') {
                           
                            
                            $where_condition['lookup_type_id'] = $lookup_type[0]->lookup_type_id;
                            $address_type2 = Helper::getLookupIdFromName(config('constants.constants.lookup.address.billing_address'), $where_condition);
                            
                            $address_high_key = Helper::getHighKey('pf_addresses', 'address_id', $user_id);
                            $create_billing_address = PfAddress::create([
                                'address_id' => (empty($address_high_key)) ? null : $address_high_key,
                                'system_object_type_id' => $system_object_type_id,
                                'ref_object_id' => $create_user_search->user_search_id,
                                'address_type' => $address_type2[0]->lookup_id,
                                'mobile_number' => $mobile_number,
                                'receive_notification' => (isset($receive_notification)) ? $receive_notification : '0',
                                'address_line_1' => $billing_street_number,
                                'address_line_2' => $billing_street,
                                'city' => $billing_city,
                                'postal_code' => $billing_zipcode,
                                'state' => $billing_state,
                                'county' => $billing_county,
                                'created_by' => $user_id,
                                'updated_by' => $user_id,
                            ]);                      
                        }                        
                        
                        if(isset($make_payment)) {
                            Session::put('token', $user_search_token);    
                        }
                        $response['session_token'] = $user_search_token;
                        /*
                        $api_values['street'] = $search_street;
                        $api_values['city'] = $search_city;
                        $api_values['state'] = $search_state_name;
                        $api_values['zip_code'] = $search_zipcode;             
                        $check_valid_address = $this->postCheckValidAddress($api_values);
                        */
                        
                        if(isset($make_payment) && $make_payment == '1' && $check_valid_address['success'] == '1') {
                            $system_object_type_id2 = Helper::toGenerateCommunicationObjectTypes('user_searches', 'system_user_id', 'user_search', $user_id);
                            if(!empty($system_object_type_id2)) {
                                $subject_comps_high_key = Helper::getHighKey('subject_comps_details', 'subject_comps_id', $user_id);
                                $create_subject_comps = SubjectCompsDetail::create([
                                    'subject_comps_id' => (empty($subject_comps_high_key)) ? null : $subject_comps_high_key,
                                    'system_object_type_id' => $system_object_type_id2,
                                    'ref_object_id' => $create_user_search->user_search_id,
                                    'type_of_house' => $type_of_house,
                                    'square_footage' => $square_footage,
                                    'bedrooms' => $total_bedrooms,
                                    'bathrooms' => $total_bathrooms,
                                    'unfinished_space' => $unfinished_space,
                                    'finished_space' => $finished_space,
                                    'garage' => $garage_count,
                                    'carport' => $carport_exist,
                                    'porch_deck' => $porch_deck_exist,
                                    'patio' => $patio_exist,
                                    'swimming_pool' => $pool_exist,
                                    'fireplace' => $fireplace_count,
                                    'created_by' => $user_id,
                                    'updated_by' => $user_id,
                                ]);             
                            }
                        }                
                        
                    }
                }    

                if(isset($make_payment)) {
                    /*
                    $mail_data['customer_email'] = $create_user->email;
                    $mail_data['customer_link'] = url('/token/'.$user_search_token);
                    $mail_data['subject'] = 'Payment Received';
                    
                    $mail_to = array($create_user->email);
                    $mail_sent = Helper::SendMail($mail_data, $mail_to);
                    */
                    $customer_link = url('/token/'.$user_search_token);
                    $mail_data['username'] = $user_email;
                    $mail_data['user_name'] = $user_name;
                    $mail_data['content'] = 'Thank you for making payment, Please <a href="'.$customer_link.'" target="_blank" style="color:#1570C3;">click</a> on the link to complete the Tax Appeal. <br>
                        Link is valid for 15 days.';
                    $mail_data['subject'] = 'Payment Received';

                    $mail_to = array($user_email);
                    $mail_sent = Helper::SendMail($mail_data, $mail_to);
                    
                    /*
                    Mail::send('emails.email_template', $mail_data, function ($message) use($create_user) {
                        $message->to($create_user->email)
                                ->subject('Payment Received');
                    });
                    */
                    $response['redirect_url'] = url('/token-status');
                }                            
                else {
                    $response['redirect_url'] = url('/thank-you');
                }
                $response['success'] = true;        
            }
            else {
                $response['success'] = false;
                $response['message'] = 'Sorry, something went wrong. 1';
            }
            
            DB::commit();            
            return $response;
            //return response()->json(['success'=>true, 'redirect_url' => url('/thankyou')]);
        
        }        
        catch (\Exception $e) 
        {   
            //$result = ['exception_message' => $e->getMessage()];
            //return view('errors.error', $result);
            DB::rollback();
            $response['success'] = false;
            $response['message'] = $e->getMessage();
            return $response;
            //return response()->json(['success'=>false, 'message' => $e->getMessage()]);
        }   
        
    }

    /**
      * Return Save Payment Data
      * @param   
      * @return Response
    **/
    public function postSavePaymentData($token=0)
    {
        try {  

            DB::beginTransaction();
            
            $first_name = Session::get('first_name'); 
            $last_name = Session::get('last_name'); 
            $email = Session::get('email'); 
            $mobile_number = Session::get('mobile_number');            
            $search_street_number = Session::get('search_street_number');
            $search_street = Session::get('search_street');
            
            $api_values['saleDateFromDate'] = "";
            $api_values['saleDateToDate'] = "";
            $api_values['searchType'] = 'subject';
            $subject_property = array();

            $api_values['street'] = $search_street_number.' '.$search_street;
            $api_values['city'] = $search_city = Session::get('search_city');
            //$api_values = Session::get('search_state'); 
            $api_values['zipcode'] = $search_zipcode = Session::get('search_zipcode'); 
            //$search_county = Session::get('search_county'); 
            $land_assessment_value = Session::get('land_assessment_value');
            $improvement_assessment_value = Session::get('improvement_assessment_value');
            $total_assessment_value = Session::get('total_assessment_value');
            if(Session::has('make_payment')) {
                $make_payment = Session::get('make_payment');
                $receive_notification = Session::get('receive_notification');
            }            
            $make_payment=1;
            $api_values['state'] = Session::get('search_state');
            
            $api_values['DistanceFromSubjectNumber'] = $api_values['LandUse'] = $api_values['LivingAreaVariancePercent'] = $api_values['NumCompsReturned'] = $api_values['MonthsBackNumber'] = '';
            $search_criteria_lookup = PfLookupType::where('name', 'Search_criteria')->get();
            if(count($search_criteria_lookup)) {
                $search_criteria_conditions = PfLookup::where('lookup_type_id', $search_criteria_lookup[0]->lookup_type_id)->get();
                if(count($search_criteria_conditions)) {
                    foreach($search_criteria_conditions as $search_criteria_condition) {
                        if($search_criteria_condition->name == 'DistanceFromSubjectNumber') {
                            $api_values['DistanceFromSubjectNumber'] = $search_criteria_condition->value;
                        }
                        if($search_criteria_condition->name == 'LandUse') {
                            $api_values['LandUse'] = '<_LAND_USE _SameAsSubjectType = "Yes" />';
                        }
                        if($search_criteria_condition->name == 'LivingAreaVariancePercent') {
                            $api_values['LivingAreaVariancePercent'] = $search_criteria_condition->value;
                        }
                        if($search_criteria_condition->name == 'NumCompsReturned') {
                            $api_values['NumCompsReturned'] = $search_criteria_condition->value;
                        }
                    }
                }
            }
            
            $check_valid_address = $this->postCheckValidAddress($api_values);
            if($check_valid_address['success'] == '1') {
                    $subject_property = $check_valid_address['result_array'];            
                    $api_street_address = $subject_property['PROPERTY']['@attributes']['_StreetAddress'];
                    $api_city = $subject_property['PROPERTY']['@attributes']['_City'];
                    $api_state = $subject_property['PROPERTY']['@attributes']['_State'];
                    $api_zipcode = $subject_property['PROPERTY']['@attributes']['_PostalCode'];
                    $api_county = $subject_property['PROPERTY']['@attributes']['_County'];

                    $type_of_house = trim($subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_SITE']['_CHARACTERISTICS']['@attributes']['_LandUseDescription']);
                    $square_footage = trim($subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_ROOM_COUNT']['@attributes']['_TotalLivingAreaSquareFeetNumber']);
                    $square_footage = ($square_footage != '') ? $square_footage : '0';
                    $total_bedrooms = trim($subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_ROOM_COUNT']['@attributes']['_TotalBedroomsCount']);
                    $total_bedrooms = ($total_bedrooms != '') ? $total_bedrooms : '0';
                    $total_bathrooms = trim($subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_ROOM_COUNT']['@attributes']['_TotalBathsCount']);
                    $total_bathrooms = ($total_bathrooms != '') ? $total_bathrooms : '0';
                    $total_basement_space = trim($subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_BASEMENT']['@attributes']['_AreaSquareFeet']);
                    $total_basement_space = ($total_basement_space != '') ? $total_basement_space : '0';
                    $finished_space = trim($subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_BASEMENT']['@attributes']['_FinishedAreaSquareFeet']);
                    $finished_space = ($finished_space != '') ? $finished_space : '0';
                    
                    $unfinished_space = $total_basement_space - $finished_space;
                    
                    if(!empty($subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_PARKING']['@attributes']['_GarageTotalCarCount'])) {
                        $garage_count = $subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_PARKING']['@attributes']['_GarageTotalCarCount'];
                    }
                    else {
                        $garage_area_total = $subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_PARKING']['@attributes']['_GarageSquareFeetNumber'] + $subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_PARKING']['@attributes']['_GarageTwoArea'];
                        if(!empty($garage_area_total) && $garage_area_total < 200) {
                            $garage_count = '1';
                        }
                        else {
                            $garage_count = round(($garage_area_total) / 400, 0);
                        }
                    }                    
                    $carport_exist = (!empty($subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_PARKING']['@attributes']['_CarportArea'])) ? '1' : '0';

                    $porch_deck_exist = '0';
                    $patio_exist = '0';
                    foreach($subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_XTRA_FEATURES'] as $extra_features) {
                        if(isset($extra_features['@attributes'])) {
                            if(((strpos($extra_features['@attributes']['_Xtra_Features_Code'], 'porch') !== false) || (strpos($extra_features['@attributes']['_Xtra_Features_Code'], 'deck') !== false)) && $porch_deck_exist == '0') {
                                $porch_deck_exist = '1';
                            }
                            if(strpos($extra_features['@attributes']['_Xtra_Features_Code'], 'patio') !== false && $patio_exist == '0') {
                                $patio_exist = '1';
                            }
                        }                    
                    }

                    $fireplace_exist = '0';
                    $fireplace_count = '0';
                    $pool_exist = '0';
                    foreach($subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_FEATURES'] as $feature_key => $features) {
                        if($feature_key == '_FIREPLACES') {
                            $fireplace_exist = ($features['@attributes']['_HasFeatureIndicator'] == 'Y') ? '1' : '0';
                            $fireplace_count = ($fireplace_exist == '1') ? $features['@attributes']['_CountNumber'] : '0';
                        }
                        if($feature_key == '_POOL') {
                            $pool_exist = ($features['@attributes']['_HasFeatureIndicator'] == 'Y') ? '1' : '0';
                        }
                    }
                    
                    $subject_year_built = $subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_GENERAL_DESCRIPTION']['@attributes']['_YearBuiltDateIdentifier'];

                    $subject_air_conditioning = (trim($subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_COOLING']['@attributes']['_CentralizedIndicator']) == 'Y') ? 'Yes' : 'No';
                     
                    $subject_owner_name = $subject_property['PROPERTY']['PROPERTY_OWNER']['@attributes']['_OwnerName'];

                    $parcel_size = $subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_SITE']['_DIMENSIONS']['@attributes']['_LotAreaSquareFeetNumber'];
                    
                    $basement_area = $subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_BASEMENT']['@attributes']['_AreaSquareFeet'];

                    $subject_sale_date = ($subject_property['PROPERTY']['_PROPERTY_HISTORY']['_SALES_HISTORY']['@attributes']['_LastSalesDate'])?date('d/m/Y', strtotime($subject_property['PROPERTY']['_PROPERTY_HISTORY']['_SALES_HISTORY']['@attributes']['_LastSalesDate'])):"";
                    $subject_sale_price = $subject_property['PROPERTY']['_PROPERTY_HISTORY']['_SALES_HISTORY']['@attributes']['_LastSalesPriceAmount'];

                    $sub_exterior = $subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_EXTERIOR_DESCRIPTION']['@attributes']['_ExteriorWallsIdentifier'];
                    
                    $half_bath_count = $subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_ROOM_COUNT']['@attributes']['_TotalHalfBathsCount'];

            } else {
                $response['success'] = false;
                $response['redirect_url'] = url('/');
                $response['message'] = 'Unfortunately, your home address is not located in our current coverage area.  We will notify you when this changes.';
                return $response;
            }
            
            $billing_street_number = Session::get('billing_street_number');
            $billing_street = Session::get('billing_street');
            $billing_city = Session::get('billing_city');
            //$billing_state = Session::get('billing_state');
            $billing_zipcode = Session::get('billing_zipcode');
            //$billing_county = Session::get('billing_county');
            
            $billing_state_details = State::where('state_abbr', Session::get('billing_state'))->where('end_date',null)->lists('state_id');
            $billing_state = $billing_state_details[0];
            //echo 'here'.Session::get('billing_county');
            $billing_county_details = County::where('state_id', $billing_state_details[0])->where('county_name', Session::get('billing_county'))->where('end_date',null)->lists('county_id');
            $billing_county = $billing_county_details[0];                
            
            
            $user_name = $first_name.' '.$last_name;
            $user_id = Auth::user()->id;              
            $user_search_high_key = Helper::getHighKey('user_searches', 'user_search_id', $user_id);  
            $getSearchDetails = Helper::getSearchDetailsWithToken($token);                
            $phase2_token = '0';
                
            if($getSearchDetails) {

                $system_object_type_id = Helper::toGenerateCommunicationObjectTypes('pf_lookups', 'name', 'address', $user_id);

                if(!empty($system_object_type_id)) {
                        
                        // update user_search table data
                        $UdateActivePage = UserSearch::where('user_search_id',$getSearchDetails->user_search_id)->update(['active_page' => 2,'sale_date' => $subject_sale_date,'sale_price' => $subject_sale_price, 'phase1_paid_amount' => Session::get('payment_amount')]);  
                        
                        $response['session_token'] = $token;
                        
                        if($check_valid_address['success'] == '1') {

                            $system_object_type_id2 = Helper::toGenerateCommunicationObjectTypes('user_searches', 'system_user_id', 'user_search', $user_id);
                            //echo $system_object_type_id2;exit;
                            if(!empty($system_object_type_id2)) {
                                $subject_comps_high_key = Helper::getHighKey('subject_comps_details', 'subject_comps_id', $user_id);
                                
                                $create_subject_comps = SubjectCompsDetail::create([
                                    'subject_comps_id' => (empty($subject_comps_high_key)) ? null : $subject_comps_high_key,
                                    'system_object_type_id' => $system_object_type_id2,
                                    'ref_object_id' => $getSearchDetails->user_search_id,
                                    'type_of_house' => $type_of_house,
                                    'square_footage' => $square_footage,
                                    'bedrooms' => $total_bedrooms,
                                    'bathrooms' => $total_bathrooms,
                                    'unfinished_space' => $unfinished_space,
                                    'finished_space' => $finished_space,
                                    'garage' => $garage_count,
                                    'carport' => $carport_exist,
                                    'porch_deck' => $porch_deck_exist,
                                    'patio' => $patio_exist,
                                    'swimming_pool' => $pool_exist,
                                    'fireplace' => $fireplace_count,
                                    'year_built' => $subject_year_built,
                                    'air_conditioning' => $subject_air_conditioning,
                                    'owner_name' => $subject_owner_name,
                                    'basement_area' => $basement_area,
                                    'parcel_size' => $parcel_size,
                                    'exterior' => $sub_exterior,
                                    'half_bath_count' => $half_bath_count,
                                    'corelogic_response' => json_encode($subject_property, true),
                                    'created_by' => $user_id,
                                    'updated_by' => $user_id,
                                ]);             
                            }
                        }                       
                    }
                }    
                $user_email=$getSearchDetails->email;
                $user_name=$getSearchDetails->first_name.' '.$getSearchDetails->last_name;
            if(isset($make_payment)) {
                   
                    $customer_link = url('/token/'.$token);
                    $mail_data['username'] = $user_email;
                    $mail_data['user_name'] = $user_name;
                    $mail_data['content'] = 'Thank you for making payment, Please <a href="'.$customer_link.'" target="_blank" style="color:#1570C3;">click</a> on the link to complete the Tax Appeal. <br>
                        Link is valid for 15 days.';

                    $mail_data['subject'] = 'Payment Received';
                    $mail_data['fname'] = $getSearchDetails->first_name;
                    $mail_data['lname'] = $getSearchDetails->last_name;
                    $mail_data['phone'] = Session::get('mobile_number');
                    $mail_data['str_n_route'] = Session::get('billing_street_number').' '.Session::get('billing_street');
                    $mail_data['city'] = Session::get('billing_city');
                    $mail_data['state'] = Session::get('billing_state');
                    $mail_data['county'] = Session::get('billing_county');
                    $mail_data['zip'] = Session::get('billing_zipcode');
                    $mail_data['pay'] = config('constants.phase1Amt');
                    $mail_to = array($user_email);

                    Mail::send('emails.phaseone', $mail_data, function($message) use ($mail_data, $mail_to)
                    {
                        $message->to($mail_to)->subject('Thank you for your payment of $'.config('constants.phase1Amt'));
                         
                    });
                   // echo "sdfkjsdjfbjsfdjdsbf";exit;
                    //$mail_sent = Helper::SendMail($mail_data, $mail_to);
                    //echo "DFgdfgdfg";exit;
                    /*
                    Mail::send('emails.email_template', $mail_data, function ($message) use($create_user) {
                        $message->to($create_user->email)
                                ->subject('Payment Received');
                    });
                    */
                    $response['redirect_url'] = url('/verify-address');
            }                            
            else {
                $response['redirect_url'] = url('/');
            }
            $response['success'] = true;   
            
            DB::commit();            
            return $response;
            //return response()->json(['success'=>true, 'redirect_url' => url('/thankyou')]);
        
        }        
        catch (\Exception $e) 
        {   
            //$result = ['exception_message' => $e->getMessage()];
            //return view('errors.error', $result);
            DB::rollback();
            $response['success'] = false;
            $response['message'] = $e->getMessage();
            return $response;
            //return response()->json(['success'=>false, 'message' => $e->getMessage()]);
        }   
        
    }
    
    /**
      * Return get Search Address Form.
      * @param   
      * @return Response
    **/
    public function getThankyou($view_status=0)
    {
        try {
            //echo "<pre>"; print_r(Session::all()); exit;
            $active = 'thankyou';       
            if(Session::has('token') || Session::has('email')) {
                $email = Session::get('email');
                return view('customer.thankyou',compact('active', 'email'));        
            }
            else {
                return redirect('/');
            }
        }
        catch (\Exception $e) 
        {
            $result = ['exception_message' => $e->getMessage()];
            return view('errors.error', $result);
        }
    }

    /**
      * redirect to address not found page
      * @param   
      * @return Response
    **/
    public function getAddressNotFound(Request $request, $user_search_id = '')
    {
        try {
            $active = 'addressNotSupported'; 
            return view('customer.addressNotSupported', compact('active'));        
        }
        catch (\Exception $e) 
        {
            $result = ['exception_message' => $e->getMessage()];
            return view('errors.error', $result);
        }
    }

    /**
      * redirect to assessment not ready page
      * @param   
      * @return Response
    **/
    public function getAssessmentNotReady(Request $request, $user_search_id = '')
    {
        try {
            $active = 'getAssessmentNotReady'; 
            return view('customer.assessmentNotReady', compact('active'));        
        }
        catch (\Exception $e) 
        {
            $result = ['exception_message' => $e->getMessage()];
            return view('errors.error', $result);
        }
    }

    /**
      * redirect to address not found page
      * @param   
      * @return Response
    **/
    public function getAddressNotFoundAfterLogin(Request $request, $user_search_id = '')
    {
        try {
            return view('customer.addressNotSupportedAfterLogin');        
        }
        catch (\Exception $e) 
        {
            $result = ['exception_message' => $e->getMessage()];
            return view('errors.error', $result);
        }
    }

    /**
      * redirect to assessment not ready page
      * @param   
      * @return Response
    **/
    public function getAssessmentNotReadyAfterLogin(Request $request, $user_search_id = '')
    {
        try {
            return view('customer.assessmentNotReadyAfterLogin');        
        }
        catch (\Exception $e) 
        {
            $result = ['exception_message' => $e->getMessage()];
            return view('errors.error', $result);
        }
    }

    /**
      * Return get Make Payemnt Form.
      * @param   
      * @return Response
    **/
    public function getMakePayment($view_status=0)
    {
        try {

            // token value maitain in $view_status variables from dashboard search listing
            if ($view_status != null && !empty($view_status) && $view_status != '0' && $view_status != '1'){
                $getSearchDetails = Helper::getSearchDetailsWithToken($view_status);

                
              
                if(!empty($getSearchDetails) ) {

                    $stateAbr = State::find($getSearchDetails->state); 
                    $CountySearch = County::where('county_id',$getSearchDetails->county)->first(); 
                   // echo $stateAbr;
                    //echo "<pre>";print_r($CountySearch);exit;
                    Session::put('search_state', $stateAbr->state_abbr);

                    Session::put('search_county', $CountySearch->county_name);
                    //echo "Sdfsdf111122";exit;
                    Session::put('email', $getSearchDetails->email);

                    Session::put('first_name', $getSearchDetails->first_name);
                    Session::put('last_name', $getSearchDetails->last_name);
                     
                    Session::put('search_street_number',$getSearchDetails->address_line_1);
                    Session::put('search_street',$getSearchDetails->address_line_2);
                    Session::put('search_city',$getSearchDetails->city);
                    Session::put('search_zipcode',$getSearchDetails->postal_code);
                    Session::put('land_assessment_value',$getSearchDetails->land_assessment_value);
                    Session::put('improvement_assessment_value',$getSearchDetails->improvement_assessment_value);
                    Session::put('total_assessment_value',$getSearchDetails->total_assessment_value);
                    Session::put('mobile_number',$getSearchDetails->mobile_number);
                    
                }

                $tokenId =  $view_status;
                $searchData = UserSearch::where('token', $tokenId)->first();
               
                if (!empty($searchData)) {
                    $userDetail = $searchData;
                    Session::put('token', $view_status);
                } else { 
                    return redirect('/')->with('error', 'Sorry, Invalid Token.');
                }
            }else{
                return redirect('/')->with('error', 'Sorry, Invalid Token.');
            }

            if(Session::has('token') || Session::has('email')) {
                $active = 'make_payment';
                $all_states = State::all();
                $states = [];
                $session_state = Session::get('search_state');
                
                $state_id = $all_states[0]->state_id;
                foreach($all_states as $state) {
                    $states[$state->state_abbr] = $state->state_name;
                    if($state->state_abbr == $session_state) {
                        $state_id = $state->state_id;
                    }
                }
                
                $token = Helper::getSessionToken();
                if($token != null) { 
                   
                    $token_details = UserSearch::where('token', $token)->where('end_date',null)->get();

                    //echo $token.'------';
                    //echo "<pre>sdf";print_r($token_details);exit;
                    if(count($token_details)) {
                        
                        if($token_details[0]->active_page >= '0' ) {
                            $token_details = $token_details[0];
                        }
                        else {
                            return redirect('/');
                        }    
                    }
                    else {
                        return redirect('/invalid-token');
                    }
                    
                    $lookup_type = Helper::getLookupTypeIdFromName(config('constants.constants.lookup_type.address'));
                    $where_condition['lookup_type_id'] = $lookup_type[0]->lookup_type_id;
                    $address_type = Helper::getLookupIdFromName(config('constants.constants.lookup.address.billing_address'), $where_condition);
                    //echo Auth::user()->id;exit;
                    $customer_billing_address = Helper::getBillingDetail(Auth::user()->id);
                    //echo "<pre>";print_r($BillingAddress);exit;
                    //$customer_billing_address = PfAddress::where('address_type', $address_type[0]->lookup_id)->where('ref_object_id', $token_details->user_search_id)->where('end_date',null)->get();
                    //echo "<pre>";print_r($customer_billing_address);exit;
                    //echo "sfdsdfsdfsdf";exit;
                    $data['address_street_number'] = $customer_billing_address[0]->address_line_1;
                   // echo "sfdsdfsdfsdf";exit;
                    $data['address_street'] = $customer_billing_address[0]->address_line_2;
                    $data['address_city'] = $customer_billing_address[0]->city;

                    //$data['address_state'] = $customer_search_address[0]->state;
                    $data['state_name'] = State::getStateName($customer_billing_address[0]->state);
                    $data['state_abbr'] = State::getStateAbbr($customer_billing_address[0]->state);   
                    $data['address_zipcode'] = $customer_billing_address[0]->postal_code;
                    //$data['address_county'] = $customer_search_address[0]->county;
                    $data['county_name'] = County::getCountyName($customer_billing_address[0]->county);
                    //echo "<pre>"; print_r($data); exit;
                      //  echo "sfdsdfsdfsdf";exit;
                    $all_counties = County::where('state_id', $customer_billing_address[0]->state)->where('end_date',null)->get();
                    $counties = [];
                    foreach($all_counties as $county) {
                        $counties[$county->county_name] = $county->county_name;
                    }
                    
                    return view('customer.make_payment',compact('active', 'token_details', 'states', 'counties'), $data);
                }
                else { //echo "sfdsdfsdfsd2222f";exit;
                    $all_counties = County::where('state_id', $state_id)->where('end_date',null)->get();
                    $counties = [];
                    foreach($all_counties as $county) {
                        $counties[$county->county_name] = $county->county_name;
                    }
                    
                    return view('customer.make_payment',compact('active', 'states', 'counties'));
                }
            }
            else {
                  //echo "sdfsd333333333";exit;
                return redirect('/');
            }
        
        }
        catch (\Exception $e) 
        {
            $result = ['exception_message' => $e->getMessage()];
            return view('errors.error', $result);
        }
        
    }
    
    /**
      * Add user of any type of role.
      * @param Request $request            
      * @return Response
    **/
    public function postMakePayment(Requests\MakePayment $request)
    {
        try {
            DB::beginTransaction();
            
            if($request->token_exist == '1') {
                
                // if search entery alreay exist in db then go to next page
                $checkStatus = Helper::getSearchCurrentStatus($request->token);
                if(count($checkStatus) > 0 && $checkStatus['msg'] != "make-payment"){
                    if($checkStatus['msg'] == "-"){
                        return redirect("/");
                    }
                    return redirect("/".$checkStatus['msg']."/".$request->token);
                }

                // When token is 0, make payment using PG. Added 27/06/2017 (MS)
                // Get payment details from this page into an array
                $paymentDetails = $request->all();
                
                $paymentDetails['billing_street'] = $request->street_number.' '.$request->route;
                $paymentDetails['billing_city'] = $request->locality;
                $paymentDetails['billing_state'] = $request->administrative_area_level_1;
                $paymentDetails['billing_zipcode'] = $request->postal_code;
				
				// Added the following to keep phase payment amount variable for each phase
				// Nov 8, 2017
				$paymentDetails['payment_amount'] = config('constants.phase1Amt');
                //$paymentDetails['payment_amount'] = 0.05;
                $payment = new Payment();
                // Call the function makePayment to paypal using these details
                $paymentResponse = $payment->makePayment($paymentDetails, config('constants.pgPaypal'));
                $paymentResponseArr = json_decode($paymentResponse, true);
             // echo "<pre>";print_r($paymentResponseArr);exit;
                $paymentStatus = "";
                if (isset($paymentResponseArr['state']) && $paymentResponseArr['state'] != "") {
                    $paymentStatus = $paymentResponseArr['state'];                    
                }
                //echo $paymentStatus;exit;
               //  $paymentStatus='approved';
                //echo config('constants.approved').'==='.$paymentStatus;exit;
                if ($paymentStatus == 'approved') {
                     
                    Session::put('make_payment', '1');           
                    Session::put('billing_street_number', $request->street_number);
                    Session::put('billing_street', $request->route);
                    Session::put('billing_city', $request->locality);
                    Session::put('billing_state', $request->administrative_area_level_1);
                    Session::put('billing_zipcode', $request->postal_code);
                    Session::put('billing_county', $request->administrative_area_level_2);
                    Session::put('payment_amount', config('constants.phase1Amt'));

                    // phase 1 billing address save code
                    $user_id = Auth::user()->id;
                    
                    $system_object_type_id = Helper::toGenerateCommunicationObjectTypes('pf_lookups', 'name', 'address', $user_id);

                    if(!empty($system_object_type_id)) {
                        $address_high_key = Helper::getHighKey('pf_addresses', 'address_id', $user_id);

                        $lookup_type = Helper::getLookupTypeIdFromName(config('constants.constants.lookup_type.address'));

                        $where_condition['lookup_type_id'] = $lookup_type[0]->lookup_type_id;

                        $address_type = Helper::getLookupIdFromName('phase1_billing_address', $where_condition);
                        
                        $state_details = State::where('state_abbr', $request->administrative_area_level_1)->where('end_date',null)->lists('state_id');
                        $search_state='';
                        $search_county='';
                        if(!empty($state_details) && $state_details != null){
                            $search_state = $state_details[0];
                            $county_details = County::where('state_id', $state_details[0])->where('county_name', $request->administrative_area_level_2)->where('end_date',null)->lists('county_id');
                            $search_county = (!empty($county_details))?($county_details[0]):'';
                        }
                        $getSearchDetails = Helper::getSearchDetailsWithToken($request->token);                

                        $userDetails = Helper::getMemberDetailsUsingUserId($getSearchDetails->system_user_id);
                        
                        $notificationType = 0;
                        if ($userDetails != null && !empty($userDetails)){
                            $notificationType = $userDetails->receive_notification;

                            $mobile_number = (!empty($userDetails->mobile_number))?($userDetails->mobile_number):''; 
                            Session::put('mobile_number', $mobile_number);
                        }

                        PfAddress::create([
                            'address_id' => (empty($address_high_key)) ? null : $address_high_key,
                            'system_object_type_id' => $system_object_type_id,
                            'ref_object_id' => $getSearchDetails->user_search_id,
                            'address_type' => (isset($address_type[0]->lookup_id) ? $address_type[0]->lookup_id : ""),
                            'mobile_number' => '',
                            'receive_notification' => $notificationType,
                            //'address_line_1' => $request->street_number,
                            'address_line_2' => $request->autocomplete_search, 
                            'city' => $request->locality,
                            'postal_code' => $request->postal_code,
                            'state' => (isset($search_state) ? $search_state : ""),
                            'county' => (isset($search_county) ? $search_county: "" ),
                            'created_by' => $user_id,
                            'updated_by' => $user_id,
                        ]); 
                        DB::commit();
                    }
                    // phase 1 billing address save code

                    $response_save_result = self::postSavePaymentData($request->token);

					Session::put('phase2_token','0');

                    if($response_save_result['success'] == true) {
                        // User opts to save payment details
                        $savePaymentDetails = isset($request->save_in_vault) ? $request->save_in_vault : '0'; 
                       // echo $savePaymentDetails;exit;
                        if ($savePaymentDetails == '1') {
                                // Add customer token to the array
                                $paymentDetails['uniqueId'] = $response_save_result['session_token'];
                                // save details in Paypal vault
                                $saveResp = $payment->saveCreditCardInVault($paymentDetails); 
                        }
                        
                        //return response()->json(['message'=>'test','savePayment' => $savePaymentDetails]);
                        DB::commit();
                        return redirect('/verify-address/'.$request->token);
                                                
                    }
                    else {
                        DB::rollback();
                        $message = (isset($response_save_result['message'])) ? $response_save_result['message'] : 'Sorry, something went wrong.';
                        //return response()->json(['success'=>false, 'message' => $message]);
                        if(isset($response_save_result['redirect_url'])){ 
                            return redirect($response_save_result['redirect_url'])->with('error',$message);    
                        }
                        return redirect('/make-payment/'.$request->token);
                    }
                }
                else {  
                    DB::rollback();
                   // return response()->json(['success'=>false, 'message' => 'Sorry, your payment has been failed. Please try again.']);
                    return redirect('/make-payment/'.$request->token)->with('error', 'Your payment has been rejected.');
                }                
            }
            else {
                DB::rollback();
                //response()->json(['success'=>true, 'redirect_url' => url('/make-payment')]);  
                return redirect('/make-payment/'.$request->token);
            }
        
        }
        catch (\Exception $e) 
        {   
            DB::rollback();

            $result = ['exception_message' => $e->getMessage()];
            return view('errors.error', "Sorry, we could not process your request. Please try again later or contact us at <a href='mailto:info@hometaxsavings.com'>info@hometaxsavings.com</a>");
            //return response()->json(['success'=>false, 'message' => $e->getMessage()]);
        }   
        
    }
    
    
    /**
      * Return get Add User Address Form.
      * @param   
      * @return Response
    **/
    public function getTokenStatus($view_status=0)
    { 
        try {
            $token = Helper::getSessionToken(); 
            if($token != null) {
                $token_details = UserSearch::where('token', $token)->where('end_date',null)->get();
                if(count($token_details)) {
                    Session::put('token', $token);
                    //echo $token_details[0]->active_page.' '.$token; exit;
                    
                    $redirect_token_url = Helper::getRedirectUrlForToken($token_details[0]->active_page);
                    return $redirect_token_url; 
                    /*
                    if($token_details[0]->active_page == '1') {
                        //return redirect('/user/add-user-payment/'.$token);
                        return redirect('/make-payment/');
                    }
                    else if($token_details[0]->active_page == '2') {
                        //return redirect('/user/verify-address/'.$token);
                        return redirect('/verify-address/');
                    }
                    else if($token_details[0]->active_page == '3') { 
                        //return redirect('/user/assessment-review/'.$token);
                        return redirect('/assessment-review/');
                    }
                    */
                }
                else {
                    return redirect('/invalid-token');
                }
            }
            else {
                return redirect('/');
            }
            $active = 'enter_address';
            return view('customer.address',compact('active'));
        }
        catch (\Exception $e) 
        {
            $result = ['exception_message' => $e->getMessage()];
            return view('errors.error', $result);
        }
    }
	
	
    
    /**
      * Return get Add User Address Form.
      * @param   
      * @return Response
    **/
    public function getInvalidToken()
    {
        try {
            return view('errors.invalid_token');
        }
        catch (\Exception $e) 
        {
            $result = ['exception_message' => $e->getMessage()];
            return view('errors.error', $result);
        }
    }
    
    
    /**
      * Return get Add User Address Form.
      * @param   
      * @return Response
    **/
    /*
    public function getAddress($view_status=0)
    {
        try {
            $data['token_status'] = 0;
            $all_states = State::all();
            $states = [];
            foreach($all_states as $state) {
                $states[$state->name] = $state->name;
            }
            
            $all_counties = County::all();
            $counties = [];
            foreach($all_counties as $county) {
                $counties[$county->name] = $county->name;
            }
            
            $token = Helper::getSessionToken();
            if($token != null) {
                $token_details = UserSearch::where('token', $token)->get();
                if(count($token_details)) {
                    $data['customer_token'] = $token_details[0]->token;
                    $data['token_status'] = $token_details[0]->status;
                    if($token_details[0]->status >= '2') {
                        //$token_details = $token_details[0];
                    }
                    else {
                        return redirect('/');
                    }
                    /*
                    if($view_status == 0) {
                        if($data['token_status'] == '1') {
                            return redirect('/make-payment');
                        }
                        else if($data['token_status'] == '3') {
                            return redirect('/verify-address/');
                        }
                        else if($data['token_status'] == '4') {
                            return redirect('/assessment-review/');
                        }
                    }           
                    /         
                }
                else {
                    return redirect('/invalid-token');
                }
            }
            else {
                return redirect('/');
            }
            
            if($data['token_status'] == 0) {
                return redirect('/');
            }
            
            //$county = County::where('state_id', $token_details[0]->home_state)->lists('name', 'id')->toArray();
            
            $data['address_street'] = $token_details[0]->home_street;
            $data['address_city'] = $token_details[0]->home_city;
            $data['address_state'] = $token_details[0]->home_state;
            $data['state_name'] = State::getStateName($token_details[0]->home_state);
            $data['address_zipcode'] = $token_details[0]->home_zipcode;
            $data['address_county'] = $token_details[0]->home_county;
            
            $active = 'enter_address';
            return view('customer.address',compact('active', 'states', 'counties'), $data);
        }
        catch (\Exception $e) 
        {
            $result = ['exception_message' => $e->getMessage()];
            return view('errors.error', $result);
        }
    }
    */
    
    
    
    /**
      * Add user of any type of role.
      * @param Request $request            
      * @return Response
    **/
    /*
    public function postAddress(Requests\Address $request)
    {
        try { 
            $token = $request->token;
            //echo "<pre>"; print_r($request->all()); exit;
            if($request->token_status == '2' || $request->token_status == '3') {
                
                $validate_address['street'] = $request->home_street;
                $validate_address['city'] = $request->home_city;
                $validate_address['state'] = 'TX'; //$request->home_state;
                $validate_address['zip_code'] = $request->home_zipcode;                
                $check_valid_address = $this->postCheckValidAddress($validate_address);
                if($check_valid_address['success'] == '0') {
                    return response()->json(['success'=>false, 'message' => $check_valid_address['message'], 'invalid_address'=> '1']);
                }
                
                $token_status = ($request->token_status == '2') ? '3' : $request->token_status;
                
                $token_details = UserSearch::where('token', $token)->get();
                if(count($token_details)) {
                    // update customer
                    $update_customer = [
                        'status'           => $token_status,
                        'home_street'   => $request->home_street,
                        'home_city'     => $request->home_city,
                        'home_state'    => $request->home_state,
                        'home_zipcode'  => $request->home_zipcode,
                        'home_county'   => $request->home_county,
                    ];
                    $update_customer_address = UserSearch::where('token', $token)->update($update_customer);
                }
                else {
                    //return redirect('/invalid-token');
                    return response()->json(['success'=>true, 'redirect_url' => url('/invalid-token')]);
                }
            }
            
            //return redirect('/token-status/');            
            return response()->json(['success'=>true, 'redirect_url' => url('/token-status')]);
        
        }
        catch (\Exception $e) 
        {   
            //$result = ['exception_message' => $e->getMessage()];
            //return view('errors.error', $result);
            return response()->json(['success'=>false, 'message' => $e->getMessage()]);
        }
        
    }
    */
    
    
    /**
      * Return get Add User Address Form.
      * @param   
      * @return Response
    **/
    public function getVerifyAddress($view_status=0)
    {
        try {             
            if ($view_status != null && !empty($view_status) && $view_status != '0' && $view_status != '1'){
                $getSearchDetails = Helper::getSearchDetailsWithToken($view_status);
                if(!empty($getSearchDetails) ) {
                    $data['token_status'] = 0;
                    $all_states = State::all();
                    $states = [];
                    foreach($all_states as $state) {
                        $states[$state->state_id] = $state->state_name;
                    }
                    $data['customer_token'] = $getSearchDetails->token;
                    $data['token_status'] = $getSearchDetails->active_page;
                    if($getSearchDetails->active_page == '2') {
                        $token_details = $getSearchDetails;   
                    } else {
                        return redirect('/');
                    }
                    
                    
                    $lookup_type = Helper::getLookupTypeIdFromName(config('constants.constants.lookup_type.address'));
                    $where_condition['lookup_type_id'] = $lookup_type[0]->lookup_type_id;
                    $address_type = Helper::getLookupIdFromName(config('constants.constants.lookup.address.search_address'), $where_condition);
                    
                    $customer_search_address = PfAddress::where(array('address_type' => $address_type[0]->lookup_id, 'system_object_type_id' => '1'))->where('ref_object_id', $token_details->user_search_id)->where('end_date',null)->get();
                    
                    $data['user_search_address_id'] = $customer_search_address[0]->address_id;
                    
                    $county = County::where('state_id', $customer_search_address[0]->state)->where('end_date',null)->lists('county_name', 'county_id')->toArray();
                    
                    $validate_address['street'] = $data['address_street'] = $customer_search_address[0]->address_line_1.' '.$customer_search_address[0]->address_line_2;
                    $validate_address['city'] = $data['address_city'] = $customer_search_address[0]->city;
                    $data['address_state'] = $customer_search_address[0]->state;
                    $validate_address['state'] = $data['state_name'] = State::getStateName($customer_search_address[0]->state);
                    $validate_address['zip_code'] = $data['address_zipcode'] = $customer_search_address[0]->postal_code;
                    $data['address_county'] = $customer_search_address[0]->county;
                    $data['land_assessment_value'] = null;//$token_details->land_assessment_value;
                    $data['improvement_assessment_value'] = '0.00';//$token_details->improvement_assessment_value;
                    $data['total_assessment_value'] = null;//$token_details->total_assessment_value;
                    $data['county_link'] = County::where('county_id', $customer_search_address[0]->county)->where('end_date',null)->lists('county_link');
                    
                    $system_object_type_id = Helper::toGenerateCommunicationObjectTypes('user_searches', 'system_user_id', 'user_search', $token_details->user_search_id);
                    
                    $house_details = SubjectCompsDetail::where('system_object_type_id', $system_object_type_id)->where('ref_object_id', $token_details->user_search_id)->where('end_date',null)->get();
                    
                    $data['user_search_id'] = $token_details->user_search_id;
                    $data['house_details'] = @$house_details[0];
                    
                    $questions_lookup_type = Helper::getLookupTypeIdFromName(config('constants.constants.lookup_type.additionl_homeowner_questions'));
                    $data['additional_homeowner_questions'] = PfLookup::where('lookup_type_id', $questions_lookup_type[0]->lookup_type_id)->where('parent_lookup_id', null)->where('end_date',null)->orderBy('display_order','ASC')->get();           
                    
                    $data['lookup_selected'] = $data['lookup_count'] = [];
                    if($token_details->active_page >= '3') {
                        $token_exist = '1';
                        
                        $homeowner_questions_details = SearchComparable::where('user_searches_id', $token_details->user_search_id)->where('lookup_id', '!=', null)->get();
                        foreach($homeowner_questions_details as $question) {
                            $data['lookup_selected'][] = $question->lookup_id;
                            $data['lookup_value'][$question->lookup_id] = $question->lookup_value;
                            $data['lookup_count'][$question->lookup_id] = $question->lookup_count;
                        }
                    } else {
                        $token_exist = '0';
                    }
                    //echo "<pre>"; print_r($token_exist); die;
                    $active = 'verify_address';
                    return view('customer.verify_address',compact('active', 'token_exist', 'states', 'county'), $data);

                } else {
                    return redirect('/');
                }
            } else {
                return redirect('/invalid-token');
            }
        }
        catch (\Exception $e) 
        {
            $result = ['exception_message' => $e->getMessage()];
            return view('errors.error', $result);
        }
        
    }
    
    
    /**
      * Complete verification process of address and find top comparables.
      * @param Request $request            
      * @return Response
    **/
    public function postVerifyAddress(Requests\VerifyAddress $request)
    {
        try { 
            DB::beginTransaction();
            
            $token = $request->token;

            // if search entery alreay exist in db then go to next page
            $checkStatus = Helper::getSearchCurrentStatus($request->token);
            if(count($checkStatus) > 0 && $checkStatus['msg'] != "verify-address"){
                if($checkStatus['msg'] == "-"){
                    return redirect("/");
                }
                return redirect("/".$checkStatus['msg']."/".$request->token);
            }

            $user_search_id = decrypt($request->user_search_id);
            $user_search_details = UserSearch::where(array('token'=> $token, 'user_search_id'=> $user_search_id) )->where('end_date',null)->first();
            if(count($user_search_details)) {
                 
                $user_id = $user_search_details->system_user_id;
                $current_asse_value = $request->total_assessment_value;//$user_search_details->total_assessment_value;
                $customer_search_address = PfAddress::where('address_id', decrypt($request->user_search_address_id))->get();
                
                // update user search comparable details
                $search_comp_detail['square_footage'] = $request->square_footage;
                $search_comp_detail['bedrooms'] = $request->bedrooms;
                $search_comp_detail['bathrooms'] = $request->bathrooms;
                $search_comp_detail['unfinished_space'] = $request->unfinished_space;
                $search_comp_detail['finished_space'] = $request->finished_space;

                if(isset($request->garage_count) && $request->garage_count >= 0) {
                    
                    $search_comp_detail['garage'] = $request->garage_count;
                } else {
                    $search_comp_detail['garage'] = '0';
                }
                
                if(isset($request->carport_exist) && $request->carport_exist == '1') {
                    $search_comp_detail['carport'] = $request->carport_exist;
                } else {
                    $search_comp_detail['carport'] = '0';
                }
                
                if(isset($request->porch_deck_exist) && $request->porch_deck_exist == '1') {
                    $search_comp_detail['porch_deck'] = $request->porch_deck_exist;
                } else {
                    $search_comp_detail['porch_deck'] = '0';
                }
                
                if(isset($request->patio_exist) && $request->patio_exist == '1') {
                    $search_comp_detail['patio'] = $request->patio_exist;
                } else {
                    $search_comp_detail['patio'] = '0';
                }
                
                if(isset($request->pool_exist) && $request->pool_exist == '1') {
                    $search_comp_detail['swimming_pool'] = $request->pool_exist;
                } else {
                    $search_comp_detail['swimming_pool'] = '0';
                }
                
                
                if(isset($request->fireplace_count) && $request->fireplace_count > 0) {
                    $search_comp_detail['fireplace'] = $request->fireplace_count;
                } else {
                    $search_comp_detail['fireplace'] = '0';
                }

                
                $system_object_type_id_search = Helper::toGenerateCommunicationObjectTypes('user_searches', 'system_user_id', 'user_search', $user_search_details->user_search_id);
                
                
                $update_search_comp_detail = SubjectCompsDetail::where('system_object_type_id', $system_object_type_id_search)->where('ref_object_id', $user_search_details->user_search_id)->where('end_date',null)->update($search_comp_detail);
                

                //$search_comp_detail['fireplace_exist'] = (isset($request->fireplace_count) && $request->fireplace_count >= '0') ? '1' : '0';

                $subject_property_detail = SubjectCompsDetail::where('system_object_type_id', $system_object_type_id_search)->where('ref_object_id', $user_search_details->user_search_id)->where('end_date',null)->first();

                if(count($subject_property_detail)>0){
                    $search_comp_detail['subject_property'] = $subject_property_detail->corelogic_response;
                } else {
                    DB::rollback();
                    return Redirect::back()->withErrors("Subject property details not found! Please try again.");
                }

                if(count($customer_search_address)) {
                    $subject_property_comparables = self::postSubjectPropertyComparables($customer_search_address[0], $current_asse_value, $search_comp_detail);
                     
                //echo "<pre>Hi... "; print_r($subject_property_comparables); die;  
                    if($subject_property_comparables['success'] == '1') {
                        $system_object_type_id_comparable = Helper::toGenerateCommunicationObjectTypes('subject_comps_details', 'system_object_type_id', 'comparable', $user_id);
                        
                        if(!empty($system_object_type_id_comparable)) {  

                            $homeowner_question_adjustment = 0;
                            
                            $lookup_count_keys = array_keys($request->lookup_count);
                            
                            foreach($request->homeowner_questions as $lookup_id => $homeowner_question_value) {                                
                                // save homeowner question values
                                $lookup_value = (in_array($lookup_id, $lookup_count_keys)) ? ($homeowner_question_value * $request->lookup_count[$lookup_id]) : $homeowner_question_value;
                                $search_comparable_high_key = Helper::getHighKey('search_comparables', 'search_comparable_id', $user_id);
                                $create_subject_comps = SearchComparable::create([
                                    'search_comparable_id' => (empty($search_comparable_high_key)) ? null : $search_comparable_high_key,
                                    'user_searches_id' => $user_search_id,
                                    'subject_comps_detail_id' => $subject_property_detail->subject_comps_id,
                                    'lookup_id' => $lookup_id,
                                    'lookup_value' => $lookup_value,
                                    'lookup_count' => (in_array($lookup_id, $lookup_count_keys)) ? $request->lookup_count[$lookup_id] : null,
                                    'created_by' => $user_id,
                                    'updated_by' => $user_id,
                                ]);    
                                $homeowner_question_adjustment = $homeowner_question_adjustment + $lookup_value;

                            }   
                            DB::commit();
                            
                            foreach($subject_property_comparables['comparables'] as $comparable) {
                                // save comparable values
                                $subject_comps_high_key = Helper::getHighKey('subject_comps_details', 'subject_comps_id', $user_id);
                                $create_subject_comps = SubjectCompsDetail::create([
                                    'subject_comps_id' => (empty($subject_comps_high_key)) ? null : $subject_comps_high_key,
                                    'system_object_type_id' => $system_object_type_id_comparable,
                                    'ref_object_id' => $user_search_id,
                                    'comparable_number' => $comparable['comparable_number'],
                                    'type_of_house' => $comparable['comparable_type_of_house'],
                                    'square_footage' => $comparable['comparable_square_footage'],
                                    'bedrooms' => $comparable['comparable_total_bedrooms'],
                                    'bathrooms' => $comparable['comparable_total_bathrooms'],
                                    'unfinished_space' => $comparable['comparable_unfinished_space'],
                                    'finished_space' => $comparable['comparable_finished_space'],
                                    'garage' => $comparable['comparable_garage_count'],
                                    'carport' => $comparable['comparable_carport_exist'],
                                    'porch_deck' => $comparable['comparable_porch_deck_exist'],
                                    'patio' => $comparable['comparable_patio_exist'],
                                    'parcel_size' => $comparable['comparable_parcel_size'],
                                    'swimming_pool' => $comparable['comparable_pool_exist'],
                                    'fireplace' => $comparable['comparable_fireplace_count'],
                                    'created_by' => $user_id,
                                    'updated_by' => $user_id,
                                    
                                ]);  
                                DB::commit();

                                $garageCount = 0;
                                if($comparable['garage'] != "-"){
                                    $garageCount = $comparable['comparable_garage_count'];
                                }

                                 // save comparable adjustment values
                                $search_comparable_high_key = Helper::getHighKey('search_comparables', 'search_comparable_id', $user_id);
                                $create_search_comps = SearchComparable::create([
                                    'search_comparable_id' => (empty($search_comparable_high_key)) ? null : $search_comparable_high_key,
                                    'user_searches_id' => $user_search_id,
                                    'subject_comps_detail_id' => $create_subject_comps->subject_comps_id,
                                    'sale_price' => $comparable['sale_price'],
                                    'year_built' => $comparable['year_built'],
                                    'sale_price_divided_sf' => $comparable['sale_price_divided_square_footage'],
                                    'data_source' => $comparable['data_source'],
                                    'subsidy' => $comparable['subsidy'],
                                    'leasehold' => $comparable['leasehold'],
                                    'square_footage' => $comparable['site'],
                                    'square_footage_price' => $comparable['square_footage'],
                                    'exterior' => $comparable['exterior'],
                                    'gross_living_area' => $comparable['gross_living_area'],
                                    'basement' => $comparable['basement'],
                                    'basement_type' => $comparable['basement_type'],
                                    'net_adjustment' => $comparable['total_adjustments'],
                                    'distance_from_subject' => $comparable['distance_from_subject'],
                                    'date_of_sale' => $comparable['comparable_date_of_sale'],
                                    'parcel_size' => $comparable['parcel_size'],
                                    'total_bedrooms' => $comparable['total_bedrooms'],
                                    'total_bathrooms' => $comparable['total_bathrooms'],
                                    'finished_space' => $comparable['finished_space'],
                                    'unfinished_space' => $comparable['unfinished_space'],
                                    'garage' => $comparable['garage_adjusted_value'],
                                    'carport' => $comparable['carport'],
                                    'swimming_pool' => $comparable['pool'],
                                    'fireplace' => $comparable['fireplace'],
                                    'fireplace_count' => $comparable['comparable_fireplace_count'],
                                    'total_adjustment_price' => $comparable['total_adjustments'],
                                    'price_after_adjustment' => $comparable['price_after_adjustment'],// - $homeowner_question_adjustment,
                                    'land_assessment_value' => $comparable['land_assessment_value'],// - $homeowner_question_adjustment,
                                    'improvement_assesment_value' => $comparable['improvement_assesment_value'],
                                    'total_assessment_value' => $comparable['total_assessment_value'],
                                    'half_bath_count' => $comparable['half_bath_count'],
                                    'created_by' => $user_id,
                                    'updated_by' => $user_id,
                                ]); 
                                DB::commit();
                                // Save top 5 comparables address
                                $lookup_type = Helper::getLookupTypeIdFromName(config('constants.constants.lookup_type.address'));
                                $whereCondition['lookup_type_id'] = $lookup_type[0]->lookup_type_id;
                                $address_type = Helper::getLookupIdFromName(config('constants.constants.lookup.address.search_address'), $whereCondition);
                                $address_high_key = Helper::getHighKey('pf_addresses', 'address_id', $user_id);
                                
                                $state_details = State::where('state_abbr', $comparable['comparable_address_state'])->where('end_date',null)->lists('state_id');
                                
                                $county_details = County::where('state_id', $state_details[0])->where('county_name', $comparable['comparable_address_county'])->where('end_date',null)->get();


                                $create_search_address = PfAddress::create([
                                    'address_id' => (empty($address_high_key)) ? null : $address_high_key,
                                    'system_object_type_id' => '3',
                                    'ref_object_id' => (empty($search_comparable_high_key)) ? null : $search_comparable_high_key,
                                    'address_type' => $address_type[0]->lookup_id,
                                    'mobile_number' => $comparable['phone_number'],
                                    'receive_notification' => '0',
                                    'address_line_1' => $comparable['comparable_address_street'],
                                    'address_line_2' => "", 
                                    'city' => $comparable['comparable_address_city'],
                                    'postal_code' => $comparable['comparable_address_zipcode'],
                                    'state' => (isset($state_details[0]) ? $state_details[0] : ""), 
                                    'county' => (isset($county_details[0]) ? $county_details[0]->county_id : "" ),
                                    'created_by' => $user_id,
                                    'updated_by' => $user_id,
                                ]); 
                                DB::commit();    
                            } 
                   
                        }
                   
                        // update customer
                        $subProDet = (isset($subject_property_comparables['subject_property']) && !empty($subject_property_comparables['subject_property']) )? $subject_property_comparables['subject_property'] : "";
                        $update_user_search_details = [ 
                            'active_page'   => '3',
                            'appeal_amount'   => ($subProDet['appeal_amount']) ? $subProDet['appeal_amount'] : "",
                            'real_tax_amount' => ($subProDet['tax_saving'])?$subProDet['tax_saving'] : "",
                            'case_1'   => ($subProDet['case_1'])?$subProDet['case_1']:"",
                            'apply_case_1'   => ($subProDet['apply_case_1'])?$subProDet['apply_case_1']:"",
                            'no_appeal_message'   => ($subProDet['no_appeal_message'])?$subProDet['no_appeal_message']:"",
                            'no_appeal_recommendation'   => ($subProDet['no_appeal_recommendation'])?$subProDet['no_appeal_recommendation']:"",
                            'total_assessed_value_amount'   => ($subProDet['total_assessed_value_amount'])?$subProDet['total_assessed_value_amount']:"",
                            'sale_date'   => ($subProDet['date_of_sale'])?$subProDet['date_of_sale']:"",
                            'sale_price'   => ($subProDet['sale_price'])?$subProDet['sale_price']:"",
                            //'comparables'   => $subject_property_comparables['api_result'],
                            'land_assessment_value'   => $request->land_assessment_value,
                            'improvement_assessment_value'   => $request->improvement_assessment_value,
                            'total_assessment_value'   => $request->total_assessment_value,
                            
                        ];
                        $update_user_search = UserSearch::where('user_search_id', $user_search_id)->update($update_user_search_details);

                        DB::commit();
                        
                        $comparables_path = public_path('search_comparables/'.$user_search_id);
                        $comparables_file_name = 'comparables.txt';
                        
                        $storage_file_path = 'search_comparables/'.$user_search_id.'/'.$comparables_file_name;
                        Storage::put($storage_file_path, $subject_property_comparables['api_result']);
                        
                        return redirect('/assessment-review/'.$token);
                    }
                    else {
                        DB::rollback();
                        //return response()->json(['success'=>false, 'message' => $subject_property_comparables['message']]);
                        return Redirect::back()->withErrors([$subject_property_comparables['message']]);
                    }
                }
                else {
                    DB::rollback();
                    //return response()->json(['success'=>false, 'message' => 'Sorry, something went wrong. Plese try again.']);
                    return Redirect::back()->withErrors(['Sorry, something went wrong. Plese try again.']);
                }
                
            }
            else {
                DB::rollback();
                //return redirect('/invalid-token');
                //return response()->json(['success'=>true, 'redirect_url' => url('/invalid-token')]);
                return redirect()->to(url('/invalid-token'));
            }
            
            DB::commit();
            //return redirect('/token-status/');
            //return response()->json(['success'=>true, 'redirect_url' => url('/token-status')]);
            return redirect()->to(url('/token-status'));
        
        }
        catch (\Exception $e) 
        {   
            DB::rollback();
            //$result = ['exception_message' => $e->getMessage()];
            //return view('errors.error', $result);
            //return response()->json(['success'=>false, 'message' => $e->getMessage()]);
            return Redirect::back()->withErrors([$e->getMessage()]);
        }
        
    }
    
    /**
      * Return top 5 comparables after applying adjustments.
      * @param   
      * @return Response
    **/
    public function postSubjectPropertyComparables($customer_search_address, $curr_asse_val, $ubject_updated_info = '') {
        try {
            $comparables_response = [];
            $comparables_response['subject_property'] = $comparables_response['comparables'] = [];
            
            $current_asse_value = trim($curr_asse_val);
            //$address_state = State::find('24');
            $stateAbr = State::find($customer_search_address->state);
                
            if($stateAbr->state_abbr == 'VA' || $stateAbr->state_abbr == 'DC' || $stateAbr->state_abbr == 'MD') {
                $state_abbr = $stateAbr->state_abbr;
            }
            else {
                $address_state = State::find('24');
                $state_abbr = $address_state->state_abbr;
            }
            
            $lookup_type = PfLookupType::where('name', 'Adjustment_'.$state_abbr)->get();
            if(count($lookup_type)) {
                $lookup_type_details = PfLookup::where('lookup_type_id', $lookup_type[0]->lookup_type_id)->get();
            }

            $api_values['saleDateFromDate'] = "";
            $api_values['saleDateToDate'] = "";
            $api_values['searchType'] = 'comparable';
            $api_values['LotSizeToNumber'] = 0;

            $subject_property = array();
            if(isset($ubject_updated_info) && !empty($ubject_updated_info['subject_property']) && isset($ubject_updated_info['subject_property'])){
                $subject_property = json_decode($ubject_updated_info['subject_property'], true);
                $api_values['LotSizeToNumber'] = (3 * trim($subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_SITE']['_DIMENSIONS']['@attributes']['_LotAreaSquareFeetNumber']) );
            }

            $form_data = array();
            $form_data['street'] = $customer_search_address->address_line_1 ." ".$customer_search_address->address_line_2;
            $form_data['city'] = $customer_search_address->city;
            $form_data['state'] = $state_abbr;
            $form_data['postal_code'] = $customer_search_address->postal_code;
            $form_data['total_assessment_value'] = $curr_asse_val;
            $form_data['county_id'] = $customer_search_address->county;
            $comparables_response['form_data'] = $form_data;

            if(isset($lookup_type_details) && count($lookup_type_details)) {
                $client = new Client();
            
                $api_values['street'] = $customer_search_address->address_line_1.' '.$customer_search_address->address_line_2 ;
                $api_values['city'] = $customer_search_address->city;
                $api_values['state'] = $state_abbr;
                $api_values['zipcode'] = $customer_search_address->postal_code;
                
                $total_assessment_value = $customer_search_address->total_assessment_value;

                $api_values['DistanceFromSubjectNumber'] = $api_values['LandUse'] = $api_values['LivingAreaVariancePercent'] = $api_values['NumCompsReturned'] = '';
                $date_of_value = "";
                if (isset($customer_search_address->county) && $customer_search_address->county != "") {

                    $county_details = County::find($customer_search_address->county);
                    
                    if(count($county_details)>0 && isset($county_details->date_of_value) && $county_details->date_of_value != null){
                        $date_of_value = $county_details->date_of_value;

                        $fromDate = date('Y-m-d', strtotime('-1 year', strtotime($county_details->date_of_value)));
                        $toDate = date('Y-m-d', strtotime('-1 day', strtotime($county_details->date_of_value)));

                        $api_values['saleDateFromDate'] = trim(str_replace('-', '', $fromDate));
                        $api_values['saleDateToDate'] = trim(str_replace('-', '', $toDate));
                    }
                }

                $search_criteria_lookup = PfLookupType::where('name', 'Search_criteria')->get();
                if(count($search_criteria_lookup)) { 
                    $search_criteria_conditions = PfLookup::where('lookup_type_id', $search_criteria_lookup[0]->lookup_type_id)->get();
                    if(count($search_criteria_conditions)) {
                        foreach($search_criteria_conditions as $search_criteria_condition) {
                            if($search_criteria_condition->name == 'DistanceFromSubjectNumber') {
                                $api_values['DistanceFromSubjectNumber'] = $search_criteria_condition->value;
                            }
                            if($search_criteria_condition->name == 'LandUse') {
                                //$LandUse = $search_criteria_condition->value;
                                $api_values['LandUse'] = '<_LAND_USE _SameAsSubjectType = "Yes" />';
                            }
                            if($search_criteria_condition->name == 'LivingAreaVariancePercent') {
                                $api_values['LivingAreaVariancePercent'] = $search_criteria_condition->value;
                                $api_values['square_footage'] = $ubject_updated_info['square_footage'];
                            }
                            if($search_criteria_condition->name == 'NumCompsReturned') {
                                $api_values['NumCompsReturned'] = $search_criteria_condition->value;
                            }
                            /*if($search_criteria_condition->name == 'MonthsBackNumber') {
                                $api_values['MonthsBackNumber'] = $search_criteria_condition->value;
                            }*/
                        }
                    }
                }
                
                $xml_request = Helper::corelogicApiXML($api_values);
                //echo "<pre>"; print_r($xml_request); die;
                $response = $client->post($xml_request['url'], ['body' => $xml_request['input_xml']]);
                
                $result = json_decode(json_encode(simplexml_load_string($response->getBody())), true);   
                
                // Subject Property Details            
                if(isset($subject_property) && !empty($subject_property) && isset($result) && !empty($result) ){
                //if(isset($result['RESPONSE']['RESPONSE_DATA']['PROPERTY_INFORMATION_RESPONSE']['_PROPERTY_INFORMATION']) && count($result['RESPONSE']['RESPONSE_DATA']['PROPERTY_INFORMATION_RESPONSE']['_PROPERTY_INFORMATION']) > 0) {

                    $subject_property_details = [];
                    $subject_property_details['appeal_amount'] = "";    
                    
                    //$subject_property = isset($result['RESPONSE']['RESPONSE_DATA']['PROPERTY_INFORMATION_RESPONSE']['_PROPERTY_INFORMATION'][0]) ? $result['RESPONSE']['RESPONSE_DATA']['PROPERTY_INFORMATION_RESPONSE']['_PROPERTY_INFORMATION'][0] : $result['RESPONSE']['RESPONSE_DATA']['PROPERTY_INFORMATION_RESPONSE']['_PROPERTY_INFORMATION'];

                    $subject_sale_price = trim($subject_property['PROPERTY']['_PROPERTY_HISTORY']['_SALES_HISTORY']['@attributes']['_LastSalesPriceAmount']);             
                    $subjectSalePrice = ($subject_sale_price != '') ? $subject_sale_price : '0';


                    // start overwrite variables here 19 feb 2018
                    if(isset($ubject_updated_info) && !empty($ubject_updated_info)){
                        $subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_ROOM_COUNT']['@attributes']['_TotalLivingAreaSquareFeetNumber'] =  $ubject_updated_info['square_footage'];

                        $subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_ROOM_COUNT']['@attributes']['_TotalBedroomsCount'] =  $ubject_updated_info['bedrooms'];

                        $subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_ROOM_COUNT']['@attributes']['_TotalBathsCount'] =  $ubject_updated_info['bathrooms'];

                        $subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_BASEMENT']['@attributes']['_FinishedAreaSquareFeet'] =  $ubject_updated_info['finished_space'];

                    }
                    // end variables overwrite here 19 feb 2018

                    $subjectSquareFeet = trim($subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_ROOM_COUNT']['@attributes']['_TotalLivingAreaSquareFeetNumber']);

                    $onePercent = ($subjectSalePrice/$subjectSquareFeet);
                    
                    $afterOnePercent = $onePercent + ($onePercent*config('constants.tier1OutlierPercent')/100) ;
                    $beforeOnePercent = $onePercent - ($onePercent*config('constants.tier1OutlierPercent')/100);

                    $afterTwentyPercent = $onePercent; //$onePercent + ($onePercent*20/100) ;
                    $beforeTwentyPercent = $onePercent - ($onePercent*config('constants.tier1Outlier2Percent')/100);

                    
                    $subject_property_details['real_tax_amount'] = $subject_property['PROPERTY']['_PROPERTY_TAX']['@attributes']['_RealEstateTotalTaxAmount'];

                    $subject_property_details['total_assessed_value_amount'] = $subject_property['PROPERTY']['_PROPERTY_TAX']['@attributes']['_TotalAssessedValueAmount'];

                    $subject_property_details['subject_salePrice_minus_1_percent'] = $beforeOnePercent;                    
                    $subject_property_details['subject_salePrice_plus_1_percent'] = $afterOnePercent;
                           
                    $subject_property_details['subject_salePrice_minus_twenty_percent'] = $beforeTwentyPercent;                    
                    $subject_property_details['subject_salePrice_plus_twenty_percent'] = $afterTwentyPercent;
                                        
                        
                    $first_diff = "";
                    $second_diff = "";

                    $subject_property_details['case_1'] = 0;
                    $subject_property_details['apply_case_1'] = 0;
                    $subject_property_details['no_appeal_message'] = "";
                    $subject_property_details['differential_value'] = 0;
                    $subject_property_details['no_appeal_recommendation'] = 0;
                    $subject_property_details['tax_saving'] = "";

                    $sale_price = trim($subject_property['PROPERTY']['_PROPERTY_HISTORY']['_SALES_HISTORY']['@attributes']['_LastSalesPriceAmount']);
                    $subject_property_details['appeal_amount'] = $sale_price;    
                    $subject_property_details['current_asse_value'] = $current_asse_value;
                    


                    //Current Assessment <= $250,000
                    if ($current_asse_value <= config('constants.caseRangeFrom')){
                        //Subject Sale Price <= 0.94 * Current Assessment
                        $subject_property_details['differential_value'] = 6;
                    } elseif ( ($current_asse_value > config('constants.caseRangeFrom')) && ($current_asse_value <= config('constants.caseRangeTo')) ) {
                        //Subject Sale Price <= 0.97 * Current Assessment
                        $subject_property_details['differential_value'] = 3;
                    } elseif ( $current_asse_value > config('constants.caseRangeTo') ) {
                        //Subject Sale Price  <=  0.98 * Current Assessment
                        $subject_property_details['differential_value'] = 2;
                    }

                    if(isset($date_of_value) && $date_of_value != "" && isset($subject_property['PROPERTY']['_PROPERTY_HISTORY']['_SALES_HISTORY']['@attributes']['_LastSalesDate']) && $subject_property['PROPERTY']['_PROPERTY_HISTORY']['_SALES_HISTORY']['@attributes']['_LastSalesDate'] != ""){
                        $subject_sale_date = date('Y-m-d', strtotime($subject_property['PROPERTY']['_PROPERTY_HISTORY']['_SALES_HISTORY']['@attributes']['_LastSalesDate']));
                        
                        $date_of_val = explode(' ', $date_of_value); 
                        $date1 = date_create($date_of_val[0]);    
                        $date2 = date_create($subject_sale_date);
                        $date_of_value_days = date_diff($date1, $date2);
                        $subject_sale_date_days = date_diff($date2, $date1);

                        
                        
                        $getMonthBack = self::getYearMonth(config('constants.recentSaleMonthsBefore'), '-');
                        $getMonthForward = self::getYearMonth(config('constants.recentSaleMonthsAfter'), '+');
                        
                        $first = date("Y-m-d", strtotime($date_of_val[0].' '.$getMonthBack));
                        $eighteenMonthBack = date_diff(date_create($date_of_val[0]), date_create($first));

                        $second = date("Y-m-d", strtotime($date_of_val[0].' '.$getMonthForward));
                        $twelveMonths = date_diff(date_create($date_of_val[0]), date_create($second));
                        
                        if( (($date_of_value_days->days <= $eighteenMonthBack->days) && ($date_of_value_days->days > 0)) || (($subject_sale_date_days->days <= $twelveMonths->days) && ($subject_sale_date_days->days > 0)) ){
                            //case 1 start here
                            $subject_property_details['case_1'] = 1;

                            if($sale_price < $current_asse_value) {
                               
                                $differential_value = ((100-$subject_property_details['differential_value'])/100);

                                if ( $sale_price <= ($differential_value * $current_asse_value) ) {
                                    $subject_property_details['apply_case_1'] = 1;
                                } else {
                                    $subject_property_details['no_appeal_recommendation'] = 1;
                                    $subject_property_details['no_appeal_message'] = "Subject sale price is > Differential_value * Current Assessment hence no appeal";  
                                }
                                
                            }else{
                                $subject_property_details['no_appeal_recommendation'] = 1;
                                $subject_property_details['no_appeal_message'] = "This sale is coming under sale_price < current_asse_value assessment hence there is no appeal";
                            }
                            //case 1 end here
                        } else {
                            // case 2                                    
                        }

                    }
                    
                    //Adjustment start here
                    $subject_property_details['type_of_house'] = $subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_SITE']['_CHARACTERISTICS']['@attributes']['_LandUseDescription'];
                    $subject_property_details['type_of_house'] = ($subject_property_details['type_of_house'] != '') ? $subject_property_details['type_of_house'] : '-';
                    $subject_property_details['sale_price'] = $subject_property['PROPERTY']['_PROPERTY_HISTORY']['_SALES_HISTORY']['@attributes']['_LastSalesPriceAmount'];
                    $subject_property_details['sale_price'] = ($subject_property_details['sale_price'] != '') ? $subject_property_details['sale_price'] : '0';
                    $subject_property_details['date_of_sale'] = ($subject_property['PROPERTY']['_PROPERTY_HISTORY']['_SALES_HISTORY']['@attributes']['_LastSalesDate'] != "")?date('Y-m-d', strtotime($subject_property['PROPERTY']['_PROPERTY_HISTORY']['_SALES_HISTORY']['@attributes']['_LastSalesDate'])):"NA";
                    $subject_property_details['parcel_size'] = $subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_SITE']['_DIMENSIONS']['@attributes']['_LotAreaSquareFeetNumber'];
                    $subject_property_details['parcel_size'] = ($subject_property_details['parcel_size'] != '') ? $subject_property_details['parcel_size'] : '0';
                    $subject_property_details['total_bedrooms'] = $subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_ROOM_COUNT']['@attributes']['_TotalBedroomsCount'];
                    $subject_property_details['total_bedrooms'] = ($subject_property_details['total_bedrooms'] != '') ? $subject_property_details['total_bedrooms'] : '0';
                    $subject_property_details['total_bathrooms'] = $subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_ROOM_COUNT']['@attributes']['_TotalBathsCount'];
                    $subject_property_details['total_bathrooms'] = ($subject_property_details['total_bathrooms'] != '') ? $subject_property_details['total_bathrooms'] : '0';
                    $subject_property_details['total_basement_space'] = $subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_BASEMENT']['@attributes']['_AreaSquareFeet'];
                    $subject_property_details['total_basement_space'] = ($subject_property_details['total_basement_space'] != '') ? $subject_property_details['total_basement_space'] : '0';
                    $subject_property_details['finished_space'] = $subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_BASEMENT']['@attributes']['_FinishedAreaSquareFeet'];
                    $subject_property_details['finished_space'] = ($subject_property_details['finished_space'] != '') ? $subject_property_details['finished_space'] : '0';

                    $subject_property_details['square_footage'] = $subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_ROOM_COUNT']['@attributes']['_TotalLivingAreaSquareFeetNumber'];
                    $subject_property_details['square_footage'] = ($subject_property_details['square_footage'] != '') ? $subject_property_details['square_footage'] : '0';
                    
                    // start overwrite variables 19 feb 2018
                    if(isset($ubject_updated_info['unfinished_space'])){
                        $subject_property_details['unfinished_space'] = $ubject_updated_info['unfinished_space']; 
                    } else {
                        $subject_property_details['unfinished_space'] = $subject_property_details['total_basement_space'] - $subject_property_details['finished_space'];
                    }

                    $subject_property_details['garage_count'] = "0";
                    if(isset($ubject_updated_info['garage']) && $ubject_updated_info['garage'] != 0 ){
                        $subject_property_details['garage_count'] = $ubject_updated_info['garage'];
                    }

                    if(isset($ubject_updated_info['carport']) && $ubject_updated_info['carport'] != 0 ){
                        $subject_property_details['carport_exist'] = $ubject_updated_info['carport'];
                    } else {
                        $subject_property_details['carport_exist'] = '0';
                    }                    

                    $subject_property_details['pool_exist'] = '0';
                    $subject_property_details['fireplace_exist'] = '0';
                    $subject_property_details['fireplace_count'] = '0';

                    if(isset($ubject_updated_info['fireplace']) && $ubject_updated_info['fireplace'] != 0 ){
                        $subject_property_details['fireplace_exist'] = '1';
                        $subject_property_details['fireplace_count'] = $ubject_updated_info['fireplace'];
                    }
                    if(isset($ubject_updated_info['swimming_pool']) && $ubject_updated_info['swimming_pool'] != 0 ){
                        $subject_property_details['pool_exist'] = '1';
                    }
                    // end overwrite variables

                    //Living area variance percent +/- 20% (logic here)
                    $sub_living_area = $subject_property_details['square_footage'];
                    
                    $subject_property_details['sub_ass_div_sf'] =  ($current_asse_value / $sub_living_area);

                    $subject_property_details['living_area_plus_twenty'] =  ((($sub_living_area * config('constants.livingAreaVarianceFilterPercent'))/100)+$sub_living_area);

                    $subject_property_details['living_area_minus_twenty'] = (($sub_living_area-($sub_living_area * config('constants.livingAreaVarianceFilterPercent'))/100));


                    $final_comparables = array();    
                    $comparable_values = array();    
                    
                    /*echo '<pre>';
                    print_r($subject_property);
                    print_r($result);
                    exit;*/
                    //comparables array start here
                    if(isset($result['RESPONSE']['RESPONSE_DATA']['PROPERTY_INFORMATION_RESPONSE']['_PROPERTY_INFORMATION'])) {           
                        // Comparables data
                        $comparables_count = $result['RESPONSE']['RESPONSE_DATA']['PROPERTY_INFORMATION_RESPONSE']['_PROPERTY_INFORMATION']['@attributes']['_TotalComparableRecordCount'];

                        $comparables_result = [];
                        for($i=1; $i<=$comparables_count; $i++) {
                            $current_comparable = $result['RESPONSE']['RESPONSE_DATA']['PROPERTY_INFORMATION_RESPONSE']['_PROPERTY_INFORMATION']['_DATA_PROVIDER_COMPARABLE_SALES'][$i];

                            if($current_comparable['PROPERTY']['_PROPERTY_HISTORY']['_SALES_HISTORY']['@attributes']['_LastSalesPriceAmount'] != '') {
                                
                                $comparables_result[$i]['comparable_number'] = $i;
                                
                                
                                $comparables_result[$i]['phone_number'] = (!empty($current_comparable['PROPERTY']['PROPERTY_OWNER']['@attributes']['_PhoneNumber'])) ? $current_comparable['PROPERTY']['PROPERTY_OWNER']['@attributes']['_PhoneNumber'] : '';

                                $comparables_result[$i]['land_assessment_value'] = (!empty($current_comparable['PROPERTY']['_PROPERTY_TAX']['@attributes']['_LandValueAmount'])) ? $current_comparable['PROPERTY']['_PROPERTY_TAX']['@attributes']['_LandValueAmount'] : '';

                                $comparables_result[$i]['improvement_assesment_value'] = (!empty($current_comparable['PROPERTY']['_PROPERTY_TAX']['@attributes']['_ImprovementValueAmount'])) ? $current_comparable['PROPERTY']['_PROPERTY_TAX']['@attributes']['_ImprovementValueAmount'] : '';

                                $comparables_result[$i]['total_assessment_value'] = (!empty($current_comparable['PROPERTY']['_PROPERTY_TAX']['@attributes']['_TotalAssessedValueAmount'])) ? $current_comparable['PROPERTY']['_PROPERTY_TAX']['@attributes']['_TotalAssessedValueAmount'] : '';

                                $comparables_result[$i]['half_bath_count'] = $current_comparable['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_ROOM_COUNT']['@attributes']['_TotalHalfBathsCount'];
                                
                                

                                //$comparables_result[$i]['comparable_address'] = "-";
                                $comparables_result[$i]['comparable_address_street'] = (!empty($current_comparable['PROPERTY']['@attributes']['_StreetAddress'])) ? $current_comparable['PROPERTY']['@attributes']['_StreetAddress'] : '';

                                $comparables_result[$i]['comparable_address_city'] = (!empty($current_comparable['PROPERTY']['@attributes']['_City'])) ? $current_comparable['PROPERTY']['@attributes']['_City'] : '';
                                $comparables_result[$i]['comparable_address_state'] = (!empty($current_comparable['PROPERTY']['@attributes']['_State'])) ? $current_comparable['PROPERTY']['@attributes']['_State'] : '';
                                $comparables_result[$i]['comparable_address_county'] = (!empty($current_comparable['PROPERTY']['@attributes']['_County'])) ? $current_comparable['PROPERTY']['@attributes']['_County'] : '';
                                $comparables_result[$i]['comparable_address_zipcode'] = (!empty($current_comparable['PROPERTY']['@attributes']['_PostalCode'])) ? $current_comparable['PROPERTY']['@attributes']['_PostalCode'] : '';
                                
                                $comparables_result[$i]['comparable_type_of_house'] = $current_comparable['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_SITE']['_CHARACTERISTICS']['@attributes']['_LandUseDescription'];
                                $comparables_result[$i]['comparable_square_footage'] = $current_comparable['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_ROOM_COUNT']['@attributes']['_TotalLivingAreaSquareFeetNumber'];

                                $comparables_result[$i]['distance_from_subject'] = $current_comparable['@attributes']['_DistanceFromSubjectNumber'];
                                $comparables_result[$i]['sale_price'] = $current_comparable['PROPERTY']['_PROPERTY_HISTORY']['_SALES_HISTORY']['@attributes']['_LastSalesPriceAmount'];
                                $comparables_result[$i]['sale_price'] = $comparables_result[$i]['comparable_sale_price'] = ($comparables_result[$i]['sale_price'] != '') ? $comparables_result[$i]['sale_price'] : '0';
                                $comparables_result[$i]['date_of_sale'] = date('Y-m-d', strtotime($current_comparable['PROPERTY']['_PROPERTY_HISTORY']['_SALES_HISTORY']['@attributes']['_LastSalesDate']));
                                $comparables_result[$i]['parcel_size'] = $current_comparable['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_SITE']['_DIMENSIONS']['@attributes']['_LotAreaSquareFeetNumber'];
                                $comparables_result[$i]['parcel_size'] = $comparables_result[$i]['comparable_parcel_size'] = ($comparables_result[$i]['parcel_size'] != '') ? $comparables_result[$i]['parcel_size'] : '0';
                                $comparables_result[$i]['total_bedrooms'] = $current_comparable['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_ROOM_COUNT']['@attributes']['_TotalBedroomsCount'];
                                $comparables_result[$i]['total_bedrooms'] = $comparables_result[$i]['comparable_total_bedrooms'] = ($comparables_result[$i]['total_bedrooms'] != '') ? $comparables_result[$i]['total_bedrooms'] : '0';
                                $comparables_result[$i]['total_bathrooms'] = $current_comparable['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_ROOM_COUNT']['@attributes']['_TotalBathsCount'];
                                $comparables_result[$i]['total_bathrooms'] = $comparables_result[$i]['comparable_total_bathrooms'] = ($comparables_result[$i]['total_bathrooms'] != '') ? $comparables_result[$i]['total_bathrooms'] : '0';
                                
                                $comparables_result[$i]['total_basement_space'] = $current_comparable['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_BASEMENT']['@attributes']['_AreaSquareFeet'];
                                $comparables_result[$i]['total_basement_space'] = ($comparables_result[$i]['total_basement_space'] != '') ? $comparables_result[$i]['total_basement_space'] : '0';
                                $comparables_result[$i]['finished_space'] = $current_comparable['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_BASEMENT']['@attributes']['_FinishedAreaSquareFeet'];
                                $comparables_result[$i]['finished_space'] = $comparables_result[$i]['comparable_finished_space'] = ($comparables_result[$i]['finished_space'] != '') ? $comparables_result[$i]['finished_space'] : '0';

                                $comparables_result[$i]['unfinished_space'] = $comparables_result[$i]['comparable_unfinished_space'] = $comparables_result[$i]['total_basement_space'] - $comparables_result[$i]['finished_space'];
                                
                                if(!empty($current_comparable['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_PARKING']['@attributes']['_GarageTotalCarCount'])) {
                                    //$comparables_result[$i]['garage_count'] = $comparables_result[$i]['comparable_garage_count'] = $current_comparable['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_PARKING']['@attributes']['_GarageTotalCarCount'];
                                    $comparables_result[$i]['garage_count'] = $comparables_result[$i]['comparable_garage_count'] = 0;
                                }
                                else {                                
                                    //$garage_area_total = $current_comparable['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_PARKING']['@attributes']['_GarageSquareFeetNumber'] + $current_comparable['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_PARKING']['@attributes']['_GarageTwoArea'];
                                    $garage_area_total = trim($current_comparable['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_PARKING']['@attributes']['_GarageSquareFeetNumber']);
                                    if(!empty($garage_area_total) && $garage_area_total < 400&& $garage_area_total >= 1) {
                                        $comparables_result[$i]['garage_count'] = $comparables_result[$i]['comparable_garage_count'] = '1';
                                    }
                                    else {
                                        $comparables_result[$i]['garage_count'] = $comparables_result[$i]['comparable_garage_count'] = round(($garage_area_total / 400));
                                    }
                                }
                                
                                $comparables_result[$i]['carport_exist'] = $comparables_result[$i]['comparable_carport_exist'] = (!empty($current_comparable['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_PARKING']['@attributes']['_CarportArea'])) ? '1' : '0';

                                $comparables_result[$i]['porch_deck_exist'] = $comparables_result[$i]['comparable_porch_deck_exist'] = '0';
                                $comparables_result[$i]['patio_exist'] = $comparables_result[$i]['comparable_patio_exist'] = '0';
                                foreach($current_comparable['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_XTRA_FEATURES'] as $extra_features) {
                                    if(isset($extra_features['@attributes'])) {
                                        if(((strpos($extra_features['@attributes']['_Xtra_Features_Code'], 'porch') !== false) || (strpos($extra_features['@attributes']['_Xtra_Features_Code'], 'deck') !== false)) && $comparables_result[$i]['porch_deck_exist'] == '0') {
                                            $comparables_result[$i]['porch_deck_exist'] = $comparables_result[$i]['comparable_porch_deck_exist'] = '1';
                                        }
                                        if(strpos($extra_features['@attributes']['_Xtra_Features_Code'], 'patio') !== false && $comparables_result[$i]['patio_exist'] == '0') {
                                            $comparables_result[$i]['patio_exist'] = $comparables_result[$i]['comparable_patio_exist'] = '1';
                                        }
                                    }                    
                                }

                                $comparables_result[$i]['pool_exist'] = $comparables_result[$i]['comparable_pool_exist'] = '0';
                                $comparables_result[$i]['fireplace_exist'] = $comparables_result[$i]['comparable_fireplace_exist'] = '0';
                                $comparables_result[$i]['fireplace_count'] = $comparables_result[$i]['comparable_fireplace_count'] = '0';
                                foreach($current_comparable['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_FEATURES'] as $feature_key => $features) {
                                    if($feature_key == '_FIREPLACES') {
                                        $comparables_result[$i]['fireplace_exist'] = $comparables_result[$i]['comparable_fireplace_exist'] = ($features['@attributes']['_HasFeatureIndicator'] == 'Y') ? '1' : '0';
                                        $comparables_result[$i]['fireplace_count'] = $comparables_result[$i]['comparable_fireplace_count'] = ($comparables_result[$i]['fireplace_exist'] == '1') ? $features['@attributes']['_CountNumber'] : '0';
                                    }
                                    if($feature_key == '_POOL') {
                                        $comparables_result[$i]['pool_exist'] = $comparables_result[$i]['comparable_pool_exist'] = ($features['@attributes']['_HasFeatureIndicator'] == 'Y') ? '1' : '0';
                                    }
                                    $comparables_result[$i]['comparable_garage_values'] = $current_comparable['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_PARKING']['@attributes']['_GarageSquareFeetNumber'];
                                }  
                                $comparables_result[$i]['is_one_percent'] = "No";
                                $comparables_result[$i]['is_twenty_percent'] = "No";     

                                $comparables_result[$i]['year_built'] = $current_comparable['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_GENERAL_DESCRIPTION']['@attributes']['_YearBuiltDateIdentifier'];

                                $comparables_result[$i]['data_source'] = "";
                                $comparables_result[$i]['site'] = $current_comparable['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_ROOM_COUNT']['@attributes']['_TotalLivingAreaSquareFeetNumber'];

                                $comparables_result[$i]['subsidy'] = "";
                                $comparables_result[$i]['leasehold'] = "";
                                
                                $comparables_result[$i]['exterior'] = $current_comparable['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_EXTERIOR_DESCRIPTION']['@attributes']['_ExteriorWallsIdentifier'];

                                $comparables_result[$i]['gross_living_area'] = $current_comparable['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_ROOM_COUNT']['@attributes']['_GrossLivingAreaSquareFeetNumber'];
                                $comparables_result[$i]['basement'] = $current_comparable['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_BASEMENT']['@attributes']['_AreaSquareFeet'];

                                $comparables_result[$i]['basement_type'] = $current_comparable['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_BASEMENT']['@attributes']['_Type'];

                                $comparables_result[$i]['net_adjustment'] = "";                
                            }

                        }

                        // store comparables detail starts here
                        $lookup_details = [];
                        foreach($lookup_type_details as $lookup_type_detail) {
                            $lookup_details[$lookup_type_detail->name]['value'] = $lookup_type_detail->value;
                            $lookup_details[$lookup_type_detail->name]['value1'] = $lookup_type_detail->value1;
                            $lookup_details[$lookup_type_detail->name]['value2'] = $lookup_type_detail->value2;
                        }

                        $comparable_values = [];
                        $adjustment_values = [];
                        $final_comparables = [];
                        $j=0;

                        foreach($comparables_result as $comparable) {
                            $comparable_values[$j] = $comparable;
                                                        
                            if($comparable['sale_price'] < 750000) {
                                $value = 'value';
                            }
                            else if($comparable['sale_price'] >= 750000 && $comparable['sale_price'] < 1500000) {
                                $value = 'value1';
                            }
                            else if($comparable['sale_price'] >= 1500000) {
                                $value = 'value2';
                            }

                            $comparable_values[$j]['date_of_sale'] = $comparable_values[$j]['square_footage'] = $comparable_values[$j]['parcel_size'] = $comparable_values[$j]['total_bedrooms'] = 0;
                            $comparable_values[$j]['total_bathrooms'] = $comparable_values[$j]['finished_space'] = $comparable_values[$j]['unfinished_space'] = 0;
                            $comparable_values[$j]['garage'] = $comparable_values[$j]['carport'] = $comparable_values[$j]['pool'] = $comparable_values[$j]['fireplace'] = 0;

                            $comparable_values[$j]['distance_from_subject'] = $comparable['distance_from_subject'];
                            
                            $comparable_values[$j]['comparable_date_of_sale'] = date('Y-m-d', strtotime($comparable['date_of_sale']));
                           
                            if($lookup_details['above_grade_sq_footage_percent'][$value] != '0') {
                                $square_footage_lookup_type = 'above_grade_sq_footage_percent';
                            }
                            else if($lookup_details['above_grade_sq_footage_amount'][$value] != '0') {
                                $square_footage_lookup_type = 'above_grade_sq_footage_amount';
                            }

                            if(substr($lookup_details[$square_footage_lookup_type][$value], -1) == '%') {
                                $comparable_values[$j]['square_footage'] = round((($subject_property_details['square_footage'] - $comparable['comparable_square_footage']) * rtrim($lookup_details[$square_footage_lookup_type][$value],"%")) / 100, 2);
                                $comparable_values[$j]['formula_square_footage'] = "round(((".$subject_property_details['square_footage']." - ".$comparable['comparable_square_footage'].") * ".rtrim($lookup_details[$square_footage_lookup_type][$value],"%").") / 100, 2)";
                            }
                            else if(substr($lookup_details[$square_footage_lookup_type][$value], -1) == '$') {
                                $comparable_values[$j]['square_footage'] = round(($subject_property_details['square_footage'] - $comparable['comparable_square_footage']) * rtrim($lookup_details[$square_footage_lookup_type][$value],"$"), 2);
                                $comparable_values[$j]['formula_square_footage'] = "round(((".$subject_property_details['square_footage']." - ".$comparable['comparable_square_footage'].") * ".rtrim($lookup_details[$square_footage_lookup_type][$value],"$")."), 2)";
                            }
                            else {
                                $comparable_values[$j]['formula_square_footage'] = "-";
                            }
                            
                            $comparable_values[$j]['parcel_percent'] = $percent_amount_compared = abs(round((($subject_property_details['parcel_size'] - $comparable_values[$j]['comparable_parcel_size']) / $subject_property_details['parcel_size']) * 100, 0));

                            if($percent_amount_compared >= 50) {
                                if($comparable_values[$j]['comparable_parcel_size'] > $subject_property_details['parcel_size']) { 
                                    if(substr($lookup_details['parcel_comp_greater_than_subject'][$value], -1) == '%') {
                                        $comparable_values[$j]['parcel_size'] = round((($subject_property_details['parcel_size'] - $comparable['comparable_parcel_size']) * rtrim($lookup_details['parcel_comp_greater_than_subject'][$value],"%")) / 100, 2);
                                        $comparable_values[$j]['formula_parcel_size'] = "round(((".$subject_property_details['parcel_size']." - ".$comparable['comparable_parcel_size'].") * ".rtrim($lookup_details['parcel_comp_greater_than_subject'][$value],"%").") / 100, 2)";
                                    }
                                    else if(substr($lookup_details['parcel_comp_greater_than_subject'][$value], -1) == '$') {
                                        $comparable_values[$j]['parcel_size'] = round(($subject_property_details['parcel_size'] - $comparable['comparable_parcel_size']) * rtrim($lookup_details['parcel_comp_greater_than_subject'][$value],"$"), 2);
                                        $comparable_values[$j]['formula_parcel_size'] = "round(((".$subject_property_details['parcel_size']." - ".$comparable['comparable_parcel_size'].") * ".rtrim($lookup_details['parcel_comp_greater_than_subject'][$value],"$").") / 100, 2)";
                                    }
                                    else {
                                        $comparable_values[$j]['formula_parcel_size'] = "-";
                                    }
                                }
                                else if($comparable_values[$j]['comparable_parcel_size'] < $subject_property_details['parcel_size']) { 
                                    if(substr($lookup_details['parcel_comp_less_than_subject'][$value], -1) == '%') {
                                        $comparable_values[$j]['parcel_size'] = round((($subject_property_details['parcel_size'] - $comparable['comparable_parcel_size']) * rtrim($lookup_details['parcel_comp_less_than_subject'][$value],"%")) / 100, 2);
                                        $comparable_values[$j]['formula_parcel_size'] = "round(((".$subject_property_details['parcel_size']." - ".$comparable['comparable_parcel_size'].") * ".rtrim($lookup_details['parcel_comp_less_than_subject'][$value],"%").") / 100, 2)";
                                    }
                                    else if(substr($lookup_details['parcel_comp_less_than_subject'][$value], -1) == '$') { 
                                        $comparable_values[$j]['parcel_size'] = round(($subject_property_details['parcel_size'] - $comparable['comparable_parcel_size']) * rtrim($lookup_details['parcel_comp_less_than_subject'][$value],"$"), 2);
                                        $comparable_values[$j]['formula_parcel_size'] = "round(((".$subject_property_details['parcel_size']." - ".$comparable['comparable_parcel_size'].") * ".rtrim($lookup_details['parcel_comp_less_than_subject'][$value],"$").") / 100, 2)";
                                    }
                                    else {
                                        $comparable_values[$j]['formula_parcel_size'] = "-";
                                    }
                                }
                                else {
                                    $comparable_values[$j]['parcel_size'] = '0';
                                    $comparable_values[$j]['formula_parcel_size'] = "-";
                                }
                            }
                            else {
                                $comparable_values[$j]['parcel_size'] = '0';
                                $comparable_values[$j]['formula_parcel_size'] = "-";
                            }                            


                            if($lookup_details['above_grade_bedrooms_percent'][$value] != '0') {
                                $bedrooms_lookup_type = 'above_grade_bedrooms_percent';
                            }
                            else if($lookup_details['above_grade_bedrooms_amount'][$value] != '0') {
                                $bedrooms_lookup_type = 'above_grade_bedrooms_amount';
                            }

                            if(substr($lookup_details[$bedrooms_lookup_type][$value], -1) == '%') {
                                $comparable_values[$j]['total_bedrooms'] = round((($subject_property_details['total_bedrooms'] - $comparable['total_bedrooms']) * rtrim($lookup_details[$bedrooms_lookup_type][$value],"%")) / 100, 2);
                                $comparable_values[$j]['formula_total_bedrooms'] = "round(((".$subject_property_details['total_bedrooms']." - ".$comparable['total_bedrooms'].") * ".rtrim($lookup_details[$bedrooms_lookup_type][$value],"%").") / 100, 2)";
                            }
                            else if(substr($lookup_details[$bedrooms_lookup_type][$value], -1) == '$') {
                                $comparable_values[$j]['total_bedrooms'] = round(($subject_property_details['total_bedrooms'] - $comparable['total_bedrooms']) * rtrim($lookup_details[$bedrooms_lookup_type][$value],"$"), 2);
                                $comparable_values[$j]['formula_total_bedrooms'] = "round(((".$subject_property_details['total_bedrooms']." - ".$comparable['total_bedrooms'].") * ".rtrim($lookup_details[$bedrooms_lookup_type][$value],"$").") / 100, 2)";
                            }
                            else {
                                $comparable_values[$j]['formula_total_bedrooms'] = "-";
                            }

                            if($lookup_details['above_grade_bathrooms_percent'][$value] != '0') {
                                $bathrooms_lookup_type = 'above_grade_bathrooms_percent';
                            }
                            else if($lookup_details['above_grade_bathrooms_amount'][$value] != '0') {
                                $bathrooms_lookup_type = 'above_grade_bathrooms_amount';
                            }

                            if(substr($lookup_details[$bathrooms_lookup_type][$value], -1) == '%') {
                                $comparable_values[$j]['total_bathrooms'] = round((($subject_property_details['total_bathrooms'] - $comparable['total_bathrooms']) * rtrim($lookup_details[$bathrooms_lookup_type][$value],"%")) / 100, 2);
                                $comparable_values[$j]['formula_total_bathrooms'] = "round(((".$subject_property_details['total_bathrooms']." - ".$comparable['total_bathrooms'].") * ".rtrim($lookup_details[$bathrooms_lookup_type][$value],"%").") / 100, 2)";
                            }
                            else if(substr($lookup_details[$bathrooms_lookup_type][$value], -1) == '$') {
                                $comparable_values[$j]['total_bathrooms'] = round(($subject_property_details['total_bathrooms'] - $comparable['total_bathrooms']) * rtrim($lookup_details[$bathrooms_lookup_type][$value],"$"), 2);
                                $comparable_values[$j]['formula_total_bathrooms'] = "round(((".$subject_property_details['total_bathrooms']." - ".$comparable['total_bathrooms'].") * ".rtrim($lookup_details[$bathrooms_lookup_type][$value],"$").") / 100, 2)";
                            }
                            else {
                                $comparable_values[$j]['formula_total_bathrooms'] = "-";
                            }

                            // finished_space & unfinished_space adjustment has been commented due to client requirement - 13 Dec 2017

                            if(substr($lookup_details['below_grade_finished_space_amount'][$value], -1) == '%') {
                                $comparable_values[$j]['finished_space'] = 0;
                                $comparable_values[$j]['formula_finished_space'] = "-";
                            }
                            else if(substr($lookup_details['below_grade_finished_space_amount'][$value], -1) == '$') {
                                $comparable_values[$j]['finished_space'] = 0;
                                $comparable_values[$j]['formula_finished_space'] = "-";
                            }
                            else {
                                $comparable_values[$j]['finished_space'] = 0;
                                $comparable_values[$j]['formula_finished_space'] = "-";
                            }

                            if(substr($lookup_details['below_grade_unfinished_space_amount'][$value], -1) == '%') {
                                $comparable_values[$j]['unfinished_space'] = 0;
                                $comparable_values[$j]['formula_unfinished_space'] = "-";
                            }
                            else if(substr($lookup_details['below_grade_unfinished_space_amount'][$value], -1) == '$') {
                                $comparable_values[$j]['unfinished_space'] = 0;
                                $comparable_values[$j]['formula_unfinished_space'] = "-";
                            }
                            else { 
                                $comparable_values[$j]['unfinished_space'] = 0;
                                $comparable_values[$j]['formula_unfinished_space'] = "-";
                            }
                           
                            if(substr($lookup_details['garage'][$value], -1) == '$') {
                                $comparable_values[$j]['garage_adjusted_value'] = $comparable_values[$j]['garage'] = round(($subject_property_details['garage_count'] - $comparable['garage_count']) * rtrim($lookup_details['garage'][$value],"$"), 2);
                                $comparable_values[$j]['formula_garage'] = "round(((".$subject_property_details['garage_count']." - ".$comparable['garage_count'].") * ".rtrim($lookup_details['garage'][$value],"$").") / 100, 2)";
                            }
                            else {
                                $comparable_values[$j]['formula_garage'] = "-";
                            }                            
                            
                            if(substr($lookup_details['included_carport'][$value], -1) == '$') {
                                $comparable_values[$j]['carport'] = round(($subject_property_details['carport_exist'] - $comparable['carport_exist']) * rtrim($lookup_details['included_carport'][$value],"$"), 2);
                                $comparable_values[$j]['formula_carport'] = "round(((".$subject_property_details['carport_exist']." - ".$comparable['carport_exist'].") * ".rtrim($lookup_details['included_carport'][$value],"$").") / 100, 2)";
                            }
                            else {
                                $comparable_values[$j]['formula_carport'] = "-";
                            }                            
                            
                            if(substr($lookup_details['swimming_pool'][$value], -1) == '$') {
                                $comparable_values[$j]['pool'] = round(($subject_property_details['pool_exist'] - $comparable['pool_exist']) * rtrim($lookup_details['swimming_pool'][$value],"$"), 2);
                                $comparable_values[$j]['formula_pool'] = "round(((".$subject_property_details['pool_exist']." - ".$comparable['pool_exist'].") * ".rtrim($lookup_details['swimming_pool'][$value],"$").") / 100, 2)";
                            }
                            else {
                                $comparable_values[$j]['formula_pool'] = "-";
                            }
                            
                            if(substr($lookup_details['fireplace'][$value], -1) == '$') {
                                $comparable_values[$j]['fireplace'] = round(($subject_property_details['fireplace_count'] - $comparable['fireplace_count']) * rtrim($lookup_details['fireplace'][$value],"$"), 2);
                                $comparable_values[$j]['formula_fireplace'] = "round(((".$subject_property_details['fireplace_count']." - ".$comparable['fireplace_count'].") * ".rtrim($lookup_details['fireplace'][$value],"$").") / 100, 2)";
                            }
                            else {
                                $comparable_values[$j]['formula_fireplace'] = "-";
                            }

                            $total_adjustments = $comparable_values[$j]['date_of_sale'] + $comparable_values[$j]['square_footage'] + $comparable_values[$j]['parcel_size'] + $comparable_values[$j]['total_bedrooms'] +$comparable_values[$j]['total_bathrooms'] + $comparable_values[$j]['finished_space'] + $comparable_values[$j]['unfinished_space'] + $comparable_values[$j]['garage'] + $comparable_values[$j]['carport'] + $comparable_values[$j]['pool'] + $comparable_values[$j]['fireplace'];
                            
                            $adjustment_values[] = $comparable_values[$j]['total_adjustments'] = $total_adjustments;
                            $distance_of_comparables[] = $comparable['distance_from_subject'];
                            
                            $comparable_values[$j]['price_after_adjustment'] = $comparable['sale_price'] + $total_adjustments;


                            $x = $comparable_values[$j]['price_after_adjustment'];
                            $y = $comparable_values[$j]['comparable_square_footage'];
                            if($x > 0 && $y > 0){
                                $compa_adjus_sale = ($x/$y);
                            }else{
                                $compa_adjus_sale = 0;
                            }

                            $comparable_values[$j]['is_one_percent'] = "No";
                            $comparable_values[$j]['is_twenty_percent'] = "No";
                            
                            if($compa_adjus_sale > $beforeOnePercent && $compa_adjus_sale < $afterOnePercent) {
                                $comparable_values[$j]['is_one_percent'] = "Yes";
                            }

                            if($compa_adjus_sale > $beforeTwentyPercent && $compa_adjus_sale < $afterTwentyPercent) {
                                $comparable_values[$j]['is_twenty_percent'] = "Yes";
                            }

                            // comparable living Area within 20% of subject living area 
                            $plusTwenty = $subject_property_details['living_area_plus_twenty'];
                            $minusTwenty = $subject_property_details['living_area_minus_twenty'];

                            $comparable_values[$j]['living_area_plus_minus_twenty'] = 0;
                            if($comparable_values[$j]['comparable_square_footage'] < $plusTwenty && $comparable_values[$j]['comparable_square_footage'] > $minusTwenty){
                                $comparable_values[$j]['living_area_plus_minus_twenty'] = 1;
                            }
                            
                            //$comparable_values[$j]['comparable_garage_values']
                            $comparable_values[$j]['garage_count'] = 0;
                            $comparable_values[$j]['garage'] = 0;
                            if(!empty($comparable_values[$j]['comparable_garage_values']) && trim($comparable_values[$j]['comparable_garage_values']) <= 400){
                                $comparable_values[$j]['garage_count'] = "1";

                                $comparable_values[$j]['formula_garage'] = "round(( ".$comparable_values[$j]['comparable_garage_values']." / 400 ))";

                                $comparable_values[$j]['garage'] = $comparable_values[$j]['comparable_garage_values'];

                            } else {
                                $comparable_values[$j]['garage_count'] = round(trim($comparable_values[$j]['comparable_garage_values'])/400);

                                $comparable_values[$j]['formula_garage'] = "round(( 0 / 400 ))";

                                $comparable_values[$j]['garage'] = "-";
                            }

                            $comparable_values[$j]['sale_price_divided_square_footage'] = ($comparable_values[$j]['price_after_adjustment']/$comparable_values[$j]['comparable_square_footage']);

                            $j++;
                        }

                        // comparables selection process starts here
                       // echo "<pre>"; print_r($subject_property_details); die;
                        //echo "<pre>before  = "; print_r($final_comparables); print_r($subject_property_details); die;
                        if(count($comparable_values)>0){
                            
                            $comparables_within_one_percent = array();
                            $comparables_within_twenty_percent = array();
                            $comparables_without_one_percent = array();

                            if ($subject_property_details['case_1'] == 1 && $subject_property_details['apply_case_1'] == 1 && $subject_property_details['no_appeal_recommendation'] == 0 ) {
                                //default selection applied based on distance

                                //Sort the results obtained by Sale price/SF (smallest to largest)
                                $afterSorting = array();
                                $distanceArrayKeys = array();
                                foreach ($comparable_values as $key => $row) {
                                    if(array_key_exists($row['distance_from_subject'], $distanceArrayKeys)) {
                                        $distanceArrayKeys[$row['distance_from_subject']][] = $row;
                                    } else {
                                        $distanceArrayKeys[$row['distance_from_subject']][] = $row;
                                    }
                                }

                                foreach ($distanceArrayKeys as $key => $distanceArray) {
                                    $sorted_comparables = array();
                                    foreach ($distanceArray as $k => $row) {
                                        $sorted_comparables[$k] = $row['sale_price_divided_square_footage'];
                                    }
                                    array_multisort($sorted_comparables, SORT_ASC, $distanceArray);
                                    $afterSorting[] = $distanceArray;
                                }
                                // refreshed array after sorting of SalePrice/SF
                                $comparable_values = array();
                                foreach ($afterSorting as $ky => $val) {
                                    foreach ($val as $y => $v ) {
                                        $comparable_values[] = $v;
                                    }
                                }

                                /*if($subject_property_details['sale_price'] > config('constants.excludeLivingAreaAboveAmount') ){
                                    $comparables_within_living_area = $comparable_values;
                                } else {
                                    //Living area variance percent +/- 20%
                                    foreach($comparable_values as $key => $comparable_value) {
                                            
                                        if($comparable_value['living_area_plus_minus_twenty'] === 1) {
                                            $comparables_within_living_area[] = $comparable_value;
                                        }                               
                                    }
                                }*/

                                if($subject_property_details['sale_price'] >= config('constants.excludeLivingAreaAboveAmount') ){
                                    //$comparables_within_living_area = $comparable_values;
                                    //Living area variance percent discard sale 40%
                                    $comparables_within_living_area = self::discardComparables($comparable_values, config('constants.livingAreaVarianceFilterPercentForGreater'), $subject_property_details['sale_price'], $subject_property_details['square_footage']);

                                    
                                } else {
                                    //Living area variance percent discard sale 20%
                                    $comparables_within_living_area = self::discardComparables($comparable_values, config('constants.livingAreaVarianceFilterPercent'), $subject_property_details['sale_price'], $subject_property_details['square_footage']);

                                }
                                
                                //Tier 1 Selection
                                //Select top 5 sales that are within 1% of the sale price/sf of the subject property 
                                foreach($comparables_within_living_area as $key => $comparable_value) {
                                        
                                    if($comparable_value['is_one_percent'] === "Yes") {
                                        $comparables_within_one_percent[] = $comparable_value;
                                    } elseif($comparable_value['is_twenty_percent'] === "Yes") {
                                        $comparables_within_twenty_percent[] = $comparable_value;
                                    } else {
                                        $comparables_without_one_percent[] = $comparable_value;
                                    }
                                }
                                
                                //If (number of comparable 1% Sales >= 3 and <= 5)
                                if(count($comparables_within_one_percent) >= config('constants.minCompsForAppeal') && count($comparables_within_one_percent) <= config('constants.maxCompsForAppeal')) {
                                    $final_comparables = $comparables_within_one_percent;

                                } else {
                                    //count($comparables_within_one_percent)
                                    
                                    //Take the 5 lowest Sale Price/SF 
                                    if(count($comparables_within_twenty_percent)>0){
                                        $sorted_comparables = array();
                                        foreach ($comparables_within_twenty_percent as $k => $row) {
                                            $sorted_comparables[] = $row['sale_price_divided_square_footage'];
                                        }
                                        array_multisort($sorted_comparables, SORT_DESC, $comparables_within_twenty_percent);
                                    }
                                   
                                    $comparables = array_merge($comparables_within_one_percent, $comparables_within_twenty_percent);

                                    
                                    if(count($comparables) >= 1) {
                                        $final_comparables = array_slice($comparables, 0, 5, true);
                                    } else {
                                        // Tier 2 Start here
                                        $withinTier2 = array();
                                        $outsideTier2 = array();
                                        if(count($comparables) < config('constants.maxCompsForAppeal') ) {
                                            foreach ($comparables_without_one_percent as $kk => $vv) {
                                                if( ($vv['price_after_adjustment']/$vv['comparable_square_footage']) <=  (100 - $subject_property_details['differential_value'])/100 * $subject_property_details['current_asse_value']/$subject_property_details['square_footage'] ) {
                                                    $withinTier2[] = $vv;
                                                } else {
                                                    $outsideTier2[] = $vv;
                                                }
                                            }
                                            
                                            $tier2_comparables = array_slice($withinTier2, 0, (config('constants.maxCompsForAppeal') - count($comparables)), true);

                                            $comparables_after_tier2 = array_merge($comparables, $tier2_comparables);
                                            
                                            if(count($comparables_after_tier2) >= config('constants.minCompsForAppeal')) {
                                                $final_comparables = array_slice($comparables_after_tier2, 0, 5, true);
                                            } else {
                                                //echo "Tier 3 start here"; 

                                                if( count($comparables_after_tier2) < config('constants.maxCompsForAppeal') && count($outsideTier2) > 0) {
                                                    //Comparable Adjusted Sale Price/SF closest to Subject Sale Price/SF

                                                    $sub_sale_price_sf = ($subject_property_details['sale_price']/$subject_property_details['square_footage']);
                                                    
                                                    foreach($outsideTier2 as $k => $item) {
                                                        $com_adj_sal_price_sf = ($item['price_after_adjustment'] / $item['comparable_square_footage']);

                                                        $diff[abs($com_adj_sal_price_sf - $sub_sale_price_sf)] = $item;
                                                    }
                                                    ksort($diff, SORT_NUMERIC); 

                                                    $comparables_after_tier3 = array_merge($comparables_after_tier2, $diff);

                                                    $final_comparables = array_slice($comparables_after_tier3, 0, 5, true);
                                                } else {
                                                    $final_comparables = array_slice($comparables_after_tier2, 0, 5, true);
                                                }
                                            }
                                        }       
                                    }
                                    
                                }
                                if(count($final_comparables)>0){
                                    $sorted_comparables = array();
                                    foreach ($final_comparables as $kData => $rowData) {
                                        $sorted_comparables[] = $rowData['sale_price_divided_square_footage'];
                                    }
                                    array_multisort($sorted_comparables, SORT_DESC, $final_comparables);
                                }
                                

                            } elseif($subject_property_details['case_1'] == 0) {
                                // case 2 selection criteria

                                //default selection applied based on distance

                                //Sort the results obtained by Sale price/SF (smallest to largest)
                                $afterSorting = array();
                                $distanceArrayKeys = array();
                                foreach ($comparable_values as $key => $row) {
                                    if(array_key_exists($row['distance_from_subject'], $distanceArrayKeys)) {
                                        $distanceArrayKeys[$row['distance_from_subject']][] = $row;
                                    } else {
                                        $distanceArrayKeys[$row['distance_from_subject']][] = $row;
                                    }
                                }

                                foreach ($distanceArrayKeys as $key => $distanceArray) {
                                    $sorted_comparables = array();
                                    foreach ($distanceArray as $k => $row) {
                                        $sorted_comparables[$k] = $row['sale_price_divided_square_footage'];
                                    }
                                    array_multisort($sorted_comparables, SORT_ASC, $distanceArray);
                                    $afterSorting[] = $distanceArray;
                                }
                                // refreshed array after sorting of SalePrice/SF
                                $comparable_values = array();
                                foreach ($afterSorting as $ky => $val) {
                                    foreach ($val as $y => $v ) {
                                        $comparable_values[] = $v;
                                    }
                                }

                                /*if($subject_property_details['current_asse_value'] > config('constants.excludeLivingAreaAboveAmount') ){
                                    $comparables_within_living_area = $comparable_values;
                                } else {
                                    //Living area variance percent +/- 20%
                                    foreach($comparable_values as $key => $comparable_value) {
                                            
                                        if($comparable_value['living_area_plus_minus_twenty'] === 1) {
                                            $comparables_within_living_area[] = $comparable_value;
                                        }                               
                                    }
                                }*/
                                if($subject_property_details['current_asse_value'] >= config('constants.excludeLivingAreaAboveAmount') ){
                                    //$comparables_within_living_area = $comparable_values;
                                    //Living area variance percent discard sale 40%
                                    $comparables_within_living_area = self::discardComparables($comparable_values, config('constants.livingAreaVarianceFilterPercentForGreater'), $subject_property_details['current_asse_value'], $subject_property_details['square_footage']);

                                    
                                } else {
                                    //Living area variance percent discard sale 20%
                                    $comparables_within_living_area = self::discardComparables($comparable_values, config('constants.livingAreaVarianceFilterPercent'), $subject_property_details['current_asse_value'], $subject_property_details['square_footage']);

                                }
                                
                                //Take the 5 lowest Sale Price/SF 
                                if(count($comparables_within_living_area)>0){
                                    $sorted_comparables = array();
                                    foreach ($comparables_within_living_area as $k => $row) {
                                        $sorted_comparables[] = $row['sale_price_divided_square_footage'];
                                    }
                                    array_multisort($sorted_comparables, SORT_DESC, $comparables_within_living_area);
                                }

                                if(count($comparables_within_living_area) > 0) {

                                   // $final_comparables = array_slice($comparables_within_living_area, 0, 5, true);
                                    $withinTwenty = array();
                                    //percentage has been changed from 20% to 10%
                                    
                                    $startRange = (config('constants.getTenPercent')*$subject_property_details['sub_ass_div_sf']);
            
                                    $endRange = ( ((100-$subject_property_details['differential_value'])/100)*$subject_property_details['sub_ass_div_sf']);
            

                                    foreach ($comparables_within_living_area as $key => $value) {
                                       
                                        if( $startRange <= $value['sale_price_divided_square_footage'] && $endRange >= $value['sale_price_divided_square_footage']) {
                                            $withinTwenty[] = $value;
                                        }
                                    }

                                    //Assessending order changed sale_price_divided_square_footage to distance_from_subject (05 Jan 2018), Revert this change distance_from_subject to sale_price_divided_square_footage (08 Jan 2018)
                                    if(count($withinTwenty)>0){
                                        $sorted_comparables = array();
                                        foreach ($withinTwenty as $k => $row) {
                                            $sorted_comparables[] = $row['sale_price_divided_square_footage'];
                                        }
                                        array_multisort($sorted_comparables, SORT_ASC, $withinTwenty);
                                    }

                                    $final_comparables = array_slice($withinTwenty, 0, 5, true);

                                    // If all top 5 lowest Sale Price /SF >= Assessment / SF => No appeal, else if only 1 Sale Price/SF < Assessment/SF then => No appeal
                                    if(isset($final_comparables) && count($final_comparables) > config('constants.minCompsForAppeal')) {
                                        $sumOfAmount = 0;
                                        foreach ($final_comparables as $keyLess => $valueLess) {
                                            $sumOfAmount = ($sumOfAmount+$valueLess['sale_price_divided_square_footage']);

                                        }
                                        
                                        $appealAmount = (($sumOfAmount/count($final_comparables))*trim($subject_property_details['square_footage']));
                                        $subject_property_details['appeal_amount'] = $appealAmount;
                                    } else { 

                                        $subject_property_details['appeal_amount'] = "";
                                        $subject_property_details['no_appeal_recommendation'] = 1;
                                        $subject_property_details['no_appeal_message'] = "count is less then 3  => No appeal, else if only 1 or 2 sales having Adjusted Sale Price/SF < Differential_value *Assessment/SF then => No appeal";  
                                    }

                                }

                            }
                        }                        
                    }
                    

                    $comparables_response['comparables']['all_comparables'] = $comparable_values;

                    if ($subject_property_details['no_appeal_recommendation'] == 1 ) {
                        
                    } else {                        
                        // tax saving code here
                        
                        $difference = ($subject_property_details['current_asse_value'] - $subject_property_details['appeal_amount']);
                        $tax_rate = ($subject_property_details['real_tax_amount']/$subject_property_details['total_assessed_value_amount']);
                        $tax_saving = ($tax_rate*$difference);
                        $subject_property_details['tax_saving'] = $tax_saving; 
                        if ($subject_property_details['case_1'] == 0) {
                            if($tax_saving < config('constants.minimumTaxSavings')){
                                $subject_property_details['no_appeal_recommendation'] = 1;
                                $subject_property_details['no_appeal_message'] = "Tax saving < minimum amount of tax saving, hence no appeal."; 
                            } else {
                                // CASE 2 Step 2 applied here

                                $subject_property_details['no_appeal_recommendation'] = 0;
                                $step2_response = self::getAppealCase2Step2TestLink($comparable_values, $subject_property_details);
                                if(count($step2_response['step2_final_comparables']) > 0){
                                    //get step 1 avarage 
                                    $sumAvg = 0;
                                    if(!empty($final_comparables)){
                                        foreach ($final_comparables as $k1 => $val1) {
                                            $sumAvg = ($sumAvg + $val1['sale_price_divided_square_footage']);
                                        }
                                    }
                                    $step1Avg = ($sumAvg/count($final_comparables));

                                    // If average of 5 closest sales is higher than sale price/SF then no appeal.
                                    if($step2_response['com_average'] > $step1Avg){
                                        $subject_property_details['no_appeal_recommendation'] = 1;
                                        $subject_property_details['no_appeal_message'] = "average of 5 closest sales is higher than sale price/SF then no appeal."; 
                                        $final_comparables = array();
                                    } else {
                                        $subject_property_details['no_appeal_recommendation'] = 0;
                                        $subject_property_details['appeal_amount'] = ($step2_response['com_average']*$subject_property_details['square_footage']);
                                        $difference = ($subject_property_details['current_asse_value'] - $subject_property_details['appeal_amount']);
                                        $tax_rate = ($subject_property_details['real_tax_amount']/$subject_property_details['total_assessed_value_amount']);
                                        $tax_saving = ($tax_rate*$difference);
                                        $subject_property_details['tax_saving'] = $tax_saving;

                                        $final_comparables = $step2_response['step2_final_comparables'];
                                    }
                                } else {
                                    $subject_property_details['no_appeal_recommendation'] = 1;
                                    $final_comparables = array();
                                    $subject_property_details['no_appeal_message'] = "Doesn't get 5 closest Sale Price/SF up to 0.3 miles from the subject.";
                                }
                                

                                $comparables_response['step2_response'] = $step2_response;
                            }
                        }
                    }

                    if ($subject_property_details['no_appeal_recommendation'] == 1 ) {
                        $final_comparables = array_slice($comparable_values, 0, 5, true);
                    }
                    $comparables_response['subject_property'] = $subject_property_details;
                    $comparables_response['success'] = '1';
                    $comparables_response['comparables'] = $final_comparables;
                    $comparables_response['api_result'] = serialize($result);
                    //Adjustment end here
                }else{
                   //echo 'No comparables exist';
                }
                
            }
            return $comparables_response;
        }
        catch (GuzzleException $e) {
            //For handling exception
            $result = ['exception_message' => $e->getMessage()];
            return view('errors.error', $result);
        }
        
    }
    
    
    
    
    
    /**
      * Return get Assessment Review page.
      * @param   
      * @return Response
    **/
    public function getAssessmentReview($view_status=0)
    {
        try { 
            $data = array();
            if ($view_status != null && !empty($view_status) && $view_status != '0' && $view_status != '1'){
                
                $token_details = Helper::getSearchDetailsWithToken($view_status);
                if(!empty($token_details) ) {
                    if( $token_details->active_page == '3' && !empty($token_details->token) ) {
                        $data['search_details'] = $token_details;
                        $data['search_comparables'] = $search_comps_details = SearchComparable::where('user_searches_id', $token_details->user_search_id)->where('lookup_id', null)->get();
                        $lookup_type = Helper::getLookupTypeIdFromName(config('constants.constants.lookup_type.address'));
                        $where_condition['lookup_type_id'] = $lookup_type[0]->lookup_type_id;
                        $address_type = Helper::getLookupIdFromName(config('constants.constants.lookup.address.search_address'), $where_condition);
                        
                        $system_object_type_id = Helper::toGenerateCommunicationObjectTypes('pf_lookups', 'name', 'address', $token_details->system_user_id);
                        
                        $data['search_address_details'] = $search_address_details = PfAddress::where('system_object_type_id', $system_object_type_id)->where('ref_object_id', $token_details->user_search_id)->where('address_type', $address_type[0]->lookup_id)->first();
                        
                        $address_county = $search_address_details->county;
                              
                        $data['county_details'] = County::where('county_id', $address_county)->first();
                        $active = 'assessment_review';


                        $comparablesList = SubjectCompsDetail::where(array('ref_object_id' => $token_details->user_search_id, 'system_object_type_id' => '3'))->where('end_date',null)->get();
                        $avgValue = 0;
                        if (count($comparablesList)>0) {
                            $sumOfSal_devived_SF = 0;
                            foreach ($comparablesList as $key => $subComparable) {
                                $subjectCompsDetail['comparables'][$key] = $subComparable;
                                
                                $comDetails = SearchComparable::select('*')->where('subject_comps_detail_id', $subComparable->subject_comps_id)->where('end_date',null)->first();
                                $sumOfSal_devived_SF = ($sumOfSal_devived_SF + $comDetails->sale_price_divided_sf);
                                $com_address = PfAddress::select('mobile_number','address_line_1','address_line_2','address_line_3','city','postal_code','state','county')->where(array('ref_object_id' => $comDetails->search_comparable_id, 'system_object_type_id'=> '3', 'address_type' => '1', 'end_date' => null))->first();

                                $state_name = State::getStateName($com_address->state);

                                $data['com_address'][$key] = $com_address->address_line_1.', '.$com_address->city.', '.$state_name.', '.$com_address->postal_code;
                            }
                            $avgValue = ($sumOfSal_devived_SF/count($comparablesList));
                        }
                        $data['avgValue'] = $avgValue;

                        //echo "<pre>"; print_r($data); die;
                        //redirect to case 1 and case 2
                        if(isset($data['search_details']) && !empty($data['search_details'])){
                            if($data['search_details']->case_1 == 1 && $data['search_details']->no_appeal_recommendation == 1){
                                return view('customer.assessment_case1_with_no_appeal',compact('active', 'data'));
                            }elseif ($data['search_details']->case_1 == 1 && $data['search_details']->no_appeal_recommendation == 0 && $data['search_details']->apply_case_1 == 1) {
                                return view('customer.assessment_review',compact('active', 'data'));
                            }elseif ($data['search_details']->case_1 == 0 && $data['search_details']->no_appeal_recommendation == 1) {
                                return view('customer.assessment_case2_with_no_appeal',compact('active', 'data'));
                            }elseif ($data['search_details']->case_1 == 0 && $data['search_details']->no_appeal_recommendation == 0) {
                                return view('customer.assessment_case2_with_appeal',compact('active', 'data'));
                            }else{
                                return redirect('/');
                            }
                        }
                    } else {
                        return redirect('/');
                    }
                } else {
                    return redirect('/invalid-token');
                }
            } else {
                return redirect('/');
            }
          
        }
        catch (\Exception $e) 
        {
            $result = ['exception_message' => $e->getMessage()];
            return view('errors.error', $result);
        }
        
    }
    
    
    /**
      * Return get Add User Address Form.
      * @param   
      * @return Response
    **/
    public function postCheckValidAddress($api_values)
    {
        try {   
            /*
            $street = $address['street'];
            $city = $address['city'];
            $state = $address['state'];
            $zip_code = $address['zip_code'];
            $response_valid_address = [];
            */
            $client = new Client();
            
            /*
            $input_xml = '<?xml version="1.0" encoding="UTF-8"?>
                <!DOCTYPE REQUEST_GROUP SYSTEM "C2DRequestv2.0.dtd">
                <REQUEST_GROUP
                    MISMOVersionID="2.1">

                    <REQUEST
                        LoginAccountIdentifier="MCINTOSHSTAGE1"
                        LoginAccountPassword="welcome1234">

                        <REQUESTDATA>
                            <PROPERTY_INFORMATION_REQUEST>
                                <_CONNECT2DATA_PRODUCT
                                    _DetailedSubjectReport="Y"
                                    _DetailedComparableReport="Y"
                                    _IncludeSearchCriteriaIndicator="Y"
                                    _IncludePDFIndicator="N"/>

                                 <_PROPERTY_CRITERIA
                                    _StreetAddress="'.$street.'"
                                    _City="'.$city.'"
                                    _State="'.$state.'"
                                    _PostalCode="'.$zip_code.'"/>

                                 <_SEARCH_CRITERIA>
                                    <_SUBJECT_SEARCH />
                                    <_COMPARABLE_SEARCH
                                        _DistanceFromSubjectNumber = "5"
                                        _SaleDateFromDate = ""
                                        _SaleDateToDate = ""
                                        _SalePriceFromAmount = ""
                                        _SalePriceToAmount = ""
                                        _LivingAreaFromNumber = "5000"
                                        _LivingAreaToNumber = "15000"
                                        _PoolOptionType = "PropertiesWithAndWithoutPools"
                                        _LastSaleDateFrom="20160101"
                                        _LastSaleDateTo="20161231"
                                        _LivingAreaVariancePercent = ""
                                        _MonthsBackNumber = ""
                                        _BedroomsFromNumber = ""
                                        _BedroomsToNumber = ""
                                        _BathroomsFromNumber = ""
                                        _BathroomsToNumber = ""
                                        _LotSizeFromNumber = ""
                                        _LotSizeToNumber = ""
                                        _CompFarmRecCountOnly = "N"
                                        _IncludeStreetMapIndicator = "N">
                                        <_LAND_USE _ResidentialType = "AllResidentialTypes" />
                                    </_COMPARABLE_SEARCH>
                                </_SEARCH_CRITERIA>

                                <_RESPONSE_CRITERIA
                                    _NumberComparablesType="50" />

                           </PROPERTY_INFORMATION_REQUEST>

                        </REQUESTDATA>

                    </REQUEST>

                </REQUEST_GROUP>';

            $url = 'https://staging.connect2data.com/';
            */
            $xml_request = Helper::corelogicApiXML($api_values);
			
            $response = $client->post($xml_request['url'], ['body' => $xml_request['input_xml']]);
            //echo $response->getBody();

            $result = json_decode(json_encode(simplexml_load_string($response->getBody())), true);
            //echo "<pre>"; print_r($result);exit;            
            if(isset($result['RESPONSE']['RESPONSE_DATA']['PROPERTY_INFORMATION_RESPONSE']['_PROPERTY_INFORMATION']) && count($result['RESPONSE']['RESPONSE_DATA']['PROPERTY_INFORMATION_RESPONSE']['_PROPERTY_INFORMATION']) > 0) {
                //return response()->json(['success'=>'1', 'message' => 'Address Verified Successfully!']);
                $response_valid_address['success'] = '1';
                $response_valid_address['message'] = 'Address Verified Successfully!';
                //$response_valid_address['result_array'] = $result['RESPONSE']['RESPONSE_DATA']['PROPERTY_INFORMATION_RESPONSE']['_PROPERTY_INFORMATION'][0];
                $response_valid_address['result_array'] = isset($result['RESPONSE']['RESPONSE_DATA']['PROPERTY_INFORMATION_RESPONSE']['_PROPERTY_INFORMATION'][0]) ? $result['RESPONSE']['RESPONSE_DATA']['PROPERTY_INFORMATION_RESPONSE']['_PROPERTY_INFORMATION'][0] : $result['RESPONSE']['RESPONSE_DATA']['PROPERTY_INFORMATION_RESPONSE']['_PROPERTY_INFORMATION'];
            }
            else {
                //return response()->json(['success'=>'0', 'message' => 'Please enter a valid address']);
                $response_valid_address['success'] = '0';
                $response_valid_address['message'] = 'Please enter a valid address';
            }
            
            return $response_valid_address;
            
        }
        catch (GuzzleException $e) {
            //For handling exception
            //return response()->json(['success'=>'0', 'message' => $e->getMessage()]);
            $response_valid_address['success'] = '0';
            $response_valid_address['message'] = $e->getMessage();
            
            return $response_valid_address;
        }
        

    }
    

    public function getGenerateSheet()
    {
        try {
            $all_states = State::all();
            $states = [];
            foreach($all_states as $state) {
                $states[$state->state_abbr] = $state->state_abbr;
            }

            $all_counties = County::orderBy('county_name', 'ASC')->whereIn('county_id', [326,1219])->where('end_date',null)->get();
            $counties = [];
            
            foreach($all_counties as $county) {
                $counties[$county->county_id] = $county->county_name;
            }

            return view('customer.generate_sheet',compact('counties', 'states'));
            
        }
        catch (GuzzleException $e) {
            //For handling exception
            $result = ['exception_message' => $e->getMessage()];
            return view('errors.error', $result);
        }

    }

    public function postGenerateSheet(Request $request)
    {
        try {
            $comparables_response = [];
            $comparables_response['subject_property'] = $comparables_response['comparables'] = [];
            
            $current_asse_value = trim($request->total_assessment_value);
            //$address_state = State::find('24');
            
            if($request->state == 'VA' || $request->state == 'DC' || $request->state == 'MD') {
                $state_abbr = $request->state;
            }
            else {
                $address_state = State::find('24');
                $state_abbr = $address_state->state_abbr;
            }
            
            $lookup_type = PfLookupType::where('name', 'Adjustment_'.$state_abbr)->get();
            if(count($lookup_type)) {
                $lookup_type_details = PfLookup::where('lookup_type_id', $lookup_type[0]->lookup_type_id)->get();
            }
            
            $api_values['saleDateFromDate'] = "";
            $api_values['saleDateToDate'] = "";
            if(isset($lookup_type_details) && count($lookup_type_details)) {
                $client = new Client();
                //$headers = ['Content-Type' => 'text/xml; charset=UTF8'];

                $api_values['street'] = $request->street;
                $api_values['city'] = $request->city;
                $api_values['state'] = $request->state;
                $api_values['zipcode'] = $request->postal_code;
                
                $total_assessment_value = $request->total_assessment_value;

                $api_values['DistanceFromSubjectNumber'] = $api_values['LandUse'] = $api_values['LivingAreaVariancePercent'] = $api_values['NumCompsReturned'] = $api_values['MonthsBackNumber'] = '';
                $date_of_value = "";
                if (isset($request->county_id) && $request->county_id != "") {

                    $county_details = County::find($request->county_id);
                    
                    if(count($county_details)>0 && isset($county_details->date_of_value) && $county_details->date_of_value != null){
                        $date_of_value = $county_details->date_of_value;

                        $fromDate = date('Y-m-d', strtotime('-1 year', strtotime($county_details->date_of_value)));
                        $toDate = date('Y-m-d', strtotime('-1 day', strtotime($county_details->date_of_value)));

                        $api_values['saleDateFromDate'] = trim(str_replace('-', '', $fromDate));
                        $api_values['saleDateToDate'] = trim(str_replace('-', '', $toDate));
                    }
                }
                $search_criteria_lookup = PfLookupType::where('name', 'Search_criteria')->get();
                if(count($search_criteria_lookup)) { 
                    $search_criteria_conditions = PfLookup::where('lookup_type_id', $search_criteria_lookup[0]->lookup_type_id)->get();
                    if(count($search_criteria_conditions)) {
                        foreach($search_criteria_conditions as $search_criteria_condition) {
                            if($search_criteria_condition->name == 'DistanceFromSubjectNumber') {
                                $api_values['DistanceFromSubjectNumber'] = $search_criteria_condition->value;
                            }
                            if($search_criteria_condition->name == 'LandUse') {
                                //$LandUse = $search_criteria_condition->value;
                                $api_values['LandUse'] = '<_LAND_USE _SameAsSubjectType = "Yes" />';
                            }
                            if($search_criteria_condition->name == 'LivingAreaVariancePercent') {
                                $api_values['LivingAreaVariancePercent'] = $search_criteria_condition->value;
                            }
                            if($search_criteria_condition->name == 'NumCompsReturned') {
                                $api_values['NumCompsReturned'] = $search_criteria_condition->value;
                            }
                            /*if($search_criteria_condition->name == 'MonthsBackNumber') {
                                $api_values['MonthsBackNumber'] = $search_criteria_condition->value;
                            }*/
                        }
                    }
                }
                $xml_request = Helper::corelogicApiXML($api_values);
                $response = $client->post($xml_request['url'], ['body' => $xml_request['input_xml']]);
                
                $result = json_decode(json_encode(simplexml_load_string($response->getBody())), true);   
                // Subject Property Details            
                if(isset($result['RESPONSE']['RESPONSE_DATA']['PROPERTY_INFORMATION_RESPONSE']['_PROPERTY_INFORMATION']) && count($result['RESPONSE']['RESPONSE_DATA']['PROPERTY_INFORMATION_RESPONSE']['_PROPERTY_INFORMATION']) > 0) {

                    $subject_property_details = [];   
                    $subject_property_details['appeal_amount'] = ""; 
                    $subject_property = isset($result['RESPONSE']['RESPONSE_DATA']['PROPERTY_INFORMATION_RESPONSE']['_PROPERTY_INFORMATION'][0]) ? $result['RESPONSE']['RESPONSE_DATA']['PROPERTY_INFORMATION_RESPONSE']['_PROPERTY_INFORMATION'][0] : $result['RESPONSE']['RESPONSE_DATA']['PROPERTY_INFORMATION_RESPONSE']['_PROPERTY_INFORMATION'];

                    $subject_property_details['deed_type_description'] = $subject_property['PROPERTY']['_PROPERTY_HISTORY']['_SALES_HISTORY']['@attributes']['_DeedTypeDescription'];
                    $subject_property_details['deed_type_damar_code'] = $subject_property['PROPERTY']['_PROPERTY_HISTORY']['_SALES_HISTORY']['@attributes']['_DeedTypeDamarCode'];

                    $subject_sale_price = trim($subject_property['PROPERTY']['_PROPERTY_HISTORY']['_SALES_HISTORY']['@attributes']['_LastSalesPriceAmount']);             
                    $subjectSalePrice = ($subject_sale_price != '') ? $subject_sale_price : '0';
                    $subjectSquareFeet = trim($subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_ROOM_COUNT']['@attributes']['_TotalLivingAreaSquareFeetNumber']);

                    $onePercent = ($subjectSalePrice/$subjectSquareFeet);
                    
                    $afterOnePercent = $onePercent + ($onePercent*config('constants.tier1OutlierPercent')/100) ;
                    $beforeOnePercent = $onePercent - ($onePercent*config('constants.tier1OutlierPercent')/100);

                    $afterTwentyPercent = $onePercent; //$onePercent + ($onePercent*20/100) ;
                    $beforeTwentyPercent = $onePercent - ($onePercent*config('constants.tier1Outlier2Percent')/100);

                    
                    $subject_property_details['subject_salePrice_minus_1_percent'] = $beforeOnePercent;                    
                    $subject_property_details['subject_salePrice_plus_1_percent'] = $afterOnePercent;

                    $subject_property_details['subject_salePrice_minus_twenty_percent'] = $beforeTwentyPercent;                    
                    $subject_property_details['subject_salePrice_plus_twenty_percent'] = $afterTwentyPercent;
                                        
                        
                    $first_diff = "";
                    $second_diff = "";
                    $subject_property_details['case_1'] = 0;
                    $subject_property_details['differential_value'] = 0;
                    $subject_property_details['no_appeal_recommendation'] = 0;
                    $subject_property_details['tax_saving'] = "";


                    $sale_price = trim($subject_property['PROPERTY']['_PROPERTY_HISTORY']['_SALES_HISTORY']['@attributes']['_LastSalesPriceAmount']);
                    $subject_property_details['appeal_amount'] = $sale_price;    
                    $subject_property_details['current_asse_value'] = $current_asse_value;
                   
                    if(isset($date_of_value) && $date_of_value != "" && isset($subject_property['PROPERTY']['_PROPERTY_HISTORY']['_SALES_HISTORY']['@attributes']['_LastSalesDate']) && $subject_property['PROPERTY']['_PROPERTY_HISTORY']['_SALES_HISTORY']['@attributes']['_LastSalesDate'] != ""){
                        $subject_sale_date = date('Y-m-d', strtotime($subject_property['PROPERTY']['_PROPERTY_HISTORY']['_SALES_HISTORY']['@attributes']['_LastSalesDate']));

                        $date_of_val = explode(' ', $date_of_value); 
                        $date1 = date_create($date_of_val[0]);    
                        $date2 = date_create($subject_sale_date);
                        $date_of_value_days = date_diff($date1, $date2);
                        $subject_sale_date_days = date_diff($date2, $date1);

                        $first = date("Y-m-d", strtotime($date_of_val[0].' -1 years -6 months'));
                        $eighteenMonthBack = date_diff(date_create($date_of_val[0]), date_create($first));

                        $second = date("Y-m-d", strtotime($date_of_val[0].' +1 years'));
                        $twelveMonths = date_diff(date_create($date_of_val[0]), date_create($second));

                        if( (($date_of_value_days->days <= $eighteenMonthBack->days) && ($date_of_value_days->days > 0)) || (($subject_sale_date_days->days <= $twelveMonths->days) && ($subject_sale_date_days->days > 0)) ){
                            //case 1 start here
                            /*$sale_price = trim($subject_property['PROPERTY']['_PROPERTY_HISTORY']['_SALES_HISTORY']['@attributes']['_LastSalesPriceAmount']);

                            $subject_property_details['appeal_amount'] = $sale_price;
                            $subject_property_details['current_asse_value'] = $current_asse_value;*/
                            if($sale_price < $current_asse_value){
                                //Current Assessment <= $250,000
                                if ($current_asse_value <= config('constants.caseRangeFrom')){
                                    //Subject Sale Price <= 0.94 * Current Assessment
                                    if ( $sale_price <= (config('constants.getSixPercent') * $current_asse_value) ) {
                                        $subject_property_details['case_1'] = 1;
                                        $subject_property_details['differential_value'] = 6;
                                    } else {
                                        $subject_property_details['no_appeal_recommendation'] = 1;  
                                    }

                                } elseif ( ($current_asse_value > config('constants.caseRangeFrom')) && ($current_asse_value <= config('constants.caseRangeTo')) ) {
                                    //Subject Sale Price <= 0.97 * Current Assessment
                                    if ( $sale_price <= (config('constants.getThreePercent') * $current_asse_value) ) {
                                        $subject_property_details['case_1'] = 1;
                                        $subject_property_details['differential_value'] = 3;
                                    } else {
                                        $subject_property_details['no_appeal_recommendation'] = 1;  
                                    }

                                } elseif ( $current_asse_value > config('constants.caseRangeTo') ) {
                                    //Subject Sale Price  <=  0.98 * Current Assessment
                                    if ( $sale_price <= (config('constants.getTwoPercent') * $current_asse_value) ) {
                                        $subject_property_details['case_1'] = 1;
                                        $subject_property_details['differential_value'] = 2;
                                    } else {
                                        $subject_property_details['no_appeal_recommendation'] = 1;  
                                    }

                                }
                            }else{
                                $subject_property_details['no_appeal_recommendation'] = 1;
                            }
                            //case 1 end here
                        } else {
                            //Current Assessment <= $250,000
                            if ($current_asse_value <= config('constants.caseRangeFrom')){
                                //Subject Sale Price <= 0.94 * Current Assessment
                                $subject_property_details['differential_value'] = 6;
                            } elseif ( ($current_asse_value > config('constants.caseRangeFrom')) && ($current_asse_value <= config('constants.caseRangeTo')) ) {
                                //Subject Sale Price <= 0.97 * Current Assessment
                                $subject_property_details['differential_value'] = 3;
                            } elseif ( $current_asse_value > config('constants.caseRangeTo') ) {
                                //Subject Sale Price  <=  0.98 * Current Assessment
                                $subject_property_details['differential_value'] = 2;
                            }                                    
                        }
                    }
                    //Adjustment start here
                    $subject_property_details['type_of_house'] = $subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_SITE']['_CHARACTERISTICS']['@attributes']['_LandUseDescription'];
                    $subject_property_details['type_of_house'] = ($subject_property_details['type_of_house'] != '') ? $subject_property_details['type_of_house'] : '-';
                    $subject_property_details['sale_price'] = $subject_property['PROPERTY']['_PROPERTY_HISTORY']['_SALES_HISTORY']['@attributes']['_LastSalesPriceAmount'];
                    $subject_property_details['sale_price'] = ($subject_property_details['sale_price'] != '') ? $subject_property_details['sale_price'] : '0';
                    $subject_property_details['date_of_sale'] = ($subject_property['PROPERTY']['_PROPERTY_HISTORY']['_SALES_HISTORY']['@attributes']['_LastSalesDate'] != "")?date('Y-m-d', strtotime($subject_property['PROPERTY']['_PROPERTY_HISTORY']['_SALES_HISTORY']['@attributes']['_LastSalesDate'])):"NA";
                    $subject_property_details['parcel_size'] = $subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_SITE']['_DIMENSIONS']['@attributes']['_LotAreaSquareFeetNumber'];
                    $subject_property_details['parcel_size'] = ($subject_property_details['parcel_size'] != '') ? $subject_property_details['parcel_size'] : '0';
                    $subject_property_details['total_bedrooms'] = $subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_ROOM_COUNT']['@attributes']['_TotalBedroomsCount'];
                    $subject_property_details['total_bedrooms'] = ($subject_property_details['total_bedrooms'] != '') ? $subject_property_details['total_bedrooms'] : '0';
                    $subject_property_details['total_bathrooms'] = $subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_ROOM_COUNT']['@attributes']['_TotalBathsCount'];
                    $subject_property_details['total_bathrooms'] = ($subject_property_details['total_bathrooms'] != '') ? $subject_property_details['total_bathrooms'] : '0';
                    $subject_property_details['total_basement_space'] = $subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_BASEMENT']['@attributes']['_AreaSquareFeet'];
                    $subject_property_details['total_basement_space'] = ($subject_property_details['total_basement_space'] != '') ? $subject_property_details['total_basement_space'] : '0';
                    $subject_property_details['finished_space'] = $subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_BASEMENT']['@attributes']['_FinishedAreaSquareFeet'];
                    $subject_property_details['finished_space'] = ($subject_property_details['finished_space'] != '') ? $subject_property_details['finished_space'] : '0';

                    $subject_property_details['unfinished_space'] = $subject_property_details['total_basement_space'] - $subject_property_details['finished_space'];
                                                    
                    $subject_property_details['square_footage'] = $subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_ROOM_COUNT']['@attributes']['_TotalLivingAreaSquareFeetNumber'];
                    $subject_property_details['square_footage'] = ($subject_property_details['square_footage'] != '') ? $subject_property_details['square_footage'] : '0';
                   
                    $subject_property_details['garage_count'] = "0";
                    if(!empty($subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_PARKING']['@attributes']['_GarageSquareFeetNumber']) && $subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_PARKING']['@attributes']['_GarageSquareFeetNumber'] <= 400){
                        $subject_property_details['garage_count'] = "1";
                    } else {
                        $subject_property_details['garage_count'] = round(trim($subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_PARKING']['@attributes']['_GarageSquareFeetNumber'])/400);
                    }
                    
                    $subject_property_details['carport_exist'] = (!empty($subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_PARKING']['@attributes']['_CarportArea'])) ? '1' : '0';

                    $subject_property_details['pool_exist'] = '0';
                    $subject_property_details['fireplace_exist'] = '0';
                    $subject_property_details['fireplace_count'] = '0';
                    foreach($subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_FEATURES'] as $feature_key => $features) {
                        if($feature_key == '_FIREPLACES') {
                            $subject_property_details['fireplace_exist'] = ($features['@attributes']['_HasFeatureIndicator'] == 'Y') ? '1' : '0';
                            $subject_property_details['fireplace_count'] = ($subject_property_details['fireplace_exist'] == '1') ? $features['@attributes']['_CountNumber'] : '0';
                        }
                        if($feature_key == '_POOL') {
                            $subject_property_details['pool_exist'] = ($features['@attributes']['_HasFeatureIndicator'] == 'Y') ? '1' : '0';
                        }
                    }

                    //Living area variance percent +/- 20% (logic here)
                    $sub_living_area = $subject_property_details['square_footage'];

                    $subject_property_details['sub_ass_div_sf'] =  ($request->total_assessment_value / $sub_living_area);
                    
                    $subject_property_details['living_area_plus_twenty'] =  ((($sub_living_area * config('constants.livingAreaVarianceFilterPercent'))/100)+$sub_living_area);

                    $subject_property_details['living_area_minus_twenty'] = (($sub_living_area-($sub_living_area * config('constants.livingAreaVarianceFilterPercent'))/100));


                    if(isset($result['RESPONSE']['RESPONSE_DATA']['PROPERTY_INFORMATION_RESPONSE']['_PROPERTY_INFORMATION'][1])) {           
                        // Comparables data
                        $comparables_count = $result['RESPONSE']['RESPONSE_DATA']['PROPERTY_INFORMATION_RESPONSE']['_PROPERTY_INFORMATION'][1]['@attributes']['_TotalComparableRecordCount'];

                        $comparables_result = [];
                        for($i=1; $i<=$comparables_count; $i++) {
                            $current_comparable = $result['RESPONSE']['RESPONSE_DATA']['PROPERTY_INFORMATION_RESPONSE']['_PROPERTY_INFORMATION'][1]['_DATA_PROVIDER_COMPARABLE_SALES'][$i];

                            if($current_comparable['PROPERTY']['_PROPERTY_HISTORY']['_SALES_HISTORY']['@attributes']['_LastSalesPriceAmount'] != '') {
                                
                                
                                $comparables_result[$i]['comparable_number'] = $i;
                                
                                $comparables_result[$i]['deed_type_description'] = $current_comparable['PROPERTY']['_PROPERTY_HISTORY']['_SALES_HISTORY']['@attributes']['_DeedTypeDescription'];
                                $comparables_result[$i]['deed_type_damar_code'] = $current_comparable['PROPERTY']['_PROPERTY_HISTORY']['_SALES_HISTORY']['@attributes']['_DeedTypeDamarCode'];
                                
                                //$comparables_result[$i]['comparable_address'] = "-";
                                $comparables_result[$i]['comparable_address_street'] = (!empty($current_comparable['PROPERTY']['@attributes']['_StreetAddress'])) ? $current_comparable['PROPERTY']['@attributes']['_StreetAddress'] : '';
                                $comparables_result[$i]['comparable_address_city'] = (!empty($current_comparable['PROPERTY']['@attributes']['_City'])) ? $current_comparable['PROPERTY']['@attributes']['_City'] : '';
                                $comparables_result[$i]['comparable_address_state'] = (!empty($current_comparable['PROPERTY']['@attributes']['_State'])) ? $current_comparable['PROPERTY']['@attributes']['_State'] : '';
                                $comparables_result[$i]['comparable_address_county'] = (!empty($current_comparable['PROPERTY']['@attributes']['_County'])) ? $current_comparable['PROPERTY']['@attributes']['_County'] : '';
                                $comparables_result[$i]['comparable_address_zipcode'] = (!empty($current_comparable['PROPERTY']['@attributes']['_PostalCode'])) ? $current_comparable['PROPERTY']['@attributes']['_PostalCode'] : '';
                                
                                $comparables_result[$i]['comparable_type_of_house'] = $current_comparable['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_SITE']['_CHARACTERISTICS']['@attributes']['_LandUseDescription'];
                                $comparables_result[$i]['comparable_square_footage'] = $current_comparable['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_ROOM_COUNT']['@attributes']['_TotalLivingAreaSquareFeetNumber'];

                                $comparables_result[$i]['distance_from_subject'] = $current_comparable['@attributes']['_DistanceFromSubjectNumber'];
                                $comparables_result[$i]['sale_price'] = $current_comparable['PROPERTY']['_PROPERTY_HISTORY']['_SALES_HISTORY']['@attributes']['_LastSalesPriceAmount'];
                                $comparables_result[$i]['sale_price'] = $comparables_result[$i]['comparable_sale_price'] = ($comparables_result[$i]['sale_price'] != '') ? $comparables_result[$i]['sale_price'] : '0';
                                $comparables_result[$i]['date_of_sale'] = date('Y-m-d', strtotime($current_comparable['PROPERTY']['_PROPERTY_HISTORY']['_SALES_HISTORY']['@attributes']['_LastSalesDate']));
                                $comparables_result[$i]['parcel_size'] = $current_comparable['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_SITE']['_DIMENSIONS']['@attributes']['_LotAreaSquareFeetNumber'];
                                $comparables_result[$i]['parcel_size'] = $comparables_result[$i]['comparable_parcel_size'] = ($comparables_result[$i]['parcel_size'] != '') ? $comparables_result[$i]['parcel_size'] : '0';
                                $comparables_result[$i]['total_bedrooms'] = $current_comparable['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_ROOM_COUNT']['@attributes']['_TotalBedroomsCount'];
                                $comparables_result[$i]['total_bedrooms'] = $comparables_result[$i]['comparable_total_bedrooms'] = ($comparables_result[$i]['total_bedrooms'] != '') ? $comparables_result[$i]['total_bedrooms'] : '0';
                                $comparables_result[$i]['total_bathrooms'] = $current_comparable['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_ROOM_COUNT']['@attributes']['_TotalBathsCount'];
                                $comparables_result[$i]['total_bathrooms'] = $comparables_result[$i]['comparable_total_bathrooms'] = ($comparables_result[$i]['total_bathrooms'] != '') ? $comparables_result[$i]['total_bathrooms'] : '0';
                                
                                $comparables_result[$i]['total_basement_space'] = $current_comparable['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_BASEMENT']['@attributes']['_AreaSquareFeet'];
                                $comparables_result[$i]['total_basement_space'] = ($comparables_result[$i]['total_basement_space'] != '') ? $comparables_result[$i]['total_basement_space'] : '0';
                                $comparables_result[$i]['finished_space'] = $current_comparable['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_BASEMENT']['@attributes']['_FinishedAreaSquareFeet'];
                                $comparables_result[$i]['finished_space'] = $comparables_result[$i]['comparable_finished_space'] = ($comparables_result[$i]['finished_space'] != '') ? $comparables_result[$i]['finished_space'] : '0';

                                $comparables_result[$i]['unfinished_space'] = $comparables_result[$i]['comparable_unfinished_space'] = $comparables_result[$i]['total_basement_space'] - $comparables_result[$i]['finished_space'];
                                
                                if(!empty($current_comparable['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_PARKING']['@attributes']['_GarageTotalCarCount'])) {
                                    $comparables_result[$i]['garage_count'] = $comparables_result[$i]['comparable_garage_count'] = 0;
                                }
                                else {                                
                                    $garage_area_total = trim($current_comparable['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_PARKING']['@attributes']['_GarageSquareFeetNumber']);
                                    if(!empty($garage_area_total) && $garage_area_total < 400&& $garage_area_total >= 1) {
                                        $comparables_result[$i]['garage_count'] = $comparables_result[$i]['comparable_garage_count'] = '1';
                                    }
                                    else {
                                        $comparables_result[$i]['garage_count'] = $comparables_result[$i]['comparable_garage_count'] = round(($garage_area_total / 400));
                                    }
                                }
                                
                                $comparables_result[$i]['carport_exist'] = $comparables_result[$i]['comparable_carport_exist'] = (!empty($current_comparable['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_PARKING']['@attributes']['_CarportArea'])) ? '1' : '0';

                                $comparables_result[$i]['porch_deck_exist'] = $comparables_result[$i]['comparable_porch_deck_exist'] = '0';
                                $comparables_result[$i]['patio_exist'] = $comparables_result[$i]['comparable_patio_exist'] = '0';
                                foreach($current_comparable['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_XTRA_FEATURES'] as $extra_features) {
                                    if(isset($extra_features['@attributes'])) {
                                        if(((strpos($extra_features['@attributes']['_Xtra_Features_Code'], 'porch') !== false) || (strpos($extra_features['@attributes']['_Xtra_Features_Code'], 'deck') !== false)) && $comparables_result[$i]['porch_deck_exist'] == '0') {
                                            $comparables_result[$i]['porch_deck_exist'] = $comparables_result[$i]['comparable_porch_deck_exist'] = '1';
                                        }
                                        if(strpos($extra_features['@attributes']['_Xtra_Features_Code'], 'patio') !== false && $comparables_result[$i]['patio_exist'] == '0') {
                                            $comparables_result[$i]['patio_exist'] = $comparables_result[$i]['comparable_patio_exist'] = '1';
                                        }
                                    }                    
                                }

                                $comparables_result[$i]['pool_exist'] = $comparables_result[$i]['comparable_pool_exist'] = '0';
                                $comparables_result[$i]['fireplace_exist'] = $comparables_result[$i]['comparable_fireplace_exist'] = '0';
                                $comparables_result[$i]['fireplace_count'] = $comparables_result[$i]['comparable_fireplace_count'] = '0';
                                foreach($current_comparable['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_FEATURES'] as $feature_key => $features) {
                                    if($feature_key == '_FIREPLACES') {
                                        $comparables_result[$i]['fireplace_exist'] = $comparables_result[$i]['comparable_fireplace_exist'] = ($features['@attributes']['_HasFeatureIndicator'] == 'Y') ? '1' : '0';
                                        $comparables_result[$i]['fireplace_count'] = $comparables_result[$i]['comparable_fireplace_count'] = ($comparables_result[$i]['fireplace_exist'] == '1') ? $features['@attributes']['_CountNumber'] : '0';
                                    }
                                    if($feature_key == '_POOL') {
                                        $comparables_result[$i]['pool_exist'] = $comparables_result[$i]['comparable_pool_exist'] = ($features['@attributes']['_HasFeatureIndicator'] == 'Y') ? '1' : '0';
                                    }
                                    $comparables_result[$i]['comparable_garage_values'] = $current_comparable['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_PARKING']['@attributes']['_GarageSquareFeetNumber'];
                                }  
                                $comparables_result[$i]['is_one_percent'] = "No";                     
                            }

                        }

                        // store comparables detail starts here
                        $lookup_details = [];
                        foreach($lookup_type_details as $lookup_type_detail) {
                            $lookup_details[$lookup_type_detail->name]['value'] = $lookup_type_detail->value;
                            $lookup_details[$lookup_type_detail->name]['value1'] = $lookup_type_detail->value1;
                            $lookup_details[$lookup_type_detail->name]['value2'] = $lookup_type_detail->value2;
                        }

                        $comparable_values = [];
                        $adjustment_values = [];
                        $final_comparables = [];
                        $j=0;

                        foreach($comparables_result as $comparable) {
                            $comparable_values[$j] = $comparable;
                                                        
                            if($comparable['sale_price'] < 750000) {
                                $value = 'value';
                            }
                            else if($comparable['sale_price'] >= 750000 && $comparable['sale_price'] < 1500000) {
                                $value = 'value1';
                            }
                            else if($comparable['sale_price'] >= 1500000) {
                                $value = 'value2';
                            }

                            $comparable_values[$j]['date_of_sale'] = $comparable_values[$j]['square_footage'] = $comparable_values[$j]['parcel_size'] = $comparable_values[$j]['total_bedrooms'] = 0;
                            $comparable_values[$j]['total_bathrooms'] = $comparable_values[$j]['finished_space'] = $comparable_values[$j]['unfinished_space'] = 0;
                            $comparable_values[$j]['garage'] = $comparable_values[$j]['carport'] = $comparable_values[$j]['pool'] = $comparable_values[$j]['fireplace'] = 0;

                            $comparable_values[$j]['distance_from_subject'] = $comparable['distance_from_subject'];
                            
                            $comparable_values[$j]['comparable_date_of_sale'] = date('Y-m-d', strtotime($comparable['date_of_sale']));
                           
                            if($lookup_details['above_grade_sq_footage_percent'][$value] != '0') {
                                $square_footage_lookup_type = 'above_grade_sq_footage_percent';
                            }
                            else if($lookup_details['above_grade_sq_footage_amount'][$value] != '0') {
                                $square_footage_lookup_type = 'above_grade_sq_footage_amount';
                            }

                            if(substr($lookup_details[$square_footage_lookup_type][$value], -1) == '%') {
                                $comparable_values[$j]['square_footage'] = round((($subject_property_details['square_footage'] - $comparable['comparable_square_footage']) * rtrim($lookup_details[$square_footage_lookup_type][$value],"%")) / 100, 2);
                                $comparable_values[$j]['formula_square_footage'] = "round(((".$subject_property_details['square_footage']." - ".$comparable['comparable_square_footage'].") * ".rtrim($lookup_details[$square_footage_lookup_type][$value],"%").") / 100, 2)";
                            }
                            else if(substr($lookup_details[$square_footage_lookup_type][$value], -1) == '$') {
                                $comparable_values[$j]['square_footage'] = round(($subject_property_details['square_footage'] - $comparable['comparable_square_footage']) * rtrim($lookup_details[$square_footage_lookup_type][$value],"$"), 2);
                                $comparable_values[$j]['formula_square_footage'] = "round(((".$subject_property_details['square_footage']." - ".$comparable['comparable_square_footage'].") * ".rtrim($lookup_details[$square_footage_lookup_type][$value],"$")."), 2)";
                            }
                            else {
                                $comparable_values[$j]['formula_square_footage'] = "-";
                            }
                            
                            $comparable_values[$j]['parcel_percent'] = $percent_amount_compared = abs(round((($subject_property_details['parcel_size'] - $comparable_values[$j]['comparable_parcel_size']) / $subject_property_details['parcel_size']) * 100, 0));

                            if($percent_amount_compared >= 50) {
                                if($comparable_values[$j]['comparable_parcel_size'] > $subject_property_details['parcel_size']) { 
                                    if(substr($lookup_details['parcel_comp_greater_than_subject'][$value], -1) == '%') {
                                        $comparable_values[$j]['parcel_size'] = round((($subject_property_details['parcel_size'] - $comparable['comparable_parcel_size']) * rtrim($lookup_details['parcel_comp_greater_than_subject'][$value],"%")) / 100, 2);
                                        $comparable_values[$j]['formula_parcel_size'] = "round(((".$subject_property_details['parcel_size']." - ".$comparable['comparable_parcel_size'].") * ".rtrim($lookup_details['parcel_comp_greater_than_subject'][$value],"%").") / 100, 2)";
                                    }
                                    else if(substr($lookup_details['parcel_comp_greater_than_subject'][$value], -1) == '$') {
                                        $comparable_values[$j]['parcel_size'] = round(($subject_property_details['parcel_size'] - $comparable['comparable_parcel_size']) * rtrim($lookup_details['parcel_comp_greater_than_subject'][$value],"$"), 2);
                                        $comparable_values[$j]['formula_parcel_size'] = "round(((".$subject_property_details['parcel_size']." - ".$comparable['comparable_parcel_size'].") * ".rtrim($lookup_details['parcel_comp_greater_than_subject'][$value],"$").") / 100, 2)";
                                    }
                                    else {
                                        $comparable_values[$j]['formula_parcel_size'] = "-";
                                    }
                                }
                                else if($comparable_values[$j]['comparable_parcel_size'] < $subject_property_details['parcel_size']) { 
                                    if(substr($lookup_details['parcel_comp_less_than_subject'][$value], -1) == '%') {
                                        $comparable_values[$j]['parcel_size'] = round((($subject_property_details['parcel_size'] - $comparable['comparable_parcel_size']) * rtrim($lookup_details['parcel_comp_less_than_subject'][$value],"%")) / 100, 2);
                                        $comparable_values[$j]['formula_parcel_size'] = "round(((".$subject_property_details['parcel_size']." - ".$comparable['comparable_parcel_size'].") * ".rtrim($lookup_details['parcel_comp_less_than_subject'][$value],"%").") / 100, 2)";
                                    }
                                    else if(substr($lookup_details['parcel_comp_less_than_subject'][$value], -1) == '$') { 
                                        $comparable_values[$j]['parcel_size'] = round(($subject_property_details['parcel_size'] - $comparable['comparable_parcel_size']) * rtrim($lookup_details['parcel_comp_less_than_subject'][$value],"$"), 2);
                                        $comparable_values[$j]['formula_parcel_size'] = "round(((".$subject_property_details['parcel_size']." - ".$comparable['comparable_parcel_size'].") * ".rtrim($lookup_details['parcel_comp_less_than_subject'][$value],"$").") / 100, 2)";
                                    }
                                    else {
                                        $comparable_values[$j]['formula_parcel_size'] = "-";
                                    }
                                }
                                else {
                                    $comparable_values[$j]['parcel_size'] = '0';
                                    $comparable_values[$j]['formula_parcel_size'] = "-";
                                }
                            }
                            else {
                                $comparable_values[$j]['parcel_size'] = '0';
                                $comparable_values[$j]['formula_parcel_size'] = "-";
                            }                            


                            if($lookup_details['above_grade_bedrooms_percent'][$value] != '0') {
                                $bedrooms_lookup_type = 'above_grade_bedrooms_percent';
                            }
                            else if($lookup_details['above_grade_bedrooms_amount'][$value] != '0') {
                                $bedrooms_lookup_type = 'above_grade_bedrooms_amount';
                            }

                            if(substr($lookup_details[$bedrooms_lookup_type][$value], -1) == '%') {
                                $comparable_values[$j]['total_bedrooms'] = round((($subject_property_details['total_bedrooms'] - $comparable['total_bedrooms']) * rtrim($lookup_details[$bedrooms_lookup_type][$value],"%")) / 100, 2);
                                $comparable_values[$j]['formula_total_bedrooms'] = "round(((".$subject_property_details['total_bedrooms']." - ".$comparable['total_bedrooms'].") * ".rtrim($lookup_details[$bedrooms_lookup_type][$value],"%").") / 100, 2)";
                            }
                            else if(substr($lookup_details[$bedrooms_lookup_type][$value], -1) == '$') {
                                $comparable_values[$j]['total_bedrooms'] = round(($subject_property_details['total_bedrooms'] - $comparable['total_bedrooms']) * rtrim($lookup_details[$bedrooms_lookup_type][$value],"$"), 2);
                                $comparable_values[$j]['formula_total_bedrooms'] = "round(((".$subject_property_details['total_bedrooms']." - ".$comparable['total_bedrooms'].") * ".rtrim($lookup_details[$bedrooms_lookup_type][$value],"$").") / 100, 2)";
                            }
                            else {
                                $comparable_values[$j]['formula_total_bedrooms'] = "-";
                            }

                            if($lookup_details['above_grade_bathrooms_percent'][$value] != '0') {
                                $bathrooms_lookup_type = 'above_grade_bathrooms_percent';
                            }
                            else if($lookup_details['above_grade_bathrooms_amount'][$value] != '0') {
                                $bathrooms_lookup_type = 'above_grade_bathrooms_amount';
                            }

                            if(substr($lookup_details[$bathrooms_lookup_type][$value], -1) == '%') {
                                $comparable_values[$j]['total_bathrooms'] = round((($subject_property_details['total_bathrooms'] - $comparable['total_bathrooms']) * rtrim($lookup_details[$bathrooms_lookup_type][$value],"%")) / 100, 2);
                                $comparable_values[$j]['formula_total_bathrooms'] = "round(((".$subject_property_details['total_bathrooms']." - ".$comparable['total_bathrooms'].") * ".rtrim($lookup_details[$bathrooms_lookup_type][$value],"%").") / 100, 2)";
                            }
                            else if(substr($lookup_details[$bathrooms_lookup_type][$value], -1) == '$') {
                                $comparable_values[$j]['total_bathrooms'] = round(($subject_property_details['total_bathrooms'] - $comparable['total_bathrooms']) * rtrim($lookup_details[$bathrooms_lookup_type][$value],"$"), 2);
                                $comparable_values[$j]['formula_total_bathrooms'] = "round(((".$subject_property_details['total_bathrooms']." - ".$comparable['total_bathrooms'].") * ".rtrim($lookup_details[$bathrooms_lookup_type][$value],"$").") / 100, 2)";
                            }
                            else {
                                $comparable_values[$j]['formula_total_bathrooms'] = "-";
                            }

                            // finished_space and unfinished_space adjustment has been commented due to client requirement - 13 Dec 2017
                            if(substr($lookup_details['below_grade_finished_space_amount'][$value], -1) == '%') {
                                $comparable_values[$j]['finished_space'] = 0;
                                $comparable_values[$j]['formula_finished_space'] = "-";
                                //$comparable_values[$j]['finished_space'] = round((($subject_property_details['finished_space'] - $comparable['finished_space']) * rtrim($lookup_details['below_grade_finished_space_amount'][$value],"%")) / 100, 2);
                                //$comparable_values[$j]['formula_finished_space'] = $comparable_values[$j]['formula_finished_space'] = "round(((".$subject_property_details['finished_space']." - ".$comparable['finished_space'].") * ".rtrim($lookup_details['below_grade_finished_space_amount'][$value],"%").") / 100, 2)";
                            }
                            else if(substr($lookup_details['below_grade_finished_space_amount'][$value], -1) == '$') {
                                $comparable_values[$j]['finished_space'] = 0;
                                $comparable_values[$j]['formula_finished_space'] = "-";

                                //$comparable_values[$j]['finished_space'] = round(($subject_property_details['finished_space'] - $comparable['finished_space']) * rtrim($lookup_details['below_grade_finished_space_amount'][$value],"$"), 2);
                                //$comparable_values[$j]['formula_finished_space'] = "round(((".$subject_property_details['finished_space']." - ".$comparable['finished_space'].") * ".rtrim($lookup_details['below_grade_finished_space_amount'][$value],"$").") / 100, 2)";
                            }
                            else {
                                $comparable_values[$j]['finished_space'] = 0;
                                $comparable_values[$j]['formula_finished_space'] = "-";
                            }

                            if(substr($lookup_details['below_grade_unfinished_space_amount'][$value], -1) == '%') {
                                $comparable_values[$j]['unfinished_space'] = 0;
                                $comparable_values[$j]['formula_unfinished_space'] = "-";

                                //$comparable_values[$j]['unfinished_space'] = (($subject_property_details['unfinished_space'] - $comparable['unfinished_space']) * rtrim($lookup_details['below_grade_unfinished_space_amount'][$value],"%")) / 100;
                                //$comparable_values[$j]['formula_unfinished_space'] = "round(((".$subject_property_details['unfinished_space']." - ".$comparable['unfinished_space'].") * ".rtrim($lookup_details['below_grade_unfinished_space_amount'][$value],"%").") / 100, 2)";
                            }
                            else if(substr($lookup_details['below_grade_unfinished_space_amount'][$value], -1) == '$') {
                                $comparable_values[$j]['unfinished_space'] = 0;
                                $comparable_values[$j]['formula_unfinished_space'] = "-";

                                //$comparable_values[$j]['unfinished_space'] = round(($subject_property_details['unfinished_space'] - $comparable['unfinished_space']) * rtrim($lookup_details['below_grade_unfinished_space_amount'][$value],"$"), 2);
                                //$comparable_values[$j]['formula_unfinished_space'] = "round(((".$subject_property_details['unfinished_space']." - ".$comparable['unfinished_space'].") * ".rtrim($lookup_details['below_grade_unfinished_space_amount'][$value],"$").") / 100, 2)";
                            }
                            else { 
                                $comparable_values[$j]['unfinished_space'] = 0;
                                $comparable_values[$j]['formula_unfinished_space'] = "-";
                            }
                           
                            if(substr($lookup_details['garage'][$value], -1) == '$') {
                                $comparable_values[$j]['garage'] = round(($subject_property_details['garage_count'] - $comparable['garage_count']) * rtrim($lookup_details['garage'][$value],"$"), 2);
                                $comparable_values[$j]['formula_garage'] = "round(((".$subject_property_details['garage_count']." - ".$comparable['garage_count'].") * ".rtrim($lookup_details['garage'][$value],"$").") / 100, 2)";
                            }
                            else {
                                $comparable_values[$j]['formula_garage'] = "-";
                            }                            
                            
                            if(substr($lookup_details['included_carport'][$value], -1) == '$') {
                                $comparable_values[$j]['carport'] = round(($subject_property_details['carport_exist'] - $comparable['carport_exist']) * rtrim($lookup_details['included_carport'][$value],"$"), 2);
                                $comparable_values[$j]['formula_carport'] = "round(((".$subject_property_details['carport_exist']." - ".$comparable['carport_exist'].") * ".rtrim($lookup_details['included_carport'][$value],"$").") / 100, 2)";
                            }
                            else {
                                $comparable_values[$j]['formula_carport'] = "-";
                            }                            
                            
                            if(substr($lookup_details['swimming_pool'][$value], -1) == '$') {
                                $comparable_values[$j]['pool'] = round(($subject_property_details['pool_exist'] - $comparable['pool_exist']) * rtrim($lookup_details['swimming_pool'][$value],"$"), 2);
                                $comparable_values[$j]['formula_pool'] = "round(((".$subject_property_details['pool_exist']." - ".$comparable['pool_exist'].") * ".rtrim($lookup_details['swimming_pool'][$value],"$").") / 100, 2)";
                            }
                            else {
                                $comparable_values[$j]['formula_pool'] = "-";
                            }
                            
                            if(substr($lookup_details['fireplace'][$value], -1) == '$') {
                                $comparable_values[$j]['fireplace'] = round(($subject_property_details['fireplace_count'] - $comparable['fireplace_count']) * rtrim($lookup_details['fireplace'][$value],"$"), 2);
                                $comparable_values[$j]['formula_fireplace'] = "round(((".$subject_property_details['fireplace_count']." - ".$comparable['fireplace_count'].") * ".rtrim($lookup_details['fireplace'][$value],"$").") / 100, 2)";
                            }
                            else {
                                $comparable_values[$j]['formula_fireplace'] = "-";
                            }

                            $total_adjustments = $comparable_values[$j]['date_of_sale'] + $comparable_values[$j]['square_footage'] + $comparable_values[$j]['parcel_size'] + $comparable_values[$j]['total_bedrooms'] +$comparable_values[$j]['total_bathrooms'] + $comparable_values[$j]['finished_space'] + $comparable_values[$j]['unfinished_space'] + $comparable_values[$j]['garage'] + $comparable_values[$j]['carport'] + $comparable_values[$j]['pool'] + $comparable_values[$j]['fireplace'];
                            
                            $adjustment_values[] = $comparable_values[$j]['total_adjustments'] = $total_adjustments;
                            $distance_of_comparables[] = $comparable['distance_from_subject'];
                            
                            $comparable_values[$j]['price_after_adjustment'] = $comparable['sale_price'] + $total_adjustments;


                            $x = $comparable_values[$j]['price_after_adjustment'];
                            $y = $comparable_values[$j]['comparable_square_footage'];
                            if($x > 0 && $y > 0){
                                $compa_adjus_sale = ($x/$y);
                            }else{
                                $compa_adjus_sale = 0;
                            }

                            $comparable_values[$j]['is_one_percent'] = "No";
                            $comparable_values[$j]['is_twenty_percent'] = "No";

                            if($compa_adjus_sale > $beforeOnePercent && $compa_adjus_sale < $afterOnePercent) {
                                $comparable_values[$j]['is_one_percent'] = "Yes";
                            }

                            if($compa_adjus_sale > $beforeTwentyPercent && $compa_adjus_sale < $afterTwentyPercent) {
                                $comparable_values[$j]['is_twenty_percent'] = "Yes";
                            }

                            // comparable living Area within 20% of subject living area 
                            $plusTwenty = $subject_property_details['living_area_plus_twenty'];
                            $minusTwenty = $subject_property_details['living_area_minus_twenty'];

                            $comparable_values[$j]['living_area_plus_minus_twenty'] = 0;
                            if($comparable_values[$j]['comparable_square_footage'] < $plusTwenty && $comparable_values[$j]['comparable_square_footage'] > $minusTwenty){
                                $comparable_values[$j]['living_area_plus_minus_twenty'] = 1;
                            }
                            
                            //$comparable_values[$j]['comparable_garage_values']
                            $comparable_values[$j]['garage_count'] = 0;
                            $comparable_values[$j]['garage'] = 0;
                            if(!empty($comparable_values[$j]['comparable_garage_values']) && trim($comparable_values[$j]['comparable_garage_values']) <= 400){
                                $comparable_values[$j]['garage_count'] = "1";

                                $comparable_values[$j]['formula_garage'] = "round(( ".$comparable_values[$j]['comparable_garage_values']." / 400 ))";

                                $comparable_values[$j]['garage'] = $comparable_values[$j]['comparable_garage_values'];

                            } else {
                                $comparable_values[$j]['garage_count'] = round(trim($comparable_values[$j]['comparable_garage_values'])/400);

                                $comparable_values[$j]['formula_garage'] = "round(( 0 / 400 ))";

                                $comparable_values[$j]['garage'] = "-";
                            }

                            $comparable_values[$j]['sale_price_divided_square_footage'] = ($comparable_values[$j]['price_after_adjustment']/$comparable_values[$j]['comparable_square_footage']);

                            $j++;
                        }

                        // comparables selection process starts here
                        if(count($comparable_values)>0){
                            
                            $comparables_within_one_percent = array();
                            $comparables_within_twenty_percent = array();
                            $comparables_without_one_percent = array();
                            if ($subject_property_details['case_1'] == 1) {
                                //default selection applied based on distance

                                //Sort the results obtained by Sale price/SF (smallest to largest)
                                $afterSorting = array();
                                $distanceArrayKeys = array();
                                foreach ($comparable_values as $key => $row) {
                                    if(array_key_exists($row['distance_from_subject'], $distanceArrayKeys)) {
                                        $distanceArrayKeys[$row['distance_from_subject']][] = $row;
                                    } else {
                                        $distanceArrayKeys[$row['distance_from_subject']][] = $row;
                                    }
                                }

                                foreach ($distanceArrayKeys as $key => $distanceArray) {
                                    $sorted_comparables = array();
                                    foreach ($distanceArray as $k => $row) {
                                        $sorted_comparables[$k] = $row['sale_price_divided_square_footage'];
                                    }
                                    array_multisort($sorted_comparables, SORT_ASC, $distanceArray);
                                    $afterSorting[] = $distanceArray;
                                }
                                // refreshed array after sorting of SalePrice/SF
                                $comparable_values = array();
                                foreach ($afterSorting as $ky => $val) {
                                    foreach ($val as $y => $v ) {
                                        $comparable_values[] = $v;
                                    }
                                }

                                if($subject_property_details['sale_price'] > config('constants.excludeLivingAreaAboveAmount') ){
                                    $comparables_within_living_area = $comparable_values;
                                } else {

                                    //Living area variance percent +/- 20%
                                    foreach($comparable_values as $key => $comparable_value) {
                                            
                                        if($comparable_value['living_area_plus_minus_twenty'] === 1) {
                                            $comparables_within_living_area[] = $comparable_value;
                                        }                               
                                    }
                                }

                                //Tier 1 Selection
                                //Select top 5 sales that are within 1% of the sale price/sf of the subject property 
                                foreach($comparables_within_living_area as $key => $comparable_value) {
                                        
                                    if($comparable_value['is_one_percent'] === "Yes") {
                                        $comparables_within_one_percent[] = $comparable_value;
                                    } elseif($comparable_value['is_twenty_percent'] === "Yes") {
                                        $comparables_within_twenty_percent[] = $comparable_value;
                                    } else {
                                        $comparables_without_one_percent[] = $comparable_value;
                                    }
                                }
                                
                                //If (number of comparable 1% Sales >= 3 and <= 5)
                                if(count($comparables_within_one_percent) >= 3 && count($comparables_within_one_percent) <= 5) {
                                    $final_comparables = $comparables_within_one_percent;

                                } else {
                                    //count($comparables_within_one_percent)
                                    
                                    //Take the 5 lowest Sale Price/SF 
                                    if(count($comparables_within_twenty_percent)>0){
                                        $sorted_comparables = array();
                                        foreach ($comparables_within_twenty_percent as $k => $row) {
                                            $sorted_comparables[] = $row['sale_price_divided_square_footage'];
                                        }
                                        array_multisort($sorted_comparables, SORT_DESC, $comparables_within_twenty_percent);
                                    }
                                   
                                    $comparables = array_merge($comparables_within_one_percent, $comparables_within_twenty_percent);

                                    
                                    if(count($comparables) >= 3) {
                                        $final_comparables = array_slice($comparables, 0, 5, true);
                                    } else {
                                        // Tier 2 Start here
                                        $withinTier2 = array();
                                        $outsideTier2 = array();
                                        if(count($comparables) < 5 ) {
                                            foreach ($comparables_without_one_percent as $kk => $vv) {
                                                if( ($vv['price_after_adjustment']/$vv['comparable_square_footage']) <=  (100 - $subject_property_details['differential_value'])/100 * $subject_property_details['current_asse_value']/$subject_property_details['square_footage'] ) {
                                                    $withinTier2[] = $vv;
                                                } else {
                                                    $outsideTier2[] = $vv;
                                                }
                                            }
                                            
                                            $tier2_comparables = array_slice($withinTier2, 0, (5 - count($comparables)), true);

                                            $comparables_after_tier2 = array_merge($comparables, $tier2_comparables);
                                            
                                            if(count($comparables_after_tier2) >= 3) {
                                                $final_comparables = array_slice($comparables_after_tier2, 0, 5, true);
                                            } else {
                                                //echo "Tier 3 start here"; 
                                                if( count($comparables_after_tier2) < 5 && count($outsideTier2) > 0) {
                                                    //Comparable Adjusted Sale Price/SF closest to Subject Sale Price/SF

                                                    $sub_sale_price_sf = ($subject_property_details['sale_price']/$subject_property_details['square_footage']);
                                                    
                                                    foreach($outsideTier2 as $k => $item) {
                                                        $com_adj_sal_price_sf = ($item['price_after_adjustment'] / $item['comparable_square_footage']);

                                                        $diff[abs($com_adj_sal_price_sf - $sub_sale_price_sf)] = $item;
                                                    }
                                                    ksort($diff, SORT_NUMERIC); 

                                                    $comparables_after_tier3 = array_merge($comparables_after_tier2, $diff);

                                                    $final_comparables = array_slice($comparables_after_tier3, 0, 5, true);
                                                } else {
                                                    $final_comparables = array_slice($comparables_after_tier2, 0, 5, true);
                                                }
                                            }
                                        }       
                                    }  
                                }
                                if(count($final_comparables)>0){
                                    $sorted_comparables = array();
                                    foreach ($final_comparables as $kData => $rowData) {
                                        $sorted_comparables[] = $rowData['sale_price_divided_square_footage'];
                                    }
                                    array_multisort($sorted_comparables, SORT_DESC, $final_comparables);
                                } 
                            } else {
                                // case 2 selection criterias        
                                //default selection applied based on distance

                                //Sort the results obtained by Sale price/SF (smallest to largest)
                                $afterSorting = array();
                                $distanceArrayKeys = array();
                                foreach ($comparable_values as $key => $row) {
                                    if(array_key_exists($row['distance_from_subject'], $distanceArrayKeys)) {
                                        $distanceArrayKeys[$row['distance_from_subject']][] = $row;
                                    } else {
                                        $distanceArrayKeys[$row['distance_from_subject']][] = $row;
                                    }
                                }

                                foreach ($distanceArrayKeys as $key => $distanceArray) {
                                    $sorted_comparables = array();
                                    foreach ($distanceArray as $k => $row) {
                                        $sorted_comparables[$k] = $row['sale_price_divided_square_footage'];
                                    }
                                    array_multisort($sorted_comparables, SORT_ASC, $distanceArray);
                                    $afterSorting[] = $distanceArray;
                                }
                                // refreshed array after sorting of SalePrice/SF
                                $comparable_values = array();
                                foreach ($afterSorting as $ky => $val) {
                                    foreach ($val as $y => $v ) {
                                        $comparable_values[] = $v;
                                    }
                                }

                                if($subject_property_details['sale_price'] > config('constants.excludeLivingAreaAboveAmount') ){
                                    $comparables_within_living_area = $comparable_values;
                                } else {
                                    //Living area variance percent +/- 20%
                                    foreach($comparable_values as $key => $comparable_value) {
                                            
                                        if($comparable_value['living_area_plus_minus_twenty'] === 1) {
                                            $comparables_within_living_area[] = $comparable_value;
                                        }                               
                                    }
                                }

                                //Take the 5 lowest Sale Price/SF 

                                if(count($comparables_within_living_area)>0){
                                    $sorted_comparables = array();
                                    foreach ($comparables_within_living_area as $k => $row) {
                                        $sorted_comparables[] = $row['sale_price_divided_square_footage'];
                                    }
                                    array_multisort($sorted_comparables, SORT_DESC, $comparables_within_living_area);
                                }

                                if(count($comparables_within_living_area) > 0) {
                                    //$final_comparables = array_slice($comparables_within_living_area, 0, 5, true);

                                    $withinTwenty = array();
                                    //percentage has been changed from 20% to 10%
                                    foreach ($comparables_within_living_area as $key => $value) {
                                        if( (config('constants.getTenPercent')*$subject_property_details['sub_ass_div_sf']) <= $value['sale_price_divided_square_footage'] ) {
                                            $withinTwenty[] = $value;
                                        }
                                    }
                                    if(count($withinTwenty)>0){
                                        $sorted_comparables = array();
                                        foreach ($withinTwenty as $k => $row) {
                                            $sorted_comparables[] = $row['sale_price_divided_square_footage'];
                                        }
                                        array_multisort($sorted_comparables, SORT_ASC, $withinTwenty);
                                    }
                                    
                                    $final_comparables = array_slice($withinTwenty, 0, 5, true);
                                    
                                    // If all top 5 lowest Sale Price /SF >= Assessment / SF => No appeal, else if only 1 Sale Price/SF < Assessment/SF then => No appeal
                                    $lowest_comparables = array();
                                    foreach ($final_comparables as $key => $final_com) {
                                        $sub_ass_div_sf = $subject_property_details['sub_ass_div_sf'];
                                        if ($final_com['sale_price_divided_square_footage'] >=  $sub_ass_div_sf) {
                                            $lowest_comparables['greater'][] = $final_com;
                                        } elseif ($final_com['sale_price_divided_square_footage'] < ($subject_property_details['differential_value'] * $sub_ass_div_sf) ) {
                                            $lowest_comparables['midd'][] = $final_com;
                                        } else {
                                            $lowest_comparables['less'][] = $final_com;
                                        }
                                    }

                                    if(isset($lowest_comparables['midd']) && count($lowest_comparables['midd']) > config('constants.minCompsForAppeal')) {
                                        $sumOfAmount = 0;
                                        foreach ($lowest_comparables['midd'] as $keyLess => $valueLess) {
                                            $sumOfAmount = ($sumOfAmount+$valueLess['sale_price_divided_square_footage']);

                                        }
                                        
                                        $appealAmount = (($sumOfAmount/count($lowest_comparables['midd']))*trim($subject_property_details['square_footage']));
                                        $subject_property_details['appeal_amount'] = $appealAmount;
                                    } else { 
                                        $subject_property_details['no_appeal_recommendation'] = 1;
                                    }
                                    
                                }                  
                            }
                        }
                    }
                }
            }

            if (count($final_comparables)==0) {
                $final_comparables = array_slice($comparable_values, 0, 5, true);
            }

            $headers = array(
                "Content-type" => "text/csv",
                "Content-Disposition" => "attachment; filename=file.csv",
                "Pragma" => "no-cache",
                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                "Expires" => "0"
            );
            $subjectAddress = $request->street.", ".$request->city.", ".$request->state.", ".$request->postal_code;

            if (count($final_comparables) > 0 && $subject_property_details['no_appeal_recommendation'] == 1) {
                
                $columns = array('Address', 'Sale Date', 'Sale Price', 'Living Area', 'Sale Price After Adjustment', '# of Bedrooms', '# of Bathrooms', 'Distance from Subject');

                $callback = function() use ($final_comparables, $columns, $subjectAddress, $subject_property_details)
                {
                    $file = fopen('php://output', 'w');
                    fputcsv($file, $columns);

                    fputcsv($file, array(
                        $subjectAddress,
                        $subject_property_details['date_of_sale'],
                        $subject_property_details['sale_price'],
                        $subject_property_details['square_footage'],
                        '-',
                        $subject_property_details['total_bedrooms'],
                        $subject_property_details['total_bathrooms'],
                        '-'
                    ));
                    fputcsv($file, array('No Appeal Recommendation', '-', '-', '-', '-', '-', '-', '-'));

                    foreach($final_comparables as $review) {
                        $comparable_address = "";
                        $comparable_address .= (!empty($review['comparable_address_street'])) ? $review['comparable_address_street'] : '';
                        $comparable_address .= (!empty($review['comparable_address_city'])) ? ', '.$review['comparable_address_city'] : '';
                        $comparable_address .= (!empty($review['comparable_address_state'])) ? ', '.$review['comparable_address_state'] : '';
                        $comparable_address .= (!empty($review['comparable_address_county'])) ? ', '.$review['comparable_address_county'] : '';
                        $comparable_address .= (!empty($review['comparable_address_zipcode'])) ? ', '.$review['comparable_address_zipcode'] : '';

                        fputcsv($file, array($comparable_address, $review['comparable_date_of_sale'], $review['comparable_sale_price'], $review['comparable_square_footage'], $review['price_after_adjustment'], $review['comparable_total_bedrooms'], $review['comparable_total_bathrooms'], $review['distance_from_subject']));
                    }
                    fclose($file);
                };
                
            } else {
                $adjustedSalePriceRange = "";
                $isCase = 0;
                $adj_sal_price_sf = "";
                if(count($subject_property_details) > 0 && isset($subject_property_details['case_1']) && $subject_property_details['case_1'] == 1){

                    $adjustedSalePriceRange = "Adjusted SalePrice/SF +/- 1% range ".$subject_property_details['subject_salePrice_minus_1_percent']." - ".$subject_property_details['subject_salePrice_plus_1_percent']." & Adjusted SalePrice/SF +/- 20% range ".$subject_property_details['subject_salePrice_minus_twenty_percent']." - ".$subject_property_details['subject_salePrice_plus_twenty_percent'];

                    $isCase = 1;
                } elseif(count($subject_property_details) > 0 && isset($subject_property_details['case_1']) && $subject_property_details['case_1'] == 0 && isset($subject_property_details['no_appeal_recommendation']) && $subject_property_details['no_appeal_recommendation'] == 0) {

                    $adjustedSalePriceRange = "Living area variance percent +/- 20% range ".$subject_property_details['living_area_minus_twenty']." - ".$subject_property_details['living_area_plus_twenty'];

                    $isCase = 2;
                    $adj_sal_price_sf = 'Adjusted Sale Price / SF';
                }


                $columns = array('Address', 'Sale Date', 'Sale Price', 'Living Area', 'Sale Price After Adjustment', '# of Bedrooms', '# of Bathrooms', 'Distance from Subject', $adjustedSalePriceRange, $adj_sal_price_sf);

                $callback = function() use ($final_comparables, $columns, $isCase, $subjectAddress, $subject_property_details)
                {
                    $file = fopen('php://output', 'w');
                    fputcsv($file, $columns);
                    $adj = '';
                    if($isCase == 2){
                        $adj = '-';
                    }
                    fputcsv($file, array(
                        $subjectAddress,
                        $subject_property_details['date_of_sale'],
                        $subject_property_details['sale_price'],
                        $subject_property_details['square_footage'],
                        '-',
                        $subject_property_details['total_bedrooms'],
                        $subject_property_details['total_bathrooms'],
                        '-',
                        '-',
                        $adj
                    ));

                    fputcsv($file, array(
                        'Appeal Amount',
                        $subject_property_details['appeal_amount'],
                        '-',
                        '-',
                        '-',
                        '-',
                        '-',
                        '-',
                        '-',
                        $adj
                    ));

                    foreach($final_comparables as $review) {
                        $comparable_address = "";
                        $comparable_address .= (!empty($review['comparable_address_street'])) ? $review['comparable_address_street'] : '';
                        $comparable_address .= (!empty($review['comparable_address_city'])) ? ', '.$review['comparable_address_city'] : '';
                        $comparable_address .= (!empty($review['comparable_address_state'])) ? ', '.$review['comparable_address_state'] : '';
                        $comparable_address .= (!empty($review['comparable_address_county'])) ? ', '.$review['comparable_address_county'] : '';
                        $comparable_address .= (!empty($review['comparable_address_zipcode'])) ? ', '.$review['comparable_address_zipcode'] : '';

                        $sqFoot = "";
                        $adSaleDivSF = "";
                        if($isCase == 1){
                            $sqFoot = $review['sale_price_divided_square_footage'];
                        }elseif ($isCase == 2) {
                            $sqFoot = $review['comparable_square_footage'];
                            $adSaleDivSF = ($review['price_after_adjustment']/$sqFoot);
                        }
                        fputcsv($file, array($comparable_address, $review['comparable_date_of_sale'], $review['comparable_sale_price'], $review['comparable_square_footage'], $review['price_after_adjustment'], $review['comparable_total_bedrooms'], $review['comparable_total_bathrooms'], $review['distance_from_subject'], $sqFoot,  $adSaleDivSF));
                    }
                    fclose($file);
                };
            }
            return Response::stream($callback, 200, $headers);
            
        }
        catch (GuzzleException $e) {
            //For handling exception
            $result = ['exception_message' => $e->getMessage()];
            return view('errors.error', $result);
        }
    }

    public function generatePlainSheet($subject_property_details, $comparable_list){
        try{
            
            
        }
        catch (GuzzleException $e) {
             //For handling exception
            $result = ['exception_message' => $e->getMessage()];
            return view('errors.error', $result);
        }
    }

    public function getTestApi()
    {
        try {
            return view('customer.test_api');
            
        }
        catch (GuzzleException $e) {
            //For handling exception
            $result = ['exception_message' => $e->getMessage()];
            return view('errors.error', $result);
        }

    }
    
    public function postTestApi(Request $request)
    {
        try {
            $client = new Client();
            //$headers = ['Content-Type' => 'text/xml; charset=UTF8'];
            /*
            $street = "4906 Timberline Dr";
            $city = "Austin";
            $state = "TX";
            $zipcode = "78746";
            */
            /*
            $street = "21 Maryland Avenue";
            $city = "Rockville";
            $state = "MD";
            $zipcode = "20850";
            */
            /*
            $street = "2345 Summerhill Drive";
            $city = "Encinitas";
            $state = "CA";
            $zipcode = "92024";
            */
            /*
            $street = "134 Stockbridge Ave";
            $city = "Atherton";
            $state = "CA";
            $zipcode = "94027";
            
            $street = "134 Stockbridge Ave";
            $city = "Atherton";
            $state = "CA";
            $zipcode = "94027";
            */
            // unsuccessfull addresses
            /*
            $street = "55423";
            $city = "Richfield";
            $state = "Minnesota";
            $zipcode = "55423";
                    
            $street = "Autauga County 53";
            $city = "Billingsley";
            $state = "Alabama";
            $zipcode = "36006";
            */  
            
            /*
            $street = "1234 Autauga Academy";
            $city = "Prattville";
            $state = "AL";
            $zipcode = "36067";
            
            $street = "250 west st apt";
            $city = "New York";
            $state = "NY";
            $zipcode = "10013";
            */
            
            
            $api_values['saleDateFromDate'] = "20160101";
            $api_values['saleDateToDate'] = "20161231";
            $api_values['street'] = $request->street;
            $api_values['city'] = $request->city;
            $api_values['state'] = $request->state;
            $api_values['zipcode'] = $request->postal_code;

            $api_values['DistanceFromSubjectNumber'] = $api_values['LandUse'] = $api_values['LivingAreaVariancePercent'] = $api_values['NumCompsReturned'] = $api_values['MonthsBackNumber'] = '';

            $search_criteria_lookup = PfLookupType::where('name', 'Search_criteria')->get();
            if(count($search_criteria_lookup)) { 
                $search_criteria_conditions = PfLookup::where('lookup_type_id', $search_criteria_lookup[0]->lookup_type_id)->get();
                if(count($search_criteria_conditions)) {
                    foreach($search_criteria_conditions as $search_criteria_condition) {
                        if($search_criteria_condition->name == 'DistanceFromSubjectNumber') {
                            $api_values['DistanceFromSubjectNumber'] = $search_criteria_condition->value;
                        }
                        if($search_criteria_condition->name == 'LandUse') {
                            //$LandUse = $search_criteria_condition->value;
                            $api_values['LandUse'] = '<_LAND_USE _SameAsSubjectType = "Yes" />';
                        }
                        if($search_criteria_condition->name == 'LivingAreaVariancePercent') {
                            $api_values['LivingAreaVariancePercent'] = $search_criteria_condition->value;
                        }
                        if($search_criteria_condition->name == 'NumCompsReturned') {
                            $api_values['NumCompsReturned'] = $search_criteria_condition->value;
                        }
                       /* if($search_criteria_condition->name == 'MonthsBackNumber') {
                            $api_values['MonthsBackNumber'] = $search_criteria_condition->value;
                        }*/
                    }
                }
            }
            
            $xml_request = Helper::corelogicApiXMLTest($api_values);
            $response = $client->post($xml_request['url'], ['body' => $xml_request['input_xml']]);
            //echo $response->getBody();

            $result = json_decode(json_encode(simplexml_load_string($response->getBody())), true);
            echo "<pre>"; print_r($result);
            
           /* $subject_property = isset($result['RESPONSE']['RESPONSE_DATA']['PROPERTY_INFORMATION_RESPONSE']['_PROPERTY_INFORMATION'][0]) ? $result['RESPONSE']['RESPONSE_DATA']['PROPERTY_INFORMATION_RESPONSE']['_PROPERTY_INFORMATION'][0] : $result['RESPONSE']['RESPONSE_DATA']['PROPERTY_INFORMATION_RESPONSE']['_PROPERTY_INFORMATION'];

            if(count($subject_property) > 0){
                $headers = array(
                    "Content-type" => "text/csv",
                    "Content-Disposition" => "attachment; filename=file.csv",
                    "Pragma" => "no-cache",
                    "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                    "Expires" => "0"
                );
                
                $columns = array('Subject Property details (using _DetailedSubjectReport)', '');
                $callback = function() use ($subject_property, $columns)
                    {
                        $file = fopen('php://output', 'w');
                        fputcsv($file, $columns);
                        
                        foreach($subject_property['PROPERTY'] as $sub_key => $sub_val) {

                            if(is_array($sub_val)){

                                fputcsv($file, array('', ''));
                                fputcsv($file, array($sub_key, ''));

                                foreach ($sub_val as $key => $value) {
                                    if(is_array($value)){

                                        if($key != "@attributes"){
                                            fputcsv($file, array('', ''));
                                            fputcsv($file, array($key, ''));
                                        }

                                        foreach ($value as $k => $v) {
                                            if(is_array($v)){
                                                if($k != "@attributes"){
                                                    fputcsv($file, array('', ''));
                                                    fputcsv($file, array($k, ''));
                                                }

                                                foreach ($v as $kk => $vv) {
                                                    if(is_array($vv)){
                                                        if($kk != "@attributes"){
                                                            fputcsv($file, array('', ''));
                                                            fputcsv($file, array($kk, ''));
                                                        }

                                                        foreach ($vv as $kkk => $vvv) {
                                                            if(is_array($vvv)){
                                                                if($kkk != "@attributes"){
                                                                    fputcsv($file, array('', ''));
                                                                    fputcsv($file, array($kkk, ''));
                                                                }

                                                                foreach ($vvv as $kkkk => $vvvv) {
                                                                    fputcsv($file, array($kkkk, $vvvv));   
                                                                }
                                                            } else {
                                                                fputcsv($file, array($kkk, $vvv));   
                                                            }
                                                        }
                                                    } else {
                                                        fputcsv($file, array($kk, $vv));   
                                                    }
                                                }
                                            } else {
                                                fputcsv($file, array($k, $v));   
                                            }
                                        }
                                    } else {
                                        fputcsv($file, array($key, $value));   
                                    }
                                }

                            } else {
                                fputcsv($file, array($sub_key, $sub_val));
                            }
                        }
                        fclose($file);
                    };

                return Response::stream($callback, 200, $headers);
            }*/


            
            
            
        }
        catch (GuzzleException $e) {
            //For handling exception
            $result = ['exception_message' => $e->getMessage()];
            return view('errors.error', $result);
        }

    }
    
    
    public function getTestApiComparables()
    {
        try {
            $all_states = State::all();
            $states = [];
            foreach($all_states as $state) {
                $states[$state->state_abbr] = $state->state_abbr;
            }

            $all_counties = County::orderBy('county_name', 'ASC')->whereIn('county_id', [326,1219])->where('end_date',null)->get();
            $counties = [];
            
            foreach($all_counties as $county) {
                $counties[$county->county_id] = $county->county_name;
            }

            return view('customer.test_api_comparables',compact('counties', 'states'));
            
        }
        catch (GuzzleException $e) {
            //For handling exception
            $result = ['exception_message' => $e->getMessage()];
            return view('errors.error', $result);
        }

    }

    public function postStateCounties(Request $request) {
        try {
            $state_abbr = $request->state_abbr;
            $state_details = State::where('state_abbr', $state_abbr)->where('end_date',null)->lists('state_id');
            $all_counties = County::where('state_id', $state_details[0])->where('end_date',null)->get();
            $counties = [];
            foreach($all_counties as $county) {
                $counties[$county->county_id] = $county->county_name;
            }
            return response()->json(['success'=>true, 'counties'=>$counties]);
        }
        catch (\Exception $e) 
        { 
            return response()->json(['success'=>false, 'message' => $e->getMessage()]);
        }
    }
    
    
    public function postTestApiComparables(Request $request)
    {
        try {
            $comparables_response = [];
            $comparables_response['subject_property'] = $comparables_response['comparables'] = [];
            
            $current_asse_value = trim($request->total_assessment_value);
            
            if($request->state == 'VA' || $request->state == 'DC' || $request->state == 'MD') {
                $state_abbr = $request->state;
            } else {
                $address_state = State::find('24');
                $state_abbr = $address_state->state_abbr;
            }
            
            $lookup_type = PfLookupType::where('name', 'Adjustment_'.$state_abbr)->get();
            if(count($lookup_type)) {
                $lookup_type_details = PfLookup::where('lookup_type_id', $lookup_type[0]->lookup_type_id)->get();
            }

            $api_values['saleDateFromDate'] = "";
            $api_values['saleDateToDate'] = "";
            $form_data = array();
            $form_data['street'] = $request->street;
            $form_data['city'] = $request->city;
            $form_data['state'] = $request->state;
            $form_data['postal_code'] = $request->postal_code;
            $form_data['total_assessment_value'] = $request->total_assessment_value;
            $form_data['county_id'] = $request->county_id;
            $comparables_response['form_data'] = $form_data;

            if(isset($lookup_type_details) && count($lookup_type_details)) {
                $client = new Client();
            
                $api_values['street'] = $request->street;
                $api_values['city'] = $request->city;
                $api_values['state'] = $request->state;
                $api_values['zipcode'] = $request->postal_code;
                
                $total_assessment_value = $request->total_assessment_value;

                $api_values['DistanceFromSubjectNumber'] = $api_values['LandUse'] = $api_values['LivingAreaVariancePercent'] = $api_values['NumCompsReturned'] = '';
                $date_of_value = "";
                if (isset($request->county_id) && $request->county_id != "") {

                    $county_details = County::find($request->county_id);
                    
                    if(count($county_details)>0 && isset($county_details->date_of_value) && $county_details->date_of_value != null){
                        $date_of_value = $county_details->date_of_value;

                        $fromDate = date('Y-m-d', strtotime('-1 year', strtotime($county_details->date_of_value)));
                        $toDate = date('Y-m-d', strtotime('-1 day', strtotime($county_details->date_of_value)));

                        $api_values['saleDateFromDate'] = trim(str_replace('-', '', $fromDate));
                        $api_values['saleDateToDate'] = trim(str_replace('-', '', $toDate));
                    }
                }
                $search_criteria_lookup = PfLookupType::where('name', 'Search_criteria')->get();
                if(count($search_criteria_lookup)) { 
                    $search_criteria_conditions = PfLookup::where('lookup_type_id', $search_criteria_lookup[0]->lookup_type_id)->get();
                    if(count($search_criteria_conditions)) {
                        foreach($search_criteria_conditions as $search_criteria_condition) {
                            if($search_criteria_condition->name == 'DistanceFromSubjectNumber') {
                                $api_values['DistanceFromSubjectNumber'] = $search_criteria_condition->value;
                            }
                            if($search_criteria_condition->name == 'LandUse') {
                                $api_values['LandUse'] = '<_LAND_USE _SameAsSubjectType = "Yes" />';
                            }
                            if($search_criteria_condition->name == 'LivingAreaVariancePercent') {
                                $api_values['LivingAreaVariancePercent'] = $search_criteria_condition->value;
                            }
                            if($search_criteria_condition->name == 'NumCompsReturned') {
                                $api_values['NumCompsReturned'] = $search_criteria_condition->value;
                            }
                            /*if($search_criteria_condition->name == 'MonthsBackNumber') {
                                $api_values['MonthsBackNumber'] = $search_criteria_condition->value;
                            }*/
                        }
                    }
                }
                
                $xml_request = Helper::corelogicApiXMLTest($api_values);
                $response = $client->post($xml_request['url'], ['body' => $xml_request['input_xml']]);
                
                $result = json_decode(json_encode(simplexml_load_string($response->getBody())), true);   
                
                // Subject Property Details            
                if(isset($result['RESPONSE']['RESPONSE_DATA']['PROPERTY_INFORMATION_RESPONSE']['_PROPERTY_INFORMATION']) && count($result['RESPONSE']['RESPONSE_DATA']['PROPERTY_INFORMATION_RESPONSE']['_PROPERTY_INFORMATION']) > 0) {

                    $subject_property_details = [];
                    $subject_property_details['appeal_amount'] = "";    

                    $subject_property = isset($result['RESPONSE']['RESPONSE_DATA']['PROPERTY_INFORMATION_RESPONSE']['_PROPERTY_INFORMATION'][0]) ? $result['RESPONSE']['RESPONSE_DATA']['PROPERTY_INFORMATION_RESPONSE']['_PROPERTY_INFORMATION'][0] : $result['RESPONSE']['RESPONSE_DATA']['PROPERTY_INFORMATION_RESPONSE']['_PROPERTY_INFORMATION'];

                    $subject_sale_price = trim($subject_property['PROPERTY']['_PROPERTY_HISTORY']['_SALES_HISTORY']['@attributes']['_LastSalesPriceAmount']);             
                    $subjectSalePrice = ($subject_sale_price != '') ? $subject_sale_price : '0';
                    $subjectSquareFeet = trim($subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_ROOM_COUNT']['@attributes']['_TotalLivingAreaSquareFeetNumber']);

                    $onePercent = ($subjectSalePrice/$subjectSquareFeet);
                    
                    $afterOnePercent = $onePercent + ($onePercent*config('constants.tier1OutlierPercent')/100) ;
                    $beforeOnePercent = $onePercent - ($onePercent*config('constants.tier1OutlierPercent')/100);

                    $afterTwentyPercent = $onePercent; //$onePercent + ($onePercent*20/100) ;
                    $beforeTwentyPercent = $onePercent - ($onePercent*config('constants.tier1Outlier2Percent')/100);

                    
                    $subject_property_details['real_tax_amount'] = $subject_property['PROPERTY']['_PROPERTY_TAX']['@attributes']['_RealEstateTotalTaxAmount'];

                    $subject_property_details['total_assessed_value_amount'] = $subject_property['PROPERTY']['_PROPERTY_TAX']['@attributes']['_TotalAssessedValueAmount'];

                    $subject_property_details['subject_salePrice_minus_1_percent'] = $beforeOnePercent;                    
                    $subject_property_details['subject_salePrice_plus_1_percent'] = $afterOnePercent;
                           
                    $subject_property_details['subject_salePrice_minus_twenty_percent'] = $beforeTwentyPercent;                    
                    $subject_property_details['subject_salePrice_plus_twenty_percent'] = $afterTwentyPercent;
                                        
                        
                    $first_diff = "";
                    $second_diff = "";

                    $subject_property_details['case_1'] = 0;
                    $subject_property_details['apply_case_1'] = 0;
                    $subject_property_details['no_appeal_message'] = "";
                    $subject_property_details['differential_value'] = 0;
                    $subject_property_details['no_appeal_recommendation'] = 0;
                    $subject_property_details['tax_saving'] = "";

                    $sale_price = trim($subject_property['PROPERTY']['_PROPERTY_HISTORY']['_SALES_HISTORY']['@attributes']['_LastSalesPriceAmount']);
                    $subject_property_details['appeal_amount'] = $sale_price;    
                    $subject_property_details['current_asse_value'] = $current_asse_value;
                    


                    //Current Assessment <= $250,000
                    if ($current_asse_value <= config('constants.caseRangeFrom')){
                        //Subject Sale Price <= 0.94 * Current Assessment
                        $subject_property_details['differential_value'] = 6;
                    } elseif ( ($current_asse_value > config('constants.caseRangeFrom')) && ($current_asse_value <= config('constants.caseRangeTo')) ) {
                        //Subject Sale Price <= 0.97 * Current Assessment
                        $subject_property_details['differential_value'] = 3;
                    } elseif ( $current_asse_value > config('constants.caseRangeTo') ) {
                        //Subject Sale Price  <=  0.98 * Current Assessment
                        $subject_property_details['differential_value'] = 2;
                    }

                    if(isset($date_of_value) && $date_of_value != "" && isset($subject_property['PROPERTY']['_PROPERTY_HISTORY']['_SALES_HISTORY']['@attributes']['_LastSalesDate']) && $subject_property['PROPERTY']['_PROPERTY_HISTORY']['_SALES_HISTORY']['@attributes']['_LastSalesDate'] != ""){
                        $subject_sale_date = date('Y-m-d', strtotime($subject_property['PROPERTY']['_PROPERTY_HISTORY']['_SALES_HISTORY']['@attributes']['_LastSalesDate']));
                        
                        $date_of_val = explode(' ', $date_of_value); 
                        $date1 = date_create($date_of_val[0]);    
                        $date2 = date_create($subject_sale_date);
                        $date_of_value_days = date_diff($date1, $date2);
                        $subject_sale_date_days = date_diff($date2, $date1);

                        
                        
                        $getMonthBack = self::getYearMonth(config('constants.recentSaleMonthsBefore'), '-');
                        $getMonthForward = self::getYearMonth(config('constants.recentSaleMonthsAfter'), '+');
                        
                        $first = date("Y-m-d", strtotime($date_of_val[0].' '.$getMonthBack));
                        $eighteenMonthBack = date_diff(date_create($date_of_val[0]), date_create($first));

                        $second = date("Y-m-d", strtotime($date_of_val[0].' '.$getMonthForward));
                        $twelveMonths = date_diff(date_create($date_of_val[0]), date_create($second));
                        
                        if( (($date_of_value_days->days <= $eighteenMonthBack->days) && ($date_of_value_days->days > 0)) || (($subject_sale_date_days->days <= $twelveMonths->days) && ($subject_sale_date_days->days > 0)) ){
                            //case 1 start here
                            $subject_property_details['case_1'] = 1;

                            if($sale_price < $current_asse_value) {
                               
                                $differential_value = ((100-$subject_property_details['differential_value'])/100);

                                if ( $sale_price <= ($differential_value * $current_asse_value) ) {
                                    $subject_property_details['apply_case_1'] = 1;
                                } else {
                                    $subject_property_details['no_appeal_recommendation'] = 1;
                                    $subject_property_details['no_appeal_message'] = "Subject sale price is > Differential_value * Current Assessment hence no appeal";  
                                }
                                
                            }else{
                                $subject_property_details['no_appeal_recommendation'] = 1;
                                $subject_property_details['no_appeal_message'] = "This is a recent sale with subject sale price > current assessment hence there is no appeal";
                            }
                            //case 1 end here
                        } else {
                            // case 2                                    
                        }

                    }
                    //echo "<pre>"; print_r($subject_property_details); die;
                    
                    //Adjustment start here
                    $subject_property_details['type_of_house'] = $subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_SITE']['_CHARACTERISTICS']['@attributes']['_LandUseDescription'];
                    $subject_property_details['type_of_house'] = ($subject_property_details['type_of_house'] != '') ? $subject_property_details['type_of_house'] : '-';
                    $subject_property_details['sale_price'] = $subject_property['PROPERTY']['_PROPERTY_HISTORY']['_SALES_HISTORY']['@attributes']['_LastSalesPriceAmount'];
                    $subject_property_details['sale_price'] = ($subject_property_details['sale_price'] != '') ? $subject_property_details['sale_price'] : '0';
                    $subject_property_details['date_of_sale'] = ($subject_property['PROPERTY']['_PROPERTY_HISTORY']['_SALES_HISTORY']['@attributes']['_LastSalesDate'] != "")?date('Y-m-d', strtotime($subject_property['PROPERTY']['_PROPERTY_HISTORY']['_SALES_HISTORY']['@attributes']['_LastSalesDate'])):"NA";
                    $subject_property_details['parcel_size'] = $subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_SITE']['_DIMENSIONS']['@attributes']['_LotAreaSquareFeetNumber'];
                    $subject_property_details['parcel_size'] = ($subject_property_details['parcel_size'] != '') ? $subject_property_details['parcel_size'] : '0';
                    $subject_property_details['total_bedrooms'] = $subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_ROOM_COUNT']['@attributes']['_TotalBedroomsCount'];
                    $subject_property_details['total_bedrooms'] = ($subject_property_details['total_bedrooms'] != '') ? $subject_property_details['total_bedrooms'] : '0';
                    $subject_property_details['total_bathrooms'] = $subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_ROOM_COUNT']['@attributes']['_TotalBathsCount'];
                    $subject_property_details['total_bathrooms'] = ($subject_property_details['total_bathrooms'] != '') ? $subject_property_details['total_bathrooms'] : '0';
                    $subject_property_details['total_basement_space'] = $subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_BASEMENT']['@attributes']['_AreaSquareFeet'];
                    $subject_property_details['total_basement_space'] = ($subject_property_details['total_basement_space'] != '') ? $subject_property_details['total_basement_space'] : '0';
                    $subject_property_details['finished_space'] = $subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_BASEMENT']['@attributes']['_FinishedAreaSquareFeet'];
                    $subject_property_details['finished_space'] = ($subject_property_details['finished_space'] != '') ? $subject_property_details['finished_space'] : '0';

                    $subject_property_details['unfinished_space'] = $subject_property_details['total_basement_space'] - $subject_property_details['finished_space'];
                                                    
                    $subject_property_details['square_footage'] = $subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_ROOM_COUNT']['@attributes']['_TotalLivingAreaSquareFeetNumber'];
                    $subject_property_details['square_footage'] = ($subject_property_details['square_footage'] != '') ? $subject_property_details['square_footage'] : '0';
                    
                    $subject_property_details['garage_count'] = "0";
                    if(!empty($subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_PARKING']['@attributes']['_GarageSquareFeetNumber']) && $subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_PARKING']['@attributes']['_GarageSquareFeetNumber'] <= 400){
                        $subject_property_details['garage_count'] = "1";
                    } else {
                        $subject_property_details['garage_count'] = round(trim($subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_PARKING']['@attributes']['_GarageSquareFeetNumber'])/400);
                    }
                    
                    $subject_property_details['carport_exist'] = (!empty($subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_PARKING']['@attributes']['_CarportArea'])) ? '1' : '0';

                    $subject_property_details['pool_exist'] = '0';
                    $subject_property_details['fireplace_exist'] = '0';
                    $subject_property_details['fireplace_count'] = '0';
                    foreach($subject_property['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_FEATURES'] as $feature_key => $features) {
                        if($feature_key == '_FIREPLACES') {
                            $subject_property_details['fireplace_exist'] = ($features['@attributes']['_HasFeatureIndicator'] == 'Y') ? '1' : '0';
                            $subject_property_details['fireplace_count'] = ($subject_property_details['fireplace_exist'] == '1') ? $features['@attributes']['_CountNumber'] : '0';
                        }
                        if($feature_key == '_POOL') {
                            $subject_property_details['pool_exist'] = ($features['@attributes']['_HasFeatureIndicator'] == 'Y') ? '1' : '0';
                        }
                    }

                    //Living area variance percent +/- 20% (logic here)
                    $sub_living_area = $subject_property_details['square_footage'];
                    
                    $subject_property_details['sub_ass_div_sf'] =  ($request->total_assessment_value / $sub_living_area);

                    $subject_property_details['living_area_plus_twenty'] =  ((($sub_living_area * config('constants.livingAreaVarianceFilterPercent'))/100)+$sub_living_area);

                    $subject_property_details['living_area_minus_twenty'] = (($sub_living_area-($sub_living_area * config('constants.livingAreaVarianceFilterPercent'))/100));

                    $final_comparables = array();    
                    $comparable_values = array();    

                    //comparables array start here
                    if(isset($result['RESPONSE']['RESPONSE_DATA']['PROPERTY_INFORMATION_RESPONSE']['_PROPERTY_INFORMATION'][1])) {           
                        // Comparables data
                        $comparables_count = $result['RESPONSE']['RESPONSE_DATA']['PROPERTY_INFORMATION_RESPONSE']['_PROPERTY_INFORMATION'][1]['@attributes']['_TotalComparableRecordCount'];

                        $comparables_result = [];
                        for($i=1; $i<=$comparables_count; $i++) {
                            $current_comparable = $result['RESPONSE']['RESPONSE_DATA']['PROPERTY_INFORMATION_RESPONSE']['_PROPERTY_INFORMATION'][1]['_DATA_PROVIDER_COMPARABLE_SALES'][$i];

                            if($current_comparable['PROPERTY']['_PROPERTY_HISTORY']['_SALES_HISTORY']['@attributes']['_LastSalesPriceAmount'] != '') {
                                
                                $comparables_result[$i]['comparable_number'] = $i;
                                
                                
                                //$comparables_result[$i]['comparable_address'] = "-";
                                $comparables_result[$i]['comparable_address_street'] = (!empty($current_comparable['PROPERTY']['@attributes']['_StreetAddress'])) ? $current_comparable['PROPERTY']['@attributes']['_StreetAddress'] : '';
                                $comparables_result[$i]['comparable_address_city'] = (!empty($current_comparable['PROPERTY']['@attributes']['_City'])) ? $current_comparable['PROPERTY']['@attributes']['_City'] : '';
                                $comparables_result[$i]['comparable_address_state'] = (!empty($current_comparable['PROPERTY']['@attributes']['_State'])) ? $current_comparable['PROPERTY']['@attributes']['_State'] : '';
                                $comparables_result[$i]['comparable_address_county'] = (!empty($current_comparable['PROPERTY']['@attributes']['_County'])) ? $current_comparable['PROPERTY']['@attributes']['_County'] : '';
                                $comparables_result[$i]['comparable_address_zipcode'] = (!empty($current_comparable['PROPERTY']['@attributes']['_PostalCode'])) ? $current_comparable['PROPERTY']['@attributes']['_PostalCode'] : '';
                                
                                $comparables_result[$i]['comparable_type_of_house'] = $current_comparable['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_SITE']['_CHARACTERISTICS']['@attributes']['_LandUseDescription'];
                                $comparables_result[$i]['comparable_square_footage'] = $current_comparable['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_ROOM_COUNT']['@attributes']['_TotalLivingAreaSquareFeetNumber'];

                                $comparables_result[$i]['distance_from_subject'] = $current_comparable['@attributes']['_DistanceFromSubjectNumber'];
                                $comparables_result[$i]['sale_price'] = $current_comparable['PROPERTY']['_PROPERTY_HISTORY']['_SALES_HISTORY']['@attributes']['_LastSalesPriceAmount'];
                                $comparables_result[$i]['sale_price'] = $comparables_result[$i]['comparable_sale_price'] = ($comparables_result[$i]['sale_price'] != '') ? $comparables_result[$i]['sale_price'] : '0';
                                $comparables_result[$i]['date_of_sale'] = date('Y-m-d', strtotime($current_comparable['PROPERTY']['_PROPERTY_HISTORY']['_SALES_HISTORY']['@attributes']['_LastSalesDate']));
                                $comparables_result[$i]['parcel_size'] = $current_comparable['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_SITE']['_DIMENSIONS']['@attributes']['_LotAreaSquareFeetNumber'];
                                $comparables_result[$i]['parcel_size'] = $comparables_result[$i]['comparable_parcel_size'] = ($comparables_result[$i]['parcel_size'] != '') ? $comparables_result[$i]['parcel_size'] : '0';
                                $comparables_result[$i]['total_bedrooms'] = $current_comparable['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_ROOM_COUNT']['@attributes']['_TotalBedroomsCount'];
                                $comparables_result[$i]['total_bedrooms'] = $comparables_result[$i]['comparable_total_bedrooms'] = ($comparables_result[$i]['total_bedrooms'] != '') ? $comparables_result[$i]['total_bedrooms'] : '0';
                                $comparables_result[$i]['total_bathrooms'] = $current_comparable['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_ROOM_COUNT']['@attributes']['_TotalBathsCount'];
                                $comparables_result[$i]['total_bathrooms'] = $comparables_result[$i]['comparable_total_bathrooms'] = ($comparables_result[$i]['total_bathrooms'] != '') ? $comparables_result[$i]['total_bathrooms'] : '0';
                                
                                $comparables_result[$i]['total_basement_space'] = $current_comparable['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_BASEMENT']['@attributes']['_AreaSquareFeet'];
                                $comparables_result[$i]['total_basement_space'] = ($comparables_result[$i]['total_basement_space'] != '') ? $comparables_result[$i]['total_basement_space'] : '0';
                                $comparables_result[$i]['finished_space'] = $current_comparable['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_BASEMENT']['@attributes']['_FinishedAreaSquareFeet'];
                                $comparables_result[$i]['finished_space'] = $comparables_result[$i]['comparable_finished_space'] = ($comparables_result[$i]['finished_space'] != '') ? $comparables_result[$i]['finished_space'] : '0';

                                $comparables_result[$i]['unfinished_space'] = $comparables_result[$i]['comparable_unfinished_space'] = $comparables_result[$i]['total_basement_space'] - $comparables_result[$i]['finished_space'];
                                
                                if(!empty($current_comparable['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_PARKING']['@attributes']['_GarageTotalCarCount'])) {
                                    //$comparables_result[$i]['garage_count'] = $comparables_result[$i]['comparable_garage_count'] = $current_comparable['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_PARKING']['@attributes']['_GarageTotalCarCount'];
                                    $comparables_result[$i]['garage_count'] = $comparables_result[$i]['comparable_garage_count'] = 0;
                                }
                                else {                                
                                    //$garage_area_total = $current_comparable['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_PARKING']['@attributes']['_GarageSquareFeetNumber'] + $current_comparable['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_PARKING']['@attributes']['_GarageTwoArea'];
                                    $garage_area_total = trim($current_comparable['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_PARKING']['@attributes']['_GarageSquareFeetNumber']);
                                    if(!empty($garage_area_total) && $garage_area_total < 400&& $garage_area_total >= 1) {
                                        $comparables_result[$i]['garage_count'] = $comparables_result[$i]['comparable_garage_count'] = '1';
                                    }
                                    else {
                                        $comparables_result[$i]['garage_count'] = $comparables_result[$i]['comparable_garage_count'] = round(($garage_area_total / 400));
                                    }
                                }
                                
                                $comparables_result[$i]['carport_exist'] = $comparables_result[$i]['comparable_carport_exist'] = (!empty($current_comparable['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_PARKING']['@attributes']['_CarportArea'])) ? '1' : '0';

                                $comparables_result[$i]['porch_deck_exist'] = $comparables_result[$i]['comparable_porch_deck_exist'] = '0';
                                $comparables_result[$i]['patio_exist'] = $comparables_result[$i]['comparable_patio_exist'] = '0';
                                foreach($current_comparable['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_XTRA_FEATURES'] as $extra_features) {
                                    if(isset($extra_features['@attributes'])) {
                                        if(((strpos($extra_features['@attributes']['_Xtra_Features_Code'], 'porch') !== false) || (strpos($extra_features['@attributes']['_Xtra_Features_Code'], 'deck') !== false)) && $comparables_result[$i]['porch_deck_exist'] == '0') {
                                            $comparables_result[$i]['porch_deck_exist'] = $comparables_result[$i]['comparable_porch_deck_exist'] = '1';
                                        }
                                        if(strpos($extra_features['@attributes']['_Xtra_Features_Code'], 'patio') !== false && $comparables_result[$i]['patio_exist'] == '0') {
                                            $comparables_result[$i]['patio_exist'] = $comparables_result[$i]['comparable_patio_exist'] = '1';
                                        }
                                    }                    
                                }

                                $comparables_result[$i]['pool_exist'] = $comparables_result[$i]['comparable_pool_exist'] = '0';
                                $comparables_result[$i]['fireplace_exist'] = $comparables_result[$i]['comparable_fireplace_exist'] = '0';
                                $comparables_result[$i]['fireplace_count'] = $comparables_result[$i]['comparable_fireplace_count'] = '0';
                                foreach($current_comparable['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_FEATURES'] as $feature_key => $features) {
                                    if($feature_key == '_FIREPLACES') {
                                        $comparables_result[$i]['fireplace_exist'] = $comparables_result[$i]['comparable_fireplace_exist'] = ($features['@attributes']['_HasFeatureIndicator'] == 'Y') ? '1' : '0';
                                        $comparables_result[$i]['fireplace_count'] = $comparables_result[$i]['comparable_fireplace_count'] = ($comparables_result[$i]['fireplace_exist'] == '1') ? $features['@attributes']['_CountNumber'] : '0';
                                    }
                                    if($feature_key == '_POOL') {
                                        $comparables_result[$i]['pool_exist'] = $comparables_result[$i]['comparable_pool_exist'] = ($features['@attributes']['_HasFeatureIndicator'] == 'Y') ? '1' : '0';
                                    }
                                    $comparables_result[$i]['comparable_garage_values'] = $current_comparable['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_PARKING']['@attributes']['_GarageSquareFeetNumber'];
                                }  
                                $comparables_result[$i]['is_one_percent'] = "No";
                                $comparables_result[$i]['is_twenty_percent'] = "No";                     
                            }

                        }

                        // store comparables detail starts here
                        $lookup_details = [];
                        foreach($lookup_type_details as $lookup_type_detail) {
                            $lookup_details[$lookup_type_detail->name]['value'] = $lookup_type_detail->value;
                            $lookup_details[$lookup_type_detail->name]['value1'] = $lookup_type_detail->value1;
                            $lookup_details[$lookup_type_detail->name]['value2'] = $lookup_type_detail->value2;
                        }

                        $comparable_values = [];
                        $adjustment_values = [];
                        $final_comparables = [];
                        $j=0;

                        foreach($comparables_result as $comparable) {
                            $comparable_values[$j] = $comparable;
                                                        
                            if($comparable['sale_price'] < 750000) {
                                $value = 'value';
                            }
                            else if($comparable['sale_price'] >= 750000 && $comparable['sale_price'] < 1500000) {
                                $value = 'value1';
                            }
                            else if($comparable['sale_price'] >= 1500000) {
                                $value = 'value2';
                            }

                            $comparable_values[$j]['date_of_sale'] = $comparable_values[$j]['square_footage'] = $comparable_values[$j]['parcel_size'] = $comparable_values[$j]['total_bedrooms'] = 0;
                            $comparable_values[$j]['total_bathrooms'] = $comparable_values[$j]['finished_space'] = $comparable_values[$j]['unfinished_space'] = 0;
                            $comparable_values[$j]['garage'] = $comparable_values[$j]['carport'] = $comparable_values[$j]['pool'] = $comparable_values[$j]['fireplace'] = 0;

                            $comparable_values[$j]['distance_from_subject'] = $comparable['distance_from_subject'];
                            
                            $comparable_values[$j]['comparable_date_of_sale'] = date('Y-m-d', strtotime($comparable['date_of_sale']));
                           
                            if($lookup_details['above_grade_sq_footage_percent'][$value] != '0') {
                                $square_footage_lookup_type = 'above_grade_sq_footage_percent';
                            }
                            else if($lookup_details['above_grade_sq_footage_amount'][$value] != '0') {
                                $square_footage_lookup_type = 'above_grade_sq_footage_amount';
                            }

                            if(substr($lookup_details[$square_footage_lookup_type][$value], -1) == '%') {
                                $comparable_values[$j]['square_footage'] = round((($subject_property_details['square_footage'] - $comparable['comparable_square_footage']) * rtrim($lookup_details[$square_footage_lookup_type][$value],"%")) / 100, 2);
                                $comparable_values[$j]['formula_square_footage'] = "round(((".$subject_property_details['square_footage']." - ".$comparable['comparable_square_footage'].") * ".rtrim($lookup_details[$square_footage_lookup_type][$value],"%").") / 100, 2)";
                            }
                            else if(substr($lookup_details[$square_footage_lookup_type][$value], -1) == '$') {
                                $comparable_values[$j]['square_footage'] = round(($subject_property_details['square_footage'] - $comparable['comparable_square_footage']) * rtrim($lookup_details[$square_footage_lookup_type][$value],"$"), 2);
                                $comparable_values[$j]['formula_square_footage'] = "round(((".$subject_property_details['square_footage']." - ".$comparable['comparable_square_footage'].") * ".rtrim($lookup_details[$square_footage_lookup_type][$value],"$")."), 2)";
                            }
                            else {
                                $comparable_values[$j]['formula_square_footage'] = "-";
                            }
                            
                            $comparable_values[$j]['parcel_percent'] = $percent_amount_compared = abs(round((($subject_property_details['parcel_size'] - $comparable_values[$j]['comparable_parcel_size']) / $subject_property_details['parcel_size']) * 100, 0));

                            if($percent_amount_compared >= 50) {
                                if($comparable_values[$j]['comparable_parcel_size'] > $subject_property_details['parcel_size']) { 
                                    if(substr($lookup_details['parcel_comp_greater_than_subject'][$value], -1) == '%') {
                                        $comparable_values[$j]['parcel_size'] = round((($subject_property_details['parcel_size'] - $comparable['comparable_parcel_size']) * rtrim($lookup_details['parcel_comp_greater_than_subject'][$value],"%")) / 100, 2);
                                        $comparable_values[$j]['formula_parcel_size'] = "round(((".$subject_property_details['parcel_size']." - ".$comparable['comparable_parcel_size'].") * ".rtrim($lookup_details['parcel_comp_greater_than_subject'][$value],"%").") / 100, 2)";
                                    }
                                    else if(substr($lookup_details['parcel_comp_greater_than_subject'][$value], -1) == '$') {
                                        $comparable_values[$j]['parcel_size'] = round(($subject_property_details['parcel_size'] - $comparable['comparable_parcel_size']) * rtrim($lookup_details['parcel_comp_greater_than_subject'][$value],"$"), 2);
                                        $comparable_values[$j]['formula_parcel_size'] = "round(((".$subject_property_details['parcel_size']." - ".$comparable['comparable_parcel_size'].") * ".rtrim($lookup_details['parcel_comp_greater_than_subject'][$value],"$").") / 100, 2)";
                                    }
                                    else {
                                        $comparable_values[$j]['formula_parcel_size'] = "-";
                                    }
                                }
                                else if($comparable_values[$j]['comparable_parcel_size'] < $subject_property_details['parcel_size']) { 
                                    if(substr($lookup_details['parcel_comp_less_than_subject'][$value], -1) == '%') {
                                        $comparable_values[$j]['parcel_size'] = round((($subject_property_details['parcel_size'] - $comparable['comparable_parcel_size']) * rtrim($lookup_details['parcel_comp_less_than_subject'][$value],"%")) / 100, 2);
                                        $comparable_values[$j]['formula_parcel_size'] = "round(((".$subject_property_details['parcel_size']." - ".$comparable['comparable_parcel_size'].") * ".rtrim($lookup_details['parcel_comp_less_than_subject'][$value],"%").") / 100, 2)";
                                    }
                                    else if(substr($lookup_details['parcel_comp_less_than_subject'][$value], -1) == '$') { 
                                        $comparable_values[$j]['parcel_size'] = round(($subject_property_details['parcel_size'] - $comparable['comparable_parcel_size']) * rtrim($lookup_details['parcel_comp_less_than_subject'][$value],"$"), 2);
                                        $comparable_values[$j]['formula_parcel_size'] = "round(((".$subject_property_details['parcel_size']." - ".$comparable['comparable_parcel_size'].") * ".rtrim($lookup_details['parcel_comp_less_than_subject'][$value],"$").") / 100, 2)";
                                    }
                                    else {
                                        $comparable_values[$j]['formula_parcel_size'] = "-";
                                    }
                                }
                                else {
                                    $comparable_values[$j]['parcel_size'] = '0';
                                    $comparable_values[$j]['formula_parcel_size'] = "-";
                                }
                            }
                            else {
                                $comparable_values[$j]['parcel_size'] = '0';
                                $comparable_values[$j]['formula_parcel_size'] = "-";
                            }                            


                            if($lookup_details['above_grade_bedrooms_percent'][$value] != '0') {
                                $bedrooms_lookup_type = 'above_grade_bedrooms_percent';
                            }
                            else if($lookup_details['above_grade_bedrooms_amount'][$value] != '0') {
                                $bedrooms_lookup_type = 'above_grade_bedrooms_amount';
                            }

                            if(substr($lookup_details[$bedrooms_lookup_type][$value], -1) == '%') {
                                $comparable_values[$j]['total_bedrooms'] = round((($subject_property_details['total_bedrooms'] - $comparable['total_bedrooms']) * rtrim($lookup_details[$bedrooms_lookup_type][$value],"%")) / 100, 2);
                                $comparable_values[$j]['formula_total_bedrooms'] = "round(((".$subject_property_details['total_bedrooms']." - ".$comparable['total_bedrooms'].") * ".rtrim($lookup_details[$bedrooms_lookup_type][$value],"%").") / 100, 2)";
                            }
                            else if(substr($lookup_details[$bedrooms_lookup_type][$value], -1) == '$') {
                                $comparable_values[$j]['total_bedrooms'] = round(($subject_property_details['total_bedrooms'] - $comparable['total_bedrooms']) * rtrim($lookup_details[$bedrooms_lookup_type][$value],"$"), 2);
                                $comparable_values[$j]['formula_total_bedrooms'] = "round(((".$subject_property_details['total_bedrooms']." - ".$comparable['total_bedrooms'].") * ".rtrim($lookup_details[$bedrooms_lookup_type][$value],"$").") / 100, 2)";
                            }
                            else {
                                $comparable_values[$j]['formula_total_bedrooms'] = "-";
                            }

                            if($lookup_details['above_grade_bathrooms_percent'][$value] != '0') {
                                $bathrooms_lookup_type = 'above_grade_bathrooms_percent';
                            }
                            else if($lookup_details['above_grade_bathrooms_amount'][$value] != '0') {
                                $bathrooms_lookup_type = 'above_grade_bathrooms_amount';
                            }

                            if(substr($lookup_details[$bathrooms_lookup_type][$value], -1) == '%') {
                                $comparable_values[$j]['total_bathrooms'] = round((($subject_property_details['total_bathrooms'] - $comparable['total_bathrooms']) * rtrim($lookup_details[$bathrooms_lookup_type][$value],"%")) / 100, 2);
                                $comparable_values[$j]['formula_total_bathrooms'] = "round(((".$subject_property_details['total_bathrooms']." - ".$comparable['total_bathrooms'].") * ".rtrim($lookup_details[$bathrooms_lookup_type][$value],"%").") / 100, 2)";
                            }
                            else if(substr($lookup_details[$bathrooms_lookup_type][$value], -1) == '$') {
                                $comparable_values[$j]['total_bathrooms'] = round(($subject_property_details['total_bathrooms'] - $comparable['total_bathrooms']) * rtrim($lookup_details[$bathrooms_lookup_type][$value],"$"), 2);
                                $comparable_values[$j]['formula_total_bathrooms'] = "round(((".$subject_property_details['total_bathrooms']." - ".$comparable['total_bathrooms'].") * ".rtrim($lookup_details[$bathrooms_lookup_type][$value],"$").") / 100, 2)";
                            }
                            else {
                                $comparable_values[$j]['formula_total_bathrooms'] = "-";
                            }

                            // finished_space & unfinished_space adjustment has been commented due to client requirement - 13 Dec 2017

                            if(substr($lookup_details['below_grade_finished_space_amount'][$value], -1) == '%') {
                                $comparable_values[$j]['finished_space'] = 0;
                                $comparable_values[$j]['formula_finished_space'] = "-";
                            }
                            else if(substr($lookup_details['below_grade_finished_space_amount'][$value], -1) == '$') {
                                $comparable_values[$j]['finished_space'] = 0;
                                $comparable_values[$j]['formula_finished_space'] = "-";
                            }
                            else {
                                $comparable_values[$j]['finished_space'] = 0;
                                $comparable_values[$j]['formula_finished_space'] = "-";
                            }

                            if(substr($lookup_details['below_grade_unfinished_space_amount'][$value], -1) == '%') {
                                $comparable_values[$j]['unfinished_space'] = 0;
                                $comparable_values[$j]['formula_unfinished_space'] = "-";
                            }
                            else if(substr($lookup_details['below_grade_unfinished_space_amount'][$value], -1) == '$') {
                                $comparable_values[$j]['unfinished_space'] = 0;
                                $comparable_values[$j]['formula_unfinished_space'] = "-";
                            }
                            else { 
                                $comparable_values[$j]['unfinished_space'] = 0;
                                $comparable_values[$j]['formula_unfinished_space'] = "-";
                            }
                           
                            if(substr($lookup_details['garage'][$value], -1) == '$') {
                                $comparable_values[$j]['garage_adjusted_value'] = $comparable_values[$j]['garage'] = round(($subject_property_details['garage_count'] - $comparable['garage_count']) * rtrim($lookup_details['garage'][$value],"$"), 2);
                                $comparable_values[$j]['formula_garage'] = "round(((".$subject_property_details['garage_count']." - ".$comparable['garage_count'].") * ".rtrim($lookup_details['garage'][$value],"$").") / 100, 2)";
                            }
                            else {
                                $comparable_values[$j]['formula_garage'] = "-";
                            }                            
                            
                            if(substr($lookup_details['included_carport'][$value], -1) == '$') {
                                $comparable_values[$j]['carport'] = round(($subject_property_details['carport_exist'] - $comparable['carport_exist']) * rtrim($lookup_details['included_carport'][$value],"$"), 2);
                                $comparable_values[$j]['formula_carport'] = "round(((".$subject_property_details['carport_exist']." - ".$comparable['carport_exist'].") * ".rtrim($lookup_details['included_carport'][$value],"$").") / 100, 2)";
                            }
                            else {
                                $comparable_values[$j]['formula_carport'] = "-";
                            }                            
                            
                            if(substr($lookup_details['swimming_pool'][$value], -1) == '$') {
                                $comparable_values[$j]['pool'] = round(($subject_property_details['pool_exist'] - $comparable['pool_exist']) * rtrim($lookup_details['swimming_pool'][$value],"$"), 2);
                                $comparable_values[$j]['formula_pool'] = "round(((".$subject_property_details['pool_exist']." - ".$comparable['pool_exist'].") * ".rtrim($lookup_details['swimming_pool'][$value],"$").") / 100, 2)";
                            }
                            else {
                                $comparable_values[$j]['formula_pool'] = "-";
                            }
                            
                            if(substr($lookup_details['fireplace'][$value], -1) == '$') {
                                $comparable_values[$j]['fireplace'] = round(($subject_property_details['fireplace_count'] - $comparable['fireplace_count']) * rtrim($lookup_details['fireplace'][$value],"$"), 2);
                                $comparable_values[$j]['formula_fireplace'] = "round(((".$subject_property_details['fireplace_count']." - ".$comparable['fireplace_count'].") * ".rtrim($lookup_details['fireplace'][$value],"$").") / 100, 2)";
                            }
                            else {
                                $comparable_values[$j]['formula_fireplace'] = "-";
                            }

                            $total_adjustments = $comparable_values[$j]['date_of_sale'] + $comparable_values[$j]['square_footage'] + $comparable_values[$j]['parcel_size'] + $comparable_values[$j]['total_bedrooms'] +$comparable_values[$j]['total_bathrooms'] + $comparable_values[$j]['finished_space'] + $comparable_values[$j]['unfinished_space'] + $comparable_values[$j]['garage'] + $comparable_values[$j]['carport'] + $comparable_values[$j]['pool'] + $comparable_values[$j]['fireplace'];
                            
                            $adjustment_values[] = $comparable_values[$j]['total_adjustments'] = $total_adjustments;
                            $distance_of_comparables[] = $comparable['distance_from_subject'];
                            
                            $comparable_values[$j]['price_after_adjustment'] = $comparable['sale_price'] + $total_adjustments;


                            $x = $comparable_values[$j]['price_after_adjustment'];
                            $y = $comparable_values[$j]['comparable_square_footage'];
                            if($x > 0 && $y > 0){
                                $compa_adjus_sale = ($x/$y);
                            }else{
                                $compa_adjus_sale = 0;
                            }

                            $comparable_values[$j]['is_one_percent'] = "No";
                            $comparable_values[$j]['is_twenty_percent'] = "No";
                            
                            if($compa_adjus_sale > $beforeOnePercent && $compa_adjus_sale < $afterOnePercent) {
                                $comparable_values[$j]['is_one_percent'] = "Yes";
                            }

                            if($compa_adjus_sale > $beforeTwentyPercent && $compa_adjus_sale < $afterTwentyPercent) {
                                $comparable_values[$j]['is_twenty_percent'] = "Yes";
                            }

                            // comparable living Area within 20% of subject living area 
                            $plusTwenty = $subject_property_details['living_area_plus_twenty'];
                            $minusTwenty = $subject_property_details['living_area_minus_twenty'];

                            $comparable_values[$j]['living_area_plus_minus_twenty'] = 0;
                            if($comparable_values[$j]['comparable_square_footage'] < $plusTwenty && $comparable_values[$j]['comparable_square_footage'] > $minusTwenty){
                                $comparable_values[$j]['living_area_plus_minus_twenty'] = 1;
                            }
                            
                            //$comparable_values[$j]['comparable_garage_values']
                            $comparable_values[$j]['garage_count'] = 0;
                            $comparable_values[$j]['garage'] = 0;
                            if(!empty($comparable_values[$j]['comparable_garage_values']) && trim($comparable_values[$j]['comparable_garage_values']) <= 400){
                                $comparable_values[$j]['garage_count'] = "1";

                                $comparable_values[$j]['formula_garage'] = "round(( ".$comparable_values[$j]['comparable_garage_values']." / 400 ))";

                                $comparable_values[$j]['garage'] = $comparable_values[$j]['comparable_garage_values'];

                            } else {
                                $comparable_values[$j]['garage_count'] = round(trim($comparable_values[$j]['comparable_garage_values'])/400);

                                $comparable_values[$j]['formula_garage'] = "round(( 0 / 400 ))";

                                $comparable_values[$j]['garage'] = "-";
                            }

                            $comparable_values[$j]['sale_price_divided_square_footage'] = ($comparable_values[$j]['price_after_adjustment']/$comparable_values[$j]['comparable_square_footage']);

                            $j++;
                        }

                        // comparables selection process starts here
                       // echo "<pre>"; print_r($subject_property_details); die;
                        if(count($comparable_values)>0){
                            
                            $comparables_within_one_percent = array();
                            $comparables_within_twenty_percent = array();
                            $comparables_without_one_percent = array();

                            if ($subject_property_details['case_1'] == 1 && $subject_property_details['apply_case_1'] == 1 && $subject_property_details['no_appeal_recommendation'] == 0 ) {
                                //default selection applied based on distance

                                //Sort the results obtained by Sale price/SF (smallest to largest)
                                $afterSorting = array();
                                $distanceArrayKeys = array();
                                foreach ($comparable_values as $key => $row) {
                                    if(array_key_exists($row['distance_from_subject'], $distanceArrayKeys)) {
                                        $distanceArrayKeys[$row['distance_from_subject']][] = $row;
                                    } else {
                                        $distanceArrayKeys[$row['distance_from_subject']][] = $row;
                                    }
                                }

                                foreach ($distanceArrayKeys as $key => $distanceArray) {
                                    $sorted_comparables = array();
                                    foreach ($distanceArray as $k => $row) {
                                        $sorted_comparables[$k] = $row['sale_price_divided_square_footage'];
                                    }
                                    array_multisort($sorted_comparables, SORT_ASC, $distanceArray);
                                    $afterSorting[] = $distanceArray;
                                }
                                // refreshed array after sorting of SalePrice/SF
                                $comparable_values = array();
                                foreach ($afterSorting as $ky => $val) {
                                    foreach ($val as $y => $v ) {
                                        $comparable_values[] = $v;
                                    }
                                }

                                if($subject_property_details['sale_price'] > config('constants.excludeLivingAreaAboveAmount') ){
                                    $comparables_within_living_area = $comparable_values;
                                } else {
                                    //Living area variance percent +/- 20%
                                    foreach($comparable_values as $key => $comparable_value) {
                                            
                                        if($comparable_value['living_area_plus_minus_twenty'] === 1) {
                                            $comparables_within_living_area[] = $comparable_value;
                                        }                               
                                    }
                                }

                                //Tier 1 Selection
                                //Select top 5 sales that are within 1% of the sale price/sf of the subject property 
                                foreach($comparables_within_living_area as $key => $comparable_value) {
                                        
                                    if($comparable_value['is_one_percent'] === "Yes") {
                                        $comparables_within_one_percent[] = $comparable_value;
                                    } elseif($comparable_value['is_twenty_percent'] === "Yes") {
                                        $comparables_within_twenty_percent[] = $comparable_value;
                                    } else {
                                        $comparables_without_one_percent[] = $comparable_value;
                                    }
                                }

                                //If (number of comparable 1% Sales >= 3 and <= 5)
                                if(count($comparables_within_one_percent) >= config('constants.minCompsForAppeal') && count($comparables_within_one_percent) <= config('constants.maxCompsForAppeal')) {
                                    $final_comparables = $comparables_within_one_percent;

                                } else {
                                    //count($comparables_within_one_percent)
                                    
                                    //Take the 5 lowest Sale Price/SF 
                                    if(count($comparables_within_twenty_percent)>0){
                                        $sorted_comparables = array();
                                        foreach ($comparables_within_twenty_percent as $k => $row) {
                                            $sorted_comparables[] = $row['sale_price_divided_square_footage'];
                                        }
                                        array_multisort($sorted_comparables, SORT_DESC, $comparables_within_twenty_percent);
                                    }
                                   
                                    $comparables = array_merge($comparables_within_one_percent, $comparables_within_twenty_percent);

                                    
                                    if(count($comparables) >= 1) {
                                        $final_comparables = array_slice($comparables, 0, 5, true);
                                    } else {
                                        // Tier 2 Start here
                                        $withinTier2 = array();
                                        $outsideTier2 = array();
                                        if(count($comparables) < config('constants.maxCompsForAppeal') ) {
                                            foreach ($comparables_without_one_percent as $kk => $vv) {
                                                if( ($vv['price_after_adjustment']/$vv['comparable_square_footage']) <=  (100 - $subject_property_details['differential_value'])/100 * $subject_property_details['current_asse_value']/$subject_property_details['square_footage'] ) {
                                                    $withinTier2[] = $vv;
                                                } else {
                                                    $outsideTier2[] = $vv;
                                                }
                                            }
                                            
                                            $tier2_comparables = array_slice($withinTier2, 0, (config('constants.maxCompsForAppeal') - count($comparables)), true);

                                            $comparables_after_tier2 = array_merge($comparables, $tier2_comparables);
                                            
                                            if(count($comparables_after_tier2) >= config('constants.minCompsForAppeal')) {
                                                $final_comparables = array_slice($comparables_after_tier2, 0, 5, true);
                                            } else {
                                                //echo "Tier 3 start here"; 

                                                if( count($comparables_after_tier2) < config('constants.maxCompsForAppeal') && count($outsideTier2) > 0) {
                                                    //Comparable Adjusted Sale Price/SF closest to Subject Sale Price/SF

                                                    $sub_sale_price_sf = ($subject_property_details['sale_price']/$subject_property_details['square_footage']);
                                                    
                                                    foreach($outsideTier2 as $k => $item) {
                                                        $com_adj_sal_price_sf = ($item['price_after_adjustment'] / $item['comparable_square_footage']);

                                                        $diff[abs($com_adj_sal_price_sf - $sub_sale_price_sf)] = $item;
                                                    }
                                                    ksort($diff, SORT_NUMERIC); 

                                                    $comparables_after_tier3 = array_merge($comparables_after_tier2, $diff);

                                                    $final_comparables = array_slice($comparables_after_tier3, 0, 5, true);
                                                } else {
                                                    $final_comparables = array_slice($comparables_after_tier2, 0, 5, true);
                                                }
                                            }
                                        }       
                                    }
                                    
                                }
                                if(count($final_comparables)>0){
                                    $sorted_comparables = array();
                                    foreach ($final_comparables as $kData => $rowData) {
                                        $sorted_comparables[] = $rowData['sale_price_divided_square_footage'];
                                    }
                                    array_multisort($sorted_comparables, SORT_DESC, $final_comparables);
                                }
                                

                            } elseif($subject_property_details['case_1'] == 0) {
                                // case 2 selection criteria

                                //default selection applied based on distance

                                //Sort the results obtained by Sale price/SF (smallest to largest)
                                $afterSorting = array();
                                $distanceArrayKeys = array();
                                foreach ($comparable_values as $key => $row) {
                                    if(array_key_exists($row['distance_from_subject'], $distanceArrayKeys)) {
                                        $distanceArrayKeys[$row['distance_from_subject']][] = $row;
                                    } else {
                                        $distanceArrayKeys[$row['distance_from_subject']][] = $row;
                                    }
                                }

                                foreach ($distanceArrayKeys as $key => $distanceArray) {
                                    $sorted_comparables = array();
                                    foreach ($distanceArray as $k => $row) {
                                        $sorted_comparables[$k] = $row['sale_price_divided_square_footage'];
                                    }
                                    array_multisort($sorted_comparables, SORT_ASC, $distanceArray);
                                    $afterSorting[] = $distanceArray;
                                }
                                // refreshed array after sorting of SalePrice/SF
                                $comparable_values = array();
                                foreach ($afterSorting as $ky => $val) {
                                    foreach ($val as $y => $v ) {
                                        $comparable_values[] = $v;
                                    }
                                }

                                if($subject_property_details['current_asse_value'] > config('constants.excludeLivingAreaAboveAmount') ){
                                    $comparables_within_living_area = $comparable_values;
                                } else {
                                    //Living area variance percent +/- 20%
                                    foreach($comparable_values as $key => $comparable_value) {
                                            
                                        if($comparable_value['living_area_plus_minus_twenty'] === 1) {
                                            $comparables_within_living_area[] = $comparable_value;
                                        }                               
                                    }
                                }
                                
                                //Take the 5 lowest Sale Price/SF 
                                if(count($comparables_within_living_area)>0){
                                    $sorted_comparables = array();
                                    foreach ($comparables_within_living_area as $k => $row) {
                                        $sorted_comparables[] = $row['sale_price_divided_square_footage'];
                                    }
                                    array_multisort($sorted_comparables, SORT_DESC, $comparables_within_living_area);
                                }

                                if(count($comparables_within_living_area) > 0) {

                                   // $final_comparables = array_slice($comparables_within_living_area, 0, 5, true);
                                    $withinTwenty = array();
                                    //percentage has been changed from 20% to 10%
                                    
                                    $startRange = (config('constants.getTenPercent')*$subject_property_details['sub_ass_div_sf']);
            
                                    $endRange = ( ((100-$subject_property_details['differential_value'])/100)*$subject_property_details['sub_ass_div_sf']);
            

                                    foreach ($comparables_within_living_area as $key => $value) {
                                       
                                        if( $startRange <= $value['sale_price_divided_square_footage'] && $endRange >= $value['sale_price_divided_square_footage']) {
                                            $withinTwenty[] = $value;
                                        }
                                    }

                                    //Assessending order changed sale_price_divided_square_footage to distance_from_subject (05 Jan 2018), Revert this change distance_from_subject to sale_price_divided_square_footage (08 Jan 2018)
                                    if(count($withinTwenty)>0){
                                        $sorted_comparables = array();
                                        foreach ($withinTwenty as $k => $row) {
                                            $sorted_comparables[] = $row['sale_price_divided_square_footage'];
                                        }
                                        array_multisort($sorted_comparables, SORT_ASC, $withinTwenty);
                                    }

                                    $final_comparables = array_slice($withinTwenty, 0, 5, true);

                                    // If all top 5 lowest Sale Price /SF >= Assessment / SF => No appeal, else if only 1 Sale Price/SF < Assessment/SF then => No appeal
                                    if(isset($final_comparables) && count($final_comparables) > config('constants.minCompsForAppeal')) {
                                        $sumOfAmount = 0;
                                        foreach ($final_comparables as $keyLess => $valueLess) {
                                            $sumOfAmount = ($sumOfAmount+$valueLess['sale_price_divided_square_footage']);

                                        }
                                        
                                        $appealAmount = (($sumOfAmount/count($final_comparables))*trim($subject_property_details['square_footage']));
                                        $subject_property_details['appeal_amount'] = $appealAmount;
                                    } else { 

                                        $subject_property_details['appeal_amount'] = "";
                                        $subject_property_details['no_appeal_recommendation'] = 1;
                                        $subject_property_details['no_appeal_message'] = "count is less then 3  => No appeal, else if only 1 or 2 sales having Adjusted Sale Price/SF < Differential_value *Assessment/SF then => No appeal";  
                                    }

                                }

                            }
                        }                        
                    }
                   /*echo "<pre>"; 
                    print_r($subject_property_details); 
                    print_r($final_comparables); 
                    die;

                    if (count($final_comparables)==0) {
                        $final_comparables = array_slice($comparable_values, 0, 5, true);
                        $comparables_response['comparables']['final_comparables'] = $final_comparables;
                    } else {                        
                        $comparables_response['comparables']['final_comparables'] = $final_comparables; 
                    }*/

                    

                    //echo "<pre>"; print_r($subject_property_details); die;
                    if ($subject_property_details['no_appeal_recommendation'] == 1 ) {

                    } else {    					
                        // tax saving code here                        
                        $difference = ($subject_property_details['current_asse_value'] - $subject_property_details['appeal_amount']);
                        $tax_rate = ($subject_property_details['real_tax_amount']/$subject_property_details['total_assessed_value_amount']);
                        $tax_saving = ($tax_rate*$difference);

                        $subject_property_details['tax_saving'] = $tax_saving;
                        if ($subject_property_details['case_1'] == 0) {
                            if($tax_saving < config('constants.minimumTaxSavings')){
                                $subject_property_details['no_appeal_recommendation'] = 1;
                                $subject_property_details['no_appeal_message'] = "Tax saving < minimum amount of tax saving, hence no appeal."; 
                            } else {
                                // CASE 2 Step 2 applied here
                                $subject_property_details['no_appeal_recommendation'] = 0;
                                $step2_response = self::getAppealCase2Step2TestLink($comparable_values, $subject_property_details);
                                if(count($step2_response['step2_final_comparables']) > 0){
                                    //get step 1 avarage 
                                    $sumAvg = 0;
                                    if(!empty($final_comparables)){
                                        foreach ($final_comparables as $k1 => $val1) {
                                            $sumAvg = ($sumAvg + $val1['sale_price_divided_square_footage']);
                                        }
                                    }
                                    $step1Avg = ($sumAvg/count($final_comparables));

                                    // If average of 5 closest sales is higher than sale price/SF then no appeal.
                                    if($step2_response['com_average'] > $step1Avg){
                                        $subject_property_details['no_appeal_recommendation'] = 1;
                                        $subject_property_details['no_appeal_message'] = "average of 5 closest sales is higher than sale price/SF then no appeal."; 
                                        $final_comparables = array();
                                    } else {
                                        $subject_property_details['no_appeal_recommendation'] = 0;
                                        $subject_property_details['appeal_amount'] = ($step2_response['com_average']*$subject_property_details['square_footage']);

                                        //$final_comparables = $step2_response['step2_final_comparables'];
                                    }
                                } else {
                                    $subject_property_details['no_appeal_recommendation'] = 1;
                                    $final_comparables = array();
                                    $subject_property_details['no_appeal_message'] = "Doesn't get 5 closest Sale Price/SF up to 0.3 miles from the subject.";
                                }
                                

                                $comparables_response['step2_response'] = $step2_response;
                            }
                        }
                    }

                    $comparables_response['comparables']['final_comparables'] = $final_comparables; 
                    $comparables_response['comparables']['all_comparables'] = $comparable_values;

                    $comparables_response['subject_property'] = $subject_property_details;
					Session::put('all_comparables',$comparables_response);
 
                    //Adjustment end here
                }else{
                   //echo 'No comparables exist';
                }
                
            }
            //echo "<pre>"; print_r($comparables_response); die;
			
            return view('customer.comparables_result', $comparables_response);
        }
        catch (GuzzleException $e) {
            //For handling exception
            $result = ['exception_message' => $e->getMessage()];
            return view('errors.error', $result);
        }

    }

    /**
    * If appeal recommended based on Step 1, then we applied step 2 
    * @param   
    * @return Response
    **/
    public function getAppealCase2Step2($comparable_values = "", $subject_property_details = ""){
        
        if( count($comparable_values) > 0 && count($subject_property_details) > 0 ){

            // sort by distance_from_subject
            if(count($comparable_values)>0){
                $sorted_comparables = array();
                foreach ($comparable_values as $k => $row) {
                    $sorted_comparables[] = $row['distance_from_subject'];
                }
                array_multisort($sorted_comparables, SORT_ASC, $comparable_values);
            }

            //Eliminate sales outside the 20% living area variance, if Current Assessment < $2,000,000
            $comparables_within_living_area = array();
            if($subject_property_details['current_asse_value'] >= config('constants.excludeLivingAreaAboveAmount') ){
                //Living area variance percent + 40%
                $comparables_within_living_area = $comparable_values;
            } else {
                //Living area variance percent +/- 20%
                foreach($comparable_values as $key => $comparable_value) {
                        
                    if($comparable_value['living_area_plus_minus_twenty'] === 1 && $comparable_value['distance_from_subject'] <= '0.3') {
                        $comparables_within_living_area[] = $comparable_value;
                    }                               
                }
            }

            //Take the 5 closest Sale Price/SF.

            $sub_sale_price_sf = ($subject_property_details['sale_price']/$subject_property_details['square_footage']);
                                                    
            foreach($comparables_within_living_area as $k => $item) {
                $com_adj_sal_price_sf = ($item['price_after_adjustment'] / $item['comparable_square_footage']);

                $diff[abs($com_adj_sal_price_sf - $sub_sale_price_sf)] = $item;
            }
            ksort($diff, SORT_NUMERIC); 

            $final_comparables = array_slice($diff, 0, 5, true);

            //If average of 5 closest sales is higher than assessment/SF then no appeal.
            $comAverage = 0;
            $sumOfSal_devived_SF = 0;
            if( count($final_comparables) > 0 ){
                foreach ($final_comparables as $key => $value) {
                    
                    $sumOfSal_devived_SF = ($sumOfSal_devived_SF + $value['sale_price_divided_square_footage'] );
                }
                $comAverage = ($sumOfSal_devived_SF/count($final_comparables));
            }

        } else {
            //echo 'No comparables exist';
            $final_comparables = array();
            $comAverage = 0;
        }

        $step2_response['step2_final_comparables'] = $final_comparables;
        $step2_response['com_average'] = $comAverage;
        $step2_response['sale_price_divided_square_footage'] = ($subject_property_details['sale_price']/$subject_property_details['square_footage']);
        $step2_response['assessment_divided_square_footage'] = $subject_property_details['sub_ass_div_sf'];

        return $step2_response;
    }
    
     /**
    * If appeal recommended based on Step 1, then we applied step 2 
    * @param   
    * @return Response
    **/
    public function getAppealCase2Step2TestLink($comparable_values = "", $subject_property_details = ""){
        //echo "<pre>"; print_r($subject_property_details); die;
        if( count($comparable_values) > 0 && count($subject_property_details) > 0 ){

            // sort by distance_from_subject
            $sorted_comparables = array();
            foreach ($comparable_values as $k => $row) {
                $sorted_comparables[] = $row['distance_from_subject'];
            }
            array_multisort($sorted_comparables, SORT_ASC, $comparable_values);



            // discard sales over 0.3 miles distance_from_subject
            $discardDistanceRange = array();
            foreach($comparable_values as $key => $comparable_value) { 
                if($comparable_value['distance_from_subject'] <= '0.3') {
                    $discardDistanceRange[] = $comparable_value;
                }                               
            }

            if(count($discardDistanceRange) > 0 ){
                
                //Eliminate sales outside the 20% living area variance, if Current Assessment < $2,000,000
                $comparables_within_living_area = array();
                if($subject_property_details['current_asse_value'] >= config('constants.excludeLivingAreaAboveAmount') ){
                    //$comparables_within_living_area = $comparable_values;
                    //Living area variance percent discard sale 40%
                    $comparables_within_living_area = self::discardComparables($discardDistanceRange, config('constants.livingAreaVarianceFilterPercentForGreater'), $subject_property_details['current_asse_value'], $subject_property_details['square_footage']);

                    
                } else {
                    //Living area variance percent discard sale 20%
                    $comparables_within_living_area = self::discardComparables($discardDistanceRange, config('constants.livingAreaVarianceFilterPercent'), $subject_property_details['current_asse_value'], $subject_property_details['square_footage']);

                }

                //Take the 5 closest Sale Price/SF.
                $sub_sale_price_sf = ($subject_property_details['sale_price']/$subject_property_details['square_footage']);
                $diff = array();
                if( count($comparables_within_living_area) > 0 ){
                    foreach($comparables_within_living_area as $k => $item) {
                        $com_adj_sal_price_sf = ($item['price_after_adjustment'] / $item['comparable_square_footage']);

                        $diff[abs($com_adj_sal_price_sf - $sub_sale_price_sf)] = $item;
                    }
                    ksort($diff, SORT_NUMERIC); 
                }

                //If average of 5 closest sales is higher than assessment/SF then no appeal.
                $comAverage = 0;
                $sumOfSal_devived_SF = 0;
                if( count($diff) > 0 ){
                    $final_comparables = array_slice($diff, 0, 5, true);
                    
                    //Quartile range code start
                    $array_adj_sal_dev_sf = array();
                    
                    //additional enhancement start here 
                    $sorted_comparables = array();
                    foreach ($final_comparables as $ak => $arow) {
                        $sorted_comparables[] = $arow['sale_price_divided_square_footage'];
                    }
                    array_multisort($sorted_comparables, SORT_ASC, $final_comparables);
                    //additional enhancement end here

                    foreach ($final_comparables as $key1 => $final_com) {   
                        $array_adj_sal_dev_sf[] = $final_com['sale_price_divided_square_footage'];
                    }

                    // we want to get quartile 1 and 3
                    $quartile1 = self::Quartile_25($array_adj_sal_dev_sf);
                    $quartile3 = self::Quartile_75($array_adj_sal_dev_sf);
                    $intQuaRange = ($quartile3 - $quartile1);
                    
                    $upperRange = ($quartile3+1.5*$intQuaRange);
                    $lowerRange = ($quartile1-1.5*$intQuaRange);

                    // top sales within upper and lower range condition
                    $topSales = array();
                    $total_adj_sal_dev_sf = 0;
                    foreach ($final_comparables as $key2 => $final_com_filter) {
                        if($final_com_filter['sale_price_divided_square_footage'] >= $lowerRange && $final_com_filter['sale_price_divided_square_footage'] <= $upperRange ){
                            $total_adj_sal_dev_sf = ($total_adj_sal_dev_sf + $final_com_filter['sale_price_divided_square_footage']);
                            $topSales[] = $final_com_filter;
                        }
                    }
                    $comAverage = ($total_adj_sal_dev_sf/count($topSales));

                    $final_comparables = $topSales;
                    //Quartile range code end
                } else {
                    $final_comparables = array();
                    $comAverage = 0;
                }
            } else {
                //echo 'No comparables found after discarding 20%/40%';
                $final_comparables = array();
                $comAverage = 0;
            }

        } else {
            //echo 'No comparables exist';
            $final_comparables = array();
            $comAverage = 0;
        }
        
        $step2_response['step2_final_comparables'] = $final_comparables;
        $step2_response['com_average'] = $comAverage;
        $step2_response['sale_price_divided_square_footage'] = ($subject_property_details['sale_price']/$subject_property_details['square_footage']);
        $step2_response['assessment_divided_square_footage'] = $subject_property_details['sub_ass_div_sf'];
        
        return $step2_response;
    }
    
    
    /**
      * Return MD In Cycle Appeal Form.pdf
      * @param   
      * @return Response
    **/
    public function getTestPdf1($customer_id = 1)
    {
        $customer_pdf_path = public_path('customer_pdf/'.$customer_id);
        $pdf_name = 'md_in_cycle_appeal_form.pdf';
        if(!file_exists($customer_pdf_path)) {
            mkdir('customer_pdf/'.$customer_id, 0777);  
        }
        else {
            if(file_exists(public_path('customer_pdf/'.$customer_id.'/'.$pdf_name))) {
                unlink(public_path('customer_pdf/'.$customer_id.'/'.$pdf_name));
            }  
        }
        
        $params = [];
        $pdf = PDF::loadView('customer.pdf.md_in_cycle_appeal_form', $params);
        $pdf->save(public_path('customer_pdf/'.$customer_id.'/'.$pdf_name));
        
        return url('/customer_pdf/'.$customer_id.'/'.$pdf_name);
    }
    
    /**
      * Return Arlington Review Application.pdf
      * @param   
      * @return Response
    **/
    public function getTestPdf2($customer_id = 1)
    {
        $customer_pdf_path = public_path('customer_pdf/'.$customer_id);
        $pdf_name = 'arlington_review_application.pdf';
        if(!file_exists($customer_pdf_path)) {
            mkdir('customer_pdf/'.$customer_id, 0777);  
        }
        else {
            if(file_exists(public_path('customer_pdf/'.$customer_id.'/'.$pdf_name))) {
                unlink(public_path('customer_pdf/'.$customer_id.'/'.$pdf_name));
            }            
        }
        
        $params = [];
        $pdf = PDF::loadView('customer.pdf.arlington_review_application', $params);
        $pdf->save(public_path('customer_pdf/'.$customer_id.'/'.$pdf_name));
        
        return url('/customer_pdf/'.$customer_id.'/'.$pdf_name);
    }
    
    /**
      * Return DCTY Paper Appeal Application.pdf
      * @param   
      * @return Response
    **/
    public function getTestPdf3($customer_id = 1)
    {
        $customer_pdf_path = public_path('customer_pdf/'.$customer_id);
        $pdf_name = 'dcty_paper_appeal_application.pdf';
        if(!file_exists($customer_pdf_path)) {
            mkdir('customer_pdf/'.$customer_id, 0777);  
        }
        else {
            if(file_exists(public_path('customer_pdf/'.$customer_id.'/'.$pdf_name))) {
                unlink(public_path('customer_pdf/'.$customer_id.'/'.$pdf_name));
            }
        }
        
        $params = [];
        $pdf = PDF::loadView('customer.pdf.dcty_paper_appeal_application', $params);
        $pdf->save(public_path('customer_pdf/'.$customer_id.'/'.$pdf_name));
        
        return url('/customer_pdf/'.$customer_id.'/'.$pdf_name);
    }
    
    /**
      * Return Fairfax Appeal Application.pdf
      * @param   
      * @return Response
    **/
    public function getTestPdf4($customer_id = 1)
    {
        $customer_pdf_path = public_path('customer_pdf/'.$customer_id);
        $pdf_name = 'fairfax_appeal_application.pdf';
        if(!file_exists($customer_pdf_path)) {
            mkdir('customer_pdf/'.$customer_id, 0777);  
        }
        else {
            if(file_exists(public_path('customer_pdf/'.$customer_id.'/'.$pdf_name))) {
                unlink(public_path('customer_pdf/'.$customer_id.'/'.$pdf_name));
            }  
        }
        //return view('customer.pdf.fairfax_appeal_application');
        
        $params = [];
        $pdf = PDF::loadView('customer.pdf.fairfax_appeal_application', $params);
        $pdf->save(public_path('customer_pdf/'.$customer_id.'/'.$pdf_name));
        
        return url('/customer_pdf/'.$customer_id.'/'.$pdf_name);
    }
    
    public function payWithPaypal()
    {
        return view('customer.paywithpaypal');
    }
    /**
     * Store a details of payment with paypal.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    
    
    /*public function getAccessToken(){
        $ch = curl_init();
        $clientId = config('constants.clientId');
        $secret = config('constants.secret');

        curl_setopt($ch, CURLOPT_URL, "https://api.sandbox.paypal.com/v1/oauth2/token");
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_USERPWD, $clientId.":".$secret);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");

        $result = curl_exec($ch);
        $err = curl_error($ch);

        $access_token="";
        if ($err) {
          echo "cURL Error #:" . $err;
        }
        else
        {
            $json = json_decode($result);
           // print_r($json->access_token);
            $access_token = $json->access_token;
            return $access_token;
        }
    }

*/
    
    public function getTestMail() 
    {
        $mail_data['username'] = 'geetika@visions.net.in';
        $mail_data['content'] = 'Thank you for making payment, Please <a href="'.url('/token/1234').'" target="_blank" style="color:#1570C3;">click</a> on the link to complete the Tax Appeal. <br>
            Link is valid for 15 days.';
        $mail_data['subject'] = 'Payment Received';

        $mail_to = array('geetika@visions.net.in');
        $mail_sent = Helper::SendMail($mail_data, $mail_to);
        echo "<pre>"; print_r($mail_sent);
    }
    
    /**
      * Return Arlington Review Application.pdf
      * @param   
      * @return Response
    **/
    public function getArlingtonPdf($token=null)
    {
        //$token = Helper::getSessionToken();
        if($token != null) { 
            $token_details = UserSearch::where('phase2_token', $token)->get();
            $generate_pdf = self::generateArlingtonPdf($token_details[0]);
            $headers = array(
                'Content-Type: application/pdf',
            );
            return Response::download($generate_pdf['pdf_path'].'/'.$generate_pdf['pdf_name'], $generate_pdf['pdf_name'], $headers);
        }
        else{
            return redirect('/invalid-token');
        }
    }
    
    /**
      * Generate Arlington Review Application.pdf
      * @param   
      * @return Response
    **/
    public function generateArlingtonPdf($token_details=null)
    {
        $params = [];
        //$params['owner_name'] = $token_details->first_name.' '.$token_details->last_name;
        
        /*$storage_file_path = 'search_comparables/'.$token_details->user_search_id.'/comparables.txt';
        $corelogic_response = unserialize(Storage::get($storage_file_path));        
        $params['owner_name'] = $corelogic_response['RESPONSE']['RESPONSE_DATA']['PROPERTY_INFORMATION_RESPONSE']['_PROPERTY_INFORMATION'][0]['PROPERTY']['PROPERTY_OWNER']['@attributes']['_OwnerName'];*/
        $user_search_id = $token_details->user_search_id;
        $system_object_type_id = Helper::toGenerateCommunicationObjectTypes('user_searches', 'system_user_id', 'user_search', $user_search_id);
        $subject_property_details = SubjectCompsDetail::where('system_object_type_id', $system_object_type_id)->where('ref_object_id', $user_search_id)->get();  
        $params['owner_name']='';
        if($subject_property_details[0]->corelogic_response != ''){
            $corelogic_response =json_decode($subject_property_details[0]->corelogic_response,true);
            $params['owner_name'] = $corelogic_response['PROPERTY']['PROPERTY_OWNER']['@attributes']['_OwnerName'];
        }
        
        $lookup_type = Helper::getLookupTypeIdFromName(config('constants.constants.lookup_type.address'));
        $where_condition['lookup_type_id'] = $lookup_type[0]->lookup_type_id;
        $address_type_billing = Helper::getLookupIdFromName(config('constants.constants.lookup.address.billing_address'), $where_condition);

        //$customer_billing_address = PfAddress::where('address_type', $address_type_billing[0]->lookup_id)->where('ref_object_id', $token_details->user_search_id)->where('end_date',null)->get();
        $customer_billing_address = Helper::getBillingDetail(Auth::user()->id);
        $billing_state_abbr = State::getStateAbbr($customer_billing_address[0]->state);
        $billing_zipcode = $customer_billing_address[0]->postal_code;
        $billing_county_name = County::getCountyName($customer_billing_address[0]->county);        
        $params['billing_address'] = $customer_billing_address[0]->address_line_1.' '.$customer_billing_address[0]->address_line_2.', '.$customer_billing_address[0]->city.', '.$billing_county_name.', '.$billing_state_abbr.', '.$billing_zipcode;
        
        $address_type_search = Helper::getLookupIdFromName(config('constants.constants.lookup.address.search_address'), $where_condition);
        $customer_search_address = PfAddress::where('address_type', $address_type_search[0]->lookup_id)->where('ref_object_id', $token_details->user_search_id)->where('end_date',null)->get();
        $search_state_abbr = State::getStateAbbr($customer_search_address[0]->state);
        $search_zipcode = $customer_search_address[0]->postal_code;
        $search_county_name = County::getCountyName($customer_search_address[0]->county); 
        $search_county = County::where('county_id',$customer_search_address[0]->county)->get();
        $params['dateOfValue'] = 'N/A';
        $params['appeal_deadline_date'] = 'N/A';
        $params['appeal_deadline_date_full'] = 'N/A';
        
        if(count($search_county)){
            $params['dateOfValue'] = date('F d, Y',strtotime($search_county[0]->date_of_value));
            $params['appeal_deadline_date'] = date('F d',strtotime($search_county[0]->appeal_deadline_date));
            $params['appeal_deadline_date_full'] = date('F d, Y',strtotime($search_county[0]->appeal_deadline_date));
        } 
        if(!($customer_search_address[0]->address_line_1 == $customer_billing_address[0]->address_line_1 && $customer_search_address[0]->address_line_2 == $customer_billing_address[0]->address_line_2 
                && $customer_search_address[0]->city == $customer_billing_address[0]->city && $search_state_abbr == $billing_state_abbr && $billing_county_name == $search_county_name && $billing_zipcode == $search_zipcode))
        {
            $params['search_address'] = $customer_search_address[0]->address_line_1.' '.$customer_search_address[0]->address_line_2.', '.$customer_search_address[0]->city.', '.$search_county_name.', '.$search_state_abbr.', '.$search_zipcode;
        }
        else {
            $params['search_address'] = '';
        }
        
        $params['total_assessment_value'] = number_format($token_details->total_assessment_value, 2);
        
        $user_search_id = $token_details->user_search_id;
        
        $response['pdf_path'] = public_path('customer_pdf/'.$user_search_id);
        $response['pdf_name'] = 'arlington_review_application.pdf';
        
        if(!file_exists($response['pdf_path'])) {
            mkdir('customer_pdf/'.$user_search_id, 0777);   
        }
        else {
            if(file_exists(public_path('customer_pdf/'.$user_search_id.'/'.$response['pdf_name']))) {
                unlink(public_path('customer_pdf/'.$user_search_id.'/'.$response['pdf_name']));
            }            
        }        
        
        $pdf = PDF::loadView('customer.pdf.arlington_review_application', $params);
        $pdf->save(public_path('customer_pdf/'.$user_search_id.'/'.$response['pdf_name']));
        
        //return url('/customer_pdf/'.$user_search_id.'/'.$pdf_name);
        return $response;
    }
    
    
    
    /**
      * Return Fairfax Application.pdf
      * @param   
      * @return Response
    **/
    public function getFairfaxPdf($token=null)
    {
        //$token = Helper::getSessionToken();
        if($token != null) { 
            $token_details = UserSearch::where('phase2_token', $token)->get();
            $generate_pdf = self::generateFairfaxPdf($token_details[0]);
            $headers = array(
                'Content-Type: application/pdf',
            );
            return Response::download($generate_pdf['pdf_path'].'/'.$generate_pdf['pdf_name'], $generate_pdf['pdf_name'], $headers);
        }
        else{
            return redirect('/invalid-token');
        }
    }
    
    /**
      * Generate Fairfax Review Application.pdf
      * @param   
      * @return Response
    **/
    public function generateFairfaxPdf($token_details=null)
    {
        $params = [];
        $user_search_id = $token_details->user_search_id;
        $topComparableData = self::getTopComparablesListData($token_details->phase2_token);
        $params['topComparableData']=$topComparableData;
        //$params['owner_name'] = $token_details->first_name.' '.$token_details->last_name;
        $lookup_type = Helper::getLookupTypeIdFromName(config('constants.constants.lookup_type.address'));
        $where_condition['lookup_type_id'] = $lookup_type[0]->lookup_type_id;
        $address_type_search = Helper::getLookupIdFromName(config('constants.constants.lookup.address.search_address'), $where_condition);
        $customer_search_address = PfAddress::where('address_type', $address_type_search[0]->lookup_id)->where('system_object_type_id', 1)->where('ref_object_id', $token_details->user_search_id)->where('end_date',null)->get();
        
        
        $search_state_abbr = State::getStateAbbr($customer_search_address[0]->state);
        $search_zipcode = $params['zip_code'] = $customer_search_address[0]->postal_code;
        $search_county_name = County::getCountyName($customer_search_address[0]->county); 
        $apealDeadLineDate = County::where('county_name',$search_county_name)->where('state_id',$customer_search_address[0]->state)->first(); 
        $appeal_deadline_date='N/A';
        $date_of_value='N/A';
        if($apealDeadLineDate != null){
            $appeal_deadline_date=date('F d, Y',strtotime($apealDeadLineDate->appeal_deadline_date));
            $date_of_value=date('F d, Y',strtotime($apealDeadLineDate->date_of_value));
        }
        $params['appeal_year'] =$token_details->appeal_year;
        $params['appeal_deadline_date'] =$appeal_deadline_date;
        $params['date_of_value'] =$date_of_value;

        $params['search_address'] = $customer_search_address[0]->address_line_1.' '.$customer_search_address[0]->address_line_2.', '.$customer_search_address[0]->city.', '.$search_county_name.', '.$search_state_abbr.', '.$search_zipcode;
        
        $params['street_address'] = $customer_search_address[0]->address_line_1.' '.$customer_search_address[0]->address_line_2;
        $params['city'] = $customer_search_address[0]->city;
        
        $params['land_assessment_value'] = '$'.number_format($token_details->land_assessment_value, 2);
        $params['improvement_assessment_value'] = '$'.number_format($token_details->improvement_assessment_value, 2);        
        $params['total_assessment_value'] = '$'.number_format($token_details->total_assessment_value, 2);
              
        //$corelogic_response = unserialize($token_details->comparables);   
        
        //$storage_file_path = 'search_comparables/'.$user_search_id.'/comparables.txt';
        //$corelogic_response = unserialize(Storage::get($storage_file_path));
        //echo "<pre>"; print_r(unserialize($token_details->comparables)); exit;        
        //echo "<pre>"; print_r($corelogic_response); exit;
        
        /*$params['year_built'] = $corelogic_response['RESPONSE']['RESPONSE_DATA']['PROPERTY_INFORMATION_RESPONSE']['_PROPERTY_INFORMATION'][0]['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_GENERAL_DESCRIPTION']['@attributes']['_YearBuiltDateIdentifier'];
        $params['air_conditioning'] = (trim($corelogic_response['RESPONSE']['RESPONSE_DATA']['PROPERTY_INFORMATION_RESPONSE']['_PROPERTY_INFORMATION'][0]['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_COOLING']['@attributes']['_CentralizedIndicator']) == 'Y') ? 'Yes' : 'No';
        $params['sale_date'] = date('d/m/Y', strtotime($corelogic_response['RESPONSE']['RESPONSE_DATA']['PROPERTY_INFORMATION_RESPONSE']['_PROPERTY_INFORMATION'][0]['PROPERTY']['_PROPERTY_HISTORY']['_SALES_HISTORY']['@attributes']['_LastSalesDate']));
        $params['sale_price'] = '$'.$corelogic_response['RESPONSE']['RESPONSE_DATA']['PROPERTY_INFORMATION_RESPONSE']['_PROPERTY_INFORMATION'][0]['PROPERTY']['_PROPERTY_HISTORY']['_SALES_HISTORY']['@attributes']['_LastSalesPriceAmount'];
        
        $params['owner_name'] = $corelogic_response['RESPONSE']['RESPONSE_DATA']['PROPERTY_INFORMATION_RESPONSE']['_PROPERTY_INFORMATION'][0]['PROPERTY']['PROPERTY_OWNER']['@attributes']['_OwnerName'];*/
        
       
        $system_object_type_id = Helper::toGenerateCommunicationObjectTypes('user_searches', 'system_user_id', 'user_search', $user_search_id);
        $subject_property_details = SubjectCompsDetail::where('system_object_type_id', $system_object_type_id)->where('ref_object_id', $user_search_id)->get();

         $params['half_bath_count'] = ($subject_property_details[0]->half_bath_count)?$subject_property_details[0]->half_bath_count:'0';
       // echo  $subject_property_details[0]->half_bath_count
        $lookUpsDetails = SearchComparable::join('pf_lookups', 'pf_lookups.lookup_id', '=', 'search_comparables.lookup_id')->select('pf_lookups.*','search_comparables.*')->where('search_comparables.subject_comps_detail_id', $subject_property_details[0]->subject_comps_id)->get();
       // echo "<pre>eee";print_r($lookUpsDetails);exit;
        if(count($lookUpsDetails)){
 
            foreach($lookUpsDetails as $lookupDetail){
                $child_details = Helper::getChildLookupDetail($lookupDetail->lookup_id);
                $additional_homeowner_question_lookup_id = (count($child_details)) ? $child_details->lookup_id : $lookupDetail->lookup_id; 
                $maxRange = $lookupDetail->value;
                $maxLimitValue = "$".$maxRange;
                $minRange = $lookupDetail->value1;
                $minimitValue = "$".$minRange;
                $percentageValue = $lookupDetail->value2;
                $concateVal = $maxRange."-".$minRange."-".$percentageValue;
              
                    if($lookupDetail->name=='Needs a new roof or repairs'){
                        $params['lookupdetail']['needs_new_roof'] = money_format('%!n', ($lookupDetail->lookup_value));
                    }
                    if($lookupDetail->name=='bathrooms'){
                        $params['lookupdetail']['bathrooms'] = money_format('%!n', ($lookupDetail->lookup_value));
                    }
                    if($lookupDetail->name=='Renovate Kitchen'){
                        $params['lookupdetail']['kitchen'] = money_format('%!n', ($lookupDetail->lookup_value));
                    }
                    //$params['lookupdetail'][$t]['name'] = $lookupDetail->name;
                    
                
            }
        }                
        //echo "<pre>eee";print_r($params);exit;
        $params['year_built']='N/A';
        $params['air_conditioning']='N/A';
        $params['owner_name']='N/A';
        $params['sale_date']='N/A';
        $params['sale_price']='N/A';
        if($subject_property_details[0]->corelogic_response != ''){
            $corelogic_response =json_decode($subject_property_details[0]->corelogic_response,true);
             //echo "<pre>e";print_r($corelogic_response);exit;
            $params['year_built'] = $corelogic_response['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_GENERAL_DESCRIPTION']['@attributes']['_YearBuiltDateIdentifier'];
            $params['air_conditioning'] = (trim($corelogic_response['PROPERTY']['_PROPERTY_CHARACTERISTICS']['_IMPROVEMENTS']['_COOLING']['@attributes']['_CentralizedIndicator']) == 'Y') ? 'Yes' : 'No';
            $params['owner_name'] = $corelogic_response['PROPERTY']['PROPERTY_OWNER']['@attributes']['_OwnerName'];
            $params['sale_date'] = date('F d, Y', strtotime($corelogic_response['PROPERTY']['_PROPERTY_HISTORY']['_SALES_HISTORY']['@attributes']['_LastSalesDate']));
            $params['sale_price'] = '$'.money_format('%!n',$corelogic_response['PROPERTY']['_PROPERTY_HISTORY']['_SALES_HISTORY']['@attributes']['_LastSalesPriceAmount']);
        
          //  echo "<pre>e";Print_r($corelogic_response['PROPERTY']);exit;

        }
        
        $params['total_bedrooms'] = $subject_property_details[0]->bedrooms;
        $params['total_fireplaces'] = $subject_property_details[0]->fireplace;
        $params['bedrooms'] = $subject_property_details[0]->bedrooms; 
        $params['bathrooms'] = $subject_property_details[0]->bathrooms; 
        $params['garage'] = $subject_property_details[0]->garage; 
        $params['carport'] = $subject_property_details[0]->carport; 
        $params['type_of_house'] = $subject_property_details[0]->type_of_house;
        $params['fireplace'] = $subject_property_details[0]->fireplace;
        $params['square_footage'] = $subject_property_details[0]->square_footage;
        // echo "<pre>ee";print_r($params);exit;    
        //$params['prior_sale_price'] =$corelogic_response['RESPONSE']['RESPONSE_DATA']['PROPERTY_INFORMATION_RESPONSE']['_PROPERTY_INFORMATION'][0]['PROPERTY']['_PROPERTY_HISTORY']['_SALES_HISTORY']['@attributes']['_PriorSalePriceAmount'];

        //$params['prior_sale_date'] =date('F d, Y',strtotime($corelogic_response['RESPONSE']['RESPONSE_DATA']['PROPERTY_INFORMATION_RESPONSE']['_PROPERTY_INFORMATION'][0]['PROPERTY']['_PROPERTY_HISTORY']['_SALES_HISTORY']['@attributes']['_PriorSaleDate']));
        
        $params['comparables_detail'] = [];
        $system_object_type_id_comparable = Helper::toGenerateCommunicationObjectTypes('subject_comps_details', 'system_object_type_id', 'comparable', $token_details->system_user_id);

        $comparable_details = SubjectCompsDetail::where('system_object_type_id', $system_object_type_id_comparable)->where('ref_object_id', $user_search_id)->orderBy('comparable_number', 'ASC')->limit(3)->get();
       // echo "<pre>"; print_r($comparable_details); exit;
        $i=1;
        foreach($comparable_details as $comparable) {
            /*$corelogic_response_comparables = $corelogic_response['RESPONSE']['RESPONSE_DATA']['PROPERTY_INFORMATION_RESPONSE']['_PROPERTY_INFORMATION'][1]['_DATA_PROVIDER_COMPARABLE_SALES'][$comparable->comparable_number];
            $street_address = $corelogic_response_comparables['PROPERTY']['@attributes']['_StreetAddress'];
            $city = $corelogic_response_comparables['PROPERTY']['@attributes']['_City'];
            $state = $corelogic_response_comparables['PROPERTY']['@attributes']['_State'];
            $zipcode = $corelogic_response_comparables['PROPERTY']['@attributes']['_PostalCode'];*/
            $comDetails = SearchComparable::select('*')->where('subject_comps_detail_id', $comparable->subject_comps_id)->where('end_date',null)->first();
            $com_address = PfAddress::select('mobile_number','address_line_1','address_line_2','address_line_3','city','postal_code','state','county')->where(array('ref_object_id' => $comDetails->search_comparable_id, 'system_object_type_id'=> '3', 'address_type' => '1', 'end_date' => null))->first();
            $street_address = $com_address->address_line_1;
            $city = $com_address->city;
            $state = State::getStateName($com_address->state);
            $zipcode = $com_address->postal_code;
            $params['comparables_detail'][$i]['property_address'] = $street_address.', '.$city.', VA, '.$zipcode;

            $params['comparables_detail'][$i]['land_assessment_value'] = ($comDetails->land_assessment_value !='')?('$'.number_format($comDetails->land_assessment_value)):('$'.number_format(0));
            $params['comparables_detail'][$i]['improvement_assesment_value'] = ($comDetails->improvement_assesment_value !='')?('$'.number_format($comDetails->improvement_assesment_value)):('$'.number_format(0));
            //$comDetails->improvement_assesment_value;            
            $params['comparables_detail'][$i]['total_assessment_value'] = ($comDetails->total_assessment_value !='')?('$'.number_format($comDetails->total_assessment_value)):('$'.number_format(0));
            //$comDetails->total_assessment_value;

            $params['comparables_detail'][$i]['sale_date'] = date('m/d/Y', strtotime($comDetails->date_of_sale));
            $params['comparables_detail'][$i]['sale_price'] = '$'.number_format($comDetails->sale_price, 2);
            $i++;
        }
        //echo "<pre>e3e";print_r($params['comparables_detail']);exit;
       
        
        $response['pdf_path'] = public_path('customer_pdf/'.$user_search_id);
        $response['pdf_name'] = 'fairfax_appeal_application.pdf';
        
        if(!file_exists($response['pdf_path'])) {
            mkdir('customer_pdf/'.$user_search_id, 0777);   
        }
        else {
            if(file_exists(public_path('customer_pdf/'.$user_search_id.'/'.$response['pdf_name']))) {
                unlink(public_path('customer_pdf/'.$user_search_id.'/'.$response['pdf_name']));
            }            
        }        
        
        //$pdf = PDF::loadView('customer.pdf.fairfax_appeal_application', $params);
        $pdf = PDF::loadView('customer.pdf.fairfax_appeal_application_TopComp', $params);

        $pdf->save(public_path('customer_pdf/'.$user_search_id.'/'.$response['pdf_name']));
        
        //return url('/customer_pdf/'.$user_search_id.'/'.$pdf_name);
        return $response;
    }
    
    /**
      * Return DC Paper Appeal Application.pdf
      * @param   
      * @return Response
    **/
    public function getDcPdf($token)
    {
       // echo $token;exit;

        //$token = Helper::getSessionToken();
        if($token != null) { 
            $token_details = UserSearch::where('phase2_token', $token)->get();
           // echo "<pre>TOken = ";print_r($token_details);exit;
            
            //echo "<pre>werwerew";print_r($topComparableData);exit;
            $generate_pdf = self::generateDcPdf($token_details[0]);

            $headers = array(
                'Content-Type: application/pdf',
            );
           // echo "<pre>TOken = ";print_r($generate_pdf);exit;
            return Response::download($generate_pdf['pdf_path'].'/'.$generate_pdf['pdf_name'], $generate_pdf['pdf_name'], $headers);
        }
        else{
            return redirect('/invalid-token');
        }
    }
    
    /**
      * Generate Fairfax Review Application.pdf
      * @param   
      * @return Response
    **/
    public function generateDcPdf($token_details=null)
    {
        $params = [];
        //$params['owner_name'] = $token_details->first_name.' '.$token_details->last_name;

        $topComparableData = self::getTopComparablesListData($token_details->phase2_token);
        //echo "<pre>";print_r($topComparableData);exit;
        $params['topComparableData']=$topComparableData;
        $lookup_type = Helper::getLookupTypeIdFromName(config('constants.constants.lookup_type.address'));
        $where_condition['lookup_type_id'] = $lookup_type[0]->lookup_type_id;
        $address_type_search = Helper::getLookupIdFromName(config('constants.constants.lookup.address.search_address'), $where_condition);
        $customer_search_address = PfAddress::where('address_type', $address_type_search[0]->lookup_id)->where('system_object_type_id', 1)->where('ref_object_id', $token_details->user_search_id)->where('end_date',null)->get();
       //echo "<pre>"; print_r($customer_search_address); die;
        $search_state_abbr = $params['state'] = State::getStateAbbr($customer_search_address[0]->state);
        $search_zipcode = $params['zip_code'] = $customer_search_address[0]->postal_code;
        $search_county_name = County::getCountyName($customer_search_address[0]->county); 
        
        $apealDeadLineDate = County::where('county_name',$search_county_name)->where('state_id',$customer_search_address[0]->state)->first(); 
        $appeal_deadline_date='N/A';
        $date_of_value='N/A';
        if($apealDeadLineDate != null){
            $appeal_deadline_date=date('F d, Y',strtotime($apealDeadLineDate->appeal_deadline_date));
            $date_of_value=date('m/Y',strtotime($apealDeadLineDate->date_of_value));
            //echo "<pre>e";print_r($apealDeadLineDate);exit;    
        }
        $params['appeal_deadline_date'] = $appeal_deadline_date;
         $params['date_of_value'] = $date_of_value;
        
        $params['search_address'] = $customer_search_address[0]->address_line_1.' '.$customer_search_address[0]->address_line_2.', '.$customer_search_address[0]->city.', '.$search_county_name.', '.$search_state_abbr.', '.$search_zipcode;
        
        $params['street_address'] = $customer_search_address[0]->address_line_1.' '.$customer_search_address[0]->address_line_2;
        $params['city'] = $customer_search_address[0]->city;
        
        
        
        $user_details = User::find($token_details->system_user_id);
        $params['email'] = $user_details->email;
        $userRelatedData = Helper::getBillingDetail($token_details->system_user_id);
        $params['phone_number'] = (count($userRelatedData))?($userRelatedData[0]->mobile_number):"";
       //echo "<pre>e";print_r($userRelatedData);exit;
        /*$storage_file_path = 'search_comparables/'.$token_details->user_search_id.'/comparables.txt';
        $corelogic_response = unserialize(Storage::get($storage_file_path));        
        $params['owner_name'] = $corelogic_response['RESPONSE']['RESPONSE_DATA']['PROPERTY_INFORMATION_RESPONSE']['_PROPERTY_INFORMATION'][0]['PROPERTY']['PROPERTY_OWNER']['@attributes']['_OwnerName'];*/
        $user_search_id = $token_details->user_search_id;
        $system_object_type_id = Helper::toGenerateCommunicationObjectTypes('user_searches', 'system_user_id', 'user_search', $user_search_id);
        $subject_property_details = SubjectCompsDetail::where('system_object_type_id', $system_object_type_id)->where('ref_object_id', $user_search_id)->get();  
        $lookUpsDetails = SearchComparable::join('pf_lookups', 'pf_lookups.lookup_id', '=', 'search_comparables.lookup_id')->select('pf_lookups.*','search_comparables.*')->where('search_comparables.subject_comps_detail_id', $subject_property_details[0]->subject_comps_id)->get();
       // echo "<pre>eee";print_r($lookUpsDetails);exit;
        if(count($lookUpsDetails)){
            $lookupsPrice=0;
            foreach($lookUpsDetails as $lookupDetail){
                $child_details = Helper::getChildLookupDetail($lookupDetail->lookup_id);
                $additional_homeowner_question_lookup_id = (count($child_details)) ? $child_details->lookup_id : $lookupDetail->lookup_id; 
                $maxRange = $lookupDetail->value;
                $maxLimitValue = "$".$maxRange;
                $minRange = $lookupDetail->value1;
                $minimitValue = "$".$minRange;
                $percentageValue = $lookupDetail->value2;
                $concateVal = $maxRange."-".$minRange."-".$percentageValue;
                $lookupsPrice=$lookupsPrice+$lookupDetail->lookup_value;
                if($lookupDetail->name=='Needs a new roof or repairs'){
                    $params['lookupdetail']['needs_new_roof'] = money_format('%!n', ($lookupDetail->lookup_value));
                }
                if($lookupDetail->name=='bathrooms'){
                    $params['lookupdetail']['bathrooms'] = money_format('%!n', ($lookupDetail->lookup_value));
                }
                if($lookupDetail->name=='Renovate Kitchen'){
                    $params['lookupdetail']['kitchen'] = money_format('%!n', ($lookupDetail->lookup_value));
                }
            }
        }       
        $sub_otherData = UserSearch::where('user_search_id', $token_details->user_search_id)->where('end_date',null)->first();
       // echo "<pre>";print_r($sub_otherData->appeal_amount);exit;      
        //$subject_property_details->appeal_amount
        $params['final_Value'] = number_format(($sub_otherData->appeal_amount-$lookupsPrice), 2);
        //$params['final_Value'] = number_format(($lookupsPrice+$token_details->total_assessment_value), 2);
        $params['owner_name']='';
            
        if($subject_property_details[0]->corelogic_response != ''){
            $corelogic_response =json_decode($subject_property_details[0]->corelogic_response,true);
            $params['owner_name'] = $corelogic_response['PROPERTY']['PROPERTY_OWNER']['@attributes']['_OwnerName'];
        } 
                
        //$params['land_assessment_value'] = '$'.$token_details->land_assessment_value;
        $params['total_assessment_value'] = number_format($token_details->total_assessment_value, 2);

        
        $user_search_id = $token_details->user_search_id;
        $response['pdf_path'] = public_path('customer_pdf/'.$user_search_id);
        $response['pdf_name'] = 'Washington_DC_Assessment_Appeal_Form.pdf';
        //echo "<pre>";print_r($params);exit;
        //$params = [];
        
        if(!file_exists($response['pdf_path'])) {
            mkdir('customer_pdf/'.$user_search_id, 0777);   
        }
        else {
            if(file_exists(public_path('customer_pdf/'.$user_search_id.'/'.$response['pdf_name']))) {
                unlink(public_path('customer_pdf/'.$user_search_id.'/'.$response['pdf_name']));
            }            
        }

        //echo "<pre>eee";print_r($params);exit;
        //$pdf = PDF::loadView('customer.pdf.Washington_DC_Assessment_Appeal_Form', $params);
        $pdf = PDF::loadView('customer.pdf.Washington_DC_Assessment_Appeal_Form_TopComp', $params);
        //$pdf = PDF::loadView('customer.pdf.dcty_paper_appeal_application', $params);
        $pdf->save(public_path('customer_pdf/'.$user_search_id.'/'.$response['pdf_name']));
        
        //return url('/customer_pdf/'.$user_search_id.'/'.$pdf_name);
        return $response;
    }
    
    
    /**
      * Return MD In Cycle Application.pdf
      * @param   
      * @return Response
    **/
    public function getMdPdf($token = null)
    {
        if($token != null) { 
            $token_details = UserSearch::where('phase2_token', $token)->get(); 
            
           // $Type='IN';

            if(count($token_details) && $token_details[0]->cycle_type=='in'){
                $generate_pdf = self::generateMdPdf($token_details[0]);
            }else{
                $generate_pdf = self::generateMdPdfOut($token_details[0]);
            }           
            
            $headers = array(
                'Content-Type: application/pdf',
            );
            return Response::download($generate_pdf['pdf_path'].'/'.$generate_pdf['pdf_name'], $generate_pdf['pdf_name'], $headers);
        }
        else{
            return redirect('/invalid-token');
        }
    }
    
    
    /**
      * Generate Fairfax Review Application.pdf
      * @param   
      * @return Response
    **/
    public function generateMdPdf($token_details=null)
    {
        $params = [];
        /* 7 Feb 2018 */
        if(count($token_details)){
            $Address_details = PfAddress::where('ref_object_id', $token_details->user_search_id)->get();
            $params['search_address'] = $Address_details;
            $params['state_name'] = State::getStateName($Address_details[0]->state);
            $params['state_abbr'] = State::getStateAbbr($Address_details[0]->state);   
            $params['county_name'] = County::getCountyName($Address_details[0]->county); 
            /*$storage_file_path = 'search_comparables/'.$token_details->user_search_id.'/comparables.txt';
            $corelogic_response = unserialize(Storage::get($storage_file_path));  
            $params['parcel_number'] = $corelogic_response['RESPONSE']['RESPONSE_DATA']['PROPERTY_INFORMATION_RESPONSE']['_PROPERTY_INFORMATION'][0]['PROPERTY']['@attributes']['_AssessorsParcelIdentifier'];
            $params['owner_name'] = $corelogic_response['RESPONSE']['RESPONSE_DATA']['PROPERTY_INFORMATION_RESPONSE']['_PROPERTY_INFORMATION'][0]['PROPERTY']['PROPERTY_OWNER']['@attributes']['_OwnerName'];
            $params['property_street'] = $corelogic_response['RESPONSE']['RESPONSE_DATA']['PROPERTY_INFORMATION_RESPONSE']['_PROPERTY_INFORMATION'][0]['PROPERTY']['@attributes']['_StreetAddress'];
            $params['property_city'] = $corelogic_response['RESPONSE']['RESPONSE_DATA']['PROPERTY_INFORMATION_RESPONSE']['_PROPERTY_INFORMATION'][0]['PROPERTY']['@attributes']['_City'];
            $params['property_city'] = $corelogic_response['RESPONSE']['RESPONSE_DATA']['PROPERTY_INFORMATION_RESPONSE']['_PROPERTY_INFORMATION'][0]['PROPERTY']['@attributes']['_City'];
            $params['property_state'] = $corelogic_response['RESPONSE']['RESPONSE_DATA']['PROPERTY_INFORMATION_RESPONSE']['_PROPERTY_INFORMATION'][0]['PROPERTY']['@attributes']['_State'];
            $params['property_postalCode'] = $corelogic_response['RESPONSE']['RESPONSE_DATA']['PROPERTY_INFORMATION_RESPONSE']['_PROPERTY_INFORMATION'][0]['PROPERTY']['@attributes']['_PostalCode'];
            $params['property_County'] = $corelogic_response['RESPONSE']['RESPONSE_DATA']['PROPERTY_INFORMATION_RESPONSE']['_PROPERTY_INFORMATION'][0]['PROPERTY']['@attributes']['_County']; */

            $user_search_id = $token_details->user_search_id;
            $system_object_type_id = Helper::toGenerateCommunicationObjectTypes('user_searches', 'system_user_id', 'user_search', $user_search_id);
            $subject_property_details = SubjectCompsDetail::where('system_object_type_id', $system_object_type_id)->where('ref_object_id', $user_search_id)->get();  
            $params['owner_name']='';
            $params['parcel_number']='';
            $params['property_street']='';
            $params['property_city']='';
            $params['property_state']='';
            $params['property_postalCode']='';
            $params['property_County']='';
            if($subject_property_details[0]->corelogic_response != ''){

                $corelogic_response =json_decode($subject_property_details[0]->corelogic_response,true);
                $params['parcel_number'] = $corelogic_response['PROPERTY']['@attributes']['_AssessorsParcelIdentifier'];
                $params['owner_name'] = $corelogic_response['PROPERTY']['PROPERTY_OWNER']['@attributes']['_OwnerName'];
                $params['property_street'] = $corelogic_response['PROPERTY']['@attributes']['_StreetAddress'];
                 $params['property_city'] = $corelogic_response['PROPERTY']['@attributes']['_City'];
                $params['property_state'] = $corelogic_response['PROPERTY']['@attributes']['_State'];
                $params['property_postalCode'] = $corelogic_response['PROPERTY']['@attributes']['_PostalCode'];
                $params['property_County'] = $corelogic_response['PROPERTY']['@attributes']['_County'];
                //echo "<pre>";print_r( $params);exit;
            } 
            
        }
        /* End 7 Feb 2018 */
       // echo "<pre>ee";print_r($params);exit;
        /* End 7 Feb 2018 */
  
        $user_search_id = $token_details->user_search_id;
        $response['pdf_path'] = public_path('customer_pdf/'.$user_search_id);
        $response['pdf_name'] = 'md_in_cycle_appeal_form.pdf';
        
        //$params = [];
        
        if(!file_exists($response['pdf_path'])) {
            mkdir('customer_pdf/'.$user_search_id, 0777);   
        }
        else {
            if(file_exists(public_path('customer_pdf/'.$user_search_id.'/'.$response['pdf_name']))) {
                unlink(public_path('customer_pdf/'.$user_search_id.'/'.$response['pdf_name']));
            }            
        }        
        
        // OLD PDF $pdf = PDF::loadView('customer.pdf.md_in_cycle_appeal_form', $params);

        //For InCycle Condition HTML
        $pdf = PDF::loadView('customer.pdf.new_md.Howard_County_md_in_cycle_appeal_form', compact('params'));


        //For Out Cycle Condition HTML
       // $pdf = PDF::loadView('customer.pdf.new_md.Howard_County_md_out_cycle_appeal_form', $params);


        $pdf->save(public_path('customer_pdf/'.$user_search_id.'/'.$response['pdf_name']));
        
        //return url('/customer_pdf/'.$user_search_id.'/'.$pdf_name);
        return $response;
    }




    /**
      * Generate Fairfax Review Application.pdf
      * @param   
      * @return Response
    **/
    public function generateMdPdfOut($token_details=null)
    {
        $params = [];
        /* 7 Feb 2018 */
        if(count($token_details)){
            $Address_details = PfAddress::where('ref_object_id', $token_details->user_search_id)->get();
            $params['search_address'] = $Address_details;
            $params['state_name'] = State::getStateName($Address_details[0]->state);
            $params['state_abbr'] = State::getStateAbbr($Address_details[0]->state);   
            $params['county_name'] = County::getCountyName($Address_details[0]->county); 
            
            /*$storage_file_path = 'search_comparables/'.$token_details->user_search_id.'/comparables.txt';
            $corelogic_response = unserialize(Storage::get($storage_file_path)); 
                 
            $params['owner_name'] = $corelogic_response['RESPONSE']['RESPONSE_DATA']['PROPERTY_INFORMATION_RESPONSE']['_PROPERTY_INFORMATION'][0]['PROPERTY']['PROPERTY_OWNER']['@attributes']['_OwnerName'];
            $params['property_street'] = $corelogic_response['RESPONSE']['RESPONSE_DATA']['PROPERTY_INFORMATION_RESPONSE']['_PROPERTY_INFORMATION'][0]['PROPERTY']['@attributes']['_StreetAddress'];
            $params['property_city'] = $corelogic_response['RESPONSE']['RESPONSE_DATA']['PROPERTY_INFORMATION_RESPONSE']['_PROPERTY_INFORMATION'][0]['PROPERTY']['@attributes']['_City'];
            $params['property_city'] = $corelogic_response['RESPONSE']['RESPONSE_DATA']['PROPERTY_INFORMATION_RESPONSE']['_PROPERTY_INFORMATION'][0]['PROPERTY']['@attributes']['_City'];
            $params['property_state'] = $corelogic_response['RESPONSE']['RESPONSE_DATA']['PROPERTY_INFORMATION_RESPONSE']['_PROPERTY_INFORMATION'][0]['PROPERTY']['@attributes']['_State'];
            $params['property_postalCode'] = $corelogic_response['RESPONSE']['RESPONSE_DATA']['PROPERTY_INFORMATION_RESPONSE']['_PROPERTY_INFORMATION'][0]['PROPERTY']['@attributes']['_PostalCode'];
            $params['property_County'] = $corelogic_response['RESPONSE']['RESPONSE_DATA']['PROPERTY_INFORMATION_RESPONSE']['_PROPERTY_INFORMATION'][0]['PROPERTY']['@attributes']['_County'];*/

            $user_search_id = $token_details->user_search_id;
            $system_object_type_id = Helper::toGenerateCommunicationObjectTypes('user_searches', 'system_user_id', 'user_search', $user_search_id);
            $subject_property_details = SubjectCompsDetail::where('system_object_type_id', $system_object_type_id)->where('ref_object_id', $user_search_id)->get();  
            $params['owner_name']='';
            $params['parcel_number']='';
            $params['property_street']='';
            $params['property_city']='';
            $params['property_state']='';
            $params['property_postalCode']='';
            $params['property_County']='';
            if($subject_property_details[0]->corelogic_response != ''){

                $corelogic_response =json_decode($subject_property_details[0]->corelogic_response,true);
                $params['parcel_number'] = $corelogic_response['PROPERTY']['@attributes']['_AssessorsParcelIdentifier'];
                $params['owner_name'] = $corelogic_response['PROPERTY']['PROPERTY_OWNER']['@attributes']['_OwnerName'];
                $params['property_street'] = $corelogic_response['PROPERTY']['@attributes']['_StreetAddress'];
                 $params['property_city'] = $corelogic_response['PROPERTY']['@attributes']['_City'];
                $params['property_state'] = $corelogic_response['PROPERTY']['@attributes']['_State'];
                $params['property_postalCode'] = $corelogic_response['PROPERTY']['@attributes']['_PostalCode'];
                $params['property_County'] = $corelogic_response['PROPERTY']['@attributes']['_County'];
                //echo "<pre>";print_r( $params);exit;
            } 
        }

        /* End 7 Feb 2018 */
  //echo "<pre>CoreLogic: ";print_r($corelogic_response['RESPONSE']['RESPONSE_DATA']);exit;
        $user_search_id = $token_details->user_search_id;
        $response['pdf_path'] = public_path('customer_pdf/'.$user_search_id);
        $response['pdf_name'] = 'md_out_cycle_appeal_form.pdf';
        
        //$params = [];
        
        if(!file_exists($response['pdf_path'])) {
            mkdir('customer_pdf/'.$user_search_id, 0777);   
        }
        else {
            if(file_exists(public_path('customer_pdf/'.$user_search_id.'/'.$response['pdf_name']))) {
                unlink(public_path('customer_pdf/'.$user_search_id.'/'.$response['pdf_name']));
            }            
        }        
        
        // OLD PDF $pdf = PDF::loadView('customer.pdf.md_in_cycle_appeal_form', $params);
        //For OutCycle Condition HTML
        $pdf = PDF::loadView('customer.pdf.new_md.Howard_County_md_out_cycle_appeal_form', compact('params'));

        $pdf->save(public_path('customer_pdf/'.$user_search_id.'/'.$response['pdf_name']));
        
        //return url('/customer_pdf/'.$user_search_id.'/'.$pdf_name);
        return $response;
    }
	
	public function downloadAllAdjustedComps(){
		$comps = Session::get('all_comparables');
		$all_comparables = $comps['comparables']['all_comparables'];
		$subject_property_details = $comps['subject_property'];
		$subjectAddressDetails = $comps['form_data'];
		
		$subjectAddress = $subjectAddressDetails['street'].", ".$subjectAddressDetails['city'].", ".$subjectAddressDetails['state'].", ".$subjectAddressDetails['postal_code'];
		$currAssessment = $subjectAddressDetails['total_assessment_value'];
		$headers = array(
                "Content-type" => "text/csv",
                "Content-Disposition" => "attachment; filename=file.csv",
                "Pragma" => "no-cache",
                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                "Expires" => "0"
            );
		
		$columns = array('Address', 'Sale Date', 'Sale Price', 'Living Area', 'Sale Price After Adjustment', '# of Bedrooms', '# of Bathrooms', 'Distance from Subject', 'Adjusted Sale Price /SF');

        $callback = function() use ($all_comparables, $columns, $subjectAddress, $subject_property_details,$currAssessment)
        {
                    $file = fopen('php://output', 'w');
                    fputcsv($file, $columns);
	           
                    fputcsv($file, array(
                        $subjectAddress,
                        $subject_property_details['date_of_sale'],
                        $subject_property_details['sale_price'],
                        $subject_property_details['square_footage'],
                        '-',
                        $subject_property_details['total_bedrooms'],
                        $subject_property_details['total_bathrooms'],
                        '-',
                        '-',
                    ));

					fputcsv($file, array(
                                    'Current Assessment Value',
                                    $currAssessment,
                                    '',
                                    '',
                                    '',
                                    '',
                                    '',
                                    '',
                                    ''
                                ));
								
                    foreach($all_comparables as $review) {
                        $comparable_address = "";
                        $comparable_address .= (!empty($review['comparable_address_street'])) ? $review['comparable_address_street'] : '';
                        $comparable_address .= (!empty($review['comparable_address_city'])) ? ', '.$review['comparable_address_city'] : '';
                        $comparable_address .= (!empty($review['comparable_address_state'])) ? ', '.$review['comparable_address_state'] : '';
                        $comparable_address .= (!empty($review['comparable_address_county'])) ? ', '.$review['comparable_address_county'] : '';
                        $comparable_address .= (!empty($review['comparable_address_zipcode'])) ? ', '.$review['comparable_address_zipcode'] : '';

                        $sqFoot = "";
                        $adSaleDivSF = "";
                       
                        $sqFoot = $review['comparable_square_footage'];
                        $adSaleDivSF = ($review['price_after_adjustment']/$sqFoot);
                       
                        fputcsv($file, array($comparable_address, $review['comparable_date_of_sale'], $review['comparable_sale_price'], $review['comparable_square_footage'], $review['price_after_adjustment'], $review['comparable_total_bedrooms'], $review['comparable_total_bathrooms'], $review['distance_from_subject'], $adSaleDivSF));
                    }
                    fclose($file);
                };
            
            return Response::stream($callback, 200, $headers);
		
	}

    public function downloadTopAdjustedComps(){
        $comps = Session::get('all_comparables');
        $subject_property_details = $comps['subject_property'];
        $subjectAddressDetails = $comps['form_data'];

        $final_comparables = array();
        if( isset($comps['comparables']['final_comparables']) ){
            $final_comparables = $comps['comparables']['final_comparables'];
        }

        $step2_response = array();
        if( isset($comps['step2_response']) ){
            $step2_response = $comps['step2_response'];
        }
        //echo "<pre>"; print_r($step2_response); die;
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=file.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        $isCase = 0;        
        if(count($subject_property_details) > 0 && isset($subject_property_details['case_1']) && $subject_property_details['case_1'] == 1){
            $isCase = 1;
        } elseif (count($subject_property_details) > 0 && isset($subject_property_details['case_1']) && $subject_property_details['case_1'] == 0 && isset($subject_property_details['no_appeal_recommendation']) && $subject_property_details['no_appeal_recommendation'] == 0) {
            $isCase = 2;
        }

        $subjectAddress = $subjectAddressDetails['street'].", ".$subjectAddressDetails['city'].", ".$subjectAddressDetails['state'].", ".$subjectAddressDetails['postal_code'];
        
        if ( $subject_property_details['no_appeal_recommendation'] == 1 ) {
            
            $columns = array('Address', 'Sale Date', 'Sale Price', 'Living Area', 'Sale Price After Adjustment', '# of Bedrooms', '# of Bathrooms', 'Distance from Subject', 'Adjusted Sale Price/SF');

            $callback = function() use ($final_comparables, $columns, $subjectAddress, $subject_property_details, $isCase)
            {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);

                fputcsv($file, array(
                    $subjectAddress,
                    $subject_property_details['date_of_sale'],
                    $subject_property_details['sale_price'],
                    $subject_property_details['square_footage'],
                    '-',
                    $subject_property_details['total_bedrooms'],
                    $subject_property_details['total_bathrooms'],
                    '-',
                    '-'
                ));
                fputcsv($file, array('No Appeal Recommendation', '-', '-', '-', '-', '-', '-', '-', '-'));
                
                $calc = array(
                    "",
                    "",
                    "",
                    "",
                    "",
                    "Assessment amount = ".$subject_property_details['current_asse_value'],
                    "",
                    $subject_property_details['no_appeal_message'],
                    "",
                );
                foreach ($calc as $k => $val) {
                    fputcsv($file, array(
                        $val,
                    ));
                }
                /*foreach($final_comparables as $review) {
                    $comparable_address = "";
                    $comparable_address .= (!empty($review['comparable_address_street'])) ? $review['comparable_address_street'] : '';
                    $comparable_address .= (!empty($review['comparable_address_city'])) ? ', '.$review['comparable_address_city'] : '';
                    $comparable_address .= (!empty($review['comparable_address_state'])) ? ', '.$review['comparable_address_state'] : '';
                    $comparable_address .= (!empty($review['comparable_address_county'])) ? ', '.$review['comparable_address_county'] : '';
                    $comparable_address .= (!empty($review['comparable_address_zipcode'])) ? ', '.$review['comparable_address_zipcode'] : '';
                    
                    $sqFoot = $review['comparable_square_footage'];
                    $adSaleDivSF = ($review['price_after_adjustment']/$sqFoot);

                    fputcsv($file, array($comparable_address, $review['comparable_date_of_sale'], $review['comparable_sale_price'], $review['comparable_square_footage'], $review['price_after_adjustment'], $review['comparable_total_bedrooms'], $review['comparable_total_bathrooms'], $review['distance_from_subject'], $adSaleDivSF));
                }

                $difference = ($subject_property_details['current_asse_value'] - $subject_property_details['appeal_amount']);
                $tax_rate = ($subject_property_details['real_tax_amount']/$subject_property_details['total_assessed_value_amount']);
                $tax_saving = ($tax_rate*$difference);

                $subject_property_details['tax_saving'] = $tax_saving;

                if($tax_saving < config('constants.minimumTaxSavings')){
                    $subject_property_details['no_appeal_recommendation'] = 1;
                } else {
                    $subject_property_details['no_appeal_recommendation'] = 0;
                }

                $difference = ($subject_property_details['current_asse_value'] - $subject_property_details['appeal_amount']);

                $tax_rate = ($subject_property_details['real_tax_amount']/$subject_property_details['total_assessed_value_amount']);
                $tax_saving = ($tax_rate*$difference);
                $message = "";
                if($tax_saving < config('constants.minimumTaxSavings')){
                    $message = "Hence there is no appeal because tax saving < $".config('constants.minimumTaxSavings');
                } else {
                    $message = "Hence appeal is recommended";
                }

                $diff_value = (100-$subject_property_details['differential_value'])/100;
                $startRange = (config('constants.getTenPercent')*$subject_property_details['current_asse_value'])/$subject_property_details['square_footage'];
                $endRange = ($diff_value*$subject_property_details['current_asse_value'])/$subject_property_details['square_footage'];
                $case_one_start_range = "";

                if($isCase == 2){
                    $case_one_start_range = config('constants.getTenPercent')."*".$subject_property_details['current_asse_value']."/".$subject_property_details['square_footage'] ."= ".$startRange;
                }

                $calc = array(
                    "",
                    "",
                    "",
                    "",
                    "",
                    "Assessment amount = ".$subject_property_details['current_asse_value'],
                    "Appeal Amount = ".$subject_property_details['appeal_amount'],
                    "Difference = ".$subject_property_details['current_asse_value'] ."-". $subject_property_details['appeal_amount'] ." = ".$difference,
                    "TaxRate =  ".$subject_property_details['real_tax_amount']."/".$subject_property_details['total_assessed_value_amount']." = ".$tax_rate,
                    "TaxSaving = TaxRate*Difference = ".$tax_rate."*".$difference." = ".$tax_saving,
                    $message,
                    "",
                    "Range in which top5 comparable adjusted sale price/SF should be",
                    $case_one_start_range,
                    $diff_value."*".$subject_property_details['current_asse_value']."/".$subject_property_details['square_footage'] ."= ".$endRange,
                    "",
                    "",
                    "",
                );
                foreach ($calc as $k => $val) {
                    fputcsv($file, array(
                        $val,
                    ));
                }

                // step 2 start here
                if ( !empty($step2_response) ) {
                    
                    foreach($step2_response['step2_final_comparables'] as $reviewStep2) {
                        $comparable_address = "";
                        $comparable_address .= (!empty($reviewStep2['comparable_address_street'])) ? $reviewStep2['comparable_address_street'] : '';
                        $comparable_address .= (!empty($reviewStep2['comparable_address_city'])) ? ', '.$reviewStep2['comparable_address_city'] : '';
                        $comparable_address .= (!empty($reviewStep2['comparable_address_state'])) ? ', '.$reviewStep2['comparable_address_state'] : '';
                        $comparable_address .= (!empty($reviewStep2['comparable_address_county'])) ? ', '.$reviewStep2['comparable_address_county'] : '';
                        $comparable_address .= (!empty($reviewStep2['comparable_address_zipcode'])) ? ', '.$reviewStep2['comparable_address_zipcode'] : '';
                        
                        $sqFoot = $reviewStep2['comparable_square_footage'];
                        $adSaleDivSF = ($reviewStep2['price_after_adjustment']/$sqFoot);

                        fputcsv($file, array($comparable_address, $reviewStep2['comparable_date_of_sale'], $reviewStep2['comparable_sale_price'], $reviewStep2['comparable_square_footage'], $reviewStep2['price_after_adjustment'], $reviewStep2['comparable_total_bedrooms'], $reviewStep2['comparable_total_bathrooms'], $reviewStep2['distance_from_subject'], $adSaleDivSF));
                    }

                    $appeal_message = "";
                    if($step2_response['com_average'] > $step2_response['sale_price_divided_square_footage']){
                        $appeal_message = "Average of top closest sales is higher than assessment/SF hence no appeal recommendation";
                    } else {
                        $appeal_message = "Average of top closest sales is less than assessment/SF hence appeal recommended";
                    }
                    $step2_calc = array(
                        "",
                        "",
                        "Average of top closest sales is = ".$step2_response['com_average'],
                        "assessment/SF = ".$step2_response['assessment_divided_square_footage'],
                        $appeal_message,
                    );
                    foreach ($step2_calc as $s_k => $s_val) {
                        fputcsv($file, array(
                            $s_val,
                        ));
                    } 
                    
                }*/
                // ste 2 end here

                fclose($file);
            };
            
        } else {
            
            $adjustedSalePriceRange = "";
            $isCase = 0;
            $adj_sal_price_sf = "";
            if(count($subject_property_details) > 0 && isset($subject_property_details['case_1']) && $subject_property_details['case_1'] == 1){

                $adjustedSalePriceRange = "Adjusted SalePrice/SF +/- 1% range ".$subject_property_details['subject_salePrice_minus_1_percent']." - ".$subject_property_details['subject_salePrice_plus_1_percent']." & Adjusted SalePrice/SF - 20% range ".$subject_property_details['subject_salePrice_minus_twenty_percent']." - ".$subject_property_details['subject_salePrice_plus_twenty_percent'];

                $isCase = 1;
            } elseif(count($subject_property_details) > 0 && isset($subject_property_details['case_1']) && $subject_property_details['case_1'] == 0 && isset($subject_property_details['no_appeal_recommendation']) && $subject_property_details['no_appeal_recommendation'] == 0) {

                $adjustedSalePriceRange = "Living area variance percent +/- 20% range ".$subject_property_details['living_area_minus_twenty']." - ".$subject_property_details['living_area_plus_twenty'];

                $isCase = 2;
                $adj_sal_price_sf = 'Adjusted Sale Price / SF';
            }


            $columns = array('Address', 'Sale Date', 'Sale Price', 'Living Area', 'Sale Price After Adjustment', '# of Bedrooms', '# of Bathrooms', 'Distance from Subject', $adjustedSalePriceRange, $adj_sal_price_sf);

            $callback = function() use ($final_comparables, $columns, $isCase, $subjectAddress, $subject_property_details, $step2_response)
            {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);
                $adj = '';
                if($isCase == 2){
                    $adj = '-';
                }
                fputcsv($file, array(
                    $subjectAddress,
                    $subject_property_details['date_of_sale'],
                    $subject_property_details['sale_price'],
                    $subject_property_details['square_footage'],
                    '-',
                    $subject_property_details['total_bedrooms'],
                    $subject_property_details['total_bathrooms'],
                    '-',
                    '-',
                    $adj
                ));

                fputcsv($file, array(
                    'Appeal Amount',
                    $subject_property_details['appeal_amount'],
                    '-',
                    '-',
                    '-',
                    '-',
                    '-',
                    '-',
                    '-',
                    $adj
                ));

                foreach($final_comparables as $review) {
                    $comparable_address = "";
                    $comparable_address .= (!empty($review['comparable_address_street'])) ? $review['comparable_address_street'] : '';
                    $comparable_address .= (!empty($review['comparable_address_city'])) ? ', '.$review['comparable_address_city'] : '';
                    $comparable_address .= (!empty($review['comparable_address_state'])) ? ', '.$review['comparable_address_state'] : '';
                    $comparable_address .= (!empty($review['comparable_address_county'])) ? ', '.$review['comparable_address_county'] : '';
                    $comparable_address .= (!empty($review['comparable_address_zipcode'])) ? ', '.$review['comparable_address_zipcode'] : '';

                    $sqFoot = "";
                    $adSaleDivSF = "";
                    if($isCase == 1){
                        $sqFoot = $review['sale_price_divided_square_footage'];
                    }elseif ($isCase == 2) {
                        $sqFoot = $review['comparable_square_footage'];
                        $adSaleDivSF = ($review['price_after_adjustment']/$sqFoot);
                    }
                    fputcsv($file, array($comparable_address, $review['comparable_date_of_sale'], $review['comparable_sale_price'], $review['comparable_square_footage'], $review['price_after_adjustment'], $review['comparable_total_bedrooms'], $review['comparable_total_bathrooms'], $review['distance_from_subject'], $sqFoot,  $adSaleDivSF));
                }

                $difference = ($subject_property_details['current_asse_value'] - $subject_property_details['appeal_amount']);
                $tax_rate = ($subject_property_details['real_tax_amount']/$subject_property_details['total_assessed_value_amount']);
                $tax_saving = ($tax_rate*$difference);

                $subject_property_details['tax_saving'] = $tax_saving;

                if($tax_saving < config('constants.minimumTaxSavings')){
                    $subject_property_details['no_appeal_recommendation'] = 1;
                } else {
                    $subject_property_details['no_appeal_recommendation'] = 0;
                }

                $difference = ($subject_property_details['current_asse_value'] - $subject_property_details['appeal_amount']);

                $tax_rate = ($subject_property_details['real_tax_amount']/$subject_property_details['total_assessed_value_amount']);
                $tax_saving = ($tax_rate*$difference);
                $message = "";
                if($tax_saving < config('constants.minimumTaxSavings')){
                    $message = "Hence there is no appeal because tax saving < $".config('constants.minimumTaxSavings');
                } else {
                    $message = "Hence appeal is recommended";
                }

                $diff_value = (100-$subject_property_details['differential_value'])/100;
                $startRange = (config('constants.getTenPercent')*$subject_property_details['current_asse_value'])/$subject_property_details['square_footage'];
                $endRange = ($diff_value*$subject_property_details['current_asse_value'])/$subject_property_details['square_footage'];
                
                $case_one_start_range = "";
                if($isCase == 2){
                    $case_one_start_range = config('constants.getTenPercent')."*".$subject_property_details['current_asse_value']."/".$subject_property_details['square_footage'] ."= ".$startRange;
                }

                $calc = array(
                    "",
                    "",
                    "",
                    "",
                    "",
                    "Assessment amount = ".$subject_property_details['current_asse_value'],
                    "Appeal Amount = ".$subject_property_details['appeal_amount'],
                    "Difference = ".$subject_property_details['current_asse_value'] ."-". $subject_property_details['appeal_amount'] ." = ".$difference,
                    "TaxRate =  ".$subject_property_details['real_tax_amount']."/".$subject_property_details['total_assessed_value_amount']." = ".$tax_rate,
                    "TaxSaving = TaxRate*Difference = ".$tax_rate."*".$difference." = ".$tax_saving,
                    $message,
                    "",
                    "Range in which top5 comparable adjusted sale price/SF should be",
                    $case_one_start_range,
                    $diff_value."*".$subject_property_details['current_asse_value']."/".$subject_property_details['square_footage'] ."= ".$endRange,
                    "",
                    "",
                    "",
                );
                foreach ($calc as $k => $val) {
                    fputcsv($file, array(
                        $val,
                    ));
                }

                // step 2 start here
                if ( !empty($step2_response) ) {
                    
                    foreach($step2_response['step2_final_comparables'] as $reviewStep2) {
                        $comparable_address = "";
                        $comparable_address .= (!empty($reviewStep2['comparable_address_street'])) ? $reviewStep2['comparable_address_street'] : '';
                        $comparable_address .= (!empty($reviewStep2['comparable_address_city'])) ? ', '.$reviewStep2['comparable_address_city'] : '';
                        $comparable_address .= (!empty($reviewStep2['comparable_address_state'])) ? ', '.$reviewStep2['comparable_address_state'] : '';
                        $comparable_address .= (!empty($reviewStep2['comparable_address_county'])) ? ', '.$reviewStep2['comparable_address_county'] : '';
                        $comparable_address .= (!empty($reviewStep2['comparable_address_zipcode'])) ? ', '.$reviewStep2['comparable_address_zipcode'] : '';
                        
                        $sqFoot = $reviewStep2['comparable_square_footage'];
                        $adSaleDivSF = ($reviewStep2['price_after_adjustment']/$sqFoot);

                        fputcsv($file, array($comparable_address, $reviewStep2['comparable_date_of_sale'], $reviewStep2['comparable_sale_price'], $reviewStep2['comparable_square_footage'], $reviewStep2['price_after_adjustment'], $reviewStep2['comparable_total_bedrooms'], $reviewStep2['comparable_total_bathrooms'], $reviewStep2['distance_from_subject'], $sqFoot, $adSaleDivSF));
                    }

                    $appeal_message = "";
                    if($step2_response['com_average'] > $step2_response['sale_price_divided_square_footage']){
                        $appeal_message = "Average of top closest sales is higher than assessment/SF hence no appeal recommendation";
                    } else {
                        $appeal_message = "Average of top closest sales is less than assessment/SF hence appeal recommended";
                    }
                    if(isset($step2_response) && isset($step2_response['step2_final_comparables']) && count($step2_response['step2_final_comparables'])>0){

                        $step2_calc = array(
                            "",
                            "",
                            "Average of top closest sales is = ".$step2_response['com_average'],
                            "assessment/SF = ".$step2_response['assessment_divided_square_footage'],
                            $appeal_message,
                        );
                        foreach ($step2_calc as $s_k => $s_val) {
                            fputcsv($file, array(
                                $s_val,
                            ));
                        }                   
                    }
                    
                }
                // ste 2 end here
                fclose($file);
            };
        }



        return Response::stream($callback, 200, $headers);
    }

    public function getYearMonth($monthCount = 0, $sign = ''){
        for($i=0; $i<=$monthCount; $i++) {
            
            if(!is_float($i/12)) {
                $years = floor($i / 12).' years';
                $years = $sign.$years;
            }

            $months = ' '.$sign.($i % 12).' months';
            

            $display = $years.' '.$months;
        }
        return $display;
    }

    /**
      * Testing html to pdf
      * @param   
      * @return Response
    **/
    public function getPdfTesting($page) {      
        $generate_pdf = self::generateTestingPdf($page);
        $headers = array(
            'Content-Type: application/pdf',
        );
        return Response::download($generate_pdf['pdf_path'].'/'.$generate_pdf['pdf_name'], $generate_pdf['pdf_name'], $headers);        
    }
        
    /**
      * Generate Testing PDF
      * @param   
      * @return Response
    **/
    public function generateTestingPdf($page)
    {
        $params = [];
        $testingFolder = "testing";
        $response['pdf_path'] = public_path('customer_pdf/'.$testingFolder);
        if ($page != "") {
            $response['pdf_name'] = $page.'.pdf';
        } else {
            $response['pdf_name'] = 'testing_html2pdf.pdf';
        }
        
        if(!file_exists($response['pdf_path'])) {
            mkdir('customer_pdf/'.$testingFolder, 0777);   
        }
        else {
            if(file_exists(public_path('customer_pdf/'.$testingFolder.'/'.$response['pdf_name']))) {
                unlink(public_path('customer_pdf/'.$testingFolder.'/'.$response['pdf_name']));
            }            
        }   

        $htmlView = 'customer.pdf.testing_html';
        if ($page != "") {
            $htmlView = 'customer.pdf.'.$page;
        }    
        
        $pdf = PDF::loadView($htmlView, $params);
        $pdf->save(public_path('customer_pdf/'.$testingFolder.'/'.$response['pdf_name']));
        
        return $response;
    }

    /**
      * redirect to address not found page
      * @param   
      * @return Response
    **/
    public function getDashboard(Request $request)
    {
        try {
            $id = Auth::user()->id;
            $searchHistory = Helper::getMemberSearchHistory($id);
            $compactData = array('searchHistory');
            return view('home',compact($compactData));
        }
        catch (\Exception $e) 
        {
            $result = ['exception_message' => $e->getMessage()];
            return view('errors.error', $result);
        }
    }

    // quartile 1 25% / 0.25
    public function Quartile_25($Array) {
        return $this->Quartile($Array, 0.25);
    }


    // quartile 3 75% / 0.75
    public function Quartile_75($Array) {
        return $this->Quartile($Array, 0.75);
    }

    // do the math
    // pass in the array of values and the quartile you are looking
    public function Quartile($data, $percentile) {
        if( 0 < $percentile && $percentile < 1 ) {
            $p = $percentile;
        } else if( 1 < $percentile && $percentile <= 100 ) {
            $p = $percentile * .01;
        } else {
            return "";
        }
        $count = count($data);
        $allindex = ($count-1)*$p;
        $intvalindex = intval($allindex);
        $floatval = $allindex - $intvalindex;
        sort($data);
        if(!is_float($floatval)) {
            $result = $data[$intvalindex];
        } else {
            if($count > $intvalindex+1)
                $result = $floatval*($data[$intvalindex+1] - $data[$intvalindex]) + $data[$intvalindex];
            else
                $result = $data[$intvalindex];
        }
        return $result; 
    }

    /**
    * Discard sale 20% and 40% as per condition
    * @return Array
    * @param array, discard range (20/40), subject sale price and subject SF
    */
    public function discardComparables($comparables = array(), $discardRange = 0, $subjectSalePrice = 0, $subjectSquareFeet = 0){

        $response = array();

        if(count($comparables) > 0 && ($discardRange == config('constants.livingAreaVarianceFilterPercentForGreater') || $discardRange == config('constants.livingAreaVarianceFilterPercent')) ){
            $sale_div_sf = ($subjectSalePrice/$subjectSquareFeet);
            $plusRange = $sale_div_sf+($sale_div_sf*$discardRange/100);
            $minusRange = $sale_div_sf-($sale_div_sf*$discardRange/100);

            foreach($comparables as $key => $comparable) {
                
                if($comparable['sale_price_divided_square_footage'] <= $plusRange && $comparable['sale_price_divided_square_footage'] >= $minusRange) {
                    $response[] = $comparable;
                }                               
            }
        }

        return $response;
    }
    
    /**
    * getTopComarablesList
    **/
    public function getTopComparablesListData($token){
        try {

            $token_details = UserSearch::where('phase2_token', $token)->where('end_date',null)->get();
                
            if(count($token_details)) {                    
                $subjectCompsDetail = array();
                $pdfLink = "";
                $lat = $sub_lat = "0.000000";
                $long = $sub_long = "-0.000000";

                if($token_details[0]->active_page == '3') {

                    $subject_details = SubjectCompsDetail::where(array('ref_object_id' => $token_details[0]->user_search_id, 'system_object_type_id' => '2'))->where('end_date',null)->get();

                    $sub_address = PfAddress::where('system_object_type_id', '1')->where('ref_object_id', $token_details[0]->user_search_id)->where('end_date',null)->first();
                        
                    if( isset($sub_address->address_line_1) && isset($sub_address->city) && isset($sub_address->state) && isset($sub_address->postal_code) ) {
                        
                        $state_name = State::getStateName($sub_address->state);
                        $state_abbr = State::getStateAbbr($sub_address->state);
                            
                        $countyName = County::getCountyName($sub_address->county);
                            
                        if($state_abbr != "" && $state_abbr == "MD"){
                                $pdfLink = "/md-pdf";
                        } elseif ($state_abbr != "" && $state_abbr == "DC") {
                                $pdfLink = "/dc-pdf";
                        } elseif ($state_abbr != "" && $state_abbr == "VA" && $countyName== "Arlington") {
                                $pdfLink = "/arlington-pdf";                                
                        } elseif ($state_abbr != "" && $state_abbr == "VA") {
                                $pdfLink = "/fairfax-pdf";
                        }

                        $address = $sub_address->address_line_1.' '.@$sub_address->address_line_2.', '.$sub_address->city.', '.$state_name.', '.$sub_address->postal_code;

                        $subject_details[0]->sub_address = $address;

                        $address = str_replace(" ", "+", $address);
                        $region = "Austria";
                            
                        $json = file_get_contents("http://maps.google.com/maps/api/geocode/json?address=$address&sensor=false&region=$region");
                        $json = json_decode($json);
                        if (count($json)>0 && !empty($json->{'results'})) { 
                            $sub_lat = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
                            $sub_long = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};
                        }                               
                    }

                    if(count($subject_details)){
                        $sub_otherData = UserSearch::where('user_search_id', $token_details[0]->user_search_id)->where('end_date',null)->first();
                        $comDetail = SearchComparable::select('*')->where('subject_comps_detail_id', $subject_details[0]->subject_comps_id)->where('end_date',null)->first();
                 
                        $lookUpsDetail = SearchComparable::join('pf_lookups', 'pf_lookups.lookup_id', '=', 'search_comparables.lookup_id')->select('pf_lookups.*','search_comparables.*')->where('search_comparables.subject_comps_detail_id', $subject_details[0]->subject_comps_id)->get();
                            $subject_details[0]->lat = $sub_lat; 
                            $subject_details[0]->long = $sub_long; 
                            $subject_details[0]->comDetail=$comDetail;
                            $subject_details[0]->subjectOtherData=$sub_otherData;
                            $subject_details[0]->lookUpsDetail=$lookUpsDetail;
                            
                    }else{
                        $subject_details[0]->lat = 0; 
                        $subject_details[0]->long = 0;
                    }
                        
                    $comparablesList = SubjectCompsDetail::where(array('ref_object_id' => $token_details[0]->user_search_id, 'system_object_type_id' => '3'))->where('end_date',null)->limit(3)->get();
                    //echo count($comparablesList);exit;
                    if (count($comparablesList)>0) {
                        foreach ($comparablesList as $key => $subComparable) {
                            $subjectCompsDetail['comparables'][$key] = $subComparable;
                                
                            $comDetails = SearchComparable::select('*')->where('subject_comps_detail_id', $subComparable->subject_comps_id)->where('end_date',null)->first();
                            $com_address = PfAddress::select('mobile_number','address_line_1','address_line_2','address_line_3','city','postal_code','state','county')->where(array('ref_object_id' => $comDetails->search_comparable_id, 'system_object_type_id'=> '3', 'address_type' => '1', 'end_date' => null))->first();
                                
                            $subjectCompsDetail['comparables'][$key]->com_address = $comDetails->search_comparable_id; 
                            $subjectCompsDetail['comparables'][$key]->com_details = $comDetails; 
                        
                                
                            if( isset($com_address->address_line_1) && isset($com_address->city) && isset($com_address->state) && isset($com_address->postal_code) ) {

                                $state_name = State::getStateName($com_address->state);

                                $address = $com_address->address_line_1.' '.$com_address->city.', '.$state_name.', '.$com_address->postal_code;

                                $subjectCompsDetail['comparables'][$key]->com_address = $address;

                                $address = str_replace(" ", "+", $address);
                                $region = "Austria";
                                    
                                $json = file_get_contents("http://maps.google.com/maps/api/geocode/json?address=$address&sensor=false&region=$region");
                                    $json = json_decode($json);
                                if (count($json)>0 &&  !empty($json->{'results'})) {    

                                    $lat = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
                                    $long = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};

                                }
                            }
                            if(count($subjectCompsDetail['comparables'][$key])){
                                $subjectCompsDetail['comparables'][$key]->lat = $lat; 
                                $subjectCompsDetail['comparables'][$key]->long = $long; 
                            }else{
                                $subjectCompsDetail['comparables'][$key]->lat = 0; 
                                $subjectCompsDetail['comparables'][$key]->long = 0; 
                            }
                                
                        }
                                
                    }
                    
                    $subjectCompsDetail['subject'] = $subject_details[0];
                    $subjectCompsDetail['pdf_link'] = $pdfLink;
                    return $subjectCompsDetail;
                            
                } else{ 
                       return array('message'=>'No data');
                }
                    
            } else {
                 return array('message'=>'invalid token');
                    //return redirect('/invalid-token');
                    return array();
            }
        }
        catch (\Exception $e) {
            $result = ['exception_message' => $e->getMessage()];
            return array('message'=>$result);
            return view('errors.error', $result);
        }       
    }
}