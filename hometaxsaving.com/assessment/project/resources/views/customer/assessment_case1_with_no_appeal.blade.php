@extends('layouts.app')
@section('pageTitle', 'ASSESSMENT REVIEW') 
@section('css')
<link href="{{ asset('/project/resources/assets/customer/css/assessment.css') }}" rel="stylesheet">
<link href="{{ asset('/project/resources/assets/customer/css/custom_common.css') }}" rel="stylesheet">
@endsection
@section('content')
  <div class="outer-search-list">
     <div class="top-search-list">
        <ul>
           <li>
              <a href="{{ url('/search-address') }}">START NEW SEARCH</a>
           </li>
        </ul>
     </div>
     <div class="inner-top-search-list">
        <ul>
           <li class="active">
              <a href="javascript:;">01</a>
           </li>
           <li class="active">
              <a href="javascript:;">02</a>
           </li>
           <li class="active">
              <a href="javascript:;">03</a>
           </li>
           <li class="active">
              <a href="javascript:;">04</a>
           </li>
        </ul>
        <div class="inner-wrapper-top-search-list">
           <ul>
              <li>
                 Search Address
              </li>
              <li>
                 Make a Payment
              </li>
              <li>
                 Verify Search
              </li>
              <li>
                 Assessment Review
              </li>
           </ul>
        </div>
     </div>
     <div class="search-list">
       <div class="search-heading">
           <h4>
              Valuation Summary
           </h4>
           <p>Your current tax year {{ ($data['search_details']->appeal_year != "")?$data['search_details']->appeal_year: date('Y') }} assessment is ${{ number_format($data['search_details']->total_assessment_value) }}.  Your house recently sold on {{ ($data['search_details']->sale_date != "")?date('m/d/Y', strtotime($data['search_details']->sale_date)): date('m/d/Y') }} for ${{ number_format($data['search_details']->sale_price) }} and therefore an appeal is not recommended.  
          <br></p>
           <h5>Recommendation: Do not appeal your assessment.</h5>
          
           <p>
              Assessments should be reviewed annually for accuracy.  As a member of HomeTaxSavings you will receive a reminder email and/or text when your assessment is ready to be reviewed.
           </p>
        </div>
        <div class="assessment-image">
           <img src="{{ asset('/project/resources/assets/customer/css/images/assessment.jpg') }}" alt="assessment">
        </div>
     </div>
  </div>
@endsection

