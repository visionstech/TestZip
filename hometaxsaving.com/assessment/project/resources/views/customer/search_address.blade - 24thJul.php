@extends('app')
@section('title')
    Make Payment
@endsection
@section('css')
<!--link rel="stylesheet" href="https://mapzen.com/js/mapzen.css">
<style>
    #map {
      height: 50%;
      width: 50%;
      position: absolute;
    }
    //html,body{margin: 0; padding: 0;}
</style-->
@endsection
@section('content')

<div class="tsg-inner-wrapper"> 
    {!! Form::open(['url' => 'search-address', 'id' => 'search_address_form', 'novalidate' => 'novalidate']) !!}
    <div class="tsg-latest tsg-common-details add-user-payment-page">
        <div class="col-xs-12 col-md-12 col-sm-12 col-lg-12">
            <?php   
                if(isset($token_details)) {
                    $token_exist = '1';
                }
                else {
                    $token_exist = '0';
                }
            ?>
            @include('errors.user_error')

            <div class="alert alert-danger errorAlertMsgMain text-left error_div_description" style="display: none">
                <strong>Whoops!</strong> There were some problems with your input.
            </div>

            
            <h2>Contact Details</h2>
            <p><b>Lorem ipsum dotor sit amet, consectetur adipiscing elit. Quisque laoreet non elit id dictum.</b></p>
            <div class="contactdetailform">
            <div class="form-group has-feedback element">
                <label>Name<span class="required">*</span></label>
                <div class="form-section">
                    @if($token_exist == '1')
                        {!! Form::text('name',null, $attributes = ['id' => 'name','class' => 'form-control', 'placeholder'=>'Name', 'maxlength' => '150', 'autocomplete' => 'off', 'disabled'=>'disabled']) !!}
                    @else 
                        {!! Form::text('name','geetika', $attributes = ['id' => 'name','class' => 'form-control', 'placeholder'=>'Name', 'id'=>'name', 'maxlength' => '150', 'autocomplete' => 'off']) !!}
                        <span class="icon fa fa-info error error_icon"><span class="tooltiptext"></span></span>
                    @endif
                </div>
            </div>
            <div class="form-group has-feedback element">
                <label>Email address<span class="required">*</span></label>
                <div class="form-section">
                    @if($token_exist == '1')
                        {!! Form::email('email', $value = $token_details->email, $attributes = array('class'=>"form-control",'placeholder'=>'Email address', 'autocomplete' => 'off', 'disabled'=>'disabled')); !!}
                    @else
                        {!! Form::email('email', $value = 'geetika@visions.net.in', $attributes = array('class'=>"form-control",'placeholder'=>'Email address', 'id'=>'email', 'autocomplete' => 'off')); !!}
                        <span class="icon fa fa-info error error_icon"><span class="tooltiptext"></span></span>
                    @endif
                </div>
            </div>
            <div class="form-group has-feedback element">
                <label>Mobile number<span class="required">*</span></label>
                <div class="form-section">
                    @if($token_exist == '1')
                        {!! Form::text('mobile_number', $value = $token_details->mobile_number, $attributes = array('class'=>"form-control",'placeholder'=>'Mobile number', 'autocomplete' => 'off', 'disabled'=>'disabled')); !!}
                    @else
                        {!! Form::text('mobile_number', $value = '9876543234', $attributes = array('class'=>"form-control",'placeholder'=>'Mobile number', 'id'=>'mobile_number', 'autocomplete' => 'off')); !!}
                        <span class="icon fa fa-info error error_icon"><span class="tooltiptext"></span></span>
                    @endif
                </div>
            </div>
        </div>
        </div>
        <?php /*
        <div class="col-xs-12 col-md-12 col-sm-12 col-lg-12">
            <h2>Search Address</h2>
            <p><b>Enter the state and county of your property for which you would like to review the assessment.</b></p>
            <div class="has-feedback element">
                <label>Search Address</label>
                <div class="form-section">
                    @if($token_exist == '1')    
                        <?php $search_address_text = '';
                            if($token_details->billing_street != '') {
                                $search_address_text .= $token_details->billing_street;
                            }
                            if($token_details->billing_city != '') {
                                $search_address_text .= ', '.$token_details->billing_city;
                            }
                            if($token_details->billing_state != '') {
                                $search_address_text .= ', '.$token_details->billing_state;
                            }
                            
                            //if($token_details->billing_county != '') {
                            //    $search_address_text .= ', '.$token_details->billing_county;
                            //}
                            
                            $search_address_text .= ', USA';
                        ?>
                        {!! Form::text('autocomplete_search', $value = $search_address_text, $attributes = array('class'=>"form-control",'placeholder'=>'Search Address', 'autocomplete' => 'off', 'disabled'=>'disabled')); !!}
                    @else
                        {!! Form::text('autocomplete_search', $value = null, $attributes = array('class'=>"form-control",'placeholder'=>'Search Address', 'id'=>'autocomplete_search', 'autocomplete' => 'off')); !!}
                    @endif
                </div>
            </div>
            <div class="form-group element autocompletesearch">
                <label></label>
                <div class="form-section">
                    <div class="" id="autocomplete_search_result_div">
                        <ul id="autocomplete_search_result"></ul>
                    </div>
                </div>
            </div>
        </div>
       */ ?>
        <div class="col-xs-12 col-md-6 col-sm-6 col-lg-6">
            <?php /*
            <div class="form-group has-feedback element">
                <label>Street<span class="required">*</span></label>
                <div class="form-section">
                    @if($token_exist == '1')    
                        {!! Form::text('search_street', $value = $token_details->search_street, $attributes = array('class'=>"form-control",'placeholder'=>'Street', 'autocomplete' => 'off', 'maxlength' => '100', 'disabled'=>'disabled')); !!}
                    @else
                        {!! Form::text('search_street', $value = null, $attributes = array('class'=>"form-control",'placeholder'=>'Street', 'maxlength' => '100', 'id'=>'search_street', 'autocomplete' => 'off')); !!}
                        <span class="icon fa fa-info error error_icon"><span class="tooltiptext"></span></span>
                    @endif
                </div>
            </div>
             */ ?>
            <div class="form-group has-feedback element street_div">
                <label>Street<span class="required">*</span></label>
                <div class="form-section">
                    @if($token_exist == '1')    
                        {!! Form::text('autocomplete_search', $value = $token_details->search_street, $attributes = array('class'=>"form-control",'placeholder'=>'Street', 'autocomplete' => 'off', 'maxlength' => '100', 'disabled'=>'disabled')); !!}
                    @else
                        {!! Form::text('autocomplete_search', $value = null, $attributes = array('class'=>"form-control",'placeholder'=>'Street', 'maxlength' => '100', 'id'=>'autocomplete_search', 'autocomplete' => 'off')); !!}
                        <input type="hidden" name="search_street" id="search_street">
                        <!--span class="icon fa fa-info error error_icon"><span class="tooltiptext"></span></span-->
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
            <div class="form-group has-feedback element">
                <label>City<span class="required">*</span></label>
                <div class="form-section">
                    @if($token_exist == '1')    
                        {!! Form::text('search_city', $value = $token_details->billing_city, $attributes = array('class'=>"form-control",'placeholder'=>'City', 'autocomplete' => 'off', 'maxlength' => '100', 'disabled'=>'disabled')); !!}
                    @else
                        {!! Form::text('search_city', $value = null, $attributes = array('class'=>"form-control",'placeholder'=>'City', 'maxlength' => '100', 'id'=>'search_city', 'autocomplete' => 'off')); !!}
                        <span class="icon fa fa-info error error_icon"><span class="tooltiptext"></span></span>
                    @endif
                </div>
            </div>
            <div class="form-group has-feedback element">
                <label>State<span class="required">*</span></label>
                <div class="form-section">
                    @if($token_exist == '1')  
                        <?php //{!! Form::text('billing_state', $value = $token_details->billing_state, $attributes = array('class'=>"form-control",'placeholder'=>'State', 'autocomplete' => 'off', 'maxlength' => '100', 'id'=>'billing_state', 'disabled'=>'disabled')); !!} ?>
                        {!! Form::select('search_state',array_replace(['' => 'Select State '],$states), $token_details->search_state ,['id' => 'search_state','class' => 'form-control', 'autocomplete' => 'off', 'disabled'=>'disabled']) !!} 
                    @else
                        <?php //{!! Form::text('billing_state', $value = null, $attributes = array('class'=>"form-control",'placeholder'=>'Zip code', 'maxlength' => '100', 'id'=>'billing_state', 'autocomplete' => 'off')); !!} ?>
                        {!! Form::select('search_state',array_replace(['' => 'Select State '],$states), null ,['id' => 'search_state','class' => 'form-control', 'autocomplete' => 'off']) !!} 
                        <span class="icon fa fa-info error error_icon"><span class="tooltiptext"></span></span>
                    @endif
                </div>
            </div>
            <div class="form-group has-feedback element">
                <label>Zip code<span class="required">*</span></label>
                <div class="form-section">
                    @if($token_exist == '1')  
                        {!! Form::text('search_zipcode', $value = $token_details->search_zipcode, $attributes = array('class'=>"form-control",'placeholder'=>'Zip code', 'autocomplete' => 'off', 'maxlength' => '100', 'id'=>'search_zipcode', 'disabled'=>'disabled')); !!}
                    @else
                        {!! Form::text('search_zipcode', $value = null, $attributes = array('class'=>"form-control",'placeholder'=>'Zip code', 'maxlength' => '100', 'id'=>'search_zipcode', 'autocomplete' => 'off')); !!}
                        <span class="icon fa fa-info error error_icon"><span class="tooltiptext"></span></span>
                    @endif
                </div>
            </div>
            <div class="form-group has-feedback element">
                <label>County<span class="required">*</span></label>
                <div class="form-section state_counties">
                    @if($token_exist == '1')  
                        <?php //{!! Form::text('billing_county', $value = $token_details->billing_county, $attributes = array('class'=>"form-control",'placeholder'=>'County', 'autocomplete' => 'off', 'maxlength' => '100', 'id'=>'billing_county', 'disabled'=>'disabled')); !!} ?>
                        {!! Form::select('search_county',array_replace(['' => 'Select County '],$counties), $token_details->search_county ,['id' => 'search_county','class' => 'form-control', 'autocomplete' => 'off', 'disabled'=>'disabled']) !!} 
                    @else
                        <?php //{!! Form::text('billing_county', $value = null, $attributes = array('class'=>"form-control",'placeholder'=>'Zip code', 'maxlength' => '100', 'id'=>'billing_county', 'autocomplete' => 'off')); !!} ?>
                        {!! Form::select('search_county',array_replace(['' => 'Select County '],$counties), null ,['id' => 'search_county','class' => 'form-control', 'autocomplete' => 'off']) !!} 
                        <span class="icon fa fa-info error error_icon"><span class="tooltiptext"></span></span>
                    @endif
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="form-group has-feedback element">
                <label>Latest Assessment Value<span class="required">*</span></label>
                <div class="form-section">
                @if($token_status >= '4')
                    {!! Form::text('latest_assessment_value', $value = $latest_assessment_value, $attributes = array('class'=>"form-control",'placeholder'=>'Latest Assessment Value', 'maxlength' => '100', 'autocomplete' => 'off', 'disabled'=>'disabled')); !!}
                @else    
                    {!! Form::text('latest_assessment_value', $value = null, $attributes = array('class'=>"form-control",'placeholder'=>'Latest Assessment Value', 'id'=>'latest_assessment_value', 'maxlength' => '100', 'autocomplete' => 'off')); !!}
                    <span class="icon fa fa-info error error_icon"><span class="tooltiptext"></span></span>
                    <div class="msg-data">
                        <a href="#" target="_blank" class="text-bold" id="county_link"></a>
                    </div>
                @endif
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="col-xs-12 col-md-6 col-sm-6 col-lg-6 map-section">
            <div class="row">
                <div id="map_canvas" class="no-top-margin top-margin-map-sm" style="width: 100%; height: 420px;"></div>
            </div>
        </div> 
    </div><!-- tsg-latest -->

        <div class="tsg-btn-wrap add_user_button">  
            @if($token_exist == '1')  
                <?php $token_link = url('/address'); ?>
                <a href="{{ $token_link }}" class="btn btn-ctrl">Continue</a>
            @else
                <?php //{!! Form::submit('Continue',$attributes = array('class'=>"btn-ctrl")); !!} ?>
                {!! Form::button('Continue',array('class'=>"btn btn-ctrl", 'id'=> 'search_address_btn')); !!} 
            @endif
        </div>
  
    
    {!! Form::hidden('token_exist', $token_exist, $attributes = ['id' => 'token_exist']); !!}
    @if($token_exist == '1')  
        {!! Form::hidden('token', $token_details->token, $attributes = ['id' => 'token']); !!}
    @endif

    {!! Form::close() !!} 
    
</div><!-- tsg-inner-wrapper -->




@endsection
@section('js')
<!-- google map js -->
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD99kTAY6833t9YvRSQCfPmdeP9Hq67W5c&callback=initMap"></script>

<script type="text/javascript">

var map;
var marker;
var infowindow;

function initMap() 
{
    var myLatLng = {lat: -25.363, lng: 131.044};
    map = new google.maps.Map(document.getElementById('map_canvas'), {
        zoom: 3,
        center: myLatLng
    });
    
}
</script>
    
<script type="text/javascript">	
$(document).ready(function() {
    
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
    
    $('#autocomplete_search').keyup(function() {
        var autocomplete_search = $('#autocomplete_search').val();
        
        console.log(autocomplete_search);
        if(autocomplete_search == '' || autocomplete_search == null) {
            $('#autocomplete_search_result').html('');
            $('#autocomplete_search_result_div').slideUp('200');
            $('.autocomplete_search_section').hide();
            //$('.street_div').removeClass('no-bottom-margin');
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
                                //console.log(obj.properties.county.slice(-6)+'test');
                                if(obj.properties.county.slice(-6) == 'County' || obj.properties.county.slice(-6) == 'county') {
                                    county = obj.properties.county.slice(0,-7);
                                }
                                else {
                                    county = obj.properties.county;
                                }                                
                            }
                            
                            $('#autocomplete_search_result').append('<li class="select_address" data-label="'+obj.properties.label+'" data-street="'+housenumber+street+'" data-city="'+city+'" data-state="'+state+'" data-zipcode="'+zipcode+'" data-county="'+county+'">'+obj.properties.label+'</li>');
                        });
                        $('.autocomplete_search_section').show();
                        $('#autocomplete_search_result_div').slideDown('200');
                        //$('.street_div').addClass('no-bottom-margin');

                },
                error: function(data) {
                    // Error...
                    console.log('error');
                    console.log(data);
                    $('#autocomplete_search_result').html('');
                    $('#autocomplete_search_result_div').slideUp('200');
                    $('.autocomplete_search_section').hide();
                    //$('.street_div').removeClass('no-bottom-margin');
                }
            })
        }
    });
    
    
    $('#autocomplete_search').keyup(function() {
        $('#search_street').val($(this).val());
    });
    
    $(document.body).on("click", ".select_address", function() 
    {
        state_name = $(this).data('state');
        county_name = $(this).data('county');
        $('#autocomplete_search').val($(this).data('label'));
        $('#search_street').val($(this).data('street'));
        $('#search_city').val($(this).data('city'));
        $('#search_state').val($(this).data('state'));
        $('#search_state').trigger('change');
        $('#search_zipcode').val($(this).data('zipcode'));
        $('#search_county').val($(this).data('county'));
        
        $('#autocomplete_search_result').html('');
        $('#autocomplete_search_result_div').slideUp('200');
        $('.autocomplete_search_section').hide();
        //$('.street_div').removeClass('no-bottom-margin');
    });
    
    
    var form_submit = 0;
    
    $("#search_address_btn").click(function()
    {	
        form_submit = 1;
        addLoader('#loader_body');
        
        var options = 
        { 
            success:function(data) 
            {
                console.log(data);
                if(data.success == false) {
                    removeLoader('#loader_body');
                    $(".error").hide();
                    $('.error_div_description').hide();
                    $('.element').removeClass('has-error');
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
                console.log(errors);
                $(".error").hide();
                $('.error_div_description').fadeIn(200);
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
		
        $("#search_address_form").ajaxForm(options);
        $("#search_address_form").submit();
			
    });
    
    
    $('#search_state').change(function()
    {
        addLoader('#loader_body');
        //$('#search_county').attr('disabled', 'disabled');
        same_state = '0';
        if(state_name == $('#search_state').find(':selected').val()) {
            same_state = '1';
        }

        var formData = {
            state_name: $('#search_state').find(':selected').val(),
            input_name : 'search_county'
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
                            $('#search_county').val(county_name);
                        }
                        else {
                            $('#search_county').val('');
                        }
                        $('#search_county').trigger('change');
                    }
                    
                    if(form_submit == 1) {
                        $('#search_address_btn').trigger('click');
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
    
    
    $(document.body).on("change", "#search_county", function() 
    {
        addLoader('#loader_body');
        
        var formData = {
            state_name: $('#search_state').find(':selected').val(),
        }
        
        if(county_name != '') {
            formData.county_name = county_name;
        }
        else {
            formData.county_name = $('#search_county').find(':selected').val();
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
