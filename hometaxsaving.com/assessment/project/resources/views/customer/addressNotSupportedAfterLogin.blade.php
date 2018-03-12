@extends('layouts.app')
@section('pageTitle', 'Property Address Not Supported') 
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
  </div>
@endsection