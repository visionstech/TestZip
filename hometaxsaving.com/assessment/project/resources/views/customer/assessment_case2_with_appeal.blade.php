@extends('layouts.app')
@section('pageTitle', 'ASSESSMENT REVIEW') 

@section('css')
<link href="{{ asset('/project/resources/assets/customer/css/assessment.css') }}" rel="stylesheet">
<link href="{{ asset('/project/resources/assets/customer/css/custom_common.css') }}" rel="stylesheet">
@endsection
@section('content')
<div class="outer-search-list">
               <div class="top-search-list">
                  <ul>
                     <li>
                        <a href="{{ url('/search-address') }}">START NEW SEARCH</a>
                     </li>
                  </ul>
               </div>
               <div class="inner-top-search-list">
                  <ul>
                     <li class="active">
                        <a href="javascript:;">01</a>
                     </li>
                     <li class="active">
                        <a href="javascript:;">02</a>
                     </li>
                     <li class="active">
                        <a href="javascript:;">03</a>
                     </li>
                     <li class="active">
                        <a href="javascript:;">04</a>
                     </li>
                  </ul>
                  <div class="inner-wrapper-top-search-list">
                     <ul>
                        <li>
                           Search Address
                        </li>
                        <li>
                           Make a Payment
                        </li>
                        <li>
                           Verify Search
                        </li>
                        <li>
                           Assessment Review
                        </li>
                     </ul>
                  </div>
               </div>
               <div class="search-list">
                  <div class="search-heading">
                     <h4>
                        Valuation Summary
                     </h4>
                     <p>Your current tax year {{ ($data['search_details']->appeal_year != "")?$data['search_details']->appeal_year: date('Y') }} assessment is ${{ number_format($data['search_details']->total_assessment_value) }}. Based on recent sales of other similar properties in your neighborhood, your property appears to be over assessed.   These sales indicate a value of ${{ number_format($data['search_details']->appeal_amount) }} for your property.    
                    <br><br>
                   The potential one year tax savings that could result from a successful appeal is ${{ ($data['search_details']->real_tax_amount  != "")? number_format($data['search_details']->real_tax_amount) : " - " }}.  With this lower base value for tax year {{ ($data['search_details']->appeal_year != "")?$data['search_details']->appeal_year: date('Y') }} the potential savings for future years is even greater.</p>
                     <h5><b>Recommendation</b>: Appeal your Assessment</h5>
                    
                     <p>
                        Please remember that these are market driven estimates of assessed value.  They incorporate a variety of adjustments.  Your local assessment office may or may not agree with these adjustments and the final indicated assessed value.
                     </p>
                     <p>
                        <b>RESULTS ARE NOT GUARANTEED BY ASSESSMENT SOLUTIONS OR HOMETAXSAVINGS.COM.</b>
                     </p>
                     <p>Press "Confirm and Proceed" to produce a final narrative report for your property and the forms required for submission to the local tax authorities.  Once you complete the purchase you will be provided with simple instruction on what to do next and how to file your appeal.</p>
                     <p>An additional fee of $49.95 will be charged to your credit card.</p>
                  </div>
                  <div class="assessment-image">
                     <img src="{{ asset('/project/resources/assets/customer/css/images/assessment.jpg') }}" alt="assessment">
                  </div>
                  <div class="back-button">
                  <?php 
                     $token = $data['search_details']->token; 
                     $encryptedToken = Crypt::encrypt($token);
                   ?>
                   <a href="{{ url('/make-phase2-payment/'.$encryptedToken) }}" class="btn btn-ctrl">CONFIRM AND PROCEED</a>   
                  </div>
               </div>
            </div>
@endsection

