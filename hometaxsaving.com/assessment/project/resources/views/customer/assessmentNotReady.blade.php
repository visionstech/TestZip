@extends('layouts.app')
@section('pageTitle', 'Assessment Not Ready') 
@section('css')
<link href="{{ asset('/project/resources/assets/customer/css/assessment_address.css') }}" rel="stylesheet">
@endsection
@section('content')
 <div class="inner-property">
  <div class="container">
    <div class="outer-property-text">
    <!--  @include('errors.user_error')-->
      <div class="inner-property-text">
          <h4>CONGRATULATIONS!</h4>
          <h6>You are now a member of a growing number of homeowners who want more control of their annual real estate taxes.
            </h6>
            <p>
              Unfortunately, at this time it appears that the newest tax assessments have not been issued in your jurisdiction.  Without the assessment, we are not able to review your taxes to determine if you are eligible for substantial tax savings.
            </p>      
            <p> We will be sending you automated updates when your new assessment is issued so we can review your taxes and determine if you will be able to save money on your taxes.
            </p>
          </div>
          <div class="inner-property-image">
             <img src="{{ asset('/project/resources/assets/customer/css/images/property.jpg') }}" alt="property-image">
          </div>
       </div>
    </div>
  </div>
 @endsection