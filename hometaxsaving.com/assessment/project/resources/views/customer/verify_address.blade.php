@extends('layouts.app')
@section('title')
   @section('pageTitle', 'Property Information Verification')
@endsection
@section('css')
<link href="{{ asset('/project/resources/assets/customer/css/verify.css') }}" rel="stylesheet">
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
 {!! Form::open(['url' => 'verify-address', 'id' => 'verify_address_form']) !!}
              <div class="search-list">
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
                 <div class="search-heading">
                    <h4>VERIFICATION AND PROPERTY DATA</h4>
                    <p>Please review the search fields carefully before proceeding</p>
                 </div>
                 <div class="outer-form">
                  @include('errors.user_error')

                   <div class="alert alert-danger errorAlertMsgMain text-left error_div_description" style="display: none">
                       <strong></strong> There were some problems with your input.
                   </div>

                  

                    <!--form class="main-verify-form"-->

                    <div class="verify-form ">
                       <div class="verify-form inner-inner">
                        <div class="inner-verify-form">
                       <label>
                          Street
                       </label>
                      {!! Form::text('home_street', $value = $address_street, $attributes = array('class'=>"form-control",'placeholder'=>'Street', 'maxlength' => '100', 'autocomplete' => 'off', 'disabled'=>'disabled')); !!}
                    </div>
                    <div class="inner-verify-form">
                        <label>
                          City
                       </label>
                      {!! Form::text('home_city', $value = $address_city, $attributes = array('class'=>"form-control",'placeholder'=>'City', 'maxlength' => '100', 'autocomplete' => 'off', 'disabled'=>'disabled')); !!}
                    </div>
                     <div class="inner-verify-form">
                       <label>
                          State
                       </label>
                       <div class="dropdown"> 
                      {!! Form::select('home_state',array_replace(['' => 'Select State '],$states), $address_state ,['id' => 'state','class' => 'form-control', 'autocomplete' => 'off', 'disabled'=>'disabled']) !!}
                    </div>
                  </div>

                    <div class="inner-verify-form">
                        <label>
                          Zip
                       </label>
           {!! Form::text('home_zipcode', $value = $address_zipcode, $attributes = array('class'=>"form-control",'placeholder'=>'Zip code', 'maxlength' => '100', 'autocomplete' => 'off', 'disabled'=>'disabled')); !!}
                    </div>

                    <div class="full-verify-form">
                       <label>
                          County
                       </label>
                       <div class="dropdown">
                        {!! Form::select('home_county',array_replace(['' => 'Select County '],$county), $address_county, ['id' => 'county','class' => 'form-control', 'autocomplete' => 'off', 'disabled'=>'disabled']) !!}
                    </div>
                    </div>
                    <div class="full-verify-form">
                       <label>
                         Land Assessment Value
                          <span>*</span>
                       </label>
                         <div class="full-verify-form-input">
                          {!! Form::text('land_assessment_value', $value = $land_assessment_value, $attributes = array('id' => 'land_assessment_value', 'class'=>"form-control",'placeholder'=>'Land Assessment Value', 'maxlength' => '100', 'autocomplete' => 'off')); !!}
                    </div>
                  </div>
                    <div class="full-verify-form">
                       <label>
                         Improvement Assessment Value <span>*</span>

                       </label>
                         <div class="full-verify-form-input">
                       {!! Form::text('improvement_assessment_value', $value = $improvement_assessment_value, $attributes = array('id' => 'improvement_assessment_value', 'class'=>"form-control",'placeholder'=>'Improvement Assessment Value', 'maxlength' => '100', 'autocomplete' => 'off')); !!}
                    </div>
                  </div>
                     <div class="full-verify-form">
                       <label>
                         Total Assessment Value <span>*</span>

                       </label>
                         <div class="full-verify-form-input">

 {!! Form::text('total_assessment_value', $value = $total_assessment_value, $attributes = array('id' => 'total_assessment_value', 'class'=>"form-control",'placeholder'=>'Total Assessment Value', 'maxlength' => '100', 'autocomplete' => 'off')); !!}
                    </div>
                  </div>
                 </div>
                 </div>
                  {!! Form::hidden('token_status', $token_status, $attributes = ['id' => 'token_status']); !!}
           {!! Form::hidden('token', $customer_token, $attributes = ['id' => 'token']); !!}
           {!! Form::hidden('user_search_id', encrypt($user_search_id), $attributes = ['id' => 'user_search_id']); !!}
           {!! Form::hidden('user_search_address_id', encrypt($user_search_address_id), $attributes = ['id' => 'user_search_address_id']); !!}

              <div style="display: none;" class="col-md-6 col-sm-6 col-lg-6 map-section verify-address-right-col">

                   <div id="map_canvas" style="width: 100%; height: 355px;"></div>

           </div>

                 <div class="heading-major">
                   <!--  <h4>Major Items of Maintenance and Repair (M&R)</h4> -->
                       <div class="verify-form inner-inner">
                       <div class="major-items-form">
                          <label>
                             Type of House
                          </label>
                            <?php $type_of_house = ( count($house_details) != 0)?($house_details->type_of_house):'';  ?>
                             {!! Form::text('type_of_house', $value = $type_of_house, $attributes = array('class'=>"form-control", 'id'=>'type_of_house', 'placeholder'=>'Type of house', 'maxlength' => '100', 'autocomplete' => 'off', 'disabled'=>'disabled')); !!}
                       </div>
                       <div class="major-items-form">
                          <label>
                             Above Grade Square footage <span>*</span>
                          </label>
                          <?php $square_footage = (count($house_details) != 0)?($house_details->square_footage):'';  ?>
                           {!! Form::text('square_footage', $value = $square_footage, $attributes = array('class'=>"form-control", 'id'=>'square_footage', 'placeholder'=>'Square Footage', 'maxlength' => '100', 'autocomplete' => 'off')); !!}
                       </div>
                       <div class="major-items-form">
                          <label>
                             Bedrooms <span>*</span>
                          </label>
                          <div class="dropdown">
                          <?php 
                            $bedrooms = [];
                            for($i=0; $i<=20; $i++) {
                              $bedrooms[] = $i;
                              //$i++;
                            }
                            $bedroom = (count($house_details) != 0)?($house_details->bedrooms):'';  
                          ?>
                          {!! Form::select('bedrooms',array_replace(['' => 'Select Bedrooms '],$bedrooms), $bedroom ,['id' => 'bedrooms','class' => 'form-control', 'autocomplete' => 'off']) !!}
                          </div>

                       </div>
                       <div class="major-items-form">
                          <label>
                             Bathrooms <span>*</span>
                          </label>
                         <div class="dropdown">
                             <?php $bathrooms = [];
                           for($j=0; $j<=20.5; $j += 0.5) {
                               $bathrooms[''.$j.''] = $j;
                           }
                           $bathroom = (count($house_details) != 0)?($house_details->bathrooms):'';  
                       ?>
                           {!! Form::select('bathrooms',array_replace(['' => 'Select Bathrooms '],$bathrooms), $bathroom ,['id' => 'bathrooms','class' => 'form-control', 'autocomplete' => 'off']) !!}
                          </div>
                       </div>
                       <div class="major-items-form">
                          <label>
                           Basement Unfinished Space/sq.ft <span>*</span>
                          </label>
                          <?php $unfinished_space = (count($house_details) != 0)?($house_details->unfinished_space):'';   ?>
                           {!! Form::text('unfinished_space', $value = $unfinished_space, $attributes = array('class'=>"form-control", 'id'=>'unfinished_space', 'placeholder'=>'Unfinished Space/sq. ft.', 'maxlength' => '100', 'autocomplete' => 'off')); !!}

                       </div>
                       <div class="major-items-form">
                          <label>
                             Basement finished Space/sq.ft <span>*</span>
                          </label>
                          <?php $finished_space = (count($house_details) != 0)?($house_details->finished_space):'';   ?>
                        
                           {!! Form::text('finished_space', $value = $finished_space, $attributes = array('class'=>"form-control", 'id'=>'finished_space', 'placeholder'=>'Finished Space/sq. ft.', 'maxlength' => '100', 'autocomplete' => 'off')); !!}

                       </div>
                       </div>
                    <div class="major-table">
                      <h4 style="font-size: 16px;">Please respond to the following questions about your home with either a “number” or “check mark” as requested.</h4>
                       <ul class="garbage">
                          <li>
                             <label class=""> Number of Garage Spaces
                              <?php 
                                $garage_exist = false;
                                $exist_garage_count = 0;
                                if(count($house_details) != 0){
                                  $garage_exist = ($house_details->garage >= '1') ? true : false; 
                                  $exist_garage_count = ($house_details->garage >= '1') ? $house_details->garage : 0; 
                                }
                              ?>
                               <!-- {!! Form::checkbox('garage_exist', $value = 1, $garage_exist, $attributes = array('class'=>"", 'id' => 'garage_exist', 'autocomplete' => 'off')); !!} -->
                               <!-- <span class="checkmark"></span> -->
                               
                            {!! Form::text('garage_count', $value = $garage_exist, $attributes = array('class'=>"form-control", 'id'=>'garage_count', 'maxlength' => '100', 'autocomplete' => 'off')); !!}
                             </label>
                          </li>
                          {!! Form::hidden('exist_garage_count', $value = $exist_garage_count, $attributes = array('id'=>'exist_garage_count', 'autocomplete' => 'off')); !!}
                          </ul>
                           <!-- {!! Form::hidden('exist_garage_count', $value = $exist_garage_count, $attributes = array('id'=>'exist_garage_count', 'autocomplete' => 'off')); !!}

                           {!! Form::text('garage_count', $value = $garage_exist, $attributes = array('class'=>"form-control", 'id'=>'garage_count', 'maxlength' => '100', 'autocomplete' => 'off')); !!} -->
                          <ul class="carport">
                          <li>
                            <label class="checkbox-div">Carport
                             <?php 
                              $carport_exist = false;
                                if(count($house_details) != 0){
                                  $carport_exist = ($house_details->carport >= '1') ? true : false; 
                                }
                              ?>
                               {!! Form::checkbox('carport_exist', $value = 1, $carport_exist, $attributes = array('class'=>"", 'id' => 'carport_exist', 'autocomplete' => 'off')); !!}
                            <span class="checkmark"></span>
                            </label>
                          </li>
                          <li>
                             <label class="checkbox-div">Porch/Deck
                              <?php 
                                $porch_deck_exist = false;
                                if(count($house_details) != 0){
                                  $porch_deck_exist = ($house_details->porch_deck >= '1') ? true : false; 
                                }
                              ?>
                              {!! Form::checkbox('porch_deck_exist', $value = 1, $porch_deck_exist, $attributes = array('class'=>"", 'id' => 'porch_deck_exist', 'autocomplete' => 'off')); !!}
                              <span class="checkmark"></span>
                             </label>
                          </li>
                        </ul>
                        <ul class="patio">
                          <li>
                           <label class="checkbox-div">Patio
                            <?php 
                              $patio_exist = false;
                              if(count($house_details) != 0){
                                $patio_exist = ($house_details->patio >= '1') ? true : false; 
                              }
                            ?>
                             {!! Form::checkbox('patio_exist', $value = 1, $patio_exist, $attributes = array('class'=>"", 'id' => 'patio_exist', 'autocomplete' => 'off')); !!}
                             <!-- Patio -->
                              <span class="checkmark"></span>
                            </label>
                          </li>
                          <li>
                            <label class="checkbox-div">Swimming pool
                              <?php $pool_exist = false;
                                if(count($house_details) != 0){
                                  $pool_exist = ($house_details->pool >= '1') ? true : false; 
                                }
                              ?>
                              {!! Form::checkbox('pool_exist', $value = 1, $pool_exist, $attributes = array('class'=>"", 'id' => 'pool_exist', 'autocomplete' => 'off')); !!}
                               
                              <span class="checkmark"></span>
                            </label>
                          </li>
                        </ul>

                          <ul class="fireplace">
                          <li>
                          <label class="">Number of Fireplaces
                            <?php 
                                $fireplace_exist = false;
                                $fireplace_count = 0;
                                $exist_fireplace_count = 0;
                                if(count($house_details) != 0){
                                  $fireplace_exist = ($house_details->fireplace >= '1') ? true : false;
                                   $exist_fireplace_count = $fireplace_count = ($house_details->fireplace >= '0') ? $house_details->fireplace : 0 ;
                                }
                            ?>
                               <!-- {!! Form::checkbox('fireplace_exist', $value = 1, $fireplace_exist, $attributes = array('class'=>"", 'id' => 'fireplace_exist', 'autocomplete' => 'off')); !!} -->
                               <!-- <span class="checkmark"></span> -->
                               {!! Form::text('fireplace_count', $value = $fireplace_count, $attributes = array('class'=>"form-control inline-input", 'id'=>'fireplace_count', 'maxlength' => '100', 'autocomplete' => 'off')); !!}
                            </label>
                          </li>
                           {!! Form::hidden('exist_fireplace_count', $value = $exist_fireplace_count, $attributes = array('id'=>'exist_fireplace_count', 'autocomplete' => 'off')); !!}
                       
                       
                        </ul>

                      
                      <!--  {!! Form::hidden('exist_fireplace_count', $value = $exist_fireplace_count, $attributes = array('id'=>'exist_fireplace_count', 'autocomplete' => 'off')); !!}
                       
                       {!! Form::text('fireplace_count', $value = $fireplace_count, $attributes = array('class'=>"form-control inline-input", 'id'=>'fireplace_count', 'maxlength' => '100', 'autocomplete' => 'off')); !!} -->

                    <h4>Tell us More About Your House and its condition</h4>
                     <p>Here is where you have the option to highlight the cost to remedy the conditions listed below </p>
                    </div>

                 </div>
                    </div>
              <div class="verify-image">
                   <div class="inner-need-help">
                           <h4>
                              NEED HELP?
                           </h4>
                           <p>
                              If you are having trouble finding your new assessment values, please review the following information.  You can also contact a representative at <a href="mailto:info@hometaxsavings.com">EMAIL</a> for more help.
                              
                           </p>
                           <ul>
                              <li>
                              <?php 
                              
                                $countyPDFLink = 'javascript:;' ;
                                if(isset($address_state) && $address_state == '10' ){
                                  $countyPDFLink = asset('/documents/TY 2018 DC Assessment Notice.docx') ;
                                } elseif (isset($address_state) && $address_state == '24') {
                                  $countyPDFLink = asset('/documents/2018 Fairfax Assessment Notice.docx') ;
                                }
                                
                              ?>
                                 Your new assessment values can be found on the notice mailed to you from your local jurisdiction. <a target="_blank" href="{{ $countyPDFLink }}" target="_blank" class="text-bold" id="county_link">Please click here for a sample notice.</a>
                              </li>
                              <li>
                              <?php $countyLink = (!empty($county_link))?($county_link[0]):''; ?>
                                 If you don’t have your notice, you can find your new assessment values online. Please <a target="_blank" href="{{ $countyLink }}">click here to go to your local jurisdiction.</a>
                              </li>
                              <li>
                                 If you still can’t find your new assessment values please <a target="_blank" href="https://www.hometaxsavings.com/enquiry">click here</a> and we will send you your values within 48 hours
                              </li>
                           </ul>
                        </div>
                        
                        
                        </div>
             
                 <div class="item-form">
                       <div class="inner-item-form special">
                        <table class="item" cellpadding="0" cellspacing="0" style="width: 100%;">
                          <tr>
                            <td>
                              <label>
                             <p>Item</p>
                             </label>
                            </td>
                            <td>
                              <span><p>Estimated cost to Fix Guidelines/Typical Range</p></span>
                            </td>
                          </tr>
                          
                       

                       <?php
                      // echo "<pre>";print_r($additional_homeowner_questions);exit;
                        $i = 0; ?>
                           @foreach($additional_homeowner_questions as $additional_homeowner_question)
                          <!--  <div class="inner-item-form"> -->
                               <?php $child_details = Helper::getChildLookupDetail($additional_homeowner_question->lookup_id);
                                //echo "<pre>"; print_r($child_details); exit;
                                $aditionalClass = '';
                                if(count($child_details)) {
                                  $question[$i]['child_details'] = $child_details;
                                  $aditionalClass = 'aditionalClass';
                                }
                                   ?>
                                   <tr>
                                   <td>
                               <label>{{ $additional_homeowner_question->name }}
                                   @if(count($child_details))

                                     {!! Form::text('lookup_count['.$child_details->lookup_id.']', $value = '0', $attributes = array('class'=>"form-control inline-input child_owner_input $aditionalClass",'placeholder'=>'Count', 'maxlength' => '99', 'autocomplete' => 'off', 'onkeyup' => 'totalOwnerInputCal()')); !!}
                                     
                                     {{ $child_details->name }}
                                   @endif
                               </label>
                             </td>
                             <td>
                              <span>

                                   <?php $question_selected = (in_array($additional_homeowner_question->lookup_id, $lookup_selected)) ? true : false; ?>
                                   
                                  <?php 
                                    $additional_homeowner_question_lookup_id = (count($child_details)) ? $child_details->lookup_id : $additional_homeowner_question->lookup_id; 
                                    
                                    $maxRange = $additional_homeowner_question->value;
                                    $maxLimitValue = "$".number_format($maxRange);
                                    
                                    $minRange = $additional_homeowner_question->value1;
                                    $minimitValue = "$".number_format($minRange);

                                    $percentageValue = $additional_homeowner_question->value2;

                                    $concateVal = $maxRange."-".$minRange."-".$percentageValue;
                                  ?>

                                  {!! Form::hidden('homeowner_questions['.$additional_homeowner_question_lookup_id.']', null, $attributes = ['id' => "$additional_homeowner_question_lookup_id"]); !!}

                                   <label> $ &nbsp; {!! Form::text('homeowner_questions_input['.$additional_homeowner_question_lookup_id.']', null, $attributes = array('alt'=>"$concateVal", 'class'=>"additional_homeowner_question form-control inline-input owner_input_value $aditionalClass", 'id'=>"in_$additional_homeowner_question_lookup_id", 'autocomplete' => 'off', 'onkeyup' => 'totalOwnerInputCal()', 'onblur' => 'confirmOwnerInput(this)')); !!} </label>
                                   
                               <b>
                                  <?php 
                                    $str = str_replace('$maxLimitValue', $maxLimitValue, $additional_homeowner_question->description);

                                    echo str_replace('$minimitValue', $minimitValue, $str);
                                  ?>
                               </b></span>
                             </td>
                             </td>
                           </tr>
                           </div>
                           <?php $i++; ?>
                           @endforeach
                           
                           {!! Form::hidden('total_homeowner_inputs', null, $attributes = array('class'=>"additional_homeowner_question form-control inline-input total_owner_input_value", 'id' => '', 'autocomplete' => 'off')); !!}

                           <!--  <div class="inner-item-form">
                              <label> <b>Total Cost for Repairs/Upgrades</b></label>
                              <span>
                                <label>
                                  $ &nbsp; {!! Form::text('total_homeowner_inputs', null, $attributes = array('class'=>"additional_homeowner_question form-control inline-input total_owner_input_value", 'id' => '', 'autocomplete' => 'off')); !!} 
                                </label>

                                <b>Downward Adjustment to Assessed Value</b> 
                              </span>
                           </div> -->
                            </table>
                          
                          
                       </div>
                 </div>

              <div class="confirm-button">
                 {!! Form::submit('Confirm and Proceed',$attributes = array('class'=>"btn btn-ctrl", 'id'=>'verify_address_btn')); !!}
              </div>


{!! Form::close() !!}


              </div>
           </div>
        </div>
     </div>

@endsection

@section('js')

<script src="{{ asset('/project/resources/assets/customer/js/verify_address_js.js') }}"></script>
<script src="{{ asset('/project/resources/assets/customer/js/jquery.validate.new.js') }}"></script>
<script src="{{ asset('/project/resources/assets/customer/js/sweetalert.min.js') }}"></script>
<script>
  $( document ).ready(function() {
      $("#land_assessment_value").keyup(function(){
        updateTotalAssessment();
      });

      $("#improvement_assessment_value").keyup(function(){
        updateTotalAssessment();
      });

  });

  function updateTotalAssessment(){
    var improvement_assessment_value = ($("#improvement_assessment_value").val() != ''?$("#improvement_assessment_value").val():0);
    var land_assessment_value = ($("#land_assessment_value").val() != ''?$("#land_assessment_value").val():0);
    var totalAssessVal = (parseFloat(improvement_assessment_value) + parseFloat(land_assessment_value));
    $("#total_assessment_value").val(totalAssessVal);
  }

  function totalOwnerInputCal(){
    var totalOwnerInput = 0;
    $(".owner_input_value").each(function(){
      //var inputVal = $(this).val();
      if ( $(this).hasClass("child_owner_input") ){
        return false;
      } else {        
        var inputId = this.id;
        var finalId = inputId.split('_');
        var assignId = finalId[1];
        
        var inputVal = $("input#"+assignId).val();


        var intRegex = /^\d+$/;
        var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;

        if(intRegex.test(inputVal) || floatRegex.test(inputVal)) {
          inputVal = inputVal;
        } else {
          inputVal = 0;
        }

        if ( $(this).hasClass("aditionalClass") && inputVal != "" && inputVal > 0 ) {
          var childValue = $(".child_owner_input").val();
          if(childValue <= 0){
            childValue = 1;
            $(".child_owner_input").val(childValue);
          }
          if(intRegex.test(childValue) || floatRegex.test(childValue)) {
            childValue = childValue;
          } else {
            childValue = 0;
          }

          var multiValue = (parseFloat(inputVal)*parseFloat(childValue))
          totalOwnerInput = (parseFloat(totalOwnerInput)+parseFloat(multiValue) );                      
        } else {
          totalOwnerInput = (parseFloat(totalOwnerInput)+parseFloat(inputVal) );                      
        }
      }

    });

    $(".total_owner_input_value").val(totalOwnerInput);
  }

  function confirmOwnerInput(currentInput){
    var inputVal = currentInput.value;
    var concateValue = currentInput.alt;
    
    var arr = concateValue.split('-');
    var maxLimitValue = arr[0];
    var minLimitValue = arr[1];
    var percentageValue = arr[2];
    
    if((parseFloat(inputVal) >= parseFloat(minLimitValue)) && (parseFloat(inputVal) <= parseFloat(maxLimitValue)) ) {
      var inputId = currentInput.id;
      var finalId = inputId.split('_');
      var assignId = finalId[1];
      var afterCal = (inputVal*percentageValue/100);
      
      $("input#"+assignId).val(afterCal);
      totalOwnerInputCal();
      return true;
    } else if (inputVal == "" || inputVal == null) {
      return true;
    } else {
      
      swal({        
        text: "The entered amount $"+inputVal+" is outside the suggested range for this type of adjustment. Please select 'Yes' to continue with this amount and 'No' to reenter your estimate amount to confirm your adjustment.",
        icon: "warning",
        buttons: {
          confirm: {
              text: "Yes",
              value: true,
              visible: true,
              className: "",
              closeModal: true
            },
          cancel: {
            text: "No",
            value: null,
            visible: true,
            className: "",
            closeModal: true,
          },
        },
      })
      .then((willDelete) => {
        if (willDelete) {
          totalOwnerInputCal();
        } else {
          totalOwnerInputCal();
          currentInput.focus();
        }
      });
    }
    
  }

  jQuery.validator.addMethod("total_assess_val", function(value, element) {
    var improvement_assessment_value = $("#improvement_assessment_value").val();
    var land_assessment_value = $("#land_assessment_value").val();
    var totalAssessVal = parseFloat(parseFloat(improvement_assessment_value) + parseFloat(land_assessment_value));
    return (value != 0) && (value == totalAssessVal);    
  }, "Total Assessment value should be equal to 'Land assessment value' and 'Improvement assessment value'");


  $("#verify_address_form").validate({
      rules: {
        land_assessment_value: {
            required: true,
            number: true,
            min: 1,
        },
        improvement_assessment_value: {
            required: true,
            number: true,
            min: 0,
        },
        total_assessment_value: {
            required: true,
            number: true,
            min: 1,
            total_assess_val: true,
        }
      },

      messages: {
        land_assessment_value: {
            min: "Number cannot be negative - no special characters ( $ or , )"
        },
        improvement_assessment_value: {
            min: "Number cannot be negative - no special characters ( $ or , )"
        },
        total_assessment_value: {
            min: "Number cannot be negative - no special characters ( $ or , )"
        }
      },
      submitHandler: function (form) {
        
          $(".loader-overlay").show();
          $("#loaderText").text(VERYFYING_ADD);
          setTimeout(function(){
            $("#loaderText").text(FETCHING_LOADING_MSG);
          }, 1500);  
          setTimeout(function(){
            $("#loaderText").text(PREPARING_MSG);
          }, 3000);
          form.submit();
        
      }
    });
  </script>
@endsection