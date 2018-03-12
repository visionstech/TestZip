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
class AdjustmentController extends Controller {
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

    public function getTestApi()
    {
        try {
            return view('adjustment.test_api');
            
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
            
            $api_values['saleDateFromDate'] = "20170101";
            $api_values['saleDateToDate'] = "20171231";
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
            $api_values['searchType'] = 'subject';
            $xml_request = Helper::corelogicApiXMLTest($api_values);
            $response = $client->post($xml_request['url'], ['body' => $xml_request['input_xml']]);
            
            $result = json_decode(json_encode(simplexml_load_string($response->getBody())), true);
            $subject_property = "";
            if(isset($result['RESPONSE']['RESPONSE_DATA']['PROPERTY_INFORMATION_RESPONSE']['_PROPERTY_INFORMATION']) && count($result['RESPONSE']['RESPONSE_DATA']['PROPERTY_INFORMATION_RESPONSE']['_PROPERTY_INFORMATION']) > 0) {

                $subject_property = isset($result['RESPONSE']['RESPONSE_DATA']['PROPERTY_INFORMATION_RESPONSE']['_PROPERTY_INFORMATION'][0]) ? $result['RESPONSE']['RESPONSE_DATA']['PROPERTY_INFORMATION_RESPONSE']['_PROPERTY_INFORMATION'][0] : $result['RESPONSE']['RESPONSE_DATA']['PROPERTY_INFORMATION_RESPONSE']['_PROPERTY_INFORMATION'];
            }

            echo "<pre>"; print_r($result); die;
            
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

            return view('adjustment.test_api_comparables',compact('counties', 'states'));
            
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
            $api_values['parcel_size_value'] = (trim($request['parcel_size_value'])!="")?(trim($request['parcel_size_value'])*3):'';
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
                //echo "<pre>"; print_r(simplexml_load_string($response->getBody())); die;
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
                                $subject_property_details['no_appeal_message'] = "This sale is coming under sale_price < current_asse_value assessment hence there is no appeal";
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
                    
                    $subject_property_details['sub_ass_div_sf'] =  ($current_asse_value / $sub_living_area);

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

                                if(count($comparables_within_living_area) > 0){
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

                                        //$final_comparables = $step2_response['step2_final_comparables'];
                                    }
                                    /*echo "<pre>"; 
                                    print_r($subject_property_details);
                                    print_r($step2_response['step2_final_comparables']); 
                                    die;*/
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
                    $comparables_response['subject_property'] = $subject_property_details;
                    //echo "<pre>"; print_r($comparables_response); die;
                    $comparables_response['comparables']['all_comparables'] = $comparable_values;

                    Session::put('all_comparables',$comparables_response);
                }else{
                   //echo 'No comparables exist';
                }
                
            }
            //echo "<pre>"; print_r($comparables_response); die;
			
            return view('adjustment.comparables_result', $comparables_response);
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
                    /*if($step2_response['com_average'] > $step2_response['sale_price_divided_square_footage']){
                        $appeal_message = "Average of top closest sales is higher than assessment/SF hence no appeal recommendation";
                    } else {
                        $appeal_message = "Average of top closest sales is less than assessment/SF hence appeal recommended";
                    }*/
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
    
}