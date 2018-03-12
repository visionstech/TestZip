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
class CronController extends Controller {

    /*
    |--------------------------------------------------------------------------
    | Cron Controller
    |--------------------------------------------------------------------------
    |
    | This controller manages Notifications For Diffrent Properties.
    |
    */
    
    /**
      * Return VA Notifications
      * @param   
      * @return Response
    **/
    public function getVaNotifications()
    {
       //echo config('constants.in_out_default_message');exit;
        
       $currentDate=date('Y-m-d');
       $constantDays='34';
       $lookup_type = Helper::getLookupTypeIdFromName(config('constants.constants.lookup_type.address'));
       $where_condition['lookup_type_id'] = $lookup_type[0]->lookup_type_id;
       $SearchAddress = Helper::getLookupIdFromName(config('constants.constants.lookup.address.search_address'), $where_condition);
       $billingAddress = Helper::getLookupIdFromName(config('constants.constants.lookup.address.billing_address'), $where_condition);        
        
       //VA Addresses
       $stateAbbr_VA = State::where('state_abbr','VA')->get();   
       //echo $stateAbbr_VA;exit;   
       $getVAUsers = User::leftJoin('user_searches', 'users.id', '=', 'user_searches.system_user_id')->leftJoin('pf_addresses', 'pf_addresses.ref_object_id', '=', 'user_searches.user_search_id')
            ->leftJoin('counties', 'counties.county_id', '=', 'pf_addresses.county')
            ->select('users.id', 'users.name', 'users.email','pf_addresses.mobile_number','pf_addresses.address_line_1','pf_addresses.address_line_2','pf_addresses.city','pf_addresses.postal_code','pf_addresses.state','pf_addresses.county','counties.notice_date','counties.appeal_deadline_date','user_searches.first_name','user_searches.last_name','pf_addresses.mobile_number','counties.county_name')
            ->where('pf_addresses.address_type',$SearchAddress[0]->lookup_id)
            ->where('pf_addresses.state',$stateAbbr_VA[0]->state_id)
            //->where('pf_addresses.receive_notification',1)
            //->where('users.id',281)
            ->orderBy('users.id','asc')
            ->get();
        $userId=array();
        $VAUsersData=array();
       // echo count($getVAUsers);exit;
        if(count($getVAUsers)){
            foreach($getVAUsers as $key=>$VAValue){
                $userId[]=$VAValue->id;
                if(in_array($VAValue->id, $userId)){
                    $VAUsersData[$VAValue->id][$VAValue->county][]=$VAValue;
                }

            }
        }
        // echo count($VAUsersData);exit;
        //echo "<pre>e";print_r($VAUsersData);exit;
        foreach($VAUsersData as $key=>$firstData){
            if(!empty($firstData)){

                foreach ($firstData as $fkey => $fvalue) {
                   // echo "<pre>";print_r($fvalue);
                    $searchAddress=array();
                    $i=0;
                    foreach ($fvalue as $fvkey => $fvvalue) {
                        $searchAddress[] = $fvalue[$i]->address_line_1.' '.$fvalue[$i]->address_line_2.', '.$fvalue[$i]->city.', '.$fvalue[$i]->county_name.', VA, '.$fvalue[$i]->postal_code;
                    $i++;
                    }

                    $data = array (
                        'user_id'=>$fvalue[0]->id,
                        'county_id'=>$fvalue[0]->county,
                        'county_name'=>$fvalue[0]->county_name,
                        
                        'search_address'=> $searchAddress,
                        'state_name'=>$stateAbbr_VA[0]->state_name,
                        'state_abbr'=>'VA',
                        'appeal_deadline_date'=> ($fvalue[0]->appeal_deadline_date != null)?(date('F d, Y',strtotime($fvalue[0]->appeal_deadline_date))):'N/A',
                        'first_name'=>$fvalue[0]->first_name,
                        'last_name'=>$fvalue[0]->last_name,
                        'email'=>$fvalue[0]->email,
                        'mobile_number'=>$fvalue[0]->mobile_number
                    );
                    //echo "<pre>ee";print_r($data);exit;
                    $constantDays='35';
                    $noticeDate=date('m-d',strtotime($fvalue[0]->notice_date));
                    $currentYearNoticeDate=date('Y').'-'.$noticeDate;
                    $NotificationCronDate = date('Y-m-d', strtotime($currentYearNoticeDate. ' '.$constantDays.' days'));
                    //echo $NotificationCronDate;
                    //echo $NotificationCronDate.'dsfsdfs'.$currentDate;exit;
                    if(strtotime($NotificationCronDate)==strtotime($currentDate)){

                        Mail::send ( 'emails.sendgrid_template', $data, function ($message) {
                                //$message->from ('qachd15@gmail.com', 'Just Laravel');
                                $message->to ('kunal@visions.net.in')->subject ( 'VA State Reminder Notification' );
                        });
                        echo "VA Cron RUN<br/>";
                        //echo "<pre>sdfsdf";print_r($data);exit;
                    }else{
                        echo '<b>VA Notification Cron date : </b>'.$NotificationCronDate.' is not equal to <b>current date: </b>'.$currentDate.'<br/>';
                    }
                    $i++;  
                }
            }
        }       
    }

    /**
      * Return DC County Notifications
      * @param  none
      * @return Response
    **/

    public function getDcNotifications(){

        $currentDate=date('Y-m-d');
        $lookup_type = Helper::getLookupTypeIdFromName(config('constants.constants.lookup_type.address'));
        $where_condition['lookup_type_id'] = $lookup_type[0]->lookup_type_id;
        $SearchAddress = Helper::getLookupIdFromName(config('constants.constants.lookup.address.search_address'), $where_condition);
        $billingAddress = Helper::getLookupIdFromName(config('constants.constants.lookup.address.billing_address'), $where_condition); 
        //DC Addresses
        $stateAbbr_DC = State::where('state_abbr','DC')->get();      
        $getDCUsers = User::join('user_searches', 'users.id', '=', 'user_searches.system_user_id')->join('pf_addresses', 'pf_addresses.ref_object_id', '=', 'user_searches.user_search_id')
            ->join('counties', 'counties.county_id', '=', 'pf_addresses.county')
            ->select('users.id', 'users.name', 'users.email','pf_addresses.mobile_number','pf_addresses.address_line_1','pf_addresses.address_line_2','pf_addresses.city','pf_addresses.postal_code','pf_addresses.state','pf_addresses.county','counties.notice_date','counties.appeal_deadline_date','user_searches.first_name','user_searches.last_name','pf_addresses.mobile_number','counties.county_name')
            ->where('pf_addresses.address_type',$SearchAddress[0]->lookup_id)
            ->where('pf_addresses.state',$stateAbbr_DC[0]->state_id)
            //->where('pf_addresses.receive_notification',1)
            //->where('users.id',281)
            ->orderBy('users.id','asc')
            ->get();
        $userId=array();
        $DCUsersData=array();
        
        if(count($getDCUsers)){
            foreach($getDCUsers as $key=>$DCValue){
                $userId[]=$DCValue->id;
                //inarray
                if(in_array($DCValue->id, $userId)){
                    $DCUsersData[$DCValue->id][$DCValue->county][]=$DCValue;
                }
            }
        }

        //echo "<pre>e";print_r($DCUsersData);exit;
        foreach($DCUsersData as $key=>$firstData){
            if(!empty($firstData)){
                foreach ($firstData as $fkey => $fvalue) {
                    $searchAddress=array();
                    $i=0;
                    foreach ($fvalue as $fvkey => $fvvalue) {
                        $searchAddress[] = $fvalue[$i]->address_line_1.' '.$fvalue[$i]->address_line_2.', '.$fvalue[$i]->city.', '.$fvalue[$i]->county_name.', VA, '.$fvalue[$i]->postal_code;
                     $i++;
                    }
                    $data = array (
                        'user_id'=>$fvalue[0]->id,
                        'county_id'=>$fvalue[0]->county,
                        'county_name'=>$fvalue[0]->county_name,
                        
                        'search_address'=> $searchAddress,
                        'state_name'=>$stateAbbr_DC[0]->state_name,
                        'state_abbr'=>'VA',
                        'appeal_deadline_date'=> ($fvalue[0]->appeal_deadline_date != null)?(date('F d, Y',strtotime($fvalue[0]->appeal_deadline_date))):'N/A',
                        'first_name'=>$fvalue[0]->first_name,
                        'last_name'=>$fvalue[0]->last_name,
                        'email'=>$fvalue[0]->email,
                    );
                    $constantDays='35';
                    $noticeDate=date('m-d',strtotime($fvalue[0]->notice_date));
                    $currentYearNoticeDate=date('Y').'-'.$noticeDate;
                    $NotificationCronDate = date('Y-m-d', strtotime($currentYearNoticeDate. ' '.$constantDays.' days'));
                    if(strtotime($NotificationCronDate)==strtotime($currentDate)){
                    //Here Email Will Shoot
                        Mail::send ( 'emails.sendgrid_template', $data, function ($message) {
                                $message->from ( 'donotreply@demo.com', 'Just Laravel' );
                                $message->to ( 'kunal@visions.net.in' )->subject ( 'DC State Reminder Notification' );
                        });
                        echo "DC Cron RUN<br/>";
                        //echo "<pre>sdfsdf";print_r($data);exit;
                    }else{
                        echo '<b>DC Notification Cron date : </b>'.$NotificationCronDate.' is not equal to <b>current date: </b>'.$currentDate.'<br/>';
                    }
                    $i++;  
                }
            }
        }
    }

    /**
      * Return MD County Notifications For year-1, year-2 both
      * @param  none
      * @return Response
    **/
    public function getMdNotifications(){
        $currentDate=date('Y-m-d');
        $lookup_type = Helper::getLookupTypeIdFromName(config('constants.constants.lookup_type.address'));
        $where_condition['lookup_type_id'] = $lookup_type[0]->lookup_type_id;
        $SearchAddress = Helper::getLookupIdFromName(config('constants.constants.lookup.address.search_address'), $where_condition);
        $billingAddress = Helper::getLookupIdFromName(config('constants.constants.lookup.address.billing_address'), $where_condition); 
        //MD Addresses
        $stateAbbr_MD = State::where('state_abbr','MD')->get();

        $getMDUsers = User::join('user_searches', 'users.id', '=', 'user_searches.system_user_id')->leftJoin('pf_addresses', 'pf_addresses.ref_object_id', '=', 'user_searches.user_search_id')
            ->leftJoin('md_in_out_cycle', 'md_in_out_cycle.county_id', '=', 'pf_addresses.county')
            ->leftJoin('counties', 'counties.county_id', '=', 'pf_addresses.county')
            ->select('users.id', 'users.name', 'users.email','pf_addresses.mobile_number','pf_addresses.address_line_1','pf_addresses.address_line_2','pf_addresses.city','pf_addresses.postal_code','pf_addresses.state','pf_addresses.county','md_in_out_cycle.incycle_notice_date','md_in_out_cycle.incycle_deadline_date','md_in_out_cycle.outcycle_notice_date','md_in_out_cycle.outcycle_deadline_date','user_searches.cycle_type','user_searches.appeal_type','user_searches.latest_assesement_year','user_searches.user_search_id','pf_addresses.address_id','counties.county_name')
            //->select('users.id','user_searches.user_search_id','pf_addresses.address_id')
            ->where('pf_addresses.address_type',$SearchAddress[0]->lookup_id)
            ->where('pf_addresses.state',$stateAbbr_MD[0]->state_id)
            ->where('users.id',66)
           // ->where('user_searches.appeal_type','Year-1')
            
            //->where('pf_addresses.receive_notification',1)
            ->where('user_searches.latest_assesement_year','!=','')
            ->get();
        $userIdData=array();
        foreach($getMDUsers as $key=>$DCValue){           
            if(isset($userIdData[$DCValue->id.'_'.$DCValue->county.'_'.$DCValue->appeal_type.'_'.$DCValue->latest_assesement_year])){
                
                $Userkey=sizeof($userIdData[$DCValue->id.'_'.$DCValue->county.'_'.$DCValue->appeal_type.'_'.$DCValue->latest_assesement_year]);
                
                if($Userkey>0){
                    for($u=0;$u<=$Userkey;$u++){

                        if(($userIdData[$DCValue->id.'_'.$DCValue->county.'_'.$DCValue->appeal_type.'_'.$DCValue->latest_assesement_year][$u]['latest_assesement_year']<$DCValue->latest_assesement_year) && ($userIdData[$DCValue->id][$DCValue->county][$u]['search_address']==($DCValue->address_line_1.' '.$DCValue->address_line_2.', '.$DCValue->city.', '.$DCValue->county_name.', MD, '.$DCValue->postal_code))){
                           
                            $userIdData[$DCValue->id.'_'.$DCValue->county.'_'.$DCValue->appeal_type.'_'.$DCValue->latest_assesement_year][$u]['latest_assesement_year']=$DCValue->latest_assesement_year;
                        }else{
                            $userIdData[$DCValue->id.'_'.$DCValue->county.'_'.$DCValue->appeal_type.'_'.$DCValue->latest_assesement_year][$Userkey]['latest_assesement_year']=$DCValue->latest_assesement_year;
                            $userIdData[$DCValue->id.'_'.$DCValue->county.'_'.$DCValue->appeal_type.'_'.$DCValue->latest_assesement_year][$Userkey]['search_address']=($DCValue->address_line_1.' '.$DCValue->address_line_2.', '.$DCValue->city.', '.$DCValue->county_name.', MD, '.$DCValue->postal_code);
                            $userIdData[$DCValue->id.'_'.$DCValue->county.'_'.$DCValue->appeal_type.'_'.$DCValue->latest_assesement_year][$Userkey]['county']=($DCValue->county);
                            $userIdData[$DCValue->id.'_'.$DCValue->county.'_'.$DCValue->appeal_type.'_'.$DCValue->latest_assesement_year][$Userkey]['appeal_type']=($DCValue->appeal_type);
                            $userIdData[$DCValue->id.'_'.$DCValue->county.'_'.$DCValue->appeal_type.'_'.$DCValue->latest_assesement_year][$Userkey]['incycle_notice_date']=($DCValue->incycle_notice_date);
                            $userIdData[$DCValue->id.'_'.$DCValue->county.'_'.$DCValue->appeal_type.'_'.$DCValue->latest_assesement_year][$Userkey]['incycle_deadline_date']=($DCValue->incycle_deadline_date);
                            $userIdData[$DCValue->id.'_'.$DCValue->county.'_'.$DCValue->appeal_type.'_'.$DCValue->latest_assesement_year][$Userkey]['outcycle_notice_date']=($DCValue->outcycle_notice_date);
                            $userIdData[$DCValue->id.'_'.$DCValue->county.'_'.$DCValue->appeal_type.'_'.$DCValue->latest_assesement_year][$Userkey]['outcycle_deadline_date']=($DCValue->outcycle_deadline_date);
                            $userIdData[$DCValue->id.'_'.$DCValue->county.'_'.$DCValue->appeal_type.'_'.$DCValue->latest_assesement_year][$Userkey]['email']=($DCValue->email);
                            $userIdData[$DCValue->id.'_'.$DCValue->county.'_'.$DCValue->appeal_type.'_'.$DCValue->latest_assesement_year][$Userkey]['first_name']=($DCValue->first_name);
                            $userIdData[$DCValue->id.'_'.$DCValue->county.'_'.$DCValue->appeal_type.'_'.$DCValue->latest_assesement_year][$Userkey]['last_name']=($DCValue->last_name);
                            $userIdData[$DCValue->id.'_'.$DCValue->county.'_'.$DCValue->appeal_type.'_'.$DCValue->latest_assesement_year][$Userkey]['county_name']=($DCValue->county_name);
                            $userIdData[$DCValue->id.'_'.$DCValue->county.'_'.$DCValue->appeal_type.'_'.$DCValue->latest_assesement_year][$Userkey]['state_abbr']='MD';
                            $userIdData[$DCValue->id.'_'.$DCValue->county.'_'.$DCValue->appeal_type.'_'.$DCValue->latest_assesement_year][$Userkey]['mobile_number']=($DCValue->mobile_number);
                        }
                    }
                }
                
            }else{
                //Check
                $userIdData[$DCValue->id.'_'.$DCValue->county.'_'.$DCValue->appeal_type.'_'.$DCValue->latest_assesement_year][0]['latest_assesement_year']=$DCValue->latest_assesement_year;
                $userIdData[$DCValue->id.'_'.$DCValue->county.'_'.$DCValue->appeal_type.'_'.$DCValue->latest_assesement_year][0]['search_address']=($DCValue->address_line_1.' '.$DCValue->address_line_2.', '.$DCValue->city.', '.$DCValue->county_name.', MD, '.$DCValue->postal_code); 
                $userIdData[$DCValue->id.'_'.$DCValue->county.'_'.$DCValue->appeal_type.'_'.$DCValue->latest_assesement_year][0]['county']=($DCValue->county);
                $userIdData[$DCValue->id.'_'.$DCValue->county.'_'.$DCValue->appeal_type.'_'.$DCValue->latest_assesement_year][0]['appeal_type']=($DCValue->appeal_type); 
                $userIdData[$DCValue->id.'_'.$DCValue->county.'_'.$DCValue->appeal_type.'_'.$DCValue->latest_assesement_year][0]['incycle_notice_date']=($DCValue->incycle_notice_date);
                $userIdData[$DCValue->id.'_'.$DCValue->county.'_'.$DCValue->appeal_type.'_'.$DCValue->latest_assesement_year][0]['incycle_deadline_date']=($DCValue->incycle_deadline_date);
                $userIdData[$DCValue->id.'_'.$DCValue->county.'_'.$DCValue->appeal_type.'_'.$DCValue->latest_assesement_year][0]['outcycle_notice_date']=($DCValue->outcycle_notice_date);
                $userIdData[$DCValue->id.'_'.$DCValue->county.'_'.$DCValue->appeal_type.'_'.$DCValue->latest_assesement_year][0]['outcycle_deadline_date']=($DCValue->outcycle_deadline_date);
                $userIdData[$DCValue->id.'_'.$DCValue->county.'_'.$DCValue->appeal_type.'_'.$DCValue->latest_assesement_year][0]['email']=($DCValue->email);
                $userIdData[$DCValue->id.'_'.$DCValue->county.'_'.$DCValue->appeal_type.'_'.$DCValue->latest_assesement_year][0]['first_name']=($DCValue->first_name);
                $userIdData[$DCValue->id.'_'.$DCValue->county.'_'.$DCValue->appeal_type.'_'.$DCValue->latest_assesement_year][0]['last_name']=($DCValue->last_name);
                $userIdData[$DCValue->id.'_'.$DCValue->county.'_'.$DCValue->appeal_type.'_'.$DCValue->latest_assesement_year][0]['county_name']=($DCValue->county_name);
                $userIdData[$DCValue->id.'_'.$DCValue->county.'_'.$DCValue->appeal_type.'_'.$DCValue->latest_assesement_year][0]['state_abbr']='MD';
                $userIdData[$DCValue->id.'_'.$DCValue->county.'_'.$DCValue->appeal_type.'_'.$DCValue->latest_assesement_year][0]['mobile_number']=($DCValue->mobile_number);
                
            }
        }

        // Case 1 (Y1 Notification after 3 years)
        //echo "<pre>sdf";print_r($userIdData);exit;
        $constantDays='45';
        foreach ($userIdData as $key => $value) {
            $vars=explode('_', $key);
            $userId=$vars[0];
            $countyId=$vars[1];
            $AppealType=$vars[2];
            $Year=$vars[3];
            if($AppealType=='Year-1'){
                echo 'Year-1 Notification For 3 Modules<br/>';
                //echo "sdfsdfsdfsdfsdf";
                $noticeDate=$value[0]['incycle_notice_date'];
                //2015 2018 2021-2015==0
                $yearType=(date('Y')-$Year);
                if($yearType%3 == 0){
                    //echo $noticeDate.'<br/>';
                    $NotificationCronDate = date('Y-m-d', strtotime($noticeDate. ' '.$constantDays.' days'));
                    echo $NotificationCronDate.'--qq<br/>';
                    if(strtotime($NotificationCronDate)== strtotime($currentDate)){
                       // echo "sdfsdfsdf";
                        $dataEmail=array('search_address'=>$value);
                        //echo "<pre>dfgdfg";print_r($dataEmail);
                        //Email Will SHoot Here.
                        /*$sent = Mail::send ( 'emails.sendgrid_MD_Year1_notification_template', $dataEmail, function ($message) {
                        
                                    $message->to ( 'mohit@visions.net.in' )->subject ( 'Md Year-1 Notification' );
                            });
                        var_dump($sent);
                        if( ! $sent) dd("something wrong");
                        dd("send");*/
                    }

                }
                //Current year === Notice Year
                $Year2_noticeDate=$value[0]['outcycle_notice_date'];
                if(date('Y') == ($Year)){
                    echo 'Year-1 Notification For Current Year<br/>';
                    //Check Year-2 Notification and Sent Here.
                    $Y2_NotificationCronDate = date('Y-m-d', strtotime($Year2_noticeDate. ' '.$constantDays.' days'));
                    echo $Y2_NotificationCronDate.'--rr<br/>';
                    if(strtotime($Y2_NotificationCronDate)== strtotime($currentDate)){
                       $dataEmail=array('search_address'=>$value);
                       // echo "<pre>dfgdfg";print_r($dataEmail);
                        //Email Will SHoot Here.
                        /*$sent = Mail::send ( 'emails.sendgrid_MD_Year1_notification_template', $dataEmail, function ($message) {
                        
                                $message->to ( 'mohit@visions.net.in' )->subject ( 'Md Year-1 Notification' );
                            });
                        var_dump($sent);
                        if( ! $sent) dd("something wrong");
                        dd("send");*/  

                    }
                }
            }

            if($AppealType=='Year-2'){

                
                if(isset($userIdData[$userId.'_'.$countyId.'_'.'Year-1'.'_'.$Year])){
                    //echo "1111";
                    $Year1Data=$userIdData[$userId.'_'.$countyId.'_'.'Year-1'.'_'.$Year];
                    $Year2Data=$userIdData[$userId.'_'.$countyId.'_'.'Year-2'.'_'.$Year];
                
                    for($g=0;$g<sizeof($Year1Data);$g++){
                        for($r=0;$r<sizeof($Year2Data);$r++){
                            if($Year1Data[$g]['search_address']==$Year2Data[$r]['search_address']){                            
                                unset($Year2Data[$r]);
                            }
                        }                   
                    }

                    if(!empty($Year2Data)){

                        $Y2_noticeDate=$Year2Data[0]['outcycle_notice_date'];
                        $yearType=(date('Y')-$Year);
                        echo $Y2_noticeDate.'--tt<br/>';
                        if($yearType%3 == 0){
                            
                            $NotificationCronDate = date('Y-m-d', strtotime($Y2_noticeDate. ' '.$constantDays.' days'));
                            if(strtotime($NotificationCronDate)== strtotime($currentDate)){
                                $dataEmail=array('search_address'=>$Year2Data);
                                echo 'Year-2 Notification For modules 3 Year<br/>';
                               //echo "<pre>YEar2 3 Year Case:";print_r($Year2Data); 
                                /*$sent = Mail::send ( 'emails.sendgrid_MD_Year1_notification_template', $dataEmail, function ($message) {
                            
                                        $message->to ( 'mohit@visions.net.in' )->subject ( 'Md Year-1 Notification' );
                                });
                                var_dump($sent);
                                if( ! $sent) dd("something wrong");
                                dd("send");*/
                            }
                        }

                        if(date('Y') == ($Year)){
                            echo 'Year-2 Notification For Current Year<br/>';
                            $Y2_NotificationCronDate = date('Y-m-d', strtotime($Y2_noticeDate. ' '.$constantDays.' days'));
                            if(strtotime($Y2_NotificationCronDate)== strtotime($currentDate)){
                                 
                                $dataEmail=array('search_address'=>$Year2Data);
                                echo "<pre>YEar2 Same Year Case:";print_r($Year2Data); 
                            
                            }                      
                        }                    
                    }


                }else{

                    //echo "2222<br/>";

                    $Y2_noticeDate=$value[0]['outcycle_notice_date'];
                    echo $Y2_noticeDate.'--uu<br/>';
                        $yearType=(date('Y')-$Year);
                        if($yearType%3 == 0){
                            //echo "YYYYYYYYYYYYYYY<br/>";
                            $NotificationCronDate = date('Y-m-d', strtotime($Y2_noticeDate. ' '.$constantDays.' days'));
                            //echo $Y2_noticeDate.'<br/>';
                            //echo $NotificationCronDate.'<br/>';
                            if(strtotime($NotificationCronDate)== strtotime($currentDate)){
                                echo 'Year-2 -2 Notification For modules 3 Year<br/>';
                                $dataEmail=array('search_address'=>$value);
                               //echo "<pre>YEar2 3 Year Case:";print_r($value);
                               /*$sent = Mail::send ( 'emails.sendgrid_MD_Year1_notification_template', $dataEmail, function ($message) {
                            
                                        $message->to ( 'mohit@visions.net.in' )->subject ( 'Md Year-1 Notification' );
                                });
                                var_dump($sent);
                                if( ! $sent) dd("something wrong");
                                dd("send");*/ 
                            }
                        }

                        if(date('Y') == ($Year)){
                            //echo "ZZZZZZZZZZZZZZZZZZZZ<br/>";
                            $Y2_NotificationCronDate = date('Y-m-d', strtotime($Y2_noticeDate. ' '.$constantDays.' days'));
                            echo $Y2_NotificationCronDate.'--ii<br/>';
                            if(strtotime($Y2_NotificationCronDate) == strtotime($currentDate)){
                                echo 'Year-2 -2 Notification For Current Year<br/>';
                                $dataEmail=array('search_address'=>$value);
                                //echo "<pre>YEar2 Same Year Case:";print_r($value); 
                                /*$sent = Mail::send ( 'emails.sendgrid_MD_Year1_notification_template', $dataEmail, function ($message) {
                            
                                        $message->to ( 'mohit@visions.net.in' )->subject ( 'Md Year-1 Notification' );
                                });
                                var_dump($sent);
                                if( ! $sent) dd("something wrong");
                                dd("send");*/
                            
                            }                      
                        }   
                }
            }
        } 
        exit;      
    }    
}