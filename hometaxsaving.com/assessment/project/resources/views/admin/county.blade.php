@extends('layouts.dashboard')
@section('content')   
        <div id="page-wrapper" >
            <div id="page-inner">
                <div class="row">
                    <div class="col-md-12">
                     <h2>Manage jurisdictions</h2>   
                        
                    </div>
                </div>              
                @if(Session::has('flash_message'))
						<div class="alert alert-success">
						{{ Session::get('flash_message') }}
						</div>
					@endif
					
					@if (count($errors) > 0)
					<div class="alert alert-danger">
						<ul>
							@foreach ($errors->all() as $error)
								<li>{{ $error }}</li>
							@endforeach
						</ul>
					</div>
				@endif
				<hr>
                
             {!! Form::open(['action' => 'HomeController@jurisdiction', 'method' => 'post']) !!}
                <div class="row" >
                    <div class="col-md-12 col-sm-12 col-xs-12">
               
                    <div class="panel panel-default">
                        <div class="panel-heading">
                           State and county list
                        </div>
                        <div class="panel-body">
						
						<div class="form-group">
							{!! Form::label('state_id', 'State:', ['class' => 'col-md-2 control-label']) !!}
							<div class="col-md-10">

							{!! Form::select('state_id', $stateList, $selectedStateId, ['class' => 'form-control','id' => 'stateId']) !!} 
							</div>
							
						</div>
														

							<hr>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
											<th>County</th>
                                            <th>Date of Value</th>
											<th>Notice Date</th>
											<th>Appeal Deadline</th>
											<th>County link</th>
											<th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
									
									@foreach($stateCountyList as $jurisdiction)
                                        <tr>
                                            <td>{{ $jurisdiction->countyName}} </td>
											
                                            <td>{{ !empty($jurisdiction->date_of_value)?date('m-d-Y',  strtotime($jurisdiction->date_of_value)):'' }} </td>
                                            <td>{{ !empty($jurisdiction->notice_date)?date('m-d-Y',  strtotime($jurisdiction->notice_date)):'' }} </td>
											<td>{{ !empty($jurisdiction->appeal_deadline_date)?date('m-d-Y',  strtotime($jurisdiction->appeal_deadline_date)):''  }} </td>
											<td>{{ $jurisdiction->county_link }} </td>
                                            <td><a href="./county/{{ $jurisdiction->county_id }}/edit" class="btn btn-info pull-left" style="margin-right: 3px;">Edit</a>
                     
											{!! Form::close() !!}</td>
                                        </tr>
                                    @endforeach   


                                    </tbody>
                                </table>
								<div class="text-center">
									{!! $stateCountyList->appends(['state_id' => $selectedStateId])->render() !!}
								</div>
                            </div>
                        </div>
                    </div>
                    
                    </div>
                </div>
                 <!-- /. ROW  -->
                         
    </div>
             <!-- /. PAGE INNER  -->
            </div>
        
	@stop
	
	
			