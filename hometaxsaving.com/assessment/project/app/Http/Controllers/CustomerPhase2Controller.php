<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use Input;
use App\User;
use Illuminate\Contracts\Auth\Guard;
use Redirect;
use Session;
use View;
use App\State;
use App\County;
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
use Auth;
use Mail;
use File;

// This controller will provide functions to cater to the Phase 2 of the wizard
class CustomerPhase2Controller extends Controller
{
     /**
      * Return get Make Payment Form.
      * @param   
      * @return Response
    **/
    public function makePhase2Payment($encryptedToken)
    {
        try {
			// Check if Session has phase2_token			
			if ($encryptedToken != null && !empty($encryptedToken)){
				$token = \Crypt::decrypt($encryptedToken);

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
				/*if (!empty($phase2_token) && $phase2_token != NULL && $phase2_token != '0'){
					// implies Phase 2 payment has been made.
					// Show the page and the button as a link
					$phase2_token = Helper::getSessionPhase2Token();
					$phase2_token_details = UserSearch::where('token', $phase2_token)->where('end_date',null)->get();
						if(count($phase2_token_details)) {
							if($phase2_token_details[0]->active_page >= '4') {
								$phase2_token_details = $phase2_token_details[0];
							}
						}
					return view('customer.phase2_make_payment',compact('active', 'phase2_token_details', 'states', 'counties'));
				}*/
				
				$token_details = UserSearch::where('token', $token)->where('end_date',null)->get();
				// $getSearchDetails = Helper::getSearchDetailsWithToken($token);
				// Session::put('mobile_number',$getSearchDetails->mobile_number);
				$mobile = Helper::getBillingDetail(Auth::user()->id);
				Session::put('mobile_number',$mobile[0]->mobile_number);
				if(count($token_details)) {
					if($token_details[0]->active_page >= '3') {
						$token_details = $token_details[0];
					} else {
						return redirect('/');
					}    
				} else {
					return redirect('/invalid-token');
				}
				
				Session::put('user_search_id', $token_details->user_search_id);

                $customer_billing_address = Helper::getBillingDetail(Auth::user()->id);
				
				$data['address_street_number'] = $customer_billing_address[0]->address_line_1;
				$data['address_street'] = $customer_billing_address[0]->address_line_2;
				$data['address_city'] = $customer_billing_address[0]->city;
				$data['state_name'] = State::getStateName($customer_billing_address[0]->state);
				$data['state_abbr'] = State::getStateAbbr($customer_billing_address[0]->state);
				$data['address_zipcode'] = $customer_billing_address[0]->postal_code;
				$data['use_same_address'] = '0';
			
				$data['county_name'] = County::getCountyName($customer_billing_address[0]->county);
				$data['state_id'] = $customer_billing_address[0]->state;
				$data['county_id'] = $customer_billing_address[0]->county;
				$all_counties = County::where('state_id', $customer_billing_address[0]->state)->where('end_date',null)->get();
				$counties = [];
				foreach($all_counties as $county) {
					$counties[$county->county_name] = $county->county_name;
				}
				$active = "make_payment";
				return view('customer.phase2_make_payment',compact('active', 'token_details', 'states', 'counties','data'));

			} else {
				return redirect('/');
			}

        } 
        catch (\Exception $e) {
            $result = ['exception_message' => $e->getMessage()];
            return view('errors.error', $result);
        }
        
    }
	
   public function postPhase2MakePayment(Requests\Phase2MakePayment $request)
    {
        try {
            DB::beginTransaction();
            
            if($request->token_phase2_exist == '0') {
                // When token is 0, make payment using PG. Added 27/06/2017 (MS)
                // Get payment details from this page into an array
                $paymentDetails = $request->all();
				
				// Get checkbox value for saving billing address
				if (isset($request->same_billing_address) && !empty($request->same_billing_address) && $request->same_billing_address != '0')
					$same_billing_address = $request->same_billing_address;
				else
					$same_billing_address = 0;
                
                $paymentDetails['billing_street'] = $request->street_number.' '.$request->route;
                $paymentDetails['billing_city'] = $request->locality;
                $paymentDetails['billing_state'] = $request->administrative_area_level_1;
                $paymentDetails['billing_zipcode'] = $request->postal_code;
				$paymentDetails['payment_amount'] = config('constants.phase2Amt');
				//$paymentDetails['payment_amount'] = 0.05;
				
				// Get user_search_id from Session
				// using this get the user email and other details
				
				$user_search_id = Session::get('user_search_id');
				$userDetails = Helper::getUserDetailsUsingSearchId($user_search_id);
				
				if ($userDetails != null && !empty($userDetails)){
					
					$email = $userDetails->email;
					$user_name = $userDetails->first_name.' '.$userDetails->last_name;
				}
				else{
					return view('errors.error', 'No user found for this search. Please contact admin');
				}
				
                
                $payment = new Payment();               
                // Call the function makePayment to paypal using these details
                $paymentResponse = $payment->makePayment($paymentDetails, config('constants.pgPaypal'));

                $paymentResponseArr = json_decode($paymentResponse, true);
                
                //$paymentStatus = $paymentResponseArr['state'];
                  //$paymentStatus = $paymentResponseArr['state'];
                $paymentStatus = 'approved';
                if ($paymentStatus == config('constants.approved')) {
                    
                    Session::put('phase2_make_payment', '1');           
                    Session::put('billing_street_number', $request->street_number);
                    Session::put('billing_street', $request->route);
                    Session::put('billing_city', $request->locality);
                    Session::put('billing_state', $request->administrative_area_level_1);
                    Session::put('billing_zipcode', $request->postal_code);
                    Session::put('billing_county', $request->administrative_area_level_2);
                    
					$user = User::where('email', $email)->get();
					if(count($user)) {
						$user_id = $user[0]->id;
						//$user_name = $user[0]->name;
						$user_email = $user[0]->email;
					}
					else {
						DB::rollback();
						//return response()->json(['success'=>false, 'message' => 'Sorry, this user does not exist. Please try again.']);
						return Redirect::back()->withErrors(['Sorry, this user does not exist in our system. Please try again.']);
                
					}
					
                    $phase2_token = $this->generatePhase2Token($user_id);
					
					
					self::savePhase2BillingAddress($user_id, $user_search_id, $request);
					
					$result = self::savePhase2Token($phase2_token, $user_search_id);
					
					
                    if($result == true) {
						DB::commit();
						// Send email on successful updation
						// Added code 13-11-2017
						$customer_link = url('/top_comparables_list/'.$phase2_token);
						$mail_data['username'] = $user_email;
						$mail_data['user_name'] = $user_name;
						$mail_data['content'] = 'Thank you for making payment, Please <a href="'.$customer_link.'" target="_blank" style="color:#1570C3;">click</a> on the link to complete the Tax Appeal. <br>
                        Link is valid for 15 days.';
						$mail_data['subject'] = 'Phase 2 Payment Received';

						$mail_to = array($user_email);

						$mail_data['fname'] = $userDetails->first_name;
	                    $mail_data['lname'] = $userDetails->last_name;
	                    $mail_data['phone'] = Session::get('mobile_number');
	                    $mail_data['str_n_route'] = Session::get('billing_street_number').' '.Session::get('billing_street');
	                    $mail_data['city'] = Session::get('billing_city');
	                    $mail_data['state'] = Session::get('billing_state');
	                    $mail_data['county'] = Session::get('billing_county');
	                    $mail_data['zip'] = Session::get('billing_zipcode');
	                    $mail_data['pay'] = config('constants.phase2Amt');



						//$mail_sent = Helper::SendMail($mail_data, $mail_to);
	                    Mail::send('emails.phasetwo', $mail_data, function($message) use ($mail_data, $mail_to)
	                    {
	                        $message->to($mail_to)->subject('Thank you for your payment of $'.config('constants.phase2Amt'));
	                         
	                    });
                    	$url = url('/top_comparables_list/'.$phase2_token);
                    	return redirect($url);
                        //return response()->json(['success'=>true, 'redirect_url' => url('/top_comparables_list/'.$phase2_token)]);
                                                    
                    }
                    else {
                        DB::rollback();
                        //$message = 'Sorry, Phase 2 token could not be generated.';
                        //return response()->json(['success'=>false, 'message' => $message]);
                        //$result = ['exception_message' => 'Sorry, Phase 2 token could not be generated.'];
            			//return view('errors.error', $result);
            			return Redirect::back()->withErrors(['Sorry, Phase 2 token could not be generated.']);
                    }
                }
                else {
                    DB::rollback();
                    //return response()->json(['success'=>false, 'message' => 'Sorry, your payment has been failed. Please try again.']);
                    //$result = ['exception_message' => 'Sorry, your payment has been failed. Please try again.'];
            		//return view('errors.error', $result);
            		return Redirect::back()->withErrors(['Your payment has been rejected.']);
                }                
            }
            else {
                DB::rollback();
                //response()->json(['success'=>true, 'redirect_url' => url('/phase2-payment')]);  
                return Redirect::back()->withErrors(['Your payment has been rejected.']);

            }
        
        }
        catch (\Exception $e) 
        {   
            DB::rollback();
            //$result = ['exception_message' => $e->getMessage()];
            //return view('errors.error', $result);
            //return response()->json(['success'=>false, 'message' => $e->getMessage()]);
            return Redirect::back()->withErrors(["Sorry, we could not process your request. Please try again later or contact us at <a href='mailto:info@hometaxsavings.com'>info@hometaxsavings.com</a>"]);
        }   
        
    }

	public function generatePhase2Token($user_id){
		$user_search_token = encrypt($user_id).'-'.Helper::getCustomerToken(10);
		return $user_search_token;
		
	}
	
	public function getCountyList($state_id){
		
		$all_counties = County::where('state_id', $state_id)->where('end_date',null)->get();
		$counties = [];
		foreach($all_counties as $county) {
			$counties[$county->county_name] = $county->county_name;
		}
		return $counties;
	}

	/**
	* getTopComarablesList
	**/
	public function getTopComparablesList($token){
		try {
			ini_set("display_errors", "1");
			error_reporting(E_ALL);
			if($token != null) {
				$token_details = UserSearch::where('phase2_token', $token)->where('end_date',null)->get();
				
				if(count($token_details)) {                    
        			$subjectCompsDetail = array();
    				$pdfLink = "";
                	$lat = $sub_lat = "0.000000";
					$long = $sub_long = "-0.000000";

                    if($token_details[0]->active_page == '3') {

                    	$subject_details = SubjectCompsDetail::where(array('ref_object_id' => $token_details[0]->user_search_id, 'system_object_type_id' => '2'))->where('end_date',null)->get();
                    	//echo "<pre>";print_r($subject_details);exit;
						
						//echo "<pre>";print_r($comDetails);exit;

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
								//echo "<pre>";Print_r();exit;
								$sub_lat = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
								$sub_long = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};
							}								
						}

						if(count($subject_details)){
							$sub_otherData = UserSearch::where('user_search_id', $token_details[0]->user_search_id)->where('end_date',null)->first();
							//echo "<pre>e";Print_r($sub_otherData->sale_price);exit;
							$comDetail = SearchComparable::select('*')->where('subject_comps_detail_id', $subject_details[0]->subject_comps_id)->where('end_date',null)->first();
							$lookUpsDetail = SearchComparable::join('pf_lookups', 'pf_lookups.lookup_id', '=', 'search_comparables.lookup_id')->select('pf_lookups.*','search_comparables.*')->where('search_comparables.subject_comps_detail_id', $subject_details[0]->subject_comps_id)->get();
							$subject_details[0]->lat = $sub_lat; 
            				$subject_details[0]->long = $sub_long; 
            				$subject_details[0]->comDetail=$comDetail;
            				$subject_details[0]->subjectOtherData=$sub_otherData;
            				$subject_details[0]->lookUpsDetail=$lookUpsDetail;
            				
            				$Nolat=0;
            				$latGoogle=($sub_lat)?($sub_lat):$Nolat;
            				$longGoogle=($sub_long)?($sub_long):$Nolat;
            				$imageUrl="https://maps.googleapis.com/maps/api/streetview?size=300x150&location=".$latGoogle.",".$longGoogle."&fov=90&heading=235&pitch=10&key=AIzaSyDZNqX2CPKidMMQgqkaGGm3FMqZ9KX5WVw";
            				$folderPath=base_path().'/google_images/'.$token_details[0]->user_search_id;
            				if(!File::exists($folderPath)) {
							    $result = File::makeDirectory(base_path().'/google_images/'.$token_details[0]->user_search_id, 0775);
							    $content = file_get_contents($imageUrl);
	            				$sub=0;
	            				$subjectImageName=$token_details[0]->user_search_id.'_'.$sub.'.jpg';
								file_put_contents(base_path().'/google_images/'.$token_details[0]->user_search_id. '/'.$subjectImageName, $content);
							}else{
								$content = file_get_contents($imageUrl);
	            				$sub=0;
	            				$subjectImageName=$token_details[0]->user_search_id.'_'.$sub.'.jpg';
								file_put_contents(base_path().'/google_images/'.$token_details[0]->user_search_id. '/'.$subjectImageName, $content);
							}     

            				$imagePath=url('/').'/project/google_images/'.$token_details[0]->user_search_id.'/'.$subjectImageName; 
            				$subject_details[0]->subjectImage = $imagePath;
            				//echo "<img src='".$imagePath."'>";exit;
						}else{
							$subject_details[0]->lat = 0; 
            				$subject_details[0]->long = 0;
            				$latGoogle=0;
            				$longGoogle=0;
            				$imageUrl="https://maps.googleapis.com/maps/api/streetview?size=300x150&location=".$latGoogle.",".$longGoogle."&fov=90&heading=235&pitch=10&key=AIzaSyDZNqX2CPKidMMQgqkaGGm3FMqZ9KX5WVw";
            				$folderPath=base_path().'/google_images/'.$token_details[0]->user_search_id;
            				if(!File::exists($folderPath)) {
							    $result = File::makeDirectory(base_path().'/google_images/'.$token_details[0]->user_search_id, 0775);
							    $content = file_get_contents($imageUrl);
	            				$sub=0;
	            				$subjectImageName=$token_details[0]->user_search_id.'_'.$sub.'.jpg';
								file_put_contents(base_path().'/google_images/'.$token_details[0]->user_search_id. '/'.$subjectImageName, $content);
							}else{
								$content = file_get_contents($imageUrl);
	            				$sub=0;
	            				$subjectImageName=$token_details[0]->user_search_id.'_'.$sub.'.jpg';
								file_put_contents(base_path().'/google_images/'.$token_details[0]->user_search_id. '/'.$subjectImageName, $content);
							}

							$imagePath=url('/').'/project/google_images/'.$token_details[0]->user_search_id.'/'.$subjectImageName; 
            				$subject_details[0]->subjectImage = $imagePath;
						}
            			
            			$comparablesList = SubjectCompsDetail::where(array('ref_object_id' => $token_details[0]->user_search_id, 'system_object_type_id' => '3'))->where('end_date',null)->limit(5)->get();
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

									$address = $com_address->address_line_1.', '.$com_address->city.', '.$state_name.', '.$com_address->postal_code;

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
                    		    	$Nolat=0;
		            				$latGoogle=($lat)?($lat):$Nolat;
		            				$longGoogle=($long)?($long):$Nolat;
		            				$imageUrl="https://maps.googleapis.com/maps/api/streetview?size=300x150&location=".$latGoogle.",".$longGoogle."&fov=90&heading=235&pitch=10&key=AIzaSyDZNqX2CPKidMMQgqkaGGm3FMqZ9KX5WVw";

		            				$folderPath=base_path().'/google_images/'.$token_details[0]->user_search_id;
		            				if(!File::exists($folderPath)) {
									    $result = File::makeDirectory(base_path().'/google_images/'.$token_details[0]->user_search_id, 0775);
									    $content = file_get_contents($imageUrl);
			            				$sub=($key+1);
			            				$subjectImageName=$token_details[0]->user_search_id.'_'.$sub.'.jpg';
										file_put_contents(base_path().'/google_images/'.$token_details[0]->user_search_id. '/'.$subjectImageName, $content);
									}else{
										$content = file_get_contents($imageUrl);
			            				$sub=($key+1);
			            				$subjectImageName=$token_details[0]->user_search_id.'_'.$sub.'.jpg';
										file_put_contents(base_path().'/google_images/'.$token_details[0]->user_search_id. '/'.$subjectImageName, $content);
									}     

		            				$imagePath=url('/').'/project/google_images/'.$token_details[0]->user_search_id.'/'.$subjectImageName; 
		            				$subjectCompsDetail['comparables'][$key]->comparableImage = $imagePath;

								}else{
									$subjectCompsDetail['comparables'][$key]->lat = 0; 
                    		    	$subjectCompsDetail['comparables'][$key]->long = 0; 
									
									$latGoogle=0;
		            				$longGoogle=0;
		            				$imageUrl="https://maps.googleapis.com/maps/api/streetview?size=300x150&location=".$latGoogle.",".$longGoogle."&fov=90&heading=235&pitch=10&key=AIzaSyDZNqX2CPKidMMQgqkaGGm3FMqZ9KX5WVw";

		            				$folderPath=base_path().'/google_images/'.$token_details[0]->user_search_id;
		            				if(!File::exists($folderPath)) {
									    $result = File::makeDirectory(base_path().'/google_images/'.$token_details[0]->user_search_id, 0775);
									    $content = file_get_contents($imageUrl);
			            				$sub=($key+1);
			            				$subjectImageName=$token_details[0]->user_search_id.'_'.$sub.'.jpg';
										file_put_contents(base_path().'/google_images/'.$token_details[0]->user_search_id. '/'.$subjectImageName, $content);
									}else{
										$content = file_get_contents($imageUrl);
			            				$sub=($key+1);
			            				$subjectImageName=$token_details[0]->user_search_id.'_'.$sub.'.jpg';
										file_put_contents(base_path().'/google_images/'.$token_details[0]->user_search_id. '/'.$subjectImageName, $content);
									}     

		            				$imagePath=url('/').'/project/google_images/'.$token_details[0]->user_search_id.'/'.$subjectImageName; 
		            				$subjectCompsDetail['comparables'][$key]->comparableImage = $imagePath;

								}
								
                    		}
                    			
                    	}
                    	//echo "aaaaaaaaaaaaaaaaaa";exit;

        				$subjectCompsDetail['subject'] = $subject_details[0];
        				$subjectCompsDetail['pdf_link'] = $pdfLink;
            			
        				//echo "<pre>";print_r($subjectCompsDetail['comparables']);exit;
            			return view('customer.top_comparables', compact('subjectCompsDetail'));                        
                    } else{ 
                        return redirect('/invalid-token');
                    }
                    
                } else {
                    return redirect('/invalid-token');
                }
	        } else { 
            	return redirect('/');
	        }
		}
		catch (\Exception $e) {
            $result = ['exception_message' => $e->getMessage()];
            return view('errors.error', $result);
        }		
	}
	
	
	public function getPhase2TokenStatus($view_status=0)
    { 
        try {
            $token = Helper::getPhase2SessionToken(); 
            if($token != null && !empty($token) && $token != 0) {
                $token_details = UserSearch::where('phase2_token', $token)->where('end_date',null)->get();
                if(count($token_details)) {
                    Session::put('phase2_token', $token);
                    //echo $token_details[0]->active_page.' '.$token; exit;
                    
                    $redirect_token_url = Helper::getRedirectUrlForToken($token_details[0]->active_page);
                    return $redirect_token_url; 
                  
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

    public function getMakePhase2Payment()
    {
        try {
        	
        	return redirect('/invalid-token');
        }
        catch (\Exception $e) 
        {
            $result = ['exception_message' => $e->getMessage()];
            return view('errors.error', $result);
        }
    }
	
	public function savePhase2Token($token, $user_search_id){
		// Update Phase2 token
		// Added function 13-11-17
		$result = '1';
		try{
			DB::table('user_searches')
				->where('user_search_id',$user_search_id)
				->whereNull('user_searches.end_date')
				->update(['phase2_token' => $token, 'phase2_paid_amount' => config('constants.phase2Amt')]);
		}catch(\Illuminate\Database\QueryException $ex){ 
			$result = '0';
		}
		
		return $result;
	}
	
	public function savePhase2BillingAddress($user_id, $user_search_id, $request){
		// Update Phase2 billing address if checkbox is checked. 14/11/2017
		$system_object_type_id = Helper::toGenerateCommunicationObjectTypes('pf_lookups', 'name', 'address', $user_id);
        if(!empty($system_object_type_id)) {
			$date = date("Y-m-d H:i:s");

			$lookup_type = Helper::getLookupTypeIdFromName(config('constants.constants.lookup_type.address'));
							$where_condition['lookup_type_id'] = $lookup_type[0]->lookup_type_id;
			$address_type = Helper::getLookupIdFromName(config('constants.constants.lookup.address.phase2_billing_address'), $where_condition);
								
			$address_high_key = Helper::getHighKey('pf_addresses', 'address_id', $user_id);
				
			$userDetails = Helper::getMemberDetailsUsingUserId($user_id);
                        
            $notificationType = 0;
            if ($userDetails != null && !empty($userDetails)){
                $notificationType = $userDetails->receive_notification;

                $mobile_number = (!empty($userDetails->mobile_number))?($userDetails->mobile_number):''; 
                Session::put('mobile_number', $mobile_number);
            }

			$phase2_billing_address = PfAddress::create([
				'address_id' => (empty($address_high_key)) ? null : $address_high_key,
				'system_object_type_id' => $system_object_type_id,
				'ref_object_id' => $user_search_id,
				'address_type' => $address_type[0]->lookup_id,
				'receive_notification' => $notificationType,
				'address_line_1' => $request->street_number,
				'address_line_2' => $request->route,
				'city' => $request->locality,
				'postal_code' => $request->postal_code,
				'state' => $request->state_id,
				'county' => $request->county_id,
				'created_by' => $user_id,
				'created_at' => $date,
				'updated_by' => $user_id,
				'updated_at' => $date,
			]); 
		}
	
		return true;	
	}

    public function getImageFromGoogle($fields)
    {
        try {
        	$header = array("Accept: application/json");
			
			foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
			
			$fields_string = rtrim($fields_string, '&');
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 'https://factory.datanerds.com/api/v2/property?'.$fields_string);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		    curl_setopt($ch, CURLOPT_ENCODING, "gzip");
		 	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			 curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)');

			// execute!
		   $response = curl_exec($ch);
		    curl_close($ch);

			$response =json_decode($response);
			return $response->property;
			/*foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
			$fields_string = rtrim($fields_string, '&');

			$image = rawurlencode($fields_string);
		    $query = "https://ajax.googleapis.com/ajax/services/search/images?v=1.0&q=".$image."&imgsz=large&as_filetype=jpg";

		    $json = get_url_contents($query);
		    $data = json_decode($json);
		    $results = array(); //define array here!
		    foreach ($data->responseData->results as $result) {
		        $results[] = array("url" => $result->url, "alt" => $result->title);
		        break;
		    }

		    return $results[0]['url'];*/
        }
        catch (\Exception $e) 
        {
            $result = ['exception_message' => $e->getMessage()];
            return view('errors.error', $result);
        }
    }	

}
