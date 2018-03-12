@extends('layouts.app')
@section('pageTitle', 'Assessment Not Ready') 
@section('css')
<link href="{{ asset('/project/resources/assets/customer/css/Search.css') }}" rel="stylesheet">
@endsection
@section('content')
  <div class="outer-search-list">
    <div class="top-search-list">
          <a href="{{ url('/search-address') }}">START NEW SEARCH</a>
    </div>
    <div class="search-list">     
      <div class="inner-table">             
        <div class="as-a-member">
            <div class="inner-property-text">
            <p> We will be sending you automated updates when your new assessment is issued so we can review your taxes and determine if you will be able to save money on your taxes.
            </p>
          </div>
          <div class="inner-property-image">
             <img src="{{ asset('/project/resources/assets/customer/css/images/property.jpg') }}" alt="property-image">
          </div>
        </div>
      </div>
    </div>  
  </div>
 @endsection