@extends('layouts.dashboard')
@section('css')
<link href="{{ asset('/project/resources/assets/customer/css/datatables.css') }}" rel="stylesheet">
<link href="{{ asset('/project/resources/assets/customer/css/datatables.min.css') }}" rel="stylesheet">
@stop
@section('content')
   <?php $publicPath = url('/'); ?>
        <!-- <div id="page-wrapper" > -->
            <div id="page-inner">		
                @if(Session::has('flash_message'))
						<div class="alert alert-success">
						{{ Session::get('flash_message') }}
						</div>
					@endif
            
             
                <div class="row" >
                    <div class="col-md-12 col-sm-12 col-xs-12">
               
                    <div class="panel panel-default">
					
					
					   <div class="panel-heading">
                           Member Search History for {{ $memberName }}
                        </div>
						
                     
                        <div class="panel-body">
						
						   
                            <div class="table-responsive">
                                <table id="example" class="display table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
											<th>Search Address</th>
											<th>Assessment Value ($)</th>
											<th>Appeal Amount ($)</th>
											<th>Date Created</th>
											<th>Status</th>
											
                                        </tr>
                                    </thead>
                                    <tbody>
									
									@if (count($memberHistory)>0)
										@foreach($memberHistory as $member)
											<?php
											$status = \Helper::getStatusForMemberSearch($member);
											?>
											<tr>
                                          
												<td>{{ $member->address_line_1." ".$member->address_line_2.", ".$member->city.", ".App\State::getStateName($member->state) }} </td>
													<td>{{ ($member->total_assessment_value != "" && $member->total_assessment_value != null && $member->total_assessment_value != 0) ? "$".number_format($member->total_assessment_value) : "-" }}</td>
													<td>{{ ($member->appeal_amount != "" && $member->appeal_amount != null && $member->appeal_amount != 0) ? "$".number_format($member->appeal_amount) : "-" }}</td>
													<td>{{ date('m/d/Y', strtotime($member->created_at)) }} 
													<td>{{ $status["status"] }}</td>
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
							
								
                            </div>
                        </div>
                    </div>
                    
                    </div>
                </div>
                 <!-- /. ROW  -->
                         
    </div>
             <!-- /. PAGE INNER  -->
            </div>
        
        <!-- </div> -->
		
	@endsection	
	@section('js')

<script type="text/javascript">
	    jQuery(document).ready(function() {
	    jQuery('#example').DataTable({
	        // "scrollX": true,
	        "autoWidth": false

	      

	    });
	} );
	</script>

	<script type="text/javascript" src="{{ asset('/project/resources/assets/customer/js/datatables.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/project/resources/assets/customer/js/datatables.min.js') }}"></script>
@stop

