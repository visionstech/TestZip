<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\State;
use App\County;
use App\PfLookup;
use App\MdInOutCycle;
class CommonController extends Controller
{
    /**
    * Fetches the counties for a specific state.
    * @param int $id            
    * @return Response
    **/	
    public function postCounty(Request $request)
    { 
        //echo "ssssssssssssssssssssss";exit;
        try {
            $state_abbr = $request->state_abbr;
            $state_details = State::where('state_abbr', $state_abbr)->where('end_date',null)->lists('state_id');
            $input_name = $request->input_name;
            $input_id = $request->input_id;
            $all_counties = County::where('state_id', $state_details[0])->where('end_date',null)->get();
            $counties = [];
            foreach($all_counties as $county) {
                $counties[$county->county_name] = $county->county_name;
            }
           
            return view('customer.getCounties', compact('counties', 'input_name', 'input_id'));
        }
        catch (\Exception $e) 
        { 
            return response()->json(['success'=>false, 'message' => $e->getMessage()]);
        }	
    }
    
    /**
    * Fetches the link for a specific county.
    * @param int $id            
    * @return Response
    **/	
    public function postCountyLink(Request $request)
    { 
        try {
            $state_abbr = $request->state_abbr;
            $state_details = State::where('state_abbr', $state_abbr)->where('end_date',null)->lists('state_id');
            $county_name = $request->county_name;
            $county_details = County::where('state_id', $state_details[0])->where('county_name', $county_name)->where('end_date',null)->lists('county_link');
            $county_link = (count($county_details)) ? $county_link = $county_details[0] : '';            
            return response()->json(['success'=>true, 'county_link' => $county_link]);
        } 
        catch (\Exception $e) 
        { 
            return response()->json(['success'=>false, 'message' => $e->getMessage()]);
        }	
    }
    
    
    /**
      * Return get Additional Homeowner Question detail.
      * @param   
      * @return Response
    **/
    public function postQuestionDescription(Request $request)
    {
        try { 
            $question_id = decrypt($request->question_id);
            $question = PfLookup::find($question_id);
            return response()->json(['success'=>true, 'description' => $question->description]);
        } 
        catch (Exception $e) {
            return response()->json(['success'=>false, 'message' => $e->getMessage()]);
        }
    }

    /**
    * Fetches the link for a specific county For MD STATE.
    * @param Year 
    * @return Response
    **/ 
    public function postCountyLinkMd(Request $request)
    { 
       
        try {
            //echo "<pre>e";print_r();exit;
            $data=$request->all();
            //echo "<pre>e";print_r($data);exit;
            $county = County::where('county_name', $data['county_id'])->where('state_id', 24)->where('end_date',null)->get();
            $response=array();
            $county_link='';
            if(count($county)){
                  
                $MdInOutDetail = MdInOutCycle::where('county_id', $county[0]['county_id'])->where('end_date',null)->get();
                //  echo "<pre>MDDDD";print_r($MdInOutDetail);exit;
                
                if(count($MdInOutDetail)){
                    $currentDate=date('Y-m-d');
                    $currentYear=date('Y');
                /*    $incycleNoticeDate=$MdInOutDetail[0]['incycle_notice_date'];
                    $outcycleNoticeDate=$MdInOutDetail[0]['outcycle_notice_date'];*/
                    $incycleLink=$MdInOutDetail[0]['incycle_link'];
                    $outcycleLink=$MdInOutDetail[0]['outcycle_link'];
                    //In-Cycle Case
                    if($data['assesment_year'] == $currentYear){
                        //echo "sdfsdfsdf";exit;
                        $county_link=$MdInOutDetail[0]['incycle_link'];
                    
                    }
                    if($data['assesment_year'] < ($currentYear)){
                         $county_link=$MdInOutDetail[0]['outcycle_link'];
                    }

                }
            }
            return response()->json(['success'=>true, 'county_link' => $county_link]);
        } 
        catch (\Exception $e) 
        { 
            return response()->json(['success'=>false, 'message' => $e->getMessage()]);
        }  
    }
    
}
