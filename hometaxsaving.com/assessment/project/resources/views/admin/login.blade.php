	@extends('layout.dashboard')
	@section('content')
    <div class="container">
        <div class="row text-center">
            <div class="col-md-12">
                <br /><br />
                <h2> Home Assessment Admin : Login 8888</h2>
               
                
                 <br />
            </div>
        </div>
         <div class="row ">
               
                  <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3 col-xs-10 col-xs-offset-1">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                        <strong>   Enter Details To Login </strong>  
                            </div>
							<?php
								print_r($errors);
							?>
							@if (count($errors) > 0)
								<div class="alert alert-danger">
									<ul>
										@foreach ($errors->all() as $error)
											<li>{{ $error }}</li>
										@endforeach
									</ul>
								</div>
							@endif
                            <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="postLogin">
								{{ csrf_field() }}

                                       <br />
                                     <div class="form-group input-group">
                                            <span class="input-group-addon"><i class="fa fa-tag"  ></i></span>
                                            <input type="text" name="UserName" class="form-control" placeholder="Your Username " />
                                        </div>
                                                                              <div class="form-group input-group">
                                            <span class="input-group-addon"><i class="fa fa-lock"  ></i></span>
                                            <input type="password" name="password" class="form-control"  placeholder="Your Password" />
                                        </div>
                                    <div class="form-group">
                                            <label class="checkbox-inline">
                                                <input type="checkbox" /> Remember me
                                            </label>
                                            <span class="pull-right">
                                                   <a href="#" >Forgot password ? </a> 
                                            </span>
                                        </div>
                                     
                                     <input type="submit" value="Login" class="btn btn-primary "/>
                                    
                                    </form>
                            </div>
                           
                        </div>
                    </div>
                
                
        </div>
    </div>
	@stop
