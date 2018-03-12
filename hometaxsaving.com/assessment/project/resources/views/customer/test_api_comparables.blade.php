@extends('app')
@section('title')
    Test Api Comparables
@endsection
@section('css')

@endsection
@section('content')

<div class="tsg-inner-wrapper"> 
    {!! Form::open(['url' => 'test-api-comparables', 'id' => 'test_api_comparables_form', 'novalidate' => 'novalidate']) !!}
    <div class="tsg-latest tsg-common-details add-user-payment-page">
        <div class="col-xs-12 col-md-12 col-sm-12 col-lg-12">
            <h2>Search:</h2>
        </div>
            
        <div class="col-xs-12 col-md-7 col-sm-7 col-lg-7">    

            <div class="contactdetailform">
            <div class="form-group has-feedback element">
                <label>Street Address<span class="required">*</span></label>
                <div class="form-section">
                    {!! Form::text('street','5300 Albemarle St', $attributes = ['id' => 'street','class' => 'form-control', 'placeholder'=>'Street Address', 'autocomplete' => 'off']) !!}
                </div>
            </div>
            <div class="form-group has-feedback element">
                <label>City<span class="required">*</span></label>
                <div class="form-section">
                    {!! Form::text('city','Bethesda', $attributes = ['id' => 'city','class' => 'form-control', 'placeholder'=>'City', 'autocomplete' => 'off']) !!}
                </div>
            </div>   
            <div class="form-group has-feedback element">
                <label>State<span class="required">*</span></label>
                <!-- <div class="form-section">
                    {!! Form::text('state','MD', $attributes = ['id' => 'state','class' => 'form-control', 'placeholder'=>'State', 'autocomplete' => 'off']) !!}
                </div> -->
                <div class="form-section">
                    {!! Form::select('state',array_replace(['' => 'Select State '],$states), null, ['id' => 'state','class' => 'form-control', 'autocomplete' => 'off']) !!} 
                    <span class="icon fa fa-info error error_icon"><span class="tooltiptext"></span></span>
                </div>
            </div>   
            <div class="form-group has-feedback element">
                <label>Postal Code<span class="required">*</span></label>
                <div class="form-section">
                    {!! Form::text('postal_code','20816', $attributes = ['id' => 'postal_code','class' => 'form-control', 'placeholder'=>'Postal Code', 'autocomplete' => 'off']) !!}
                </div>
            </div>

            <div class="form-group has-feedback element">
                <label>County<span class="required">*</span></label>
                <div class="form-section state_counties">
                    {!! Form::select('county_id',array_replace(['' => 'Select County '],$counties), null, ['id' => 'county_id','class' => 'form-control', 'autocomplete' => 'off']) !!} 
                    <span class="icon fa fa-info error error_icon"><span class="tooltiptext"></span></span>
                </div>
            </div>

            <div class="form-group has-feedback element">
                <label>Total Assessment Value<span class="required">*</span></label>
                <div class="form-section">
                    {!! Form::text('total_assessment_value', '1130300', $attributes = ['id' => 'total_assessment_value','class' => 'form-control', 'placeholder'=>'Total Assessment Value', 'autocomplete' => 'off']) !!}
                </div>
            </div>
        </div>
        </div>
     
    </div><!-- tsg-latest -->
    
    <div class="tsg-btn-wrap add_user_button">  
        {!! Form::submit('Continue',array('class'=>"btn btn-ctrl")); !!} 
    </div>


    {!! Form::close() !!} 
    
</div><!-- tsg-inner-wrapper -->

@endsection
@section('js')
<script type="text/javascript">
    $(document).ready(function() {
        $("#state").change(function(e){
            if($("#state").val() !=""){
                
                var formData = {
                    state_abbr: $("#state").val(),
                }      

                formData._token = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    type     : 'POST',
                    url      : path+'/get-state-counties',
                    data     : formData,
                    datatype : 'html',
                    success  : function(data) {
                        if(data.success == false){
                            removeLoader('#loader_body');
                        }else{
                            console.log(data.counties);
                            $("#county_id").html("");
                           var html = '';
                            html += '<option value="" selected="selected">Select County </option>';
                           $.each(data.counties, function(key, count_val){
                            html += '<option value="'+key+'">'+count_val+'</option>';
                           });
                           $("#county_id").html(html);
                        }
                    },
                    error: function(data) {
                        // Error...
                        var errors = data.responseJSON;
                        console.log(errors);
                        removeLoader('#loader_body');
                    }
                });
            }
        });
    });
</script>
@endsection
