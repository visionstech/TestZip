
var state_name = '';
var county_name = '';
var same_state = '0';

$(document).ready(function() {

    var form_submit = 0;
    
    
    $('#administrative_area_level_1').change(function()
    {
                
        same_state = '0';
        if(state_name == $('#administrative_area_level_1').find(':selected').val()) {
            same_state = '1';
        }
        
        var formData = {
            state_id: $('#administrative_area_level_1').find(':selected').val(),
            input_name : 'administrative_area_level_2',  //'search_county',
            input_id : 'administrative_area_level_2'
        }
        
        formData._token = $('meta[name="csrf-token"]').attr('content');
        var county_name = document.getElementById("county_name").value;
        $.ajax({
            type     : 'POST',
            //url      : path+'state-counties',
            url      : path+'getProfileCounties',
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
                    $.each(data.counties, function(key, count_val){
                        if(county_name != "" && county_name == count_val){
                            html += '<option selected value="'+count_val+'">'+count_val+'</option>';
                        } else {
                            html += '<option value="'+count_val+'">'+count_val+'</option>';
                        }
                    });
                    $("#administrative_area_level_2").html(html);
                    
                

                    
                }
            },
            error: function(data) {
                // Error...
                var errors = data.responseJSON;
                
            }
        })


    });
    
    
    $(document.body).on("change", "#administrative_area_level_2", function() 
    {
        
        var formData = {
            state_id: $('#administrative_area_level_1').find(':selected').val(),
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
                    $('#county_link').html(data.county_link);
                    $('#county_link').attr('href', data.county_link);
                    
                    
                }
            },
            error: function(data) {
                // Error...
                var errors = data.responseJSON;
                console.log(errors);
                
            }
        })


    });
    
});


