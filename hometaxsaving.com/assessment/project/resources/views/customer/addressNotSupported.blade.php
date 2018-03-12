@extends('layouts.app')
@section('pageTitle', 'Property Address Not Supported') 
@section('css')
<link href="{{ asset('/project/resources/assets/customer/css/assessment_address.css') }}" rel="stylesheet">
@endsection
@section('content')
  <div class="inner-property">
         <div class="container">
            <div class="outer-property-text">
            <div class="inner-property-text">
               <h4>CONGRATULATIONS!</h4>
               <h6>You are now a member of a growing number of homeowners who want more control of their annual real estate taxes.
                </h6>
                <p>
                    Unfortunately, your home address is not located in our current coverage area.  We will notify you when this changes.
                </p>
            </div>
            <div class="inner-property-image">
               <img src="{{ asset('/project/resources/assets/customer/css/images/property.jpg') }}" alt="property-image">
            </div>
         </div>
      </div>
   </div>
@endsection