@extends('app')
@section('title')
    Make Payment
@endsection
@section('css')

@endsection
@section('content')

<div class="tsg-inner-wrapper"> 
    <div class="tsg-latest tsg-common-details add-user-payment-page">

        <h2>Pay $10.00</h2>
        <p><b>Lorem Ipsum is simply dummy text of the printing and typesetting industry. 
            Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, 
            when an unknown printer took a galley of type and scrambled it to make a type 
            specimen book. It has survived not only five centuries, but also the leap into 
            electronic typesetting, remaining essentially unchanged. </b>
        </p>
        <?php   $card_types = ['MasterCard'=> 'MasterCard', 'Visa'=> 'Visa', 'American Express'=> 'American Express', 'Discover'=> 'Discover']; 
                $months = ['01'=> 'January', '02'=> 'February', '03'=> 'March', '04'=> 'April', '05'=> 'May', '06'=> 'June', '07'=> 'July', '08'=> 'August', '09'=> 'September', '10'=> 'October', '11'=> 'November', '12'=> 'December'];
                $date = date('Y');
                $max_date = $date+10;
                //$years = range($date, $date+10);

                for ($i = $date; $i <= $max_date; $i++) {
                  $years[$i] = $i;
                }
                //echo "<pre>"; print_r($months); print_r($years); exit;
                
                //$states = ['State1' => 'State1', 'State2' => 'State2', 'State3' => 'State3'];
                //$county = ['County1' => 'County1', 'County2' => 'County2', 'County3' => 'County3'];
                //$county = [];
                
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
        
        {!! Form::open(['url' => 'make-payment', 'id' => 'make_payment_form', 'novalidate' => 'novalidate']) !!}
        <h2>Payment Detail</h2>
        <?php /*
        <div class="makpaymentform">
            <div class="form-group has-feedback element">

                <label>Email<span class="required">*</span></label>
                <div class="form-section">
                @if($token_exist == '1')
                    {!! Form::email('email', $value = $token_details->email, $attributes = array('class'=>"form-control",'placeholder'=>'Email', 'autocomplete' => 'off', 'disabled'=>'disabled')); !!}
                @else
                    <?php $session_email = (Session::has('email')) ? Session::get('email') : ''; ?>
                    {!! Form::email('email', $session_email, $attributes = array('class'=>"form-control",'placeholder'=>'Email', 'id'=>'email', 'autocomplete' => 'off')); !!}
                    <span class="icon fa fa-info error error_icon"><span class="tooltiptext"></span></span>
                @endif
                </div>
            </div>
            <div class="form-group has-feedback element">
                <label>Confirm Email<span class="required">*</span></label>
                <div class="form-section">
                @if($token_exist == '1')
                    {!! Form::email('email_confirmation', $value = $token_details->email, $attributes = array('class'=>"form-control",'placeholder'=>'Email', 'autocomplete' => 'off', 'disabled'=>'disabled')); !!}
                @else
                    {!! Form::email('email_confirmation', $value = null, $attributes = array('class'=>"form-control",'placeholder'=>'Confirm Email', 'id'=>'email_confirmation', 'autocomplete' => 'off')); !!}
                    <span class="icon fa fa-info error error_icon"><span class="tooltiptext"></span></span>
                @endif
                </div>
            </div>
        </div>
        */ ?>
        <div class="makpaymentformcrcard">
            <div class="form-group has-feedback element">
                <label>Credit Card Type</label>
                <div class="form-section">
                    @if($token_exist == '1')
                        {!! Form::select('card_type',array_replace(['' => 'Select Card Type '],$card_types), null ,['id' => 'card_type','class' => 'form-control', 'autocomplete' => 'off', 'disabled'=>'disabled']) !!}
                    @else
                        {!! Form::select('card_type',array_replace(['' => 'Select Card Type '],$card_types), 'Discover' ,['id' => 'card_type','class' => 'form-control', 'autocomplete' => 'off']) !!}
                        <span class="icon fa fa-info error error_icon"><span class="tooltiptext"></span></span>
                    @endif
                </div>
                <label class="details-cards"></label>

            </div>
            <div class="social-icons payment-cards">    
                <ul>
                    <li><a href="#"><img src="{{ asset('project/resources/assets/customer/images/master-card.png') }}"></a></li>
                    <li><a href="#"><img src="{{ asset('project/resources/assets/customer/images/visa.png') }}"></a></li>   
                    <li><a href="#"><img src="{{ asset('project/resources/assets/customer/images/american-express.png') }}"></a></li>
                    <li><a href="#"><img src="{{ asset('project/resources/assets/customer/images/discover.png') }}"></a></li>
                </ul>
            </div>
             </div>
         <div class="makpaymentform">
        <div class="form-group has-feedback element">
            <label>Credit Card Number<span class="required">*</span></label>
            <div class="form-section">
                @if($token_exist == '1')
                    {!! Form::text('card_number', $value = '6011111111111117', $attributes = array('class'=>"form-control",'placeholder'=>'Card Number', 'maxlength' => '100', 'autocomplete' => 'off', 'disabled'=>'disabled')); !!}
                @else
                    {!! Form::text('card_number', $value = '6011111111111117', $attributes = array('class'=>"form-control",'placeholder'=>'Card Number', 'maxlength' => '100', 'id'=>'card_number', 'autocomplete' => 'off')); !!}
                    <span class="icon fa fa-info error error_icon"><span class="tooltiptext"></span></span>
                @endif
            </div>
        </div>
        <div class="form-group has-feedback ">
            <label>Expiration Date<span class="required">*</span></label>
            <div class="form-section">
                <div class="row">
                    <div class="half-input col-xs-6 col-md-6 col-sm-6 col-lg-6 element">
                        @if($token_exist == '1')
                            {!! Form::select('ex_month',array_replace(['' => 'Select Month '],$months), null ,['id' => 'ex_month','class' => 'form-control', 'autocomplete' => 'off', 'disabled'=>'disabled']) !!}
                        @else
                            {!! Form::select('ex_month',array_replace(['' => 'Select Month '],$months), '02' ,['id' => 'ex_month','class' => 'form-control', 'autocomplete' => 'off']) !!}
                            <span class="icon fa fa-info error error_icon"><span class="tooltiptext"></span></span>
                        @endif
                    </div>
                    <div class="half-input col-xs-6 col-md-6 col-sm-6 col-lg-6 element">   
                        @if($token_exist == '1')
                            {!! Form::select('ex_year',array_replace(['' => 'Select Year '],$years), null ,['id' => 'ex_year','class' => 'form-control', 'autocomplete' => 'off', 'disabled'=>'disabled']) !!}
                        @else 
                            {!! Form::select('ex_year',array_replace(['' => 'Select Year '],$years), '2020' ,['id' => 'ex_year','class' => 'form-control', 'autocomplete' => 'off']) !!}
                            <span class="icon fa fa-info error error_icon"><span class="tooltiptext"></span></span>
                        @endif 
                    </div>
                </div>
            </div>            
        </div>
        <!--div class="form-group has-feedback element">
            <label></label>
           
        </div-->
        <div class="form-group has-feedback element">
            <label>CVV Code<span class="required">*</span></label>
            <div class="form-section">
                @if($token_exist == '1')
                    {!! Form::text('cvv', null, $attributes = array('class'=>"form-control",'placeholder'=>'CVV', 'maxlength' => '4', 'autocomplete' => 'off', 'disabled'=>'disabled')); !!}
                @else
                    {!! Form::text('cvv', '123', $attributes = array('class'=>"form-control",'placeholder'=>'CVV', 'maxlength' => '4', 'id'=>'cvv', 'autocomplete' => 'off')); !!}
                    <span class="icon fa fa-info error error_icon"><span class="tooltiptext"></span></span>
                @endif
            </div>
        </div>
        <div class="form-group has-feedback element">
            <label>Name on Card<span class="required">*</span></label>
            <div class="form-section">
                @if($token_exist == '1')
                    {!! Form::text('name_on_card',null, $attributes = ['id' => 'name_on_card','class' => 'form-control', 'placeholder'=>'Name on Card', 'maxlength' => '150', 'autocomplete' => 'off', 'disabled'=>'disabled']) !!}
                @else 
                    {!! Form::text('name_on_card',null, $attributes = ['id' => 'name_on_card','class' => 'form-control', 'placeholder'=>'Name on Card', 'id'=>'name_on_card', 'maxlength' => '150', 'autocomplete' => 'off']) !!}
                    <span class="icon fa fa-info error error_icon"><span class="tooltiptext"></span></span>
                @endif
            </div>
        </div>
         </div>
		 <div class="form-group has-feedback element">    
           
            @if($token_exist == '1')  
                <?php $checked = ($token_details->save_in_vault == '1') ? true : false; ?>
                {!! Form::checkbox('save_in_vault', $value = 1, $checked, $attributes = array('class'=>"", 'id' => 'use_same_address', 'autocomplete' => 'off', 'disabled'=>'disabled')); !!}
            @else   
                {!! Form::checkbox('save_in_vault', $value = 1, false, $attributes = array('class'=>"", 'id' => 'use_same_address', 'autocomplete' => 'off')); !!}
                <span class="icon fa fa-info error error_icon"><span class="tooltiptext"></span></span>
            @endif
           Save Credit card details in Paypal vault. {!! Html::link('http://www.paypal.com','Learn More'); !!}
        </div>
        <hr class="makepayorder">
        
        <h2>Billing Address</h2>
          <div class="makpaymentform">
        <div class="has-feedback element">
            <label>Search Address</label>
            <div class="form-section margin-btm-30">
                @if($token_exist == '1')    
                    <?php $search_address_text = '';
                        if($address_street != '') {
                            $search_address_text .= $address_street;
                        }
                        if($address_city != '') {
                            $search_address_text .= ', '.$address_city;
                        }
                        if($state_abbr != '') {
                            $search_address_text .= ', '.$state_abbr;
                        }
                        /*
                        if($token_details->billing_county != '') {
                            $search_address_text .= ', '.$token_details->billing_county;
                        }
                         */
                        $search_address_text .= ', USA';
                    ?>
                    {!! Form::text('autocomplete_search', $value = $search_address_text, $attributes = array('class'=>"form-control",'placeholder'=>'Search Address', 'autocomplete' => 'off', 'disabled'=>'disabled')); !!}
                @else
                    <?php $session_autocomplete = (Session::has('autocomplete_search')) ? Session::get('autocomplete_search') : null; ?>
                    {!! Form::text('autocomplete_search', $session_autocomplete, $attributes = array('class'=>"form-control",'placeholder'=>'Search Address', 'id'=>'autocomplete_search', 'autocomplete' => 'off')); !!}
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
      
        <div class="form-group has-feedback element">
            <label>Street<span class="required">*</span></label>
            <div class="form-section">
                @if($token_exist == '1')    
                    {!! Form::text('billing_street', $value = $address_street, $attributes = array('class'=>"form-control",'placeholder'=>'Street', 'autocomplete' => 'off', 'maxlength' => '100', 'disabled'=>'disabled')); !!}
                @else
                    <?php $session_street = (Session::has('search_street')) ? Session::get('search_street') : null; ?>
                    {!! Form::text('billing_street', $session_street, $attributes = array('class'=>"form-control",'placeholder'=>'Street', 'maxlength' => '100', 'id'=>'street_number', 'autocomplete' => 'off')); !!}
                    <span class="icon fa fa-info error error_icon"><span class="tooltiptext"></span></span>
                @endif
            </div>
        </div>
        <div class="form-group has-feedback element">
            <label>City<span class="required">*</span></label>
            <div class="form-section">
                @if($token_exist == '1')    
                    {!! Form::text('billing_city', $value = $address_city, $attributes = array('class'=>"form-control",'placeholder'=>'City', 'autocomplete' => 'off', 'maxlength' => '100', 'disabled'=>'disabled')); !!}
                @else
                    <?php $session_city = (Session::has('search_city')) ? Session::get('search_city') : null; ?>
                    {!! Form::text('billing_city', $session_city, $attributes = array('class'=>"form-control",'placeholder'=>'City', 'maxlength' => '100', 'id'=>'locality', 'autocomplete' => 'off')); !!}
                    <span class="icon fa fa-info error error_icon"><span class="tooltiptext"></span></span>
                @endif
            </div>
        </div>
        <div class="form-group has-feedback element">
            <label>State<span class="required">*</span></label>
            <div class="form-section">
                @if($token_exist == '1')  
                    <?php //{!! Form::text('billing_state', $value = $token_details->billing_state, $attributes = array('class'=>"form-control",'placeholder'=>'State', 'autocomplete' => 'off', 'maxlength' => '100', 'id'=>'billing_state', 'disabled'=>'disabled')); !!} ?>
                    {!! Form::select('billing_state',array_replace(['' => 'Select State '],$states), $state_abbr ,['id' => 'billing_state','class' => 'form-control', 'autocomplete' => 'off', 'disabled'=>'disabled']) !!} 
                @else
                    <?php //{!! Form::text('billing_state', $value = null, $attributes = array('class'=>"form-control",'placeholder'=>'Zip code', 'maxlength' => '100', 'id'=>'billing_state', 'autocomplete' => 'off')); !!} ?>
                    <?php $session_state = (Session::has('search_state')) ? Session::get('search_state') : null; ?>
                    {!! Form::select('billing_state',array_replace(['' => 'Select State '],$states), $session_state ,['id' => 'administrative_area_level_1','class' => 'form-control', 'autocomplete' => 'off']) !!} 
                    <span class="icon fa fa-info error error_icon"><span class="tooltiptext"></span></span>
                @endif
            </div>
        </div>
        <div class="form-group has-feedback element">
            <label>Zip code<span class="required">*</span></label>
            <div class="form-section">
                @if($token_exist == '1')  
                    {!! Form::text('billing_zipcode', $value = $address_zipcode, $attributes = array('class'=>"form-control",'placeholder'=>'Zip code', 'autocomplete' => 'off', 'maxlength' => '100', 'id'=>'billing_zipcode', 'disabled'=>'disabled')); !!}
                @else
                    <?php $session_zipcode = (Session::has('search_zipcode')) ? Session::get('search_zipcode') : null; ?>
                    {!! Form::text('billing_zipcode', $session_zipcode, $attributes = array('class'=>"form-control",'placeholder'=>'Zip code', 'maxlength' => '100', 'id'=>'postal_code', 'autocomplete' => 'off')); !!}
                    <span class="icon fa fa-info error error_icon"><span class="tooltiptext"></span></span>
                @endif
            </div>
        </div>
        <div class="form-group has-feedback element">
            <label>County<span class="required">*</span></label>
            <div class="form-section state_counties">
                @if($token_exist == '1')  
                    <?php //{!! Form::text('billing_county', $value = $token_details->billing_county, $attributes = array('class'=>"form-control",'placeholder'=>'County', 'autocomplete' => 'off', 'maxlength' => '100', 'id'=>'billing_county', 'disabled'=>'disabled')); !!} ?>
                    {!! Form::select('billing_county',array_replace(['' => 'Select County '],$counties), $county_name ,['id' => 'billing_county','class' => 'form-control', 'autocomplete' => 'off', 'disabled'=>'disabled']) !!} 
                @else
                    <?php //{!! Form::text('billing_county', $value = null, $attributes = array('class'=>"form-control",'placeholder'=>'Zip code', 'maxlength' => '100', 'id'=>'billing_county', 'autocomplete' => 'off')); !!} ?>
                    <?php $session_county = (Session::has('search_county')) ? Session::get('search_county') : null; ?>
                    {!! Form::select('billing_county',array_replace(['' => 'Select County '],$counties), $session_county ,['id' => 'billing_county','class' => 'form-control', 'autocomplete' => 'off']) !!} 
                    <span class="icon fa fa-info error error_icon"><span class="tooltiptext"></span></span>
                @endif
            </div>
        </div>
        <?php /*
        <div class="form-group has-feedback element">    
            <label></label>
            @if($token_exist == '1')  
                <?php $checked = ($token_details->use_same_address == '1') ? true : false; ?>
                {!! Form::checkbox('use_same_address', $value = 1, $checked, $attributes = array('class'=>"", 'id' => 'use_same_address', 'autocomplete' => 'off', 'disabled'=>'disabled')); !!}
            @else   
                {!! Form::checkbox('use_same_address', $value = 1, false, $attributes = array('class'=>"", 'id' => 'use_same_address', 'autocomplete' => 'off')); !!}
                <span class="icon fa fa-info error error_icon"><span class="tooltiptext"></span></span> 
            @endif
            <span class="serarchcheckbox">Search address same as billing address</span> 
        </div>
        */ ?>
              
        @if($token_exist == '0')  
            {!! Form::hidden('ex_date', null, $attributes = ['id' => 'ex_date']); !!}   
        @endif
            {!! Form::hidden('token_exist', $token_exist, $attributes = ['id' => 'token_exist']); !!}
        @if($token_exist == '1')  
            {!! Form::hidden('token', $token_details->token, $attributes = ['id' => 'token']); !!}
        @endif
        <?php //{!! Form::hidden('user_email', Session::get('user_email'), $attributes = ['id' => 'user_email']); !!} ?>
        
        
        <!--div id="map"></div-->
        
        </div>
    </div><!-- tsg-latest -->
</div><!-- tsg-inner-wrapper -->

<div class="tsg-inner-wrapper">
    <div class="tsg-btn-wrap add_user_button">  
        @if($token_exist == '1')  
            <?php $token_link = url('/verify-address'); ?>
            <a href="{{ $token_link }}" class="btn btn-ctrl">Continue</a>
        @else
            <?php //{!! Form::submit('Continue',$attributes = array('class'=>"btn-ctrl")); !!} ?>
            {!! Form::button('Continue',array('class'=>"btn-ctrl", 'id'=> 'make_payment_btn')); !!} 
        @endif
    </div><!-- tsg-inner-wrapper -->
</div>
</div>
 {!! Form::close() !!} 
@endsection
@section('js')
<?php /*
<script src="https://mapzen.com/js/mapzen.min.js"></script>
<script>
    // Add a Mapzen API key
    L.Mapzen.apiKey = "mapzen-7ta2nBV";
    
    // Add a map to the 'map' div
    var map = L.Mapzen.map('map');
    
    // Set the center of the map to be the San Francisco Bay Area at zoom level 12
    map.setView([37.7749, -122.4194], 12);
    
    var geocoder = L.Mapzen.geocoder();
    geocoder.addTo(map);
</script>
*/ ?>
    
<script type="text/javascript">	
$(document).ready(function() {
    
    $('#autocomplete_search').keyup(function() {
        var autocomplete_search = $('#autocomplete_search').val();
        
        var housenumber = '';
        var street = '';
        var city = '';
        var state = '';
        var zipcode = '';
        var country = '';
        var county = '';
        var state_name = '';
        var county_name = '';
        var same_state = '0';
        
        state_name = $('#billing_state').val();
        county_name = $('#billing_county').val();
        
        console.log(autocomplete_search);
        if(autocomplete_search == '' || autocomplete_search == null) {
            $('#autocomplete_search_result').html('');
            $('#autocomplete_search_result_div').slideUp('200');
        }
        else {
            $.ajax({
                type     : 'GET',
                url      : "https://search.mapzen.com/v1/autocomplete?text="+autocomplete_search+"&api_key=mapzen-7ta2nBV&boundary.country=USA",
                datatype : 'json',
                success  : function(data) {
                        console.log('success');
                        console.log(data);	

                        $('#autocomplete_search_result').html('');
                        
                        housenumber = '';
                        street = '';
                        city = '';
                        state = '';
                        zipcode = '';
                        country = '';
                        county = '';

                        $.each(data.features, function(i, obj)
                        {
                            //console.log(i);
                            //console.log(obj.properties);
                            if(obj.properties.hasOwnProperty('housenumber')) {
                                housenumber = obj.properties.housenumber+' ';
                            } 
                            if(obj.properties.hasOwnProperty('street')) {
                                street = obj.properties.street;
                            }
                            else if(obj.properties.hasOwnProperty('name')){
                                street = obj.properties.name;
                            }
                            if(obj.properties.hasOwnProperty('locality')) { 
                                city = obj.properties.locality;
                            }
                            if(obj.properties.hasOwnProperty('region')) {
                                state = obj.properties.region;
                            }
                            if(obj.properties.hasOwnProperty('postalcode')) {
                                zipcode = obj.properties.postalcode;
                            }
                            if(obj.properties.hasOwnProperty('county')) {
                                //county = obj.properties.county;
                                if(obj.properties.county.slice(-6) == 'County' || obj.properties.county.slice(-6) == 'county') {
                                    county = obj.properties.county.slice(0,-7);
                                }
                                else {
                                    county = obj.properties.county;
                                } 
                            }
                            
                            $('#autocomplete_search_result').append('<li class="select_address" data-label="'+obj.properties.label+'" data-street="'+housenumber+street+'" data-city="'+city+'" data-state="'+state+'" data-zipcode="'+zipcode+'" data-county="'+county+'">'+obj.properties.label+'</li>');
                        });
                        $('#autocomplete_search_result_div').slideDown('200');

                },
                error: function(data) {
                    // Error...
                    console.log('error');
                    console.log(data);
                    $('#autocomplete_search_result').html('');
                    $('#autocomplete_search_result_div').slideUp('200');

                }
            })
        }
    });
    
    
    $(document.body).on("click", ".select_address", function() 
    {
        state_name = $(this).data('state');
        county_name = $(this).data('county');
        $('#autocomplete_search').val($(this).data('label'));
        $('#billing_street').val($(this).data('street'));
        $('#billing_city').val($(this).data('city'));
        $('#billing_state').val($(this).data('state'));
        $('#billing_state').trigger('change');
        $('#billing_zipcode').val($(this).data('zipcode'));
        $('#billing_county').val($(this).data('county'));
        
        $('#autocomplete_search_result').html('');
        $('#autocomplete_search_result_div').slideUp('200');
    });
    
    
    $('#make_payment_form').submit(function(e) {
        //e.preventDefault();
        var ex_month = $('#ex_month').val();
        var ex_year = $('#ex_year').val();
        $('#ex_date').val(ex_month+'-'+ex_year);
        //$('#make_payment_form').submit();
    });
    
    var form_submit = 0;
    
    $("#make_payment_btn").click(function()
    {	
        form_submit = 1;
        var ex_month = $('#ex_month').val();
        var ex_year = $('#ex_year').val();
        $('#ex_date').val(ex_month+'-'+ex_year);
        
        addLoader('#loader_body');
        
        var options = 
        { 
            success:function(data) 
            {
                if(data.success == false) {
                    removeLoader('#loader_body');                    
                    if(data.hasOwnProperty('message')) {
                        $('.error_div_description').fadeIn(200);
                        $('#invalid_address_msg').html('<br>'+data.message);
                    }
                    else {
                        $(".error").hide();
                        $('.error_div_description').hide();
                        $('#invalid_address_msg').text('');
                        $('.element').removeClass('has-error');
                    }
                }
                else {
                    //setTimeout("reload()", 3000);
                    window.location = data.redirect_url;
                    //console.log('here');
                }	
            },				
            error: function(data)
            {
                var errors = $.parseJSON(data.responseText);
                //console.log(errors);
                $(".error").hide();
                $('.error_div_description').fadeIn(200);
                $('#invalid_address_msg').text('');
                $('.element').find('.error').hide();
                $('.element').removeClass('has-error');
                $.each(errors, function(i, obj)
                {
                    $('#'+i).closest('.element').find('.error').fadeIn(200);
                    $('#'+i).closest('.element').addClass('has-error');
                    $('#'+i).closest('.element').find('.error .tooltiptext').html(obj);
                });	
                removeLoader('#loader_body');	
            }
        };
		
        $("#make_payment_form").ajaxForm(options);
        $("#make_payment_form").submit();
			
    });
    
    
    $('#billing_state').change(function()
    {
        addLoader('#loader_body');
        $('#billing_county').attr('disabled', 'disabled');
        
        same_state = '0';
        if(state_name == $('#billing_state').find(':selected').val()) {
            same_state = '1';
        }

        var formData = {
            state_name: $('#billing_state').find(':selected').val(),
            input_name : 'billing_county'
        }

        formData._token = $('meta[name="csrf-token"]').attr('content');
        $.ajax({
            type     : 'POST',
            url      : path+'/state-counties',
            data     : formData,
            datatype : 'html',
            success  : function(data) {
                //console.log(data);
                
                if(data.success == false)
                {
                    console.log(data.message);
                    removeLoader('#loader_body');
                }
                else
                {
                    $('.state_counties').html(data);
                    if(county_name != '') {
                        if(same_state == '1') {
                            $('#billing_county').val(county_name);
                        }
                        else {
                            $('#billing_county').val('');
                        }
                        
                        $('#billing_county').trigger('change');
                    }
                    
                    if(form_submit == 1) {
                        $('#make_payment_btn').trigger('click');
                    }
                    else {
                        removeLoader('#loader_body');
                    }
                    /*
                    if(form_submit == 1) {
                        $('#billing_county').closest('.element').find('.error').fadeIn(200);
                        $('#billing_county').closest('.element').addClass('has-error');
                        $('#billing_county').closest('.element').find('.error .tooltiptext').html('The billing county field is required.');
                    }
                    
                    $(".error").hide();
                    $('.error_div_description').fadeIn(200);
                    $('.element').find('.error').hide();
                    $('.element').removeClass('has-error');
                    */
                }
            },
            error: function(data) {
                // Error...
                var errors = data.responseJSON;
                console.log(errors);
                removeLoader('#loader_body');
            }
        })


    });
    
    
    $(document.body).on("change", "#billing_county", function() 
    {
        addLoader('#loader_body');
        
        var formData = {
            state_name: $('#billing_state').find(':selected').val(),
        }
        
        if(county_name != '') {
            formData.county_name = county_name;
        }
        else {
            formData.county_name = $('#billing_county').find(':selected').val();
        }

        formData._token = $('meta[name="csrf-token"]').attr('content');
        $.ajax({
            type     : 'POST',
            url      : path+'/county-link',
            data     : formData,
            datatype : 'html',
            success  : function(data) {
                //console.log(data);
                
                if(data.success == false)
                {
                    console.log(data.message);
                    removeLoader('#loader_body');
                }
                else
                {
                    $('#county_link').html(data.county_link);
                    $('#county_link').attr('href', data.county_link);
                    removeLoader('#loader_body');
                }
            },
            error: function(data) {
                // Error...
                var errors = data.responseJSON;
                console.log(errors);
                removeLoader('#loader_body');
            }
        })


    });
    
});

</script>
@endsection
