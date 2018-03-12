@extends('layouts.dashboard')
@section('title')
Manage Settings
@endsection
@section('content')
        <div id="page-wrapper" >
            <div id="page-inner">
                <div class="row">
                    <div class="col-md-12">
                     <h2>Manage settings</h2>   
                        
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
                
             {!! Form::open(['action' => 'HomeController@lookups', 'method' => 'post']) !!}
                <div class="row" >
                    <div class="col-md-12 col-sm-12 col-xs-12">
               
                    <div class="panel panel-default">
                        <div class="panel-heading">
                           Lookup types and associated lookups
                        </div>
                        <div class="panel-body">
						
						<div class="form-group">
							{!! Form::label('lookup_type_id', 'Lookup type Id:', ['class' => 'col-md-2 control-label']) !!}
							<div class="col-md-10">

							{!! Form::select('lookup_type_id', $lookupTypeList, $lookupTypeId, ['class' => 'form-control','id' => 'lookupTypeId']) !!} 
							</div>
							
						</div>
														

							<hr>
							@if (count($lookupList) == 0)
								<div class="table-responsive">
								<div class="alert alert-success">
									No records found !!!
								</div>

							@else

                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
											
                                            <th>Description</th>
											<th>Value 1</th>
											<th>Value 2</th>
											<th>Value 3</th>
                                            <th>Updated on</th>
											<th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
									
									@foreach($lookupList as $lookup)
                                        <tr>
											
											<td>{{ $lookup->lookupDescription }} </td>
                                            
                                            <td>{{ $lookup->value }} </td>
											<td>{{ $lookup->value1 }} </td>
											<td>{{ $lookup->value2 }} </td>
                                            <td>{{ date('m-d-Y', strtotime($lookup->updated_at)) }} </td>
                                            <td><a href="./lookup/{{ $lookup->lookup_id }}/edit" class="btn btn-info pull-left" style="margin-right: 3px;">Edit</a>
                     
											{!! Form::close() !!}</td>
                                        </tr>
                                    @endforeach   


                                    </tbody>
                                </table>
							<div class="text-center">
							{!! $lookupList->appends(['lookup_type_id' => $lookupTypeId])->render() !!}
							</div>
							@endif
                                                        <div class="text-left">
							<a href="./lookup/add" class="btn btn-success">Add lookup</a>
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
	
	
			