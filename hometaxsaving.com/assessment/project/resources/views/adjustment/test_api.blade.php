@extends('app')
@section('title')
    Test Api Comparables
@endsection
@section('css')

@endsection
@section('content')

<div class="tsg-inner-wrapper"> 
    {!! Form::open(['url' => 'adjustment-test-api', 'id' => 'test_api_form', 'novalidate' => 'novalidate']) !!}
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
                <div class="form-section">
                    {!! Form::text('state','MD', $attributes = ['id' => 'state','class' => 'form-control', 'placeholder'=>'State', 'autocomplete' => 'off']) !!}
                </div>
            </div>   
            <div class="form-group has-feedback element">
                <label>Postal Code<span class="required">*</span></label>
                <div class="form-section">
                    {!! Form::text('postal_code','20816', $attributes = ['id' => 'postal_code','class' => 'form-control', 'placeholder'=>'Postal Code', 'autocomplete' => 'off']) !!}
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

