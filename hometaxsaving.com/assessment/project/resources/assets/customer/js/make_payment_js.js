
var state_name = '';
var county_name = '';
var same_state = '0';

$(document).ready(function() {

    var form_submit = 0;
    
   /* $('#make_payment_form').submit(function(e) {
  return false;
        var ex_month = $('#ex_month').val();
        var ex_year = $('#ex_year').val();
        $('#ex_date').val(ex_month+'-'+ex_year);
    });
    */


    /*$("#make_payment_btn").click(function()
    {	
        form_submit = 1;
        var ex_month = $('#ex_month').val();
        var ex_year = $('#ex_year').val();
        $('#ex_date').val(ex_month+'-'+ex_year);
        $(".loader-overlay").show();
        $("#loaderText").text(PAYMENT_LOADING_MSG);
        
        var options = 
        { 
            success:function(data) 
            {
                if(data.success == false) {                 
                    $(".loader-overlay").hide();
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
                    $('#invalid_address_msg').text('');
                    window.location = data.redirect_url;
                }	
            },				
            error: function(data)
            {
                var errors = $.parseJSON(data.responseText);
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
                $(".loader-overlay").hide();
            }
        };
		
        $("#make_payment_form").ajaxForm(options);
        $("#make_payment_form").submit();
			
    });*/
    

    $('#administrative_area_level_1').change(function()
    {
        addLoader('#loader_body');
        //$('#administrative_area_level_2').attr('disabled', 'disabled');
        
        same_state = '0';
        if(state_name == $('#administrative_area_level_1').find(':selected').val()) {
            same_state = '1';
        }

        var formData = {
            state_abbr: $('#administrative_area_level_1').find(':selected').val(),
            input_name : 'administrative_area_level_2',  //'billing_county',
            input_id : 'administrative_area_level_2'
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
                            $('#administrative_area_level_2').val(county_name);
                        }
                        else {
                            $('#administrative_area_level_2').val('');
                        }
                        
                        $('#administrative_area_level_2').trigger('change');
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
    
    
    $(document.body).on("change", "#administrative_area_level_2", function() 
    {
        addLoader('#loader_body');
        
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
