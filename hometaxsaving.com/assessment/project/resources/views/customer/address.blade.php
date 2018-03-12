@extends('app')
@section('title')
    Add Address
@endsection
@section('content')



<div class="tsg-inner-wrapper"> 
    <div class="tsg-latest tsg-common-details add-user-payment-page add-user-address">
        <?php   //$states = ['State1' => 'State1', 'State2' => 'State2', 'State3' => 'State3'];
                //$county = ['County1' => 'County1', 'County2' => 'County2', 'County3' => 'County3'];
        ?>
        <h2  class="col-xs-12">Enter Home Address</h2>
         {!! Form::open(['url' => 'address', 'id' => 'add_address_form']) !!}
         <div class="element form-group">
            <label>Search Address</label>
            <div class="form-section">
                <?php $search_address_text = '';
                    if($address_street != '') {
                        $search_address_text .= $address_street;
                    }
                    if($address_city != '') {
                        $search_address_text .= ', '.$address_city;
                    }
                    if($address_state != '') {
                        $search_address_text .= ', '.$address_state;
                    }
                    /*
                    if($token_details->billing_county != '') {
                        $search_address_text .= ', '.$token_details->billing_county;
                    }
                     */
                    $search_address_text .= ', USA';
                ?>
                @if($token_status >= '4')
                    {!! Form::text('autocomplete_search', $value = $search_address_text, $attributes = array('class'=>"form-control",'placeholder'=>'Search Address', 'autocomplete' => 'off', 'disabled'=>'disabled')); !!}
                @else
                    {!! Form::text('autocomplete_search', $value = $search_address_text, $attributes = array('class'=>"form-control",'placeholder'=>'Search Address', 'id'=>'autocomplete_search', 'autocomplete' => 'off')); !!}
                @endif
            </div>
        </div>
        <div class="col-xs-6 col-md-6 col-sm-6 col-lg-6">
        @include('errors.user_error')
        
        <div class="alert alert-danger errorAlertMsgMain text-left error_div_description" style="display: none">
            <strong></strong> There were some problems with your input.
	</div>
        
        <div class="alert alert-danger errorAlertMsgMain text-left invalid_address_description" style="display: none"></div>
        
       
        <div class="form-group has-feedback element autocompletesearch">
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
            @if($token_status >= '4')
                {!! Form::text('home_street', $value = $address_street, $attributes = array('class'=>"form-control",'placeholder'=>'Street', 'maxlength' => '100', 'id'=>'home_street', 'autocomplete' => 'off', 'disabled'=>'disabled')); !!}
            @else
                {!! Form::text('home_street', $value = $address_street, $attributes = array('class'=>"form-control",'placeholder'=>'Street', 'maxlength' => '100', 'id'=>'home_street', 'autocomplete' => 'off')); !!}
                <span class="icon fa fa-info error error_icon"><span class="tooltiptext"></span></span>
            @endif
        </div>
        </div>
        <div class="form-group has-feedback element">
            <label>City<span class="required">*</span></label>
            <div class="form-section">
            @if($token_status >= '4')
                {!! Form::text('home_city', $value = $address_city, $attributes = array('class'=>"form-control",'placeholder'=>'City', 'maxlength' => '100', 'id'=>'home_city', 'autocomplete' => 'off', 'disabled'=>'disabled')); !!}
            @else
                {!! Form::text('home_city', $value = $address_city, $attributes = array('class'=>"form-control",'placeholder'=>'City', 'maxlength' => '100', 'id'=>'home_city', 'autocomplete' => 'off')); !!}
                <span class="icon fa fa-info error error_icon"><span class="tooltiptext"></span></span>
            @endif
        </div>
        </div>
        <div class="form-group has-feedback element">
            <label>State<span class="required">*</span></label>
            <div class="form-section">
            @if($token_status >= '4')
                <?php //{!! Form::text('home_state', $value = $address_state, $attributes = array('class'=>"form-control",'placeholder'=>'State', 'maxlength' => '100', 'id'=>'home_state', 'autocomplete' => 'off', 'disabled'=>'disabled')); !!} ?>
                {!! Form::select('home_state',array_replace(['' => 'Select State '],$states), $address_state ,['id' => 'home_state','class' => 'form-control', 'autocomplete' => 'off', 'disabled'=>'disabled']); !!} 
            @else
                <?php //{!! Form::text('home_state', $value = $address_state, $attributes = array('class'=>"form-control",'placeholder'=>'State', 'maxlength' => '100', 'id'=>'home_state', 'autocomplete' => 'off')); !!} ?>
                {!! Form::select('home_state',array_replace(['' => 'Select State '],$states), $address_state ,['id' => 'home_state','class' => 'form-control', 'autocomplete' => 'off']) !!} 
                <span class="icon fa fa-info error error_icon"><span class="tooltiptext"></span></span>
            @endif
        </div>
        </div>
        <div class="form-group has-feedback element">
            <label>Zip code<span class="required">*</span></label>
            <div class="form-section">
            @if($token_status >= '4')
                {!! Form::text('home_zipcode', $value = $address_zipcode, $attributes = array('class'=>"form-control",'placeholder'=>'Zip code', 'maxlength' => '100', 'id'=>'home_zipcode', 'autocomplete' => 'off', 'disabled'=>'disabled')); !!}
            @else
                {!! Form::text('home_zipcode', $value = $address_zipcode, $attributes = array('class'=>"form-control",'placeholder'=>'Zip code', 'maxlength' => '100', 'id'=>'home_zipcode', 'autocomplete' => 'off')); !!}
                <span class="icon fa fa-info error error_icon"><span class="tooltiptext"></span></span>
            @endif
        </div>
        </div>
        <div class="form-group has-feedback element">
            <label>County<span class="required">*</span></label>
            <div class="form-section state_counties">
            @if($token_status >= '4')
                <?php //{!! Form::text('home_county', $value = $address_county, $attributes = array('class'=>"form-control",'placeholder'=>'County', 'maxlength' => '100', 'id'=>'home_county', 'autocomplete' => 'off', 'disabled'=>'disabled')); !!} ?>
                {!! Form::select('home_county',array_replace(['' => 'Select County '],$counties), $address_county, ['id' => 'home_county','class' => 'form-control', 'autocomplete' => 'off', 'disabled'=>'disabled']); !!} 
            @else
                <?php //{!! Form::text('home_county', $value = $address_county, $attributes = array('class'=>"form-control",'placeholder'=>'County', 'maxlength' => '100', 'id'=>'home_county', 'autocomplete' => 'off')); !!} ?>
                {!! Form::select('home_county',array_replace(['' => 'Select County '],$counties), $address_county, ['id' => 'home_county','class' => 'form-control    ', 'autocomplete' => 'off']) !!} 
                <span class="icon fa fa-info error error_icon"><span class="tooltiptext"></span></span>
            @endif
        </div>
        </div>
        
        {!! Form::hidden('token_status', $token_status, $attributes = ['id' => 'token_status']); !!}
        {!! Form::hidden('token', $customer_token, $attributes = ['id' => 'token']); !!}
        </div>
        <div class="col-xs-6 col-md-6 col-sm-6 col-lg-6"><div class="row"> <div id="map_canvas" style="width: 100%; height: 290px;"></div></div></div>
       
    </div><!-- tsg-latest -->
    <div class="tsg-btn-wrap add_user_button">  
        <?php $token_link = url('/'); ?>
        @if(Session::has('token'))
            <?php   $token = Session::get('token'); 
                    $token_link = url('/make-payment');
            ?>
        @endif
        <a href="{{ $token_link }}" class="btn btn-ctrl">Back</a>
        
        @if($token_status >= '4')
            <?php $continue_link = url('/verify-address'); ?>
            <a href="{{ $continue_link }}" class="btn btn-ctrl">Continue</a>
        @else
           <?php //{!! Form::submit('Continue',$attributes = array('class'=>"btn-ctrl")); !!} ?>
            {!! Form::button('Continue',$attributes = array('class'=>"btn btn-ctrl", 'id'=>'add_address_btn')); !!}
        @endif
    </div><!-- tsg-inner-wrapper --> 
       
</div><!-- tsg-inner-wrapper -->

 {!! Form::close() !!} 
@endsection
@section('js')

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
                                county = obj.properties.county;
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
        $('#autocomplete_search').val($(this).data('label'));
        $('#home_street').val($(this).data('street'));
        $('#home_city').val($(this).data('city'));
        $('#home_state').val($(this).data('state'));
        $('#home_zipcode').val($(this).data('zipcode'));
        $('#home_county').val($(this).data('county'));
        
        $('#autocomplete_search_result').html('');
        $('#autocomplete_search_result_div').slideUp('200');
    });
    
    
    var form_submit = 0;
    
    $("#add_address_btn").click(function()
    {	
        form_submit = 1;
        addLoader('#loader_body');
        
        var options = 
        { 
            success:function(data) 
            {
                if(data.success == false) {
                    if ('invalid_address' in data) {
                        console.log('invalid address');
                    }
                    $('.invalid_address_description').html(data.message);
                    $('.invalid_address_description').fadeIn(200);
                    
                    removeLoader('#loader_body');
                    $(".error").hide();
                    $('.error_div_description').hide();
                    $('.element').removeClass('has-error');
                }
                else {
                    //setTimeout("reload()", 3000);
                    window.location = data.redirect_url;
                }	
            },				
            error: function(data)
            {
                var errors = $.parseJSON(data.responseText);
                //console.log(errors);
                $(".error").hide();
                $('.invalid_address_description').hide();
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
		
        $("#add_address_form").ajaxForm(options);
        $("#add_address_form").submit();
			
    });
    
    
    $('#home_state').change(function()
    {
        addLoader('#loader_body');
        $('#home_county').attr('disabled', 'disabled');

        var formData = {
            state_id: $('#home_state').find(':selected').val(),
            input_name : 'home_county'
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
                    if(form_submit == 1) {
                        $('#add_address_btn').trigger('click');
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
    
});


</script>

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

@if($address_street != '' && $address_city != '' && $address_state != '' && $address_zipcode != '' && $address_county != '')
<script>
    
    $.ajax({
        type     : 'GET',
        //url      : "https://maps.googleapis.com/maps/api/geocode/json?address=<?php echo $address_city; ?>,+<?php echo $address_state; ?>,+<?php echo '$project->country->name'; ?>&key=AIzaSyD99kTAY6833t9YvRSQCfPmdeP9Hq67W5c",
        url      : "https://maps.googleapis.com/maps/api/geocode/json?address=<?php echo $address_street; ?>,+<?php echo $address_city; ?>,+<?php echo $state_name; ?>&key=AIzaSyD99kTAY6833t9YvRSQCfPmdeP9Hq67W5c",
        datatype : 'json',
        success  : function(data) {
                console.log(data);	
                //console.log(data.results[0]['geometry']['location']);
                if($.isEmptyObject(data.results)) {
                    //address_map("<?php echo $address_state; ?>,+<?php echo '$project->country->name'; ?>");
                    address_map("<?php echo $state_name; ?>");
                }
                else {
                    var lat = data.results[0]['geometry']['location']['lat'];
                    var lng = data.results[0]['geometry']['location']['lng'];

                    var myLatLng = {lat: lat, lng: lng};
                    map.setCenter(myLatLng);
                    map.setZoom(3);
                    var marker = new google.maps.Marker({
                        map: map,
                        position: myLatLng,
                        title: data.results[0]['formatted_address']
                    });

                    infowindow = new google.maps.InfoWindow({
                        content: data.results[0].formatted_address
                    });

                    google.maps.event.addListener(marker, 'click', function() {
                        infowindow.open(map,marker);
                    });
                }
        },
        error: function(data) {
            // Error...
            //var errors = $.parseJSON(data.responseText);
            console.log(data);
            
            //address_map("<?php echo $state_name; ?>,+<?php echo '$project->country->name'; ?>");
            address_map("<?php echo $address_street; ?>,+<?php echo $state_name; ?>");
        }
    })
    
    
    function address_map(address) 
    {
        $.ajax({
            type     : 'GET',
            url      : "https://maps.googleapis.com/maps/api/geocode/json?address="+address+"&key=AIzaSyD99kTAY6833t9YvRSQCfPmdeP9Hq67W5c",
            datatype : 'json',
            success  : function(data) {
                    //console.log(data);	
                    console.log(data.results[0]['geometry']['location']);
                    var lat = data.results[0]['geometry']['location']['lat'];
                    var lng = data.results[0]['geometry']['location']['lng'];

                    var myLatLng = {lat: lat, lng: lng};
                    map.setCenter(myLatLng);
                    map.setZoom(3);
                    var marker = new google.maps.Marker({
                        map: map,
                        position: myLatLng,
                        title: data.results[0]['formatted_address']
                    });

                    infowindow = new google.maps.InfoWindow({
                        content: data.results[0].formatted_address
                    });

                    google.maps.event.addListener(marker, 'click', function() {
                        infowindow.open(map,marker);
                    });

            },
            error: function(data) {
                // Error...
                //var errors = $.parseJSON(data.responseText);
                console.log(data);
            }
        })
    }
</script>
@endif

@endsection
