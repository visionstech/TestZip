@extends('layouts.app')
@section('title')
    @section('pageTitle', 'SEARCH ADDRESS')
@endsection
@section('css')
<link href="{{ asset('/project/resources/assets/customer/css/Search.css') }}" rel="stylesheet">
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
                     <li>
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
               {!! Form::open(['url' => 'search-address', 'id' => 'search_address_form','class'=>'', 'novalidate' => 'novalidate']) !!}
                 {{ csrf_field() }}
               <div class="search-list">
                  <div class="search-heading">
                      @if(Session::has('success_msg'))
                        <h4> {{ Session::get('success_msg') }}</h4>
                      @endif
                      <?php 
                        $submitButtonText = "CONTINUE";
                        if( !empty(Session::get('firstTime')) && Session::get('firstTime') == 1 ){
                          $submitButtonText = "CONFIRM";
                        }
                        Session::forget('success_msg'); 
                        Session::forget('firstTime'); 
                        Session::forget('user_search_id'); 
                      ?>
                     
                  </div>
                  
                  <div class="assessment-form">
                  <!-- ,'onsubmit'=>"return validateAddressSearch()" -->
                  <div class="inner-wrapper-assessment-form">
                     
                      @include('errors.user_error')
                    <div class="alert alert-danger errorAlertMsgMain text-left error_div_description" style="display: none">
                        <strong></strong> There were some problems with your input.
                        <span id="invalid_address_msg"></span>
                    </div>
                    <?php $search_id = ($userDetail->count())?($userDetail->user_search_id):''; ?>

                    {!! Form::hidden('search_id',$search_id, $attributes = ['id' => 'search_id']) !!}
                    {!! Form::hidden('in_out_case',0, $attributes = ['id' => 'in_out_case']) !!}

                    <?php $firstname=($userDetail->count())?($userDetail->first_name):''; ?>

                    {!! Form::hidden('first_name',$firstname, $attributes = ['id' => 'first_name','class' => 'form-control', 'placeholder'=>'First Name', 'maxlength' => '50', 'autocomplete' => 'off', 'required'=>'required']) !!}

                    <?php $lastname=($userDetail->count())?($userDetail->last_name):''; ?>

                    {!! Form::hidden('last_name',$lastname, $attributes = ['id' => 'last_name','class' => 'form-control', 'placeholder'=>'Last Name', 'maxlength' => '50', 'autocomplete' => 'off', 'required'=>'required']) !!}
                                       
                    <?php $emailAddress=(Auth::user())?(Auth::user()->email):''; ?>
                
                    {!! Form::hidden('email', $value = $emailAddress, $attributes = array('class'=>"form-control",'placeholder'=>'Email address', 'id'=>'email', 'autocomplete' => 'off')); !!}
               
                    <?php $mobileNumber = ( !empty($addressDetail) )?($addressDetail->mobile_number):''; ?>
                     
                    {!! Form::hidden('mobile_number', $value = $mobileNumber, $attributes = array('class'=>"form-control",'placeholder'=>'Mobile number', 'id'=>'mobile_number', 'autocomplete' => 'off')); !!}
               
                    <div class="inner-assessment-form form-group has-feedback element">
                      <?php 
                        $prefillAddress = (!empty($addressDetail))?($addressDetail->address_line_1." "):''; 
                        $prefillAddress .= (!empty($addressDetail))?($addressDetail->address_line_2.", "):'';
                        $prefillAddress .= (!empty($addressDetail))?($addressDetail->city.", "):'';
                        $prefillAddress .= (!empty($stateName))?($stateName[0]['state_abbr'].", "):'';
                        $prefillAddress .= (!empty($addressDetail))?($addressDetail->postal_code.", "):'';
                        ?>
                      <label>Search Address</label>
                      <div class="form-section">
                        <div class="inner-assessment-form-input-search"> 
                              {!! Form::text('autocomplete_search', $value = $prefillAddress, $attributes = array('class'=>"form-control",'placeholder'=>'Search Address', 'maxlength' => '100', 'id'=>'autocomplete', 'autocomplete' => 'off')); !!}                        
                              <span class="icon error error_icon"><span class="tooltiptext"></span></span>
                        </div>
                      </div>
                    </div>

                    <div class="inner-assessment-form form-group has-feedback element">
                    <?php $SearchAddress2 = (!empty($addressDetail))?($addressDetail->address_line_1):''; ?>
                      <label>Street <span>*</span></label>
                      <div class="street-divided">
                        <div class="inner-assessment-form-input-search"> 
                            {!! Form::text('street_number', $value = $SearchAddress2, $attributes = array('class'=>"form-control street-house-left",'placeholder'=>'Street Number', 'maxlength' => '100', 'id'=>'street_number', 'autocomplete' => 'off')); !!}
                            <span class="icon error error_icon"><span class="tooltiptext"></span></span>
                        </div>
                      </div>
                    </div>
                    <div class="inner-assessment-form form-group has-feedback element">
                    <?php $SearchAddress3 = (!empty($addressDetail))?($addressDetail->address_line_2):''; ?>
                    <label><span></span></label>
                       <div class="street-divided">
                        <div class="inner-assessment-form-input-search"> 
                            {!! Form::text('route', $value = $SearchAddress3, $attributes = array('class'=>"form-control",'placeholder'=>'Street', 'maxlength' => '100', 'id'=>'route', 'autocomplete' => 'off')); !!}
                           <span class="icon error error_icon"><span class="tooltiptext"></span></span>
                         
                      </div>
                    </div>
                    </div>
                    <div class="form-group element autocompletesearch autocomplete_search_section" style="display: none">
                        <label></label>
                        <div class="">
                            <div class="" id="autocomplete_search_result_div">
                                <ul id="autocomplete_search_result"></ul>
                            </div>
                        </div>
                    </div>
                        <div class="inner-assessment-form form-group has-feedback element">
                         <?php $city = (!empty($addressDetail))?($addressDetail->city):''; ?>
                           <label>City <span>*</span></label>
                            <div class="">
                              <div class="inner-assessment-form-input-search"> 
                                  {!! Form::text('locality', $value = $city, $attributes = array('class'=>"form-control",'placeholder'=>'City', 'maxlength' => '100', 'id'=>'locality', 'autocomplete' => 'off')); !!}
                                  <span class="icon error error_icon"><span class="tooltiptext"></span></span>
                            </div>
                          </div>
                        </div>
                        <div class="inner-assessment-form form-group has-feedback element">
                        
                          <?php 
                         //echo "<pre>sdf";print_r($addressDetail);exit;
                          $state = (!empty($stateName))?($stateName[0]['state_abbr']):'';  ?>   
                         
                           <label>State <span>*</span></label>
                           <div class="dropdown">
                            <div class="inner-assessment-form-input-search"> 
                              {!! Form::select('administrative_area_level_1',array_replace(['' => 'Select State '],$states), $state ,['id' => 'administrative_area_level_1','class' => 'form-control', 'autocomplete' => 'off']) !!} 
                              <span class="icon error error_icon"><span class="tooltiptext"></span></span>
                          </div>
                        </div>
                        </div>

                        <div class="inner-assessment-form form-group has-feedback element">
                         <?php $Zip = (!empty($addressDetail))?($addressDetail->postal_code):''; ?>
                           <label>Zip Code <span>*</span></label>
                           <div class="">
                            <div class="inner-assessment-form-input-search"> 
                              {!! Form::text('postal_code', $value = $Zip, $attributes = array('class'=>"form-control",'placeholder'=>'Zip code', 'maxlength' => '100', 'id'=>'postal_code', 'autocomplete' => 'off')); !!}
                              <span class="icon error error_icon"><span class="tooltiptext"></span></span>
                          </div>
                        </div>
                        </div>
                        
                        <div class="inner-assessment-form form-group has-feedback element">
                        <?php 
                        //echo "<pre>";print_r($countyName);exit;
                        $country = (!empty($countyName))?($countyName[0]['county_name']):''; 
                      
                        ?>
                          {!! Form::hidden('county_name', null, $attributes = ['id' => 'county_name']) !!}

                           <label>County <span>*</span></label>
                           <div class="dropdown">
                            <div class="inner-assessment-form-input-search state_counties"> 
                              {!! Form::select('administrative_area_level_2',array_replace(['' => 'Select County '],$counties), $country ,['id' => 'administrative_area_level_2','class' => 'form-control', 'autocomplete' => 'off']) !!} 
                             <span class="icon error error_icon"><span class="tooltiptext"></span></span>
                             <div class="msg-data">
                              <?php 
                                $county_link = (!empty($countyName))?($countyName[0]['county_link']):''; 
                                $linkText = (!empty($county_link))?'click here to go to your local jurisdiction.':'';
                              ?>
                                <a href="{{ $county_link }}" target="_blank" class="text-bold" id="county_link"><!-- {{ $linkText }} --></a>
                              </div>
                          </div>
                        </div>
                        </div>
                        <div class="inner-assessment-form form-group has-feedback element assesment_year_div" style="display: none;">
                        <?php   $latest_assesement_year = ($userDetail->count())?($userDetail->latest_assesement_year):''; 
                          $latest_assesement_year = ($newSearch == 1)?'':($latest_assesement_year);
                         ?>
                           <label>Latest Assesment Year <span>*</span></label>
                           <div class="">
                            <div class="inner-assessment-form-input-search"> 
                                {!! Form::text('assessment_year', $value = $latest_assesement_year, $attributes = array('class'=>"form-control",'placeholder'=>'Latest Assesment Year', 'id'=>'assessment_year', 'maxlength' => '4', 'autocomplete' => 'off')); !!}
                                <span class="icon error error_icon"><span class="tooltiptext"></span></span>
                              </div>
                            </div>
                        </div>
                        <div class="inner-assessment-form form-group has-feedback element conf_assesment_year_div" style="display: none;">
                           <label>Confirm Latest Assesment Year <span>*</span></label>
                           <div class="">
                            <div class="inner-assessment-form-input-search"> 
                                {!! Form::text('confirm_assessment_year', $value = $latest_assesement_year, $attributes = array('class'=>"form-control",'placeholder'=>'Confirm Latest Assesment Year', 'id'=>'confirm_assessment_year', 'maxlength' => '4', 'autocomplete' => 'off')); !!}
                                <span class="icon error error_icon"><span class="tooltiptext"></span></span>
                              </div>
                            </div>
                        </div>
                       <!--  <div class="continue-button display">
                          {!! Form::submit('CONTINUE',array('class'=>"btn btn-ctrl", 'id'=> 'search_address_btn')); !!} 
                          </div> -->
                       <!--  <div class="inner-assessment-form">
                          <div class="">
                          <!--   <div class="inner-assessment-form-input-search">  
                                <div class="msg-data">
                                    <a href="#" target="_blank" class="text-bold" id="county_link"><?php //echo $county_link = (!empty($countyName))?($countyName[0]['county_link']):''; ?></a>
                                </div>
                              </div>
                          </div> -->
                        <!-- </div> -->
                         <!-- Amit 15 feb 2018 -->
                           <div class="inner-login-form inner-inner special">
                              <label></label>
                              {!! Form::checkbox('condominium', $value = 1, true, $attributes = array('class'=>"", 'id' => 'condominium', 'autocomplete' => 'off')); !!}
                                <span>Is your property "Single Family Residential, Town House, or Row House" ? </span>
                                <!-- <p class="con_msg" style="display: none; color: red;">HomeTaxSavings.com does not cover property types that are not Single Family Residential, Town House, or Row House. Please  <a href="http://tax.solutionsgroup.us/wp/contact-us/">Contact us</a> to know when other property types (condominiums and non-residential) will be available. If you have a commercial property, we can connect you to a commercial property tax service</p> -->
                           </div>
                           
                        <!-- Amit 15 feb 2018 -->
                       
                      </div>
                    <!--  <div class="inner-wrapper-assessment">
                        <div class="inner-need-help">
                           <h4>
                              NEED HELP?
                           </h4>
                           <p>
                              If you are having trouble finding your new assessment values, please review the following information.  You can also contact a representative at <a>EMAIL</a> for more help.
                              
                           </p>
                           <ul>
                              <li>
                                 Your new assessment values can be found on the notice mailed to you from your local jurisdiction. <a>Please click here for a sample notice.</a>
                              </li>
                              <li>
                                 If you don’t have your notice, you can find your new assessment values online. Please <a>click here to go to your local jurisdiction.</a>
                              </li>
                              <li>
                                 If you still can’t find your new assessment values please <a>click here</a> and we will send you your values within 48 hours
                              </li>
                           </ul>
                        </div>
                        
                        <div class="need-help-image">
                           <img src="{{ asset('/project/resources/assets/customer/css/images/need-help.png') }}">
                        </div>
                        </div> -->
                        <p class="con_msg" style="display: none; color: red;">HomeTaxSavings.com does not cover property types that are not Single Family Residential, Town House, or Row House. Please  <a target="_blank" href="https://www.hometaxsavings.com/enquiry">Contact us</a> to know when other property types (condominiums and non-residential) will be available. If you have a commercial property, we can connect you to a commercial property tax service</p>
                        

                        <div class="continue-button">
                          {!! Form::submit($submitButtonText,array('class'=>"btn btn-ctrl", 'id'=> 'search_address_btn')); !!} 
                          </div>
                          
                  
                     
                  </div>
               </div>
               {!! Form::close() !!}
            </div>
         

      <div id="map_canvas" class="no-top-margin" style="display:none; width: 100%; height: 420px;"></div>
    
@endsection
@section('js')

<script src="{{ asset('/project/resources/assets/customer/js/autocomplete_maps.js') }}"></script> 
<script src="{{ asset('/project/resources/assets/customer/js/search_address_js.js') }}"></script> 


<!-- <script src="{{ asset('/project/resources/assets/customer/js/jquery.validate.js') }}"></script>  -->

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBlQK-PlX2L5HD_bahlqPjkixMd2PkSxdU&libraries=places&callback=initialize" async defer"></script>
<script src="{{ asset('/project/resources/assets/customer/js/jquery.validate.new.js') }}"></script>

<script>
  $( document ).ready(function() {
      $("#autocomplete").change(function(){
        $("#search_id").val("");
      });
  });

  function minmax(value, min, max) 
  {
      var d = new Date();
      var n = d.getFullYear();
      if(parseInt(value) < min || isNaN(parseInt(value))) 
          return 0; 
      else if(parseInt(value) > max) 
          return n; 
      else return value;
  }

  $("#search_address_form").validate({
      rules: {
            street_number: {
                required: true,
                maxlength: 100,
            },
            route: {
                required: true,
                maxlength: 100,
            },
            locality: {
                required: true,
                maxlength: 100,
            },
            administrative_area_level_1: {
                required: true
            },
            postal_code: {
                required: true,
                maxlength: 100
            },
            administrative_area_level_2: {
                required: true
            }
        },

        messages: {
            street_number: {
                required: "This field is Required"
            },
            route: {
                required: "This field is Required"
            },
            confirm_assessment_year:{
              equalTo : "Latest assessment year should be same as confirm assessment year."
            }
        },
        submitHandler: function (form) {
          if($('#condominium').is(":checked") == true ) {
            $('.con_msg').hide();
            form.submit();
          } else {
            $('.con_msg').show();
            return false;
          }
      }
  });
    $('#condominium').on('click', function(){
      if($('#condominium').is(":checked") == true ) {
        $('.con_msg').hide();
      } else {
        $('.con_msg').show();
      }
    });

  $('#administrative_area_level_1,#administrative_area_level_2').change(function()
  {

    /* 8 Feb 2018 For In-Out Cycle */
    var State_abbrivation =$('#administrative_area_level_1').val();
    if(State_abbrivation=='MD'){
      $('.assesment_year_div').show();
      $('.conf_assesment_year_div').show();
      $('#in_out_case').val(1);
      $('#assessment_year').rules('add', {
            required: true,
            number:true,
            max: 2018
      });
      $('#confirm_assessment_year').rules('add', {
            required: true,
            number:true,
            max: 2018,
            equalTo: "#assessment_year"
      });
    }else{
      $('.assesment_year_div').hide();
      $('.conf_assesment_year_div').hide();
      $('#in_out_case').val(0);
      $('#assessment_year').rules('add', {
            required: false,
            number:false,
            max: false
      });
      $('#confirm_assessment_year').rules('add', {
            required: false,
            number:false,
            equalTo: false,
            max: false
      });
    }
                    
    /* End 8 Feb 2018 */

  });

</script>

@endsection
