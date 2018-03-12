@extends('layouts.dashboard')
@section('content')
    @include('include.sidebar')
   
        <div id="page-wrapper" >
            <div id="page-inner">
                <div class="row">
                    <div class="col-md-12">
                     <h2>Admin Dashboard</h2>   
                    </div>
                </div>              
                @if(Session::has('flash_message'))
						<div class="alert alert-success">
						{{ Session::get('flash_message') }}
						</div>
					@endif
                
             
                <div class="row" >
                    <div class="col-md-12 col-sm-12 col-xs-12">
               
                    <div class="panel panel-default">
                        <div class="panel-heading">
                           Admin Users
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
											<th>Email</th>
                                            <th>Created On</th>
											<th>Updated On</th>
											<th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
									
									@foreach($userList as $user)
                                        <tr>
                                            <td>{{ $user->id }} </td>
                                            <td>{{ $user->name }} </td>
                                            <td>{{ $user->email }} </td>
											<td>{{ date('m-d-Y', strtotime($user->created_at)) }} 
                                            <td>{{ date('m-d-Y', strtotime($user->updated_at)) }} </td>
                                            <td><a href="./user/{{ $user->id }}/edit" class="btn btn-info pull-left" style="margin-right: 3px;">Edit</a><a href="./user/{{ $user->id }}/delete" class="btn btn-danger" style="margin-right: 3px;">Delete</a>

                       
                        {{ Form::close() }}</td>
                                        </tr>
                                    @endforeach   


                                    </tbody>
                                </table>
								<a href="./user/add" class="btn btn-success">Add User</a>
								
                            </div>
                        </div>
                    </div>
                    
                    </div>
                </div>
                 <!-- /. ROW  -->
                         
    </div>
             <!-- /. PAGE INNER  -->
            </div>
         <!-- /. PAGE WRAPPER  -->
        </div>
    @stop