@extends('layouts.app')
@section('title')
    @section('pageTitle', 'Dashboard')
@endsection
@section('css')
<link href="{{ asset('/project/resources/assets/customer/css/datatables.css') }}" rel="stylesheet">
<link href="{{ asset('/project/resources/assets/customer/css/datatables.min.css') }}" rel="stylesheet">
<link href="{{ asset('/project/resources/assets/customer/css/dashboard.css') }}" rel="stylesheet">
@endsection
@section('content')            
	<div class="outer-search-list">
 		<div class="top-search-list">
			<ul>
				<li>
					 <a class="dashboard-search">Search List</a>
				</li>
				<li>
					<a href="{{ url('/search-address') }}">START NEW SEARCH</a>
				</li>
			</ul>
		</div>
			@include('errors.user_error')
	
		<div class="search-list">    	
			<div class="inner-table">
	           <table id="example" class="display" cellspacing="0" width="100%">
			        <thead>
			            <tr>
			                <th>Search Address</th>
			                <th>Assessment Value ($)</th>
			                <th>Appeal Amount ($)</th>
			                <th>Appeal Year</th>
			                <th>Date Created</th>
			                <th>Status</th>
			                <th>Action</th>
			            </tr>
		        	</thead>
	               	<tbody>
	               	<?php 
	               	//echo "<pre>e";print_r($searchHistory);exit;

	               	?> 
	               	@if(count($searchHistory)>0)
	               		@foreach($searchHistory as $key => $search)
				            <?php
								$status = \Helper::getStatusForMemberSearch($search);
								$appealAmount = "-";
								if(isset($search->no_appeal_recommendation) && $search->no_appeal_recommendation == 1){
									$appealAmount = "-";
								} else {
									$appealAmount = ($search->appeal_amount != "" && $search->appeal_amount != null) ? money_format('%.0n', $search->appeal_amount) : "-";
								}
							?>
	                        <tr>
				                <td>{{ $search->address_line_1." ".$search->address_line_2.", ".$search->city.", ".App\State::getStateName($search->state).", ".$search->postal_code }}</td>
				                <td>{{ ($search->total_assessment_value != "" && $search->total_assessment_value != null && $search->total_assessment_value != '0.00') ? money_format('%.0n', $search->total_assessment_value) : "-"}}</td>
				                <td>{{ $appealAmount }}</td>
				                <td>{{ ($search->appeal_year != "" && $search->appeal_year != null) ? $search->appeal_year : "-"}}</td>
				                <td>{{ date('m/d/Y', strtotime($search->created_at)) }}</td>
				                <td>{{ $status['status'] }}</td>
				                <td class="view-button">
				                	<a href="{{ $status['link'] }}">View</a>
						        </td>
				            </tr>
	                    @endforeach
                	@else
			            <tr class="odd">
			                <td valign="top" colspan="6" class="dataTables_empty">No record found</td>
			            </tr>
                    @endif
			        </tbody>
			    </table>
				<div class="as-a-member">
					<h4>As a Member you will enjoy:</h4>
					<ul style="margin-bottom: 0;">
						<li>
							Being kept up to date on national and local assessment issues
						</li>
						<li>
							Knowing when your new assessments have been issued and appeal due dates
						</li>
						<li>
							Being prepared to analyze the reasonableness of your new home tax assessment in two steps:
						</li>
						<ul class="inner-list">
							<li>
								Step 1-will show your new potential assessed value and potential tax savings estimate; fee +$9.95
							</li>
							<li>
								Step 2-prepares assessment analysis and local documentation for submission to local tax authority; report fee +$49.95
							</li>
						</ul>
					</ul>
					<ul style="margin-top: 0;">
						<li>
							Prepare a tax appeal to save $$$
						</li>
						<ul class="second-inner-list">
							<li>
								Meet appeal timeframe for filing, usually less than 60 days from tax notice
							</li>
							<li>
								Search local market for comparable transactions
							</li>
							<li>
								Analyze actual sale prices in order to development a comparable assessed value
							</li>
							<li>
								Prepare documentation suitable for filing with your local assessment office
							</li>
						</ul>
					</ul>				
				</div>
			</div>
		</div>	
	</div>		

@endsection
@section('js')

	<script type="text/javascript">
	    jQuery(document).ready(function() {
	    jQuery('#example').DataTable({
	        "scrollX": true,
	        "aaSorting": [],
	    });
	} );
	</script>

	<script type="text/javascript" src="{{ asset('/project/resources/assets/customer/js/datatables.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/project/resources/assets/customer/js/datatables.min.js') }}"></script>
@endsection
