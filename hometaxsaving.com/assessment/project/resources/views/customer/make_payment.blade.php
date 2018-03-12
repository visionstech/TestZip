@extends('layouts.app')
@section('title')
    @section('pageTitle', 'INITIAL ASSESSMENT PAYMENT')
@endsection
@section('css')
<link href="{{ asset('/project/resources/assets/customer/css/make-payment.css') }}" rel="stylesheet">
<link href="{{ asset('/project/resources/assets/customer/css/custom_common.css') }}" rel="stylesheet">
@endsection
@section('content')
<div class="outer-search-list">
               <div class="top-search-list">
                <a href="{{ url('/search-address') }}">START NEW SEARCH</a>
               </div>
               <div class="inner-top-search-list">
                  <ul>
                     <li class="active">
                        <a href="javascript:;">01</a>
                     </li>
                     <li class="active">
                        <a href="javascript:;">02</a>
                     </li>
                     <li>
                        <a href="javascript:;">03</a>
                     </li>
                     <li>
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
               {!! Form::open(['url' => 'make-payment', 'id' => 'make_payment_form', 'novalidate' => 'novalidate','class'=>'']) !!}
               <div class="search-list">
                  <div class="search-heading">
                     <h4>
                        The initial review is only $9.95. 
                     </h4>
                  </div>
                  <?php  $card_types = ['MasterCard'=> 'MasterCard', 'Visa'=> 'Visa', 'American Express'=> 'American Express', 'Discover'=> 'Discover']; 
                            $months = ['01'=> 'January', '02'=> 'February', '03'=> 'March', '04'=> 'April', '05'=> 'May', '06'=> 'June', '07'=> 'July', '08'=> 'August', '09'=> 'September', '10'=> 'October', '11'=> 'November', '12'=> 'December'];
                            $date = date('Y');
                            $max_date = $date+10;
                            //$years = range($date, $date+10);

                            for ($i = $date; $i <= $max_date; $i++) {
                              $years[$i] = $i;
                            }
                            
                            if(isset($token_details)) {
                                $token_exist = '1';
                            }
                            else {
                                $token_exist = '0';
                            }                            
                    ?>
                    @include('errors.user_error')
                    
                    <div class="alert alert-danger errorAlertMsgMain text-left error_div_description" style="display: none">
                        <strong></strong> There were some problems with your input.
                        <span id="invalid_address_msg"></span>
                   </div>
                  <div class="assessment-form">
                    <div class="inner-wrapper-assessment-form">
                     
                        <div class="inner-assessment-form">
                           <label>
                              Credit Card Type
                           </label>

                           <div class="dropdown">
                                <div class="inner-assessment-form-input"> 
                                    {!! Form::select('card_type',array_replace(['' => 'Select Card Type '],$card_types), 'Discover' ,['id' => 'card_type','class' => 'form-control', 'autocomplete' => 'off']) !!}
                    
                            </div>
                          </div>
                          
                        </div>
                        <div class="inner-assessment-form">
                           <label>
                              Credit Card Number <span>*</span>
                           </label>
                           <div class="inner-assessment-form-input"> 
                         
                                {!! Form::text('card_number', $value = null, $attributes = array('class'=>"form-control",'placeholder'=>'Card Number', 'maxlength' => '16', 'id'=>'card_number', 'autocomplete' => 'off')); !!}
                  
                            </div>
                        </div>
                        
                        <div class="inner-assessment-form inner-inner">
                          <label>
                           Expiration Date <span>*</span>
                        </label>
                        <div class="inner-assessment-form-input inner-inner special">
                        <div class="dropdown">
                        
                            {!! Form::select('ex_month',array_replace(['' => 'Month'],$months), '02' ,['id' => 'ex_month','class' => 'form-control', 'autocomplete' => 'off']) !!}
          
                        </div>
                        <div class="dropdown">
                      
                            {!! Form::select('ex_year',array_replace(['' => 'Year'],$years), '2020' ,['id' => 'ex_year','class' => 'form-control', 'autocomplete' => 'off']) !!}
                  
                        </div>
                        </div>
                        </div>
                        <div class="inner-assessment-form inner-inner inner-inner-inner">
                          <label>
                           CVV Code <span>*</span>
                        </label>
                        <div class="inner-assessment-form-input inner-inner special"> 
                      
                  
                            <!--input id="cvv" name="cvv" class="form-control" placeholder="CVV" autocomplete ='off' type="password" inputmode="numeric" minlength="3" maxlength="4"-->
                            {!! Form::password('cvv', null, $attributes = array('class'=>"form-control",'placeholder'=>'CVV', 'minlength'=>3,'maxlength' => 4, 'id'=>'cvv', 'autocomplete' => 'off')); !!}
                    
                        </div>
                        </div>
                     
                        <div class="inner-assessment-form">
                          <label>
                           Name on Card <span>*</span>
                        </label>
                        <div class="inner-assessment-form-input"> 
                        
                            {!! Form::text('name_on_card',null, $attributes = ['id' => 'name_on_card','class' => 'form-control', 'placeholder'=>'Name on Card', 'id'=>'name_on_card', 'maxlength' => '150', 'autocomplete' => 'off']) !!}
                      
                        </div>
                        </div>
                        <div class="outer-wrapper-inner-assessment">
                        <div class="inner-assessment-form inner-inner">
                          <label>
                           Billing Address <span>*</span>
                           
                        </label>
                        <div class="inner-assessment-form-input inner-inner special"> 
                        @if($token_exist == '1')    
                            <?php $search_address_text = '';
                                if($address_street_number != '') {
                                    $search_address_text .= $address_street_number;
                                }
                                if($address_street != '') {
                                    $search_address_text .= ' '.$address_street;
                                }
                                if($address_city != '') {
                                    $search_address_text .= ', '.$address_city;
                                }
                                if($state_abbr != '') {
                                    $search_address_text .= ', '.$state_abbr;
                                }
                                if($address_zipcode != '') {
                                    $search_address_text .= ', '.$address_zipcode;
                                }
                                $search_address_text .= ', USA';
                            ?>
                            {!! Form::text('autocomplete_search', $value = $search_address_text, $attributes = array('class'=>"form-control",'placeholder'=>'Search Address', 'autocomplete' => 'off')); !!}
                        @else
                            <?php $session_autocomplete = (Session::has('autocomplete_search')) ? Session::get('autocomplete_search') : null; ?>
                            {!! Form::text('autocomplete_search', $session_autocomplete, $attributes = array('class'=>"form-control",'placeholder'=>'Search Address', 'id'=>'autocomplete', 'autocomplete' => 'off')); !!}
                        @endif
                        </div>
                        </div>
                        <div class="form-group autocompletesearch has-feedback element autocomplete_search_section autocompletesearch_full"> 
                            <label></label>
                            <div class="form-section">
                                <div class="" id="autocomplete_search_result_div">
                                    <ul id="autocomplete_search_result"></ul>
                                </div>
                            </div>
                        </div>
                        @if($token_exist == '1')    
                            {!! Form::hidden('street_number', $value = $address_street_number, $attributes = array('class'=>"form-control",'placeholder'=>'Street Number', 'autocomplete' => 'off', 'maxlength' => '100')); !!}
                        @else
                            <?php $session_street_number = (Session::has('search_street_number')) ? Session::get('search_street_number') : null; ?>
                            {!! Form::hidden('street_number', $session_street_number, $attributes = array('class'=>"form-control",'placeholder'=>'Street Number', 'maxlength' => '100', 'id'=>'street_number', 'autocomplete' => 'off')); !!}
                        @endif
                        @if($token_exist == '1')    
                            {!! Form::hidden('route', $value = $address_street, $attributes = array('class'=>"form-control",'placeholder'=>'Street', 'autocomplete' => 'off', 'maxlength' => '100')); !!}
                        @else
                            <?php $session_street = (Session::has('search_street')) ? Session::get('search_street') : null; ?>
                            {!! Form::hidden('route', $session_street, $attributes = array('class'=>"form-control",'placeholder'=>'', 'maxlength' => '100', 'id'=>'route', 'autocomplete' => 'off')); !!}
                        @endif

                        <div class="inner-assessment-form inner-inner inner-inner-inner">
                          <label>
                           City <span>*</span>
                        </label>
                        <div class="inner-assessment-form-input inner-inner special"> 
                         @if($token_exist == '1')    
                            {!! Form::text('locality', $value = $address_city, $attributes = array('class'=>"form-control",'placeholder'=>'City', 'autocomplete' => 'off', 'maxlength' => '100')); !!}
                        @else
                            <?php $session_city = (Session::has('search_city')) ? Session::get('search_city') : null; ?>
                            {!! Form::text('locality', $session_city, $attributes = array('class'=>"form-control",'placeholder'=>'City', 'maxlength' => '100', 'id'=>'locality', 'autocomplete' => 'off')); !!}
                        @endif
                        </div>
                        </div>
                        </div>
                        <div class="inner-assessment-form inner-inner">
                           <label>
                              State <span>*</span>
                           </label>
                           <div class="inner-assessment-form-input inner-inner case"> 
                           <div class="dropdown">
                               @if($token_exist == '1')  
                                    {!! Form::select('administrative_area_level_1',array_replace(['' => 'Select State '],$states), $state_abbr ,['id' => 'billing_state','class' => 'form-control', 'autocomplete' => 'off']) !!} 
                                @else
                                    <?php $session_state = (Session::has('search_state')) ? Session::get('search_state') : null; ?>
                                    {!! Form::select('administrative_area_level_1',array_replace(['' => 'Select State '],$states), $session_state ,['id' => 'administrative_area_level_1','class' => 'form-control', 'autocomplete' => 'off']) !!}
                                @endif
                            </div>   
                            </div>                       
                        </div>
                        <div class="inner-assessment-form inner-inner inner-inner-inner">
                          <label>
                           Zip <span>*</span>

                        </label>
                        <div class="inner-assessment-form-input inner-inner special"> 
                         @if($token_exist == '1')  
                            {!! Form::text('postal_code', $value = $address_zipcode, $attributes = array('class'=>"form-control",'placeholder'=>'Zip code', 'autocomplete' => 'off', 'maxlength' => '100', 'id'=>'billing_zipcode')); !!}
                        @else
                            <?php $session_zipcode = (Session::has('search_zipcode')) ? Session::get('search_zipcode') : null; ?>
                            {!! Form::text('postal_code', $session_zipcode, $attributes = array('class'=>"form-control",'placeholder'=>'Zip code', 'maxlength' => '100', 'id'=>'postal_code', 'autocomplete' => 'off')); !!}
                        @endif
                        </div> 
                        </div>
                        @if($token_exist == '1')    
                            {!! Form::hidden('administrative_area_level_2', $value = $county_name, $attributes = array('class'=>"form-control",'placeholder'=>'Country', 'autocomplete' => 'off', 'maxlength' => '100')); !!}
                        @else
                            <?php $session_county = (Session::has('search_county')) ? Session::get('search_county') : null; ?>
                            {!! Form::hidden('administrative_area_level_2', $session_county, $attributes = array('class'=>"form-control",'placeholder'=>'Street Number', 'maxlength' => '100', 'id'=>'street_number', 'autocomplete' => 'off')); !!}
                        @endif

                        @if($token_exist == '0')  
                            {!! Form::hidden('ex_date', null, $attributes = ['id' => 'ex_date']); !!}   
                        @endif
                            {!! Form::hidden('token_exist', $token_exist, $attributes = ['id' => 'token_exist']); !!}
                        @if($token_exist == '1')  
                            {!! Form::hidden('token', $token_details->token, $attributes = ['id' => 'token']); !!}
                        @endif
                     </div>
                     <div class="inner-wrapper-assessment">
                      <img src="{{ asset('/project/resources/assets/customer/css/images/credit-card.jpg') }}">
                        </div>
                        </div>
                   
                     <div class="continue-button">
                        <!--@if($token_exist == '1')  
                            <?php $token_link = url('/verify-address'); ?>
                            <a href="{{ $token_link }}" class="btn btn-ctrl">COMPLETE PAYMENT</a>
                        @else
                            {!! Form::submit('COMPLETE PAYMENT',array('class'=>"btn-ctrl", 'id'=> 'make_payment_btn')); !!} 
                        @endif-->
                        {!! Form::submit('COMPLETE PAYMENT',array('class'=>"btn-ctrl", 'id'=> '')); !!} 
                     </div>
                     </div>
                  
 {!! Form::close() !!} 
               </div>
            </div>

@endsection
@section('js')
<script src="{{ asset('/project/resources/assets/customer/js/make_payment_js.js') }}"></script> 
<script src="{{ asset('/project/resources/assets/customer/js/autocomplete.js') }}"></script> 

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBlQK-PlX2L5HD_bahlqPjkixMd2PkSxdU&libraries=places&callback=initAutocomplete" async defer"></script>
<script src="{{ asset('/project/resources/assets/customer/js/jquery.validate.new.js') }}"></script>

<script>
$("#make_payment_form").validate({
      rules: {
            card_type: {
                required: true
            },
            card_number: {
                required: true,
                number:true
            },
            ex_month: {
                required: true
            },
            ex_year: {
                required: true
            },
            cvv: {
                required: true,
                number:true
            },
            name_on_card: {
                required: true,
                maxlength: 150
            },
            autocomplete_search: {
                required: true,
                maxlength: 100
            },
            locality: {
                required: true,
                maxlength: 100
            },
            postal_code: {
                required: true,
                maxlength: 100
            }
        },
        submitHandler: function (form) {
          $(".loader-overlay").show();
          $("#loaderText").text(PAYMENT_LOADING_MSG);
          form.submit();
          setTimeout(function(){
            $("#loaderText").text(FETCHING_LOADING_MSG);
          }, 3000);
            console.log("Submitted!");
        }
  });

</script>
@endsection
