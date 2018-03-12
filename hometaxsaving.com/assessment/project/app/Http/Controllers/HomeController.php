<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Database\QueryException;


use App\Item;
use App\County;
use App\State;
use App\PfLookup;
use Auth;
use Session;
use DB;
use App\Helpers\Helper;
use Plivo;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('welcome');
    }
	
	public function settings()
	{
		return view('admin.settings');
		
	}
	
	
	
	public function deleteItem(Request $request, $itemId){
		Item::destroy($itemId);
		Session::flash('flash_message', 'Item with id '.$itemId.' successfully deleted!');
		return redirect()->back();
	}
	
	public function lookups(Request $request)
	{
		$lookup_type_id = $request->input('lookup_type_id');
		if ($lookup_type_id != NULL && !empty($lookup_type_id) && $lookup_type_id !='')
			$lookupTypeId = $lookup_type_id;
		else $lookupTypeId = 1;
			
		$lookupTypeList = DB::table('pf_lookup_types')->pluck('name', 'lookup_type_id');
		$lookupList = DB::table('pf_lookup_types')
			->join('pf_lookups', 'pf_lookup_types.lookup_type_id', '=', 'pf_lookups.lookup_type_id')
			->select('pf_lookup_types.lookup_type_id','pf_lookup_types.name as lookupTypeName', 'pf_lookup_types.description', 'pf_lookups.lookup_id','pf_lookups.name as lookupName', 'pf_lookups.description as lookupDescription','pf_lookups.value','pf_lookups.value1','pf_lookups.value2', 'pf_lookups.updated_at')
			->where('pf_lookup_types.lookup_type_id',$lookupTypeId)
			->paginate();
		
		$compactData=array('lookupTypeList', 'lookupList','lookupTypeId');
		return view('admin.index',compact($compactData));	
		
	}
	
	public function addLookup(Request $request){
		$lookup = new PfLookup();
		$lookupTypeList = DB::table('pf_lookup_types')
							->whereNull('end_date')->pluck('name', 'lookup_type_id');
		$lookupList = DB::table('pf_lookups')->whereNull('end_date')->pluck('name', 'lookup_id');
		$compactData=array('lookupTypeList', 'lookup', 'lookupList');
	
		return view('admin.addLookup',compact($compactData));
	}
	
	public function editLookup(Request $request, $lookupId){
		$lookup = PfLookup::find($lookupId);
		$lookupTypeList = DB::table('pf_lookup_types')
							->whereNull('end_date')
							->pluck('name', 'lookup_type_id');
		$lookupList = DB::table('pf_lookups')->whereNull('end_date')->pluck('name', 'lookup_id');
		$compactData=array('lookupTypeList', 'lookup', 'lookupList');
	
		return view('admin.editLookup',compact($compactData));
	}
	
	public function saveLookup(Request $request){
		
		$input = $request->all();
		$lookup_type_id = $request->lookup_type_id;
		$lookup_name = $request->lookup_name;
		$lookup_value = $request->lookup_value;
		$lookup_value1 = $request->lookup_value1;
		$lookup_value2 = $request->lookup_value2;
		$lookup_desc = $request->lookup_desc;
		// Added condition 26th Sep 2017
		if (!empty($request->parent_lookup_id) && $request->parent_lookup_id != 0)  
			$parent_lookup_id = $request->parent_lookup_id;
		else 
			$parent_lookup_id = NULL;
		$display_order = $request->display_order;
		
		
		if (Helper::CheckIfAdjustmentSetting($lookup_type_id)){
			$this->validate($request, [
			'lookup_name' => 'required',
			'lookup_value' => ['required','regex:/0|(\d+\.?\d*\x24|\x25)/'],
			'lookup_value1' => ['required','regex:/0|^\d+\.?\d*\x24|\x25$/'],
			'lookup_value2' => ['required','regex:/0|^\d+\.?\d*\x24|\x25$/'],
			
			]);
			
		}
		else{
			$this->validate($request, [
				'lookup_name' => 'required',
				'lookup_value' => 'required',
				
			]);
		}
		
		
		$date = date("Y-m-d H:i:s");
		DB::beginTransaction();
		try{
			$userId = Auth::user()->id;
			$lookup_id = Helper::getHighKey('pf_lookups','lookup_id',$userId);
			$input = array('lookup_id' => $lookup_id,
							'lookup_type_id' => $lookup_type_id,
							'name' => $lookup_name,
							'value'=>$lookup_value,
							'value1'=> $lookup_value1,
							'value2'=> $lookup_value2,
							'description'=>$lookup_desc,
							'parent_lookup_id' => $parent_lookup_id,
							'display_order' => 'display_order',
							'created_by'=>Auth::id(),
							'updated_by'=>Auth::id(),
							'created_at'=>$date,
							'updated_at'=>$date,
							'end_date'=>null
							);
							
					
			PfLookup::create($input);
			DB::commit();
			Session::flash('flash_message', 'Lookup successfully added!');
			//return redirect('lookups');
			// Added so that the redirect back is to the same list from which edited., 3/10/17
			return redirect()->action('HomeController@lookups',['lookup_type_id' => $lookup_type_id]);
					

		}
		catch (QueryException $e) 
        {
			DB::rollback();
            $result = ['exception_message' => $e->getMessage()];
            return view('errors.error', $result);
        }
		catch (\Exception $e) 
        {
			DB::rollback();
            $result = ['exception_message' => $e->getMessage()];
            return view('errors.error', $result);
        }

	}
	
	public function updateLookup(Request $request)
	{
		
		$this->validate($request, [
			'lookup_name' => 'required',
			'lookup_value' => 'required'
			
		]);
		
		$lookupId = $request->lookup_id;
		$lookup_type_id = $request->lookup_type_id;
		$lookup_name = $request->lookup_name;
		$lookup_value = $request->lookup_value;
		$lookup_value1 = $request->lookup_value1;
		$lookup_value2 = $request->lookup_value2;
		$lookup_desc = $request->lookup_desc;
		// Added condition 26th Sep 2017
		if (!empty($request->parent_lookup_id) && $request->parent_lookup_id != 0)  
			$parent_lookup_id = $request->parent_lookup_id;
		else 
			$parent_lookup_id = NULL;
		$display_order = $request->display_order;
		
		if (Helper::CheckIfAdjustmentSetting($lookup_type_id)){
			$this->validate($request, [
			'lookup_name' => 'required',
			'lookup_value' => ['required','regex:/0|(\d+\.?\d*\x24|\x25)/'],
			'lookup_value1' => ['required','regex:/0|^\d+\.?\d*\x24|\x25$/'],
			'lookup_value2' => ['required','regex:/0|^\d+\.?\d*\x24|\x25$/'],
			]);
			
		}
		else{
			$this->validate($request, [
				'lookup_name' => 'required',
				'lookup_value' => 'required',
				
			]);
		}
		
		$date = date("Y-m-d H:i:s");
		try{
			// Find the item to be updated
			$lookup = PfLookup::find($lookupId);
			$lookup->lookup_type_id = $lookup_type_id;
			$lookup->name = $lookup_name;
			$lookup->value = $lookup_value;
			$lookup->value1 = $lookup_value1;
			$lookup->value2 = $lookup_value2;
			$lookup->description = $lookup_desc;
			$lookup->parent_lookup_id = $parent_lookup_id;
			$lookup->display_order = $display_order;
				
			$lookup->save();
			Session::flash('flash_message', 'Lookup successfully updated!');
			// Added condition so after editing takes back to the same list.
			return redirect()->action('HomeController@lookups',['lookup_type_id' => $lookup_type_id]);
					


		}
		catch (QueryException $e) 
        {
            $result = ['exception_message' => $e->getMessage()];
            return view('errors.error', $result);
        }
		catch (\Exception $e) 
        {
            $result = ['exception_message' => $e->getMessage()];
            return view('errors.error', $result);
        }
	}
	
	public function cancelLookup(Request $request){
		$lookup_type_id = $request->lookup_type_id;
		return redirect()->action('HomeController@lookups',['lookup_type_id' => $lookup_type_id]);
					
		
	}
	
	
	public function jurisdiction(Request $request)
	{
	/*	if ($request->isMethod('post')){
			$stateId = $request->state_id;
			if ($stateId == NULL || empty($stateId))
				$stateId = 1;
		}
		elseif($request->isMethod('get') */
		$state_id = $request->input('state_id');
		if ($state_id != NULL && !empty($state_id) && $state_id !='')
			$stateId = $state_id;
		else $stateId = 1;
	
		
		$stateList = DB::table('states')->pluck('state_name', 'state_id');
		$stateCountyList = DB::table('states')
            ->leftjoin('counties', 'states.state_id', '=', 'counties.state_id')
			->select('states.state_id','state_name as stateName', 'state_abbr', 'county_id','counties.county_name as countyName', 'date_of_value','notice_date','appeal_deadline_date','county_link')
			->where('states.state_id',$stateId)
			->paginate(10);
		$selectedStateId = $stateId;
		$compactData=array('stateList', 'stateCountyList', 'selectedStateId');
		return view('admin.county',compact($compactData));	
		
	}
	
		
	public function editCounty(Request $request, $countyId){
		$stateList = DB::table('states')->pluck('state_name', 'state_id');
		$county = County::find($countyId);
		$compactData=array('stateList', 'county');
		return view('admin.editCounty',compact($compactData));
	}
	
		
	
	public function updateCounty(Request $request){
		
		$county_id = $request->county_id;
		$state_id = $request->state_id;
		$county_name = $request->county_name;
		$date_of_value = $request->date_of_value;
		$notice_date = $request->notice_date;
		$appeal_deadline_date = $request->appeal_deadline_date;
		$county_link = $request->county_link;
		$date = date("Y-m-d H:i:s");
		
		// Start the transaction
		DB::beginTransaction();
		try {
			// Find the item to be updated
			$county = County::find($county_id);

			$county->county_name = $county_name;
			$county->state_id = $state_id;
			if ($date_of_value === NULL || empty($date_of_value) || $date_of_value == '' )
				$county->date_of_value = NULL;
			else
				$county->date_of_value = $date_of_value;	
			
			if ($notice_date === NULL || empty($notice_date) || $notice_date == '' )
				$county->notice_date = NULL;
			else
				$county->notice_date = $notice_date;			
			
			if ($appeal_deadline_date === NULL || empty($appeal_deadline_date) || $appeal_deadline_date == '' )
				$county->appeal_deadline_date = NULL;
			else
				$county->appeal_deadline_date = $appeal_deadline_date;			
		
		
			$county->county_link = $county_link;
						
			$county->save();
			
			if ($notice_date !== NULL && !empty($notice_date) && $notice_date !== ''){
					$jurisdiction['state_id'] = $state_id;
					$jurisdiction['county_id'] = $county_id;
					$userList = Helper::getUserDetailsByJurisdiction($jurisdiction);
					$userListArr = json_decode(json_encode($userList), true);
					
					// Send SMS to users in list
					Helper::sendSMS($userListArr);
					
					
					$this->sendPlivoSMS();
					
					// Send email to all the users
				//	$this->sendEmailToCustomers($userListArr);
				
			}
			Session::flash('flash_message', 'County details successfully updated and SMS sent to the customers!');

			DB::commit();
			return redirect('jurisdiction');
		}
		catch (QueryException $e) 
        {
			DB::rollback();
            $result = ['exception_message' => $e->getMessage()];
            return view('errors.error', $result);
        }
		catch (\Exception $e) 
        {
			DB::rollback();
            $result = ['exception_message' => $e->getMessage()];
            return view('errors.error', $result);
        }
	}
	
	public function sendEmailToCustomers($customerList){
		$data['subject'] = 'Assessment is available.';
		foreach($customerList as $customer){
			$data['username'] = $customer['name'];
			$data['content'] = 'Dear Mr./Ms. '.$customer['name'].', Your assessment is now available. Please visit our site for further details.';
			Helper::sendMail($data, $customer['email']);
		} //end foreach
		
		
	} //end sendEmailToCustomers
	
	public function sendPlivoSMS(){

		// Your Account SID and Auth Token from twilio.com/console
		
		// Use the client to do fun stuff like send text messages!
		$params = array(
			'src' => '+919960233875',
			'dst' => '+919552178430',
			'text' => 'Please note that assessment for your county is now available.'	
			);
		$response = Plivo::sendSMS($params);
		
		
	}
	
   


	
}
