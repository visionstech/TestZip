@extends('layouts.app')
@section('title')
    @section('pageTitle', 'MAKE PAYMENT')
@endsection
@section('css')
<link href="{{ asset('/project/resources/assets/customer/css/make-payment-2.css') }}" rel="stylesheet">
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
             <li >
                <a href="javascript:;">02</a>
             </li>
            
          </ul>
          <div class="inner-wrapper-top-search-list">
             <ul>
                <li>
                   Make Payment
                </li>
                <li>
                   View And Download Reports
                </li>
               
             </ul>
          </div>
       </div>
       
       <div class="search-list">
          <div class="search-heading">
             <h4>
                Pay <?php echo config('constants.currencySymbol').config('constants.phase2Amt');?>
             </h4>
             <p>
                Congratulations! You have chosen to continue and download all the documents relevant to filing your appeal. However, you need to pay to continue.
             </p>
          </div>
            <?php   
              $card_types = ['MasterCard'=> 'MasterCard', 'Visa'=> 'Visa', 'American Express'=> 'American Express', 'Discover'=> 'Discover']; 
              $months = ['01'=> 'January', '02'=> 'February', '03'=> 'March', '04'=> 'April', '05'=> 'May', '06'=> 'June', '07'=> 'July', '08'=> 'August', '09'=> 'September', '10'=> 'October', '11'=> 'November', '12'=> 'December'];
              $date = date('Y');
              $max_date = $date+10;
              
              for ($i = $date; $i <= $max_date; $i++) {
                $years[$i] = $i;
              }
              
              if(isset($token_details)) {
                  $token_exist = '1';
              } else {
                  $token_exist = '0';
              }
      
              if(isset($phase2_token_details)) {
                  $token_phase2_exist = '1';
              } else {
                  $token_phase2_exist = '0';
              }
            ?>
          @include('errors.user_error')
        
          <div class="alert alert-danger errorAlertMsgMain text-left error_div_description" style="display: none">
            <strong></strong> There were some problems with your input.
            <span id="invalid_address_msg"></span>
          </div>
          <!-- form start here -->
          {!! Form::open(['url' => '/phase2-payment', 'method' => 'post', 'id' => 'phase2_make_payment_form']) !!}
          <div class="assessment-form">
             <div class="inner-wrapper-assessment-form">
                <div class="outer-wrapper-assessment-form">
                <div class="inner-assessment-form">
                   <label>
                      Credit Card Type
                   </label>
                  <div class="dropdown">
                    @if($token_phase2_exist == '1')
                        {!! Form::select('card_type',array_replace(['' => 'Select Card Type '],$card_types), null ,['id' => 'card_type','class' => 'form-control', 'autocomplete' => 'off', 'disabled'=>'disabled']) !!}
                    @else
                        {!! Form::select('card_type',array_replace(['' => 'Select Card Type '],$card_types), 'Discover' ,['id' => 'card_type','class' => 'form-control', 'autocomplete' => 'off']) !!}
                        
                    @endif 
                  </div>
                  
                </div>
                <div class="inner-assessment-form">
                   <label>
                      Credit Card Number <span>*</span>
                   </label>
                   @if($token_phase2_exist == '1')
                      {!! Form::text('card_number', $value = null, $attributes = array('class'=>"form-control",'placeholder'=>'Card Number', 'maxlength' => '100', 'autocomplete' => 'off', 'disabled'=>'disabled')); !!}
                  @else
                      {!! Form::text('card_number', $value = null, $attributes = array('class'=>"form-control",'placeholder'=>'Card Number', 'maxlength' => '100', 'id'=>'card_number', 'autocomplete' => 'off')); !!}
                      
                  @endif
                </div>
                
                <div class="inner-assessment-form inner-inner">
                  <label>Expiration Date <span>*</span></label>
                  <div class="inner-assessment-form-input inner-inner special">
                    <div class="dropdown">
                    
                        @if($token_phase2_exist == '1')
                            {!! Form::select('ex_month',array_replace(['' => 'Month '],$months), null ,['id' => 'ex_month','class' => 'form-control', 'autocomplete' => 'off', 'disabled'=>'disabled']) !!}
                        @else
                            {!! Form::select('ex_month',array_replace(['' => 'Month '],$months), '02' ,['id' => 'ex_month','class' => 'form-control', 'autocomplete' => 'off']) !!}
                            
                        @endif
      
                    </div>
                    <div class="dropdown">
                  
                        @if($token_phase2_exist == '1')
                            {!! Form::select('ex_year',array_replace(['' => 'Year '],$years), null ,['id' => 'ex_year','class' => 'form-control', 'autocomplete' => 'off', 'disabled'=>'disabled']) !!}
                        @else 
                            {!! Form::select('ex_year',array_replace(['' => 'Year '],$years), '2020' ,['id' => 'ex_year','class' => 'form-control', 'autocomplete' => 'off']) !!}
                            
                        @endif 
              
                    </div>
                  </div>
                </div>
                <div class="inner-assessment-form inner-inner inner-inner-inner">
                  <label>CVV Code <span>*</span></label>
                  @if($token_phase2_exist == '1')
                      {!! Form::password('cvv', null, $attributes = array('class'=>"form-control",'placeholder'=>'CVV', 'maxlength' => '4', 'autocomplete' => 'off', 'disabled'=>'disabled')); !!}
                  @else
                      {!! Form::password('cvv', null, $attributes = array('class'=>"form-control",'placeholder'=>'CVV', 'maxlength' => '4', 'id'=>'cvv', 'autocomplete' => 'off')); !!}
                      
                  @endif
                </div>
             
                <div class="inner-assessment-form">
                  <label>Name on Card <span>*</span></label>
                  @if($token_phase2_exist == '1')
                      {!! Form::text('name_on_card',null, $attributes = ['id' => 'name_on_card','class' => 'form-control', 'placeholder'=>'Name on Card', 'maxlength' => '150', 'autocomplete' => 'off', 'disabled'=>'disabled']) !!}
                  @else 
                      {!! Form::text('name_on_card',null, $attributes = ['id' => 'name_on_card','class' => 'form-control', 'placeholder'=>'Name on Card', 'id'=>'name_on_card', 'maxlength' => '150', 'autocomplete' => 'off']) !!}
                      
                  @endif
                </div>
                </div>
                <div class="outer-billing-form">
                  <div class="inner-billing-form"> 
                    <label>Change billing address</label>
                    <?php $billing_address = 1; ?>
                    {!! Form::checkbox('billing_address', $value = 1, $billing_address, $attributes = array('id' => 'billing_address', 'onchange' => 'changeBillingAddress()')); !!}
                    <span class="checkmark"></span>
                  </div>
                  <div class="inner-billing-form"> 
                      <label>
                         Billing Address <span>*</span>
                      </label>
                      <?php 
                        $search_address_text = '';
                        if($data['address_street_number'] != '') {
                            $search_address_text .= $data['address_street_number'];
                        }
                        if($data['address_street'] != '') {
                            $search_address_text .= ' '.$data['address_street'];
                        }
                        if($data['address_city'] != '') {
                            $search_address_text .= ', '.$data['address_city'];
                        }
                        if($data['state_abbr'] != '') {
                            $search_address_text .= ', '.$data['state_abbr'];
                        }
                        if($data['county_name'] != '') {
                            $search_address_text .= ', '.$data['county_name'];
                        }
                        if($data['address_zipcode'] != '') {
                            $search_address_text .= ', '.$data['address_zipcode'];
                        } 
                        $search_address_text .= ', USA';
                      ?>
                      @if($token_phase2_exist == '1')    
                        {!! Form::text('autocomplete_search', $value = $search_address_text, $attributes = array('class'=>"form-control",'placeholder'=>'Search Address', 'autocomplete' => 'off', 'disabled'=>'disabled')); !!}
                    @else
                        <?php $session_autocomplete = (Session::has('autocomplete_search')) ? Session::get('autocomplete_search') : null; ?>
                        {!! Form::text('autocomplete_search', $value = $search_address_text, $attributes = array('class'=>"form-control",'placeholder'=>'Search Address', 'id'=>'autocomplete', 'autocomplete' => 'off')); !!}
                    @endif
                   </div>

                   <div class="form-group autocompletesearch has-feedback element autocomplete_search_section autocompletesearch_full"> 
                        <label></label>
                        <div class="form-section">
                            <div class="" id="autocomplete_search_result_div">
                                <ul id="autocomplete_search_result"></ul>
                            </div>
                        </div>
                    </div>

                   <div class="inner-billing-form inner-inner">
                    <label>Street <span>*</span></label>
                    {!! Form::text('street_number', $data['address_street_number'], $attributes = array('class'=>"form-control",'placeholder'=>'Street Number', 'maxlength' => '100', 'id'=>'street_number', 'autocomplete' => 'off')); !!}
                   </div>
                   <div class="inner-billing-form inner-inner special">
                      <label>
                         
                      </label>
                    {!! Form::text('route', $data['address_street'], $attributes = array('class'=>"form-control",'placeholder'=>'', 'maxlength' => '100', 'id'=>'route', 'autocomplete' => 'off')); !!}
                   </div>
                   <div class="inner-billing-form inner-inner">
                      <label>
                         City <span>*</span>
                      </label>
                    <?php $session_city = (Session::has('search_city')) ? Session::get('search_city') : null; ?>
                    {!! Form::text('locality', $data['address_city'], $attributes = array('class'=>"form-control",'placeholder'=>'City', 'maxlength' => '100', 'id'=>'locality', 'autocomplete' => 'off')); !!}
                   </div>
                   <div class="inner-billing-form inner-inner special">
                        <label>State <span>*</span></label>
                        <div class="dropdown">
                          <?php $session_state = (Session::has('search_state')) ? Session::get('search_state') : null; ?>
                          {!! Form::select('administrative_area_level_1',array_replace(['' => 'Select State '],$states), $data['state_abbr'] ,['id' => 'administrative_area_level_1','class' => 'form-control', 'autocomplete' => 'off']) !!} 
                          {!! Form::hidden('state_id', $data['state_id'], $attributes = ['id' => 'state_id']); !!}
                        </div>
                   </div>
                    <div class="inner-billing-form inner-inner">
                      <label>
                         Zip Code <span>*</span>
                      </label>
                      <?php $session_zipcode = (Session::has('search_zipcode')) ? Session::get('search_zipcode') : null; ?>
                      {!! Form::text('postal_code', $data['address_zipcode'], $attributes = array('class'=>"form-control",'placeholder'=>'Zip code', 'maxlength' => '100', 'id'=>'postal_code', 'autocomplete' => 'off')); !!}
                      
                   </div>
                   <div class="inner-billing-form inner-inner special">
                      <label>
                         County <span>*</span>
                      </label>
                      <div class="dropdown">
                      <?php $session_county = (Session::has('search_county')) ? Session::get('search_county') : null; ?>
                        {!! Form::select('administrative_area_level_2',array_replace(['' => 'Select County '],$counties), $data['county_name'] ,['id' => 'administrative_area_level_2','class' => 'form-control', 'autocomplete' => 'off']) !!} 
                         {!! Form::hidden('county_id', $data['county_id'], $attributes = ['id' => 'county_id']); !!}
                   </div>
               </div>

            </div>
         </div>
        @if($token_exist == '0')  
            {!! Form::hidden('ex_date', null, $attributes = ['id' => 'ex_date']); !!}   
        @endif
            {!! Form::hidden('token_exist', $token_exist, $attributes = ['id' => 'token_exist']); !!}
            {!! Form::hidden('token_phase2_exist', $token_phase2_exist, $attributes = ['id' => 'token_phase2_exist']); !!}
        @if($token_phase2_exist == '1')  
            {!! Form::hidden('token', $token_phase2_details->token, $attributes = ['id' => 'token_phase2']); !!}
        @endif
            
        <div class="inner-wrapper-assessment"><img src="{{ asset('project/resources/assets/customer/css/images/credit-card.jpg') }}"></div>
           
         <div class="continue-button">
             @if($token_phase2_exist == '1')  
                <?php $token_link = url('/list-comparables'); ?>
                <a href="{{ $token_link }}" class="btn btn-ctrl">Continue</a>
            @else
                <?php //{!! Form::submit('Continue',$attributes = array('class'=>"btn-ctrl")); !!} ?>
                {!! Form::button('Continue',array('class'=>"btn-ctrl", 'id'=> 'phase2_make_payment_btn')); !!} 
            @endif
         </div>

        {!! Form::close() !!}          
      </div>
  </div>
</div>

@endsection
@section('js')
<script src="{{ asset('/project/resources/assets/customer/js/phase2_make_payment_js.js') }}"></script> 
<script src="{{ asset('/project/resources/assets/customer/js/autocomplete.js') }}"></script> 

<script type="text/javascript">
  function changeBillingAddress(){
    //$(".outer-billing-form").find('input:text').val('');
    $('.outer-billing-form').find('input:text').each(function() {
        $(this).val('');
      }
    );

    $('.outer-billing-form').find('select').each(function() {
        $(this).val('');
      }
    );
  }
</script>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBlQK-PlX2L5HD_bahlqPjkixMd2PkSxdU&libraries=places&callback=initAutocomplete" async defer"></script>

@endsection
