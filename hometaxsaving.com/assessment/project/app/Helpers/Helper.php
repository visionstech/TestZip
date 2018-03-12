<?php
namespace App\Helpers;

use DB;
use App\PfSystemObjectType;
use App\PfHighKey;
use Auth;
use App\PfLookup;
use Twilio\Rest\Client;
use Config;
use Mail;
use Session;
use App\State;


class Helper{
    public static function getHighKey($tableName, $columnName, $created_by)
    {        
        $check_pf_high_key = PfHighKey::where('table_name', $tableName)->where('column_name', $columnName)->where('end_date', null)->get();
        if(count($check_pf_high_key)) {
            $pf_high_key = $check_pf_high_key[0]->high_key + 1;
            $increment_pf_high_key = PfHighKey::where('table_name', $tableName)->where('column_name', $columnName)->where('end_date', null)->update(['high_key' => $pf_high_key]);
        }
        else {
            $create_pf_high_key = PfHighKey::firstOrCreate([
                'key_description' => 'High Key for table '.ucfirst($tableName),
                'schema_name'     => 'taxwizard',
                'table_name'      => $tableName,
                'column_name'     => $columnName,
                'high_key'        => '1',
                'created_by'      => $created_by,
                'updated_by'      => $created_by,
            ]);
            $pf_high_key = $create_pf_high_key->high_key;
        }
        
        return $pf_high_key;
        
        /*
        DB::table('pf_high_key')
                ->where([
                    ['table_name', '=', $tableName],
                    ['column_name', '=', $columnName]
                ])
                ->whereNull('end_date')
                ->increment('high_key');

        // The schema name is set here and can be taken as a constant from table PF_LOOKUPS 
        $highKey =  DB::table('pf_high_key')
                    ->where([
                        ['table_name', '=', $tableName],
                        ['column_name', '=', $columnName]
                    ])
                    ->whereNull('end_date')
                    ->value('high_key');
                
        return $highKey;
        */
    }
    
    
    public static function toGenerateCommunicationObjectTypes($tableName,$columnName,$name,$created_by)
    {
        $date = date("Y-m-d H:i:s");
        /*
        if (empty($_SESSION['PF_ORG_CONTACTS']['contact_id'])) {
            $createdBy = $user->uid;    // this should be the id of anonymous user
        }
        else {
            $createdBy = $_SESSION['PF_ORG_CONTACTS']['contact_id'];
        }
        */
        
        $data =  PfSystemObjectType::where('table_name', $tableName)
                    ->where('column_name', $columnName)
                    ->where('name', $name)
                    ->whereNull('end_date')
                    ->get(['system_object_type_id']);

        if(count($data)) {  
            $systemobjecttypeid = $data[0]['system_object_type_id'];
        }
        else {    
            $systemobjecttypeid =  self::getHighKey('pf_system_object_types','system_object_type_id', $created_by);
            $pfsystemobject_create = PfSystemObjectType::create([
                'system_object_type_id' =>  (empty($systemobjecttypeid)) ? NULL : $systemobjecttypeid,
                'name'                  =>  $name,
                'description'           =>  '',
                'table_name'            =>  $tableName,
                'column_name'           =>  $columnName,
                'created_by'            =>  $created_by,
                'updated_by'            =>  $created_by,
            ]); 
        }

        return $systemobjecttypeid;

    }
    
    public static function getChildLookupDetail($parent_lookup_id) {
        $response = [];
        $child_lookup_details = PfLookup::where('parent_lookup_id', $parent_lookup_id)->get();
        if(count($child_lookup_details)) {
            $response = $child_lookup_details[0];
        }
        return $response;
    }
	
    // Function to send SMS when the notice date is changed in the system through admin
    // This uses the Tilio API. SID and Token will be stored in database

    public static function sendSMS($customerList){
		
        // Your Account SID and Auth Token from twilio.com/console
        $sid = Config::get('constants.smsSid');
        $token = Config::get('constants.smsToken');
        $client = new Client($sid, $token);

        // Get the user phone numbers for this jurisdiction who have visited our site.
        // Details picked from user table
        foreach($customerList as $customer){

                $sms = $client->messages->create(
                // the number you'd like to send the message to
                '+91'.$customer['mobile_number'],
                array(
                // A Twilio phone number you purchased at twilio.com/console
                'from' => Config::get('constants.smsFromNumber'),
                // the body of the text message you'd like to send
                'body' => 'Hello '.$customer['name'].'. The assessments for your county are available now. Please review your tax liability and try to save.'
                )
                );
        }
		
    }
    

    public static function getUserDetailsByJurisdiction($jurisdiction){
    // Select all user email, mobile numbers where user belongs to the given jurisdiction

            $stateId = $jurisdiction['state_id'];
            $countyId = $jurisdiction['county_id'];
            $results = '';
            if (empty($stateId) || empty($countyId)){

            // log the details
                    $results = "State or county not defined";
            }
            else{
                     $results = DB::table('users')
                            ->join('pf_addresses', 'users.id', '=', 'pf_addresses.ref_object_id')
            ->select('users.id', 'users.name', 'users.email','pf_addresses.mobile_number')
                            ->where([
                                                    ['pf_addresses.state','=',$stateId],
                                                    ['pf_addresses.county','=',$countyId]
                                            ])
                            ->whereNull('pf_addresses.end_date')
                            ->get();
            }
            return $results;
    }
	
	public static function getUserDetailsUsingSearchId($user_search_id){
			// returns an array
			$user_details =	DB::table('users')
                            ->join('user_searches', 'users.id', '=', 'user_searches.system_user_id')
							->select('users.id', 'user_searches.first_name', 'user_searches.last_name', 'user_searches.token','users.email')
                            ->where('user_search_id', $user_search_id)
                            ->whereNull('user_searches.end_date')
							->whereNull('users.end_date')
                            ->get();
			if ($user_details != null && !empty($user_details))
				return $user_details[0];
			else return '';
	}
    
     	
    public static function sendMail($data, $mail_to, $file_path=null)
    {	
        $emailcontent = array(
            'content' => $data['content'],
            'subject' => $data['subject'],
            'username' => $data['username'],
            'user_name' => (isset($data['user_name'])) ? $data['user_name'] : '',
        );

        Mail::send('emails.email_template', $emailcontent, function($message) use ($data, $mail_to, $file_path)
        {
            $message->to($mail_to)->subject($data['subject']);
            if($file_path != null) {
                $message->attach($file_path);
            }   
        });
    }
	
    // Function to get lookupTypeId for a lookuptypename
    public static function getLookupTypeIdFromName($lookupTypeName) {
        $response = '';
        $lookupTypeId = DB::table('pf_lookup_types')
                        ->select('lookup_type_id')
                        ->where('name', $lookupTypeName)
                        ->whereNull('end_date')
                        ->get();
        if(count($lookupTypeId)) {
            $response = $lookupTypeId;
        }
        return $response;
    }
    
    // Function to get lookupId for a lookupname
    public static function getLookupIdFromName($lookupTypeName, $where_condition = []) {
        $response = '';
        $lookupId = DB::table('pf_lookups')
                        ->select('lookup_id')
                        ->where('name', $lookupTypeName)
                        ->where($where_condition)
                        ->whereNull('end_date')
                        ->get();
        if(count($lookupId)) {
            $response = $lookupId;
        }
        return $response;
    }
	
    public static function checkIfAdjustmentSetting($lookupTypeId){

            $lookupTypeIdList =  DB::table('pf_lookup_types')
                                ->where('name', 'like', 'Adjustment%')
                                ->whereNull('end_date')
                                ->pluck('lookup_type_id');


            if (in_array($lookupTypeId, $lookupTypeIdList)){
                    return 1;
            }
            else{
                    return 0;
            }
    }
    
    
    public static function corelogicApiXML($api_values, $makePayment='')
    {
        $currentDate = date('Y-m-d');
        $toDate = date('Y-m-d', strtotime('-1 day', strtotime($currentDate)));
        $finalToDate = trim(str_replace('-', '', $toDate));

        $saleDateFromDate = "";
        if(isset($api_values['saleDateFromDate'])){
            $saleDateFromDate = $api_values['saleDateFromDate'];

        }

        $detailedSubjectReport = "Y";
        $detailedComparableReport = "Y";
        if(isset($api_values['searchType']) && $api_values['searchType']=='subject' ){
            $detailedSubjectReport = 'Y';
            $detailedComparableReport = 'N';
            $api_values['LandUse'] = '<_LAND_USE _ResidentialType = "AllResidentialTypes" />';
        }elseif (isset($api_values['searchType']) && $api_values['searchType']=='comparable' ) {
            $detailedSubjectReport = 'N';
            $detailedComparableReport = 'Y';
        }

        $saleDateToDate = "";
        if(isset($api_values['saleDateToDate'])){
            if($api_values['saleDateToDate'] > $finalToDate){
                $saleDateToDate = $finalToDate;
            } else {
                $saleDateToDate = $api_values['saleDateToDate'];
            }
        }

		if ( isset($api_values['square_footage']) && $api_values['square_footage'] != ""){
			//_LivingAreaVariancePercent = "'.$api_values['LivingAreaVariancePercent'].'"
			
            if($api_values['square_footage']>0){ 
               $livingAreaFrom = ($api_values['square_footage']*$api_values['LivingAreaVariancePercent'])/100;
               $livingAreaTo= intval($livingAreaFrom)+intVal($api_values['square_footage']);
               $_LivingAreaVariancePercent = '_LivingAreaFromNumber = "'.$livingAreaFrom.'"
                                               _LivingAreaToNumber = "'.$livingAreaTo.'"'; 
            }
            else{
               $_LivingAreaVariancePercent = '_LivingAreaVariancePercent = "'.$api_values['LivingAreaVariancePercent'].'"'; 
            }

		} else{
			$subjectReport = 'N';
			$compReport = 'Y';
		}
		
        $response['input_xml'] = '<?xml version="1.0" encoding="UTF-8"?>
                    <!DOCTYPE REQUEST_GROUP SYSTEM "C2DRequestv2.0.dtd">
                    <REQUEST_GROUP
                        MISMOVersionID="2.1">

                        <REQUEST
                            LoginAccountIdentifier="KEITH001@MCINTOSHTAXLLC.COM"
                            LoginAccountPassword="welcome1"
                                    >

                            <REQUESTDATA>
                                <PROPERTY_INFORMATION_REQUEST>
                                    <_CONNECT2DATA_PRODUCT
                                        _DetailedSubjectReport="'.$detailedSubjectReport.'"
                                        _DetailedComparableReport="'.$detailedComparableReport.'"
                                        _IncludeSearchCriteriaIndicator="Y"
                                        _IncludePDFIndicator="N"/>

                                    <_PROPERTY_CRITERIA
                                        _StreetAddress="'.$api_values['street'].'"
                                        _City="'.$api_values['city'].'"
                                        _State="'.$api_values['state'].'"
                                        _PostalCode="'.$api_values['zipcode'].'"/>';
                                
                                if (isset($api_values['searchType']) && $api_values['searchType']=='comparable' ) {
                                   $response['input_xml'] .= '<_SEARCH_CRITERIA>
                                        <_SUBJECT_SEARCH />
                                        <_COMPARABLE_SEARCH
                                            _LotSizeFromNumber = "1"
                                            _LotSizeToNumber = "'.$api_values['LotSizeToNumber'].'" 
                                            _DistanceFromSubjectNumber = "'.$api_values['DistanceFromSubjectNumber'].'"
                                            _SaleDateFromDate = ""
                                            _SaleDateToDate = ""
                                            _SalePriceFromAmount = ""
                                            _SalePriceToAmount = ""
                                            _PoolOptionType = "PropertiesWithAndWithoutPools"
                                            _LastSaleDateFrom="'.$saleDateFromDate.'"
                                            _LastSaleDateTo="'.$saleDateToDate.'"
                                            _BedroomsFromNumber = ""
                                            _BedroomsToNumber = ""
                                            _BathroomsFromNumber = ""
                                            _BathroomsToNumber = ""
                                            _CompFarmRecCountOnly = "N"
                                            _IncludeStreetMapIndicator = "N"
                                            '.$_LivingAreaVariancePercent.'
                                            >
                                            '.$api_values['LandUse'].'
                                            
                                        </_COMPARABLE_SEARCH>
                                    </_SEARCH_CRITERIA>';
                                }
                                    $response['input_xml'] .='<_RESPONSE_CRITERIA
                                        _NumberComparablesType="'.$api_values['NumCompsReturned'].'" />

                               </PROPERTY_INFORMATION_REQUEST>

                            </REQUESTDATA>

                        </REQUEST>

                    </REQUEST_GROUP>';
        
        $response['url'] = 'https://xml.connect2data.com/';
        //echo "<pre>"; print_r($response); die;
        return $response;
    }
    
    /**
      * Generates Random String Will be used as Strong Token Generator.
      * @param Length $length of Password            
      * @return Response
    **/
    public static function getSessionToken()
    {        
        $session_token = null;
        if(Session::has('token')) {
            $session_token = Session::get('token');
        }
        return $session_token;
    }
	
	/**
      * Generates Random String Will be used as Strong Token Generator.
      * @param Length $length of Password            
      * @return Response
    **/
    public static function getSessionPhase2Token()
    {    
		// Added on 26th Oct 2017 for retrieving session for Phase 2
        $session_token = null;
        if(Session::has('phase2_token')) {
            $session_token = Session::get('phase2_token');
        }
        return $session_token;
    }
    
    /**
      * Return redirect url for token.
      * @param Length $token 
      * @return Response
    **/
    public static function getRedirectUrlForToken($active_page)
    {        
        if($active_page == '1') {
            return redirect('/make-payment/');
        }
        else if($active_page == '2') {
            return redirect('/verify-address/');
        }
        else if($active_page == '3') {
            return redirect('/assessment-review/');
        }
		/* Added on 25th Oct for addition of Phase2 Make Payment page */
		else if($active_page == '4') {
            return redirect('/phase2-payment/');
        }
        else if($active_page == '0') {
            return redirect('/search-address/');
        }
        else {
            return redirect('/');
        }    
    }
	
	/**
      * Generates Random String Will be used as Strong Token Generator.
      * @param Length $length of Password            
      * @return Response
    **/
    public static function getCustomerToken($length=null)
    {        
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    // Function to get lookup value for a lookupname
    public static function getConfigurableItemValue() {
        $lookup_type_id = 8;
        $response = '';
        $lookupValue = DB::table('pf_lookups')
                        ->select('name', 'value')
                        ->where('lookup_type_id', $lookup_type_id)
                        ->whereNull('end_date')
                        ->get();
        if(count($lookupValue)) {
            $response = $lookupValue;
        }
        return $response;
    }
	
	public static function getMemberDetailsList(){
			// returns an array
			$system_object_type_id = Helper::toGenerateCommunicationObjectTypes('pf_lookups', 'name', 'address', Auth::user()->id);
		$lookup_type = Helper::getLookupTypeIdFromName(Config::get('constants.constants.lookup_type.address'));
        $where_condition['lookup_type_id'] = $lookup_type[0]->lookup_type_id;
        $address_type = Helper::getLookupIdFromName(Config::get('constants.constants.lookup.address.billing_address'), $where_condition);
			$user_details =	DB::table('users')
                            ->join('user_searches', 'users.id', '=', 'user_searches.system_user_id')
							->join('pf_addresses', 'user_searches.user_search_id', '=', 'pf_addresses.ref_object_id')
							->select('users.id', 'user_searches.first_name', 'user_searches.last_name','users.email', 'users.created_at','pf_addresses.mobile_number')
                            ->where('user_type','=',Config::get('constants.member'))
							->where('pf_addresses.address_type','=',$address_type[0]->lookup_id)
							->where('pf_addresses.system_object_type_id','=',$system_object_type_id)
            
							->whereNull('pf_addresses.end_date')
                            ->whereNull('user_searches.end_date')
							->whereNull('users.end_date')
							->distinct()
                            ->paginate(10);
			if ($user_details != null && !empty($user_details))
				return $user_details;
			else return '';
	}
	
	public static function getMemberSearchHistory($userId){
		$system_object_type_id = Helper::toGenerateCommunicationObjectTypes('pf_lookups', 'name', 'address', Auth::user()->id);
		$lookup_type = Helper::getLookupTypeIdFromName(Config::get('constants.constants.lookup_type.address'));
        $where_condition['lookup_type_id'] = $lookup_type[0]->lookup_type_id;
        $address_type = Helper::getLookupIdFromName(Config::get('constants.constants.lookup.address.search_address'), $where_condition);
		// returns an array
		$user_details =	DB::table('user_searches')
            ->join('pf_addresses', 'user_searches.user_search_id', '=', 'pf_addresses.ref_object_id')
			->select('user_searches.first_name', 'user_searches.last_name', 'user_searches.appeal_amount','user_searches.created_at','user_searches.total_assessment_value','user_searches.token','user_searches.phase2_token','user_searches.active_page','user_searches.appeal_year','user_searches.status','user_searches.no_appeal_recommendation','pf_addresses.address_line_1','pf_addresses.address_line_2','pf_addresses.address_line_3','pf_addresses.city', 'pf_addresses.postal_code','pf_addresses.state')
			->where('user_searches.system_user_id', $userId)
            ->where('pf_addresses.address_type','=',$address_type[0]->lookup_id)
            ->where('pf_addresses.system_object_type_id','=',$system_object_type_id)
            ->whereNull('user_searches.end_date')
			->whereNull('pf_addresses.end_date')
            ->orderBy('user_searches.user_search_id', 'DESC')
            ->get();
		if ($user_details != null && !empty($user_details))
			return $user_details;
		else return '';
	}
	
		
	public static function getMemberDetailsUsingUserId($user_id){
			// returns an array
		$system_object_type_id = Helper::toGenerateCommunicationObjectTypes('pf_lookups', 'name', 'address', Auth::user()->id);
		$lookup_type = Helper::getLookupTypeIdFromName(Config::get('constants.constants.lookup_type.address'));
        $where_condition['lookup_type_id'] = $lookup_type[0]->lookup_type_id;
        $address_type = Helper::getLookupIdFromName(Config::get('constants.constants.lookup.address.billing_address'), $where_condition);
		$user_details =	DB::table('users')
                            ->join('user_searches', 'users.id', '=', 'user_searches.system_user_id')
							->join('pf_addresses', 'user_searches.user_search_id', '=', 'pf_addresses.ref_object_id')
							->select('users.id', 'users.email','user_searches.first_name', 'user_searches.last_name','user_searches.user_search_id','pf_addresses.address_line_1','pf_addresses.address_line_2','pf_addresses.address_line_3','pf_addresses.city', 'pf_addresses.postal_code','pf_addresses.mobile_number','pf_addresses.state','pf_addresses.receive_notification','pf_addresses.county','pf_addresses.address_type')
							->where('user_type','=',Config::get('constants.member'))
                            ->where('users.id', $user_id)
							->where('pf_addresses.address_type','=',$address_type[0]->lookup_id)
							->where('pf_addresses.system_object_type_id','=',$system_object_type_id)
							->whereNull('pf_addresses.end_date')
                            ->whereNull('user_searches.end_date')
							->whereNull('users.end_date')
                            ->get();
        //echo "<pre>22";print_r($user_details);exit;                  
		if ($user_details != null && !empty($user_details)){
			return $user_details[0];	
		}
			
		//else return '';
	}

    public static function getSearchDetailsWithToken($token){
		
		$lookup_type = Helper::getLookupTypeIdFromName(Config::get('constants.constants.lookup_type.address'));
        $where_condition['lookup_type_id'] = $lookup_type[0]->lookup_type_id;
        $address_type = Helper::getLookupIdFromName(Config::get('constants.constants.lookup.address.search_address'), $where_condition);
		
		$system_object_type_id = self::getSystemObjectTypeId('name','pf_lookups','name');
		
        if (!empty($token)) {
            $user_details = DB::table('user_searches')
                ->join('pf_addresses', 'user_searches.user_search_id', '=', 'pf_addresses.ref_object_id')
                ->join('users', 'users.id', '=', 'user_searches.system_user_id')
                ->select('pf_addresses.state', 'user_searches.system_user_id', 'user_searches.user_search_id', 'users.email','pf_addresses.*','user_searches.*')
                ->where('user_searches.token', $token)
                ->where('pf_addresses.address_type','=',$address_type[0]->lookup_id)
                ->where('pf_addresses.system_object_type_id','=','1')
                ->whereNull('user_searches.end_date')
                ->whereNull('pf_addresses.end_date')
                ->first();
            if ($user_details != null && !empty($user_details)) {
                return $user_details;
            } else {
                return '';
            }
        } else {
            return '';
        }
    }
	
	public static function getSystemObjectTypeId($name, $table_name, $column_name){
		
		$objectTypeDetails = DB::table('pf_system_object_types')
                            ->select('system_object_type_id')
                ->where('name', '=', $name)
                ->where('table_name','=',$table_name)
                ->where('column_name','=',$column_name)
                ->whereNull('end_date')
                ->get();
		if ($objectTypeDetails != null && !empty($objectTypeDetails)){
			return $objectTypeDetails[0];
		}else return '';
		
	}

    public static function getBillingDetail($userId){
         $Address=array();
        $Address = DB::table('user_searches')
                ->join('pf_addresses', 'user_searches.user_search_id', '=', 'pf_addresses.ref_object_id')
                ->join('users', 'users.id', '=', 'user_searches.system_user_id')
                ->select('pf_addresses.state','pf_addresses.mobile_number', 'user_searches.system_user_id', 'user_searches.user_search_id', 'users.email','pf_addresses.*','user_searches.*')
                ->where('user_searches.system_user_id', $userId)
                ->where('pf_addresses.address_type','=',2)
                ->get();
        if( $Address != null){
             return $Address;
        }
        
    }
    
    public static function getSearchDetail($user_search_id){
        
        if (!empty($user_search_id)) {
            $user_details = DB::table('user_searches')
                ->select('*')
                ->where('user_searches.user_search_id', $user_search_id)
                ->whereNull('user_searches.end_date')
                ->first();
            if ($user_details != null && !empty($user_details)) {
                return $user_details;
            } else {
                return '';
            }
        }
    }
	
	public static function getStatusForMemberSearch($search){
		$search->phase1_pay_amount = Config::get('constants.phase1Amt');
		$search->phase2_pay_amount = Config::get('constants.phase2Amt');
		$continue_link = url('/'); 
        $status = "-";
		$msg = "-";
		if($search->active_page == '1'){
			$continue_link = url('/make-payment/'.$search->token); 
			$status = "Payment( $".$search->phase1_pay_amount." ) Pending"; 
            $msg = "make-payment";
		}
		elseif($search->active_page == '2'){
			$continue_link = url('/verify-address/'.$search->token); 
            //$status = "Verify address";         
			$status = "Search address verification pending.";
            $msg = "verify-address";	
					           		
		}
		elseif($search->active_page == '3' && $search->phase2_token == '' && $search->no_appeal_recommendation  == '1'){
            
            $continue_link = url('/assessment-review/'.$search->token); 
            $status = "Appeal not recommended";
            $msg = "assessment-review"; 
        }

        elseif($search->active_page == '3' && $search->phase2_token == '' && $search->no_appeal_recommendation  == '0'){
			
			$continue_link = url('/assessment-review/'.$search->token); 
			$status = "Appeal recommended and Payment ( $".$search->phase2_pay_amount." ) Pending";
            $msg = "assessment-review";    
		}			           		
		elseif($search->active_page == '4'){
						        
            $continue_link = url('/phase2-payment/'.$search->token); 
       		$status = "Appeal recommended and Payment ( $".$search->phase2_pay_amount." ) Pending";
            $msg = "phase2-payment";    
					           	
		}
		elseif($search->phase2_token != '0' && $search->phase2_token != null){
						        
       		$continue_link = url('/top_comparables_list/'.$search->phase2_token); 
       		$status = "View and Download Reports";
		}		           	
        elseif($search->active_page == '0' && $search->status == '0'){
        
       		$continue_link = url('/search-address/'.$search->token); 
       		$status = "Payment( $".$search->phase1_pay_amount." ) Pending";
            $msg = "search-address";
       	
		} 
        elseif($search->active_page == '0' && $search->status == '1'){
        
       		$continue_link = url('/search-address/'.$search->token); 
       		$status = "Address not supported";
            $msg = "search-address";
		}
        elseif($search->active_page == '0' && $search->status == '2'){
        
       		$continue_link = url('/search-address/'.$search->token); 
       		$status = "Assessment not ready";
            $msg = "search-address";
       	}
        else{
			$continue_link = url('/'); 
        }
						        
		$response = ["status" => $status, "link" => $continue_link, "msg" => $msg];
        return $response;
	}
	
	public static function updateProfileDetails($addressDetails){
		
		try {  

		
        DB::beginTransaction();
		// Update the default i.e. billing address in pf_addresses
	   $lookup_type = Helper::getLookupTypeIdFromName(Config::get('constants.constants.lookup_type.address'));
        $where_condition['lookup_type_id'] = $lookup_type[0]->lookup_type_id;
        $address_type = Helper::getLookupIdFromName(Config::get('constants.constants.lookup.address.billing_address'), $where_condition);
		DB::table('pf_addresses')
			->where([
					['ref_object_id','=',$addressDetails['user_search_id']],
					['address_type','=',$addressDetails['address_type']],
					])
			->whereNull('end_date')
			->update([
				'receive_notification' => $addressDetails['receive_notification'],
				'address_line_1' => $addressDetails['address_line_1'],
				'address_line_2' => $addressDetails['address_line_2'],
				'city' => $addressDetails['city'],
				'state' => $addressDetails['state'],
				'county' => $addressDetails['county'],
				'postal_code' => $addressDetails['zipcode']
				]);
				
		// Update the receive_notification field for First search address
		// Get the search_address address_type
		$system_object_type_id = Helper::toGenerateCommunicationObjectTypes('pf_lookups', 'name', 'address', $addressDetails['user_id']);
		
		$lookup_type = Helper::getLookupTypeIdFromName(Config::get('constants.constants.lookup_type.address'));
        $where_condition['lookup_type_id'] = $lookup_type[0]->lookup_type_id;
        $address_type = Helper::getLookupIdFromName(Config::get('constants.constants.lookup.address.search_address'), $where_condition);
	
	
		DB::table('pf_addresses')
				->where([
					['ref_object_id','=',$addressDetails['user_search_id']],
					//['address_type','=',$address_type[0]->lookup_id],
					//['system_object_type_id','=',$system_object_type_id],
					])
				->whereNull('end_date')
			/*	->orderBy('pf_addresses.user_search_id', 'ASC')
				-first()*/
				->update(['receive_notification' => $addressDetails['receive_notification']]);
				
			DB::commit(); 
			return 1;
		}catch (\Exception $e) 
        {   
            DB::rollback();
			return 0;
		}
	}

    public static function corelogicApiXMLTest($api_values)
    {
        $currentDate = date('Y-m-d');
        $toDate = date('Y-m-d', strtotime('-1 day', strtotime($currentDate)));
        $finalToDate = trim(str_replace('-', '', $toDate));

        $saleDateFromDate = "";
        if(isset($api_values['saleDateFromDate'])){
            $saleDateFromDate = $api_values['saleDateFromDate'];

        }

        $saleDateToDate = "";
        if(isset($api_values['saleDateToDate'])){
            if($api_values['saleDateToDate'] > $finalToDate){
                $saleDateToDate = $finalToDate;
            } else {
                $saleDateToDate = $api_values['saleDateToDate'];
            }
        }
        $parcelValueFrom = '';
        $parcelValueTo = '';
        if(!empty($api_values['parcel_size_value']) && $api_values['parcel_size_value'] > 0){
            $parcelValueFrom = '0';
            $parcelValueTo = $api_values['parcel_size_value'];
        }

        $response['input_xml'] = '<?xml version="1.0" encoding="UTF-8"?>
                    <!DOCTYPE REQUEST_GROUP SYSTEM "C2DRequestv2.0.dtd">
                    <REQUEST_GROUP
                        MISMOVersionID="2.1">

                        <REQUEST
                            LoginAccountIdentifier="MCINTOSHSTAGE1"
                            LoginAccountPassword="welcome1234"
                                    >

                            <REQUESTDATA>
                                <PROPERTY_INFORMATION_REQUEST>
                                    <_CONNECT2DATA_PRODUCT
                                        _DetailedSubjectReport="Y"
                                        _DetailedComparableReport="Y"
                                        _IncludeSearchCriteriaIndicator="Y"
                                        _IncludePDFIndicator="N"/>

                                    <_PROPERTY_CRITERIA
                                        _StreetAddress="'.$api_values['street'].'"
                                        _City="'.$api_values['city'].'"
                                        _State="'.$api_values['state'].'"
                                        _PostalCode="'.$api_values['zipcode'].'"/>

                                    <_SEARCH_CRITERIA>
                                        <_SUBJECT_SEARCH />
                                        <_COMPARABLE_SEARCH
                                            _DistanceFromSubjectNumber = "'.$api_values['DistanceFromSubjectNumber'].'"
                                            _SaleDateFromDate = ""
                                            _SaleDateToDate = ""
                                            _SalePriceFromAmount = ""
                                            _SalePriceToAmount = ""
                                            _LivingAreaFromNumber = ""
                                            _LivingAreaToNumber = ""
                                            _PoolOptionType = "PropertiesWithAndWithoutPools"
                                            _LastSaleDateFrom="'.$saleDateFromDate.'"
                                            _LastSaleDateTo="'.$saleDateToDate.'"
                                            _LivingAreaVariancePercent = "'.$api_values['LivingAreaVariancePercent'].'"
                                            _BedroomsFromNumber = ""
                                            _BedroomsToNumber = ""
                                            _BathroomsFromNumber = ""
                                            _BathroomsToNumber = ""
                                            _LotSizeFromNumber = "'.$parcelValueFrom.'"
                                            _LotSizeToNumber = "'.$parcelValueTo.'"
                                            _CompFarmRecCountOnly = "N"
                                            _IncludeStreetMapIndicator = "N">
                                            '.$api_values['LandUse'].'
                                        </_COMPARABLE_SEARCH>
                                    </_SEARCH_CRITERIA>

                                    <_RESPONSE_CRITERIA
                                        _NumberComparablesType="'.$api_values['NumCompsReturned'].'" />

                               </PROPERTY_INFORMATION_REQUEST>

                            </REQUESTDATA>

                        </REQUEST>

                    </REQUEST_GROUP>';
        
        $response['url'] = 'https://staging.connect2data.com/';
        
        return $response;
    }	

    /** get current search status with token */
    public static function getSearchCurrentStatus($token = ""){ 
        if (!empty($token)) {
            $user_details = DB::table('user_searches')
                ->select('user_searches.*')
                ->where('user_searches.token', $token)
                ->whereNull('user_searches.end_date')
                ->first();
            if ($user_details != null && !empty($user_details)) {
                $searchStatus = Helper::getStatusForMemberSearch($user_details);
                //echo "<pre>"; print_r($searchStatus); 
                return $searchStatus;
            } else {
                return '';
            }
        } else {
            return '';
        }
    }
	
}
?>
