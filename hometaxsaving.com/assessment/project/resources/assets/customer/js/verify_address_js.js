
$(document).ready(function() {
    /*$("#verify_address_btn").click(function()
    {	
        //addLoader('#loader_body');
        $(".loader-overlay").show();
        $("#loaderText").text(FETCHING_LOADING_MSG);
        setTimeout(function(){
            $("#loaderText").text(PREPARING_MSG);
        }, 3000);
        
        var options = 
        { 
            success:function(data) 
            {
                if(data.success == false) {
                    //removeLoader('#loader_body');
                    $(".loader-overlay").hide();
                    $(".error").hide();
                    $('.error_div_description').hide();
                    $('.element').removeClass('has-error');
                }
                else {
                    //setTimeout("reload()", 3000);
                    window.location = data.redirect_url;
                    //console.log('done');
                }	
            },				
            error: function(data)
            {
                var errors = $.parseJSON(data.responseText);
                //console.log(errors);
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
                //removeLoader('#loader_body');	
                $(".loader-overlay").hide();
            }
        };
		
        $("#verify_address_form").ajaxForm(options);
        $("#verify_address_form").submit();
			
    });*/
    /*$('#verify_address_form').submit(function() {
        $(".loader-overlay").show();
          $("#loaderText").text(VERYFYING_ADD);
        setTimeout(function(){
            $("#loaderText").text(FETCHING_LOADING_MSG);
          }, 1500);  
          setTimeout(function(){
            $("#loaderText").text(PREPARING_MSG);
          }, 3000);
          // return false;
           $('#verify_address_form').submit();
    });*/
        
    $('.view_question_detail').click(function ()
    {
        addLoader('#loader_body');
        var question_id = $(this).data('question_id');
        var formData = {
            question_id: question_id,
        }

        formData._token = $('meta[name="csrf-token"]').attr('content');
        $.ajax({
            type: 'POST',
            url: path + '/question-description',
            data: formData,
            datatype: 'html',
            success: function (data) {
                //console.log(data);	
                if (data.success == false) {
                    removeLoader('#loader_body');
                }
                else {
                    $('#view_question_desc_div').html(data.description);
                    $('#view_question_desc_popup').modal('show');
                    removeLoader('#loader_body');
                }
            },
            error: function (data) {
                // Error...
                var errors = $.parseJSON(data.responseText);
                removeLoader('#loader_body');
            }
        })

    });

    
});

jQuery(document).ready(function($)
{
    $h_1 = $(".left-col-for-map").outerHeight();
$('.map-section #map_canvas').css('height', $h_1 - 90);

$(window).resize(function() {
       $h_11 = $(".left-col-for-map").outerHeight();
$('.map-section #map_canvas').css('height', $h_11 - 90);
 });

});
