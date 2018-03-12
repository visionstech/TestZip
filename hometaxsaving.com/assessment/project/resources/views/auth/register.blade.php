@extends('layouts.app')
@section('pageTitle', 'REGISTER') 
@section('css')

<link href="{{ asset('/project/resources/assets/customer/css/sign-up.css') }}" rel="stylesheet">
<link href="{{ asset('/project/resources/assets/customer/css/custom_common.css') }}" rel="stylesheet">
@endsection
@section('content')
   <div class="container">
            <div class="inner-free-regs">
               <h4>
                  FREE REGISTRATION
               </h4>
               <p>
                  Register today and realize a number of benefits from HomeTaxSavings™!
               </p>
               <ul>
                  <li>
                     Your free membership with HomeTaxSavings.com™ will ensure you don’t miss out on the opportunity to challenge your assessed value and save money.
                  </li>
                  <li>
                    Even if your assessment notice gets lost in the mail, we will notify when your new assessed value is issued along with advanced warning of the upcoming appeal deadline.
                  </li>
                  <li>
                     Periodic updates on local market conditions in the housing market and other relevant assessment issues and filing requirements.
                  </li>
                  <li>
                     Tips on when it would be advantageous to request the local assessor come to your home for an inspection. (For example, your property is in need of major repairs and renovation which should affect the overall value and what you are paying taxes on)
                  </li>
               </ul>
            </div>
             <form class="outer-login-regs" method="post" action="{{ url('/registerUser') }}" id="registration_form" novalidate="novalidate">
             {!! Form::hidden('in_out_case',0, $attributes = ['id' => 'in_out_case']) !!}
            <div class="outer-wrapper-login-regs">
               <div class="inner-login-regs">
               <h4>SIGN UP HERE</h4>
              
                  {{ csrf_field() }}
                  <?php   
                     if(isset($token_details)) {
                        $token_exist = '1';
                     } else {
                        $token_exist = '0';
                     }
                  ?>
                  @if ($errors->has('server_error_msg'))
                     <span class="help-block" style="color: red;">
                        <strong>{{ $errors->first('server_error_msg') }}</strong>
                     </span>
                  @endif

                  @if ($errors->has('server_success_msg'))
                     <span class="help-block" style="color: green;">
                        <strong>{{ $errors->first('server_error_msg') }}</strong>
                     </span>
                  @endif 
                  <div class="login-form">
                     <label>
                        First Name <span>*</span><br>
                     </label>
                     <input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}" placeholder="Enter First Name" >
                     @if ($errors->has('first_name'))
                        <span class="help-block">
                           <strong>{{ $errors->first('first_name') }}</strong>
                        </span>
                     @endif
                  </div>
                   <div class="login-form">
                     <label>
                       Last Name <span>*</span><br>
                     </label>
                     <input id="last_name" type="text" name="last_name" value="{{ old('last_name') }}" placeholder="Enter Last Name" >

                     @if ($errors->has('last_name'))
                        <span class="help-block">
                           <strong>{{ $errors->first('last_name') }}</strong>
                        </span>
                     @endif
                  </div>

                  <div class="login-form">
                     <label>
                        Email <span>*</span><br>
                     </label>
                     <input id="email" type="email" placeholder="Enter Email" name="email" value="{{ old('email') }}">

                     @if ($errors->has('email'))
                        <span class="help-block">
                           <strong>{{ $errors->first('email') }}</strong>
                        </span>
                     @endif
                  </div>
                  <div class="login-form">
                     <label>
                        Cell Phone <span>*</span><br>
                     </label>
                     <input id="mobile_number" type="text" placeholder="Enter Cell Phone" name="mobile_number" value="{{ old('mobile_number') }}">

                    @if ($errors->has('mobile_number'))
                        <span class="help-block">
                            <strong>{{ $errors->first('mobile_number') }}</strong>
                        </span>
                    @endif
                  </div>
               
         </div>
          <div class="third_grid">
               <img src="{{ asset('/project/resources/assets/customer/css/images/login-image.png') }}" alt="login-image">
            </div>
            </div>
            <div class="outer-login-form">
                  <div class="inner-wrapper-login-form">
                  <div class="inner-login-form notoppadineg">
                  <label>
                     Password <span>*</span>
                  </label>
                  <input id="password" type="password" placeholder="Enter Password" name="password">

                 @if ($errors->has('password'))
                     <span class="help-block">
                        <strong>{{ $errors->first('password') }}</strong>
                     </span>
                 @endif
               </div>
               <div class="inner-login-form inner-inner notoppadineg">
                  <label>
                    Confirm Password <span>*</span>
                  </label>
                  <input id="password-confirm" type="password" placeholder="Re-Enter Password" name="password_confirmation">

                  @if ($errors->has('password_confirmation'))   
                     <span class="help-block">
                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                     </span>
                  @endif
               </div>
               <div class="inner-login-form inner-inner special">
                  <label></label>
                  {!! Form::checkbox('receive_notification', $value = 1, true, $attributes = array('class'=>"", 'id' => 'receive_notification', 'autocomplete' => 'off')); !!}
                    <span>Subscribe for text messages</span>
               </div>
               <div class="inner-login-form inner-inner-inner notoppadineg">
                  <label>
                    Search Address <span>*</span>
                  </label>
                  @if($token_exist == '1')    
                     {!! Form::text('autocomplete_search', $value = $token_details->search_street, $attributes = array('class'=>"form-control",'placeholder'=>'Search Address', 'autocomplete' => 'off', 'maxlength' => '100', 'disabled'=>'disabled')); !!}
                 @else
                     {!! Form::text('autocomplete_search', $value = null, $attributes = array('class'=>"form-control",'placeholder'=>'Search Address', 'maxlength' => '100', 'id'=>'autocomplete', 'autocomplete' => 'off')); !!}
                 @endif
                  @if ($errors->has('autocomplete_search'))
                     <span class="help-block">
                         <strong>{{ $errors->first('autocomplete_search') }}</strong>
                     </span>
                  @endif
               </div>
               <div class="inner-login-form notoppadineg2">
                  <label>
                     Street <span>*</span>
                  </label>
                  <input id="street_number" type="text" placeholder="Street Number" name="street_number" value="{{ old('street_number') }}">

                    @if ($errors->has('street_number'))
                        <span class="help-block">
                            <strong>{{ $errors->first('street_number') }}</strong>
                        </span>
                    @endif
               </div>
                <div class="inner-login-form inner-inner notoppadineg2">
                  <label>
                   
                  </label>
                  <input id="route" type="text" placeholder="Street" name="route" value="{{ old('route') }}">

                    @if ($errors->has('route'))
                        <span class="help-block">
                            <strong>{{ $errors->first('route') }}</strong>
                        </span>
                    @endif
               </div>
               <div class="form-group element autocompletesearch autocomplete_search_section" style="display: none">
                  <label></label>
                  <div class="form-section">
                     <div class="" id="autocomplete_search_result_div">
                           <ul id="autocomplete_search_result"></ul>
                     </div>
                  </div>
               </div>
               <div class="inner-login-form notoppadineg2">
                  <label>
                    City <span>*</span>
                  </label>
                  <input id="locality" type="text" placeholder="City" name="locality" value="{{ old('locality') }}">

                    @if ($errors->has('locality'))
                        <span class="help-block">
                            <strong>{{ $errors->first('locality') }}</strong>
                        </span>
                    @endif
               </div>
                <div class="inner-login-form inner-inner notoppadineg2">
                  <label>State <span>*</span>
                  </label>
                  <div class="dropdown">
                     @if($token_exist == '1')  
                        {!! Form::select('administrative_area_level_1',array_replace(['' => 'Select State '],$states), $token_details->search_state ,['id' => 'administrative_area_level_1','class' => 'form-control', 'autocomplete' => 'off', 'disabled'=>'disabled']) !!} 
                    @else
                        {!! Form::select('administrative_area_level_1',array_replace(['' => 'Select State '],$states), null ,['id' => 'administrative_area_level_1','class' => 'form-control', 'autocomplete' => 'off']) !!} 
                    @endif
                     @if ($errors->has('administrative_area_level_1'))
                        <span class="help-block">
                            <strong>{{ $errors->first('administrative_area_level_1') }}</strong>
                        </span>
                    @endif
                  </div>
               </div>
               <div class="inner-login-form notoppadineg2">
                  <label>
                    Zip Code <span>*</span>
                  </label>
                  <input id="postal_code" type="text" placeholder="Zip Code" name="postal_code" value="{{ old('postal_code') }}">

                    @if ($errors->has('postal_code'))
                        <span class="help-block">
                            <strong>{{ $errors->first('postal_code') }}</strong>
                        </span>
                    @endif
               </div>
                <div class="inner-login-form inner-inner notoppadineg2">
                  <label>
                   County <span>*</span>
                  </label>
                  {!! Form::hidden('county_name', null, $attributes = ['id' => 'county_name']) !!}
                  <div class="dropdown">
                     @if($token_exist == '1')   
                        {!! Form::select('administrative_area_level_2',array_replace(['' => 'Select County '],$counties), $token_details->search_county ,['id' => 'administrative_area_level_2','class' => 'form-control', 'autocomplete' => 'off', 'disabled'=>'disabled']) !!} 
                    @else
                        {!! Form::select('administrative_area_level_2',array_replace(['' => 'Select County '],$counties), null ,['id' => 'administrative_area_level_2','class' => 'form-control', 'autocomplete' => 'off']) !!} 
                    @endif
                    @if ($errors->has('administrative_area_level_2'))
                        <span class="help-block">
                            <strong>{{ $errors->first('administrative_area_level_2') }}</strong>
                        </span>
                    @endif
               </div>
               </div>
               
               <!-- In Out Fields 9 Feb 2018 -->
               <div class="inner-login-form notoppadineg2 assesment_year_div" style="display: none;">
                  <label>Latest Assesment Year<span>*</span></label>
                  {!! Form::text('assessment_year', $value = null, $attributes = array('class'=>"form-control",'placeholder'=>'Latest Assesment Year', 'id'=>'assessment_year', 'maxlength' => '4', 'autocomplete' => 'off')); !!}
                  <span class="icon error error_icon"><span class="tooltiptext"></span></span>
                             
                </div>
                <div class="inner-login-form inner-inner notoppadineg2   conf_assesment_year_div" style="display: none;">
                    <label>Confirm Latest Assesment Year<span>*</span></label>
                    {!! Form::text('confirm_assessment_year', $value = null, $attributes = array('class'=>"form-control",'placeholder'=>'Confirm Latest Assesment Year', 'id'=>'confirm_assessment_year', 'maxlength' => '4', 'autocomplete' => 'off')); !!}
                    <span class="icon error error_icon"><span class="tooltiptext"></span></span>
                </div>
                  <div class="inner-login-form inner-inner special">
                  <label></label>
                  {!! Form::checkbox('condominium', $value = 1, true, $attributes = array('class'=>"", 'id' => 'condominium', 'autocomplete' => 'off')); !!}
                    <span>Is your property "Single Family Residential, Town House, or Row House" ? </span>
               </div>
               <p class="con_msg" style="display: none; color: red;">HomeTaxSavings.com does not cover property types that are not Single Family Residential, Town House, or Row House. Please  <a target="_blank" href="https://www.hometaxsavings.com/enquiry">Contact us</a> to know when other property types will be available.</p>
                <!-- In Out Fields 9 Feb 2018 -->
               <div class="inner-login-form">                 
                <!-- @if($token_exist == '1')  
                  <?php //$token_link = url('/address'); ?>
                    <a href="{{ $token_link }}" class="btn btn-ctrl">Register</a>
                @else
                  {!! Form::submit('Register',array('class'=>"btn btn-ctrl", 'id'=> 'search_address_btn')); !!} 
                @endif -->
                  <!-- <input type="submit" name="submit" value="SIGN UP" id="search_address_btn"> -->
                  <input type="submit" name="submit" value="SIGN UP">
                  <div class="msg-data">
                    <a href="#" target="_blank" class="text-bold" id="county_link"></a>
                  </div>
               </div>
               <div class="col-xs-12 col-md-5 col-sm-5 col-lg-5 map-section search-map-section">
                  <div class="row">
                      <div id="map_canvas" class="no-top-margin" style="width: 100%; height: 420px; display: none;"></div>
                  </div>
              </div> 
            </div>
               </form>
               
            </div>
            
       </div>
           

         </div><!--container-->
@endsection
@section('js')

<script src="{{ asset('/project/resources/assets/customer/js/search_address_js.js') }}"></script> 
<script src="{{ asset('/project/resources/assets/customer/js/autocomplete_maps.js') }}"></script> 

<script src="{{ asset('/project/resources/assets/customer/js/jquery.validate.js') }}"></script> 

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBlQK-PlX2L5HD_bahlqPjkixMd2PkSxdU&libraries=places&callback=initialize" async defer"></script>
<script src="{{ asset('/project/resources/assets/customer/js/jquery.validate.new.js') }}"></script>

<script>

$("#registration_form").validate({
      rules: {
            email: {
                required: true,
                email: true
            },
            first_name:{
              required: true,
            },
            last_name:{
              required: true,
            },
            password: {
              required: true,
              minlength: 6              
            },
            password_confirmation: {
                required: true,
                equalTo: "#password"
            },
            mobile_number: {
              required: true,
              number:true,
            },
            autocomplete_search:{
              required: true,
            },
            street_number:{
              required: true,
            },
            route:{
              required: true
            }, 
            locality:{
              required: true
            },
            postal_code:{
              required: true,
              number:true
            },
            administrative_area_level_1:{
              required: true
            },
            administrative_area_level_2:{
              required: true
            },

        } ,
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

</script>

@endsection