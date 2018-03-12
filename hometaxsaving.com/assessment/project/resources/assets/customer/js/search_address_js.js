
var state_name = '';
var county_name = '';
var same_state = '0';

$(document).ready(function() {

    var form_submit = 0;
    /* 8 Feb 2018 For In-Out Cycle */
    var State_abbrivation =$('#administrative_area_level_1').val();
    if(State_abbrivation=='MD'){
        $('.assesment_year_div').show();
        $('.conf_assesment_year_div').show();
        $('#in_out_case').val(1);
    }else{
        $('.assesment_year_div').hide();
        $('.conf_assesment_year_div').hide();
        $('#in_out_case').val(0);
    }                    
    /* End 8 Feb 2018 */
    
    /*$("#search_address_btn").click(function()
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
                        $('#invalid_address_msg').text('');
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
            $("#search_address_form").ajaxForm(options);
            $("#search_address_form").submit();
        	
    });*/
    
    
    //$('#search_state').change(function()
    $('#administrative_area_level_1').change(function()
    {
        addLoader('#loader_body');
        //$('#search_county').attr('disabled', 'disabled');
        same_state = '0';
        if(state_name == $('#administrative_area_level_1').find(':selected').val()) {
            same_state = '1';
        }
        
        var formData = {
            state_abbr: $('#administrative_area_level_1').find(':selected').val(),
            input_name : 'administrative_area_level_2',  //'search_county',
            input_id : 'administrative_area_level_2'
        }
        
        formData._token = $('meta[name="csrf-token"]').attr('content');
        var county_name = document.getElementById("county_name").value;
        $.ajax({
            type     : 'POST',
            //url      : path+'state-counties',
            url      : path+'get-state-counties',
            data     : formData,
            datatype : 'html',
            success  : function(data) {
                console.log("data");
                console.log(data);
                if(data.success == false){
                    removeLoader('#loader_body');
                } else{
                    $("#administrative_area_level_2").html("");
                    var html = '';
                    html += '<option value="" selected="selected">Select County </option>';
                    console.log("county_name");
                    console.log(county_name);
                    $.each(data.counties, function(key, count_val){
                        if(county_name != "" && county_name == count_val){
                            html += '<option selected value="'+count_val+'">'+count_val+'</option>';
                        } else {
                            html += '<option value="'+count_val+'">'+count_val+'</option>';
                        }
                    });
                    
                    $("#administrative_area_level_2").html(html);
                    
                    /* 8 Feb 2018 For In-Out Cycle */
                    var State_abbrivation =$('#administrative_area_level_1').val();
                    if(State_abbrivation=='MD'){                       

                        $('.assesment_year_div').show();
                        $('.conf_assesment_year_div').show();
                        $('#in_out_case').val(1);
                    }else{                        
                        $('#administrative_area_level_2').trigger('change');
                        $('.assesment_year_div').hide();
                        $('.conf_assesment_year_div').hide();
                        $('#in_out_case').val(0);
                    }
                    
                    /* End 8 Feb 2018 */
                    //$('#registration_form').valid();
                    removeLoader('#loader_body');

                    /*removeLoader('#loader_body');
                    $('.state_counties').html(data);
                    if(county_name != '') {
                        if(same_state == '1') {
                            $('#administrative_area_level_2').val(county_name);
                        } else {
                            $('#administrative_area_level_2').val('');
                        }
                        $('#administrative_area_level_2').trigger('change');
                    }
                    
                    if(form_submit == 1) {
                        $('#search_address_btn').trigger('click');
                    } else {
                       // removeLoader('#loader_body');
                    }*/
                    
                }
            },
            error: function(data) {
                // Error...
                var errors = data.responseJSON;
                removeLoader('#loader_body');
            }
        })


    });
    
    
    $(document.body).on("change", "#administrative_area_level_2", function() 
    {
        /*addLoader('#loader_body');
        
        var formData = {
            state_abbr: $('#administrative_area_level_1').find(':selected').val(),
        }
        
        if(county_name != '') {
            formData.county_name = county_name;
        }
        else {
            formData.county_name = $('#administrative_area_level_2').find(':selected').val();
        }

        formData._token = $('meta[name="csrf-token"]').attr('content');
        $.ajax({
            type     : 'POST',
            url      : path+'county-link',
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
                    console.log('sssssssssssssss');
                    var str = window.location.href;
                    var currentOpenurl = str.includes("/register");
                     //var currentOpenurl = window.location.href;
                     console.log("currentOpenurl");
                    if(currentOpenurl==false){
                        //$('#county_link').html(data.county_link);
                        $('#county_link').html("click here to go to your local jurisdiction.");
                        $('#county_link').attr('href', data.county_link);   
                    }else{
                        $('#county_link').html("");
                    } 
                    console.log(currentOpenurl);
                    
                    
                    var State_abbrivation =$('#administrative_area_level_1').val();
                    if(State_abbrivation=='MD'){
                        $('.assesment_year_div').show();
                        $('.conf_assesment_year_div').show();
                        $('#in_out_case').val(1);
                    }else{
                        $('.assesment_year_div').hide();
                        $('.conf_assesment_year_div').hide();
                        $('#in_out_case').val(0);
                    }
                    
                    removeLoader('#loader_body');
                }
            },
            error: function(data) {
                
                var errors = data.responseJSON;
                console.log(errors);
                removeLoader('#loader_body');
            }
        })*/


    });
    
});

/*
function validateAddressSearch(){
    $("#myform").validate({
      submitHandler: function(form) {
        $(form).submit();
      }
     });
return false;
}*/

jQuery(document).on('blur','#confirm_assessment_year',function(){
    // alert('sdfsdfsdf');
//console.log(path + 'md-county-link');return false;
        var formData = {
            assesment_year: jQuery('#confirm_assessment_year').val(),
            county_id: jQuery('#administrative_area_level_2').val()
        }
        formData._token = $('meta[name="csrf-token"]').attr('content');
        $.ajax({
            type     : 'POST',
            url      : path + 'county-link-md',
            data     : formData,
            datatype : 'html',
            success  : function(data) {
                if(data.success == true)
                {
                    $('#county_link').html(data.county_link);
                    $('#county_link').attr('href', data.county_link);
                }
            }
        }); 

});


jQuery(document).ready(function($)
{
    $h_1 = $(".search-map-height-left").outerHeight();
$('.search-map-section #map_canvas').css('height', $h_1 - 106);

$(window).resize(function() {
       $h_11 = $(".search-map-height-left").outerHeight();
$('.search-map-section #map_canvas').css('height', $h_11 - 106);
 });
/*var str = window.location.href;
 var currentOpenurl = str.includes("/register");

console.log("currentOpenurl");
if(currentOpenurl==false){
$('#county_link').html(data.county_link);
$('#county_link').attr('href', data.county_link);   
}else{
$('#county_link').html("");
} */
});
