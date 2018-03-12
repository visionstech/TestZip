@extends('layouts.app')
@section('pageTitle', 'Edit Profile') 
@section('css')
<link href="{{ asset('/project/resources/assets/customer/css/My-profile.css') }}" rel="stylesheet">
<link href="{{ asset('/project/resources/assets/customer/css/custom_common.css') }}" rel="stylesheet">

@endsection
@section('content')
        <div class="outer-search-list">
          <div class="top-search-list">
            <ul>
              <li>
                <a href="#"><span>My Profile</span></a>
              </li>
              <li>
                <a href="{{ url('/search-address') }}">START NEW SEARCH</a>
              </li>
            </ul>
          </div>

          <div class="search-list">
           

              <!-- <div class="outer-wrapper-profile-regs"> -->
                <!-- <div class="inner-profile-regs"> -->
               
               
      {!! Form::open(['id' => 'profile_form','url' => '/updateProfile', 'method' => 'post','class'=> 'form-horizontal']) !!}
                  {{ csrf_field() }}
                 
                  @if ($errors->has('server_error_msg'))
                     <span class="help-block" style="color: red;">
                        <strong>{{ $errors->first('server_error_msg') }}</strong>
                     </span>
                  @endif

                  @if (Session::has('flash_message'))
                     <span class="help-block update" style="color: green;">
                        <strong>{{ Session::get('flash_message') }}</strong>
                     </span>
                  @endif 
                <!--   <div class="profile-form"> -->
          <input type="hidden" name="user_id" value="{{ $addressDetails->user_id }}" />
          <input type="hidden" name="user_search_id" value="{{ $addressDetails->user_search_id }}" />
           <input type="hidden" name="address_type" value="{{ $addressDetails->address_type}}" />

           <?php 
                //echo "<pre>";print_r($countyName);exit;
                $country = (!empty($addressDetails->county_name))?($addressDetails->county_name):''; 
                      
            ?>
            {!! Form::hidden('county_name', $country, $attributes = ['id' => 'county_name']) !!}
            <div class="my-profile-form">
                <div class="name-form">
                     <div class="first-name-form non-edit">
                     <label>
                        First Name <br>
                     </label>
                     <input id="first_name" type="text" name="first_name" value="{{ $addressDetails->first_name }}" placeholder="Enter First Name" disabled>
                     @if ($errors->has('first_name'))
                        <span class="help-block">
                           <strong>{{ $errors->first('first_name') }}</strong>
                        </span>
                     @endif
                    </div>
                
               <!--  </div> -->
                   <!-- <div class="profile-form"> -->
                    <div class="last-name-form non-edit">
                     <label>
                       Last Name <br>
                     </label>
                     <input id="last_name" type="text" name="last_name" value="{{ $addressDetails->last_name }}" placeholder="Enter Last Name" disabled>

                     @if ($errors->has('last_name'))
                        <span class="help-block">
                           <strong>{{ $errors->first('last_name') }}</strong>
                        </span>
                     @endif
                  </div>
                </div>

                  <!-- <div class="profile-form"> -->
                  <div class="name-form">
                    <div class="email-form">
                     <label>
                        Email <br>
                     </label>
                     <input id="email" type="email" placeholder="Enter Email" name="email" value="{{ $addressDetails->email }}" disabled>

                     </div>
                 
                 <!--  <div class="profile-form"> -->
                     <div class="Phone-form non-edit">
                     <label>
                        Cell Phone<br>
                     </label>
                     <input id="mobile_number" type="text" placeholder="Enter Cell Phone" name="mobile_number" value="{{ $addressDetails->mobile_number }}" disabled>

                    @if ($errors->has('mobile_number'))
                        <span class="help-block">
                            <strong>{{ $errors->first('mobile_number') }}</strong>
                        </span>
                    @endif
                  </div>
                </div>
            <!-- </div>  --> 
        <?php if ($addressDetails->receive_notification == 1)
          $checkFlag = true;
        else
          $checkFlag = false;
        
        ?>
                <div class="inner-profile-form inner-inner">
                  {!! Form::checkbox('receive_notification', $value = null, $checkFlag, $attributes = array('class'=>"", 'id' => 'receive_notification')); !!}
                    <span>Subscribe for text messages</span>
               </div> 
              <!--  <div class="inner-profile-form inner-inner-inner"> -->
                <div class="search-address-form">
                  <label>
                    Default (Billing) Address <span>*</span>
                  </label>
                 
                     {!! Form::text('autocomplete_search', $value = $addressDetails->prefillAddress, $attributes = array('class'=>"form-control",'placeholder'=>'Billing Address', 'maxlength' => '100', 'id'=>'autocomplete', 'autocomplete' => 'off')); !!}
                 
                  @if ($errors->has('autocomplete_search'))
                     <span class="help-block">
                         <strong>{{ $errors->first('autocomplete_search') }}</strong>
                     </span>
                  @endif
               </div>
             <!--   <div class="inner-profile-form"> -->
               <div class="name-form">
                 <div class="street-form">
                  <label>
                     Street <span>*</span>
                  </label>
                  <input id="street_number" type="text" placeholder="Street Number" name="street_number" value="{{ $addressDetails->address_line_1 }}">

                    @if ($errors->has('street_number'))
                        <span class="help-block">
                            <strong>{{ $errors->first('street_number') }}</strong>
                        </span>
                    @endif
               </div>
                <!-- <div class="inner-profile-form inner-inner"> -->
                  <div class="street-empty-form">
                  <label class="nbsp">
                  
                  </label>
                  <input id="route" type="text" placeholder="Street" name="route" value="{{ $addressDetails->address_line_2 }}">

                    @if ($errors->has('route'))
                        <span class="help-block">
                            <strong>{{ $errors->first('route') }}</strong>
                        </span>
                    @endif
               </div>
             </div>
               <div class="form-group element autocompletesearch autocomplete_search_section" style="display: none">
                  <label></label>
                  <div class="form-section">
                     <div class="" id="autocomplete_search_result_div">
                           <ul id="autocomplete_search_result"></ul>
                     </div>
                  </div>
               </div>
               <!-- <div class="inner-profile-form"> -->
                <div class="name-form">
                  <div class="city-form">
                  <label>
                    City <span>*</span>
                  </label>
                  <input id="locality" type="text" placeholder="City" name="locality" value="{{ $addressDetails->city }}">

                    @if ($errors->has('locality'))
                        <span class="help-block">
                            <strong>{{ $errors->first('locality') }}</strong>
                        </span>
                    @endif
               </div>
         
                <!-- <div class="inner-profile-form inner-inner"> -->
                  <div class="state-form">
                  <label>State <span>*</span>
                  </label>
                  <div class="dropdown">
                 
                        {!! Form::select('administrative_area_level_1',array_replace(['' => 'Select State '],$states), $addressDetails->state_abbr ,['id' => 'administrative_area_level_1','class' => 'form-control']) !!} 
                     @if ($errors->has('administrative_area_level_1'))
                        <span class="help-block">
                            <strong>{{ $errors->first('administrative_area_level_1') }}</strong>
                        </span>
                    @endif
                  </div>
               </div>
             </div>
              <!--  <div class="inner-profile-form"> -->
              <div class="name-form clear">
                <div class="zip-form">
                  <label>
                    Zip Code <span>*</span>
                  </label>
                  <input id="postal_code" type="text" placeholder="Zip Code" name="postal_code" value="{{ $addressDetails->postal_code }}">

                    @if ($errors->has('postal_code'))
                        <span class="help-block">
                            <strong>{{ $errors->first('postal_code') }}</strong>
                        </span>
                    @endif
               </div>
                <!-- <div class="inner-profile-form inner-inner"> -->
                  <div class="county-form">
                  <label>
                   County <span>*</span>
                  </label>
                  <div class="dropdown">
                
                        {!! Form::select('administrative_area_level_2',array_replace(['' => 'Select County '],$counties), $addressDetails->county_name ,['id' => 'administrative_area_level_2','class' => 'form-control', 'autocomplete' => 'off']) !!} 
                    @if ($errors->has('administrative_area_level_2'))
                        <span class="help-block">
                            <strong>{{ $errors->first('administrative_area_level_2') }}</strong>
                        </span>
                    @endif
               </div>
             </div>
            </div>
          </div>
               <!-- <div class="inner-profile-form">   -->               
                <div class="submit-button">  
                  <input type="submit" name="submit" value="Submit">
               </div>
              
      {!! Form::close() !!}
               
          
          </div>
        </div>
      </div>
           
      <div id="map_canvas" class="no-top-margin" style="display:none; width: 100%; height: 420px;"></div>
       <!--   </div>container -->
@endsection
@section('js')


<!--script src="{{ asset('project/resources/assets/customer/js/edit_profile_js.js') }}"></script-->
<script src="{{ asset('/project/resources/assets/customer/js/autocomplete_maps.js') }}"></script> 
<script src="{{ asset('/project/resources/assets/customer/js/search_address_js.js') }}"></script> 
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBlQK-PlX2L5HD_bahlqPjkixMd2PkSxdU&libraries=places&callback=initialize" async defer"></script>
<script src="{{ asset('/project/resources/assets/customer/js/jquery.validate.js') }}"></script> 
<script>
   $( document ).ready(function() {
      $("#autocomplete").change(function(){
        $("#search_id").val("");
      });
  });
  $(document).ready(function() {
   //$('#administrative_area_level_1').trigger('change');

  });
   $("#profile_form").validate({
      rules: {
            autocomplete_search: {
                required: true,
            },
            street_number: {
                required: true
            },
            route: {
                required: true,
            },
            locality: {
                required: true
            },
            administrative_area_level_1: {
                required: true
            },
            postal_code: {
                required: true
            },            
            administrative_area_level_2: {
                required: true
            }
        }
  });
</script>


@endsection