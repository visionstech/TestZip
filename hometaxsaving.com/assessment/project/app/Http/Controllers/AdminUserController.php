<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\User;
use Session;
use DB;
use App\Http\Controllers\Auth\AuthController;
use App\Helpers\Helper;
use Hash;
use Auth;
use App\State;
use App\County;
use App\UserSearch;
use App\PfAddress;
use App\PfLookup;



class AdminUserController extends Controller
{
	
    public function listUser()
    {
		$userList = DB::table('users')->where('user_type','=',\Config::get('constants.adminUser'))->paginate(10);
		return view('users.listuser')->with('userList', $userList);
    }
	
	public function addUser(){
		$user = new User();
		return view('users.addUser',['user'=>$user]);
	}
	
	public function saveUser(Request $request){
		$this->validate($request, [
				'name' => 'required',
				'email' => 'required|unique:users',
				'password' => 'required',
				'confirm_password' => 'required'
		]);

		$input = $request->all();
		$name = $request->name;
		$email = $request->email;
		$password = $request->password;
		$ip_address = $request->ip_address;
			
	
		$date = date("Y-m-d H:i:s");
		$input = array('name' => $name,
						'email'=>$email,
						'password'=>bcrypt($password),
						'ip_address'=> \Request::getClientIp(true),
						'user_type' => \Config::get('constants.adminUser'),

						);
		User::create($input);

		Session::flash('flash_message', 'User successfully added with admin !');

		return redirect('users');
		
	}
	
	public function editUser(Request $request, $id){
		$user = User::find($id);
		return view('users.editUser',compact('user'));
	}
	
	public function updateUser(Request $request){
		
		$this->validate($request, [
				'name' => 'required',
				'email' => 'required',
				'password' => 'required',
				'confirm_password' => 'required'
		]);
		
		$userId = $request->id;
		$name = $request->name;
		$email = $request->email;
		$password = $request->password;
		$ip_address = \Request::getClientIp(true);

		
		// Find the user to be updated
		$user = User::find($userId);
		$date = date("Y-m-d H:i:s");
		$user->name = $name;
		$user->email = $email;
		$user->password = bcrypt($password);	
		$user->ip_address = $ip_address;
		$user->user_type = \Config::get('constants.adminUser');
					
		$user->save();
		Session::flash('flash_message', 'User successfully updated!');

		return redirect('users');
	}
	
	public function deleteUser(Request $request, $userId){
		// User::destroy($userId);
		// Find the user to be deleted and update end_date
		$user = User::find($userId);
		$date = date("Y-m-d H:i:s");
		
		$user->end_date = $date;
		$user->save();
		Session::flash('flash_message', 'User with id '.$userId.' successfully deleted!');
		return redirect()->back();
	}
	
	/* Member related functions */
	public function listMember()
    {
		$memberList = Helper::getMemberDetailsList();
		return view('users.listMember')->with('memberList', $memberList);
    }
	
	public function viewMemberDetails(Request $request, $id){
		$memberHistory = Helper::getMemberSearchHistory($id);
		$memberName = $memberHistory[0]->first_name." ".$memberHistory[0]->last_name;
		
		$compactData = array('memberHistory', 'memberName');
		return view('users.listMemberSearchHistory',compact($compactData));
	}
	
	// Change password for member function
	public function showChangePasswordForm(){
		return view('auth.changePassword');
	}

	public function changePassword(Request $request){
		if (!(Hash::check($request->get('current-password'), Auth::user()->password))) {
			// The passwords do not match
			return redirect()->back()->with("error","Your current password does not match the password you provided. Please try again.");
		}
		if(strcmp($request->get('current-password'), $request->get('new-password')) == 0){
			//Current password and new password are same
			return redirect()->back()->with("error","New Password cannot be same as your current password. Please choose a different password.");
		}
		$validatedData = $this->validate($request,[
				'current-password' => 'required',
				'new-password' => 'required|string|min:6|confirmed',
			]);
		//Change Password
		$user = Auth::user();
		$user->password = bcrypt($request->get('new-password'));
		$user->save();
		
		return redirect()->back()->with("success","Your password has been successfully changed. !");
	}

	
	// Edit Profile for member
	
	public function showEditProfileForm(){
		try {

            $userDetail=array();
            $addressDetail=array();
            //echo Auth::user()->id;exit;
            if(Auth::user()->id){
				$userId = Auth::user()->id;
				
                $addressDetails = Helper::getMemberDetailsUsingUserId($userId);
                $prefillAddress='';
                if(!empty($addressDetails) && $addressDetails != ''){
					
					$addressDetails->user_id = $userId; 
					$state_id = $addressDetails->state;
					$BillingState = State::where('state_id',$state_id)->get();
					$countyDetail= County::where([['state_id',$state_id],['county_id',$addressDetails->county],['end_date',null]])->get();
					$addressDetails->county_name=(!empty($countyDetail) && count($countyDetail))?($countyDetail[0]['county_name']):'';
					//echo "<pre>ee";print_r($BillingState);exit;
					$addressDetails->state_abbr =(!empty($BillingState) && count($BillingState))?($BillingState[0]['state_abbr']):'';

					$addressDetails->county_name = County::getCountyName($addressDetails->county);
					$prefillAddress = (!empty($addressDetails))?($addressDetails->address_line_1." "):''; 
                    $prefillAddress .= (!empty($addressDetails))?($addressDetails->address_line_2.", "):'';
                    $prefillAddress .= (!empty($addressDetails))?($addressDetails->city.", "):'';
                    $prefillAddress .= (!empty($BillingState) && count($BillingState))?($BillingState[0]['state_abbr'].", "):'';
                    $prefillAddress .= (!empty($addressDetails))?($addressDetails->postal_code.", "):'';
                    //echo $prefillAddress;exit;
                    $addressDetails->prefillAddress=$prefillAddress;
					//echo "<pre>ee";print_r($addressDetails);exit;
				}	
		
            }        
         	//echo "<pre>ee";print_r($addressDetails);exit;
			/* Get states dropdown */
            $all_states = State::all();
            $states = [];
            foreach($all_states as $state) {
                $states[$state->state_abbr] = $state->state_name;
            }
         	//->where([['state_id',$state_id],['end_date',null]])->
			/* Get counties dropdown */
            $all_counties = County::where([['state_id',$state_id],['end_date',null]])->orderBy('county_name', 'ASC')->get();
            $counties = [];
            foreach($all_counties as $county) {
                $counties[$county->county_name] = $county->county_name;
            }
            
            return view('customer.edit_profile',compact('states', 'counties','addressDetails'));
            
        }
        catch (\Exception $e) 
        {
            $result = ['exception_message' => $e->getMessage()];
            return view('errors.error', $result);
        }
	}

	
	public function updateProfile(Request $request){
	
		$addressDetails=array();
	
		$this->validate($request, [
				'street_number' => 'required',
				'route' => 'required',
				'locality' => 'required',
				'administrative_area_level_1' => 'required',
				'administrative_area_level_2' => 'required',
				'postal_code' => 'required',
				'autocomplete_search'=>'required'
			
		], [		
			'street_number.required'=>'This field is required.',
		    'route.required'    => 'This field is required.',
		    'locality.required'    => 'This field is required',
		    'administrative_area_level_1.required' => 'This field is required',
		    'administrative_area_level_2.required'      => 'This field is required',
		    'postal_code.required' => 'This field is required',
		    'autocomplete_search.required'=>'This field is required.',
		]);
		$addressDetails['user_id'] = $request->user_id;
		$addressDetails['user_search_id'] = $request->user_search_id;
		$addressDetails['address_line_1'] = $request->street_number;
		$addressDetails['address_line_2'] = $request->route;
		$addressDetails['city'] = $request->locality;
		$state_abbr = $request->administrative_area_level_1;
		$state_details = State::where('state_abbr', $state_abbr)->where('end_date',null)->lists('state_id');
		//echo "<pre>eeeeState: ";print_r($state_details[0]);exit;
		$addressDetails['state'] = ($state_details)?($state_details[0]):'';
		$countyDetail = County::where('county_name', $request->administrative_area_level_2)->where('end_date',null)->lists('county_id');
		//echo "<pre>e";print_r($countyDetail[0]);exit;
		$addressDetails['county'] = $countyDetail[0];
		$addressDetails['address_type'] = $request->address_type;
		$addressDetails['zipcode'] = $request->postal_code;
		
		if (isset($request->receive_notification) && !empty($request->receive_notification) && $request->receive_notification!= '0') 
				$addressDetails['receive_notification'] = 1;
		else	
				$addressDetails['receive_notification'] = 0;
		
		$returnVal = Helper::updateProfileDetails($addressDetails);
		if ($returnVal == 1)		
			Session::flash('flash_message', 'Your profile details have been successfully updated!');
		else
			Session::flash('flash_message', 'Sorry, your profile details could not be saved. Please try again.');
			
		return redirect()->back()->withInput();

	}
	
	
	
}
