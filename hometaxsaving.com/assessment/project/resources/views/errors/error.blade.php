@extends('layouts.app')
@section('pageTitle', 'Error') 
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
                <h4>Error!</h4>
               <h4><b><span style="color:red">ERROR:</span> {{ $exception_message }}</b></h4>
            </div>
            <div class="inner-property-image">
               <img src="{{ asset('/project/resources/assets/customer/css/images/property.jpg') }}" alt="property-image">
            </div>   
        </div>
      </div>
    </div>  
  </div>
@endsection