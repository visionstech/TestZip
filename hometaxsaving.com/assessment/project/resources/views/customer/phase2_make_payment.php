@extends('app')
@section('title')
    Make Phase2 payment
@endsection
@section('content')

<div class="tsg-inner-wrapper"> 
    <div class="tsg-latest tsg-common-details add-user-payment-page">

       
Hello
 
</div>
</div>
 
@endsection
@section('js')
<script src="{{ asset('/project/resources/assets/customer/js/phase2_make_payment_js.js') }}"></script> 
<script src="{{ asset('/project/resources/assets/customer/js/autocomplete.js') }}"></script> 

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD99kTAY6833t9YvRSQCfPmdeP9Hq67W5c&libraries=places&callback=initAutocomplete" async defer"></script>

@endsection
