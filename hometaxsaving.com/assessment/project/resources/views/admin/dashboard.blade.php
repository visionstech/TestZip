@extends('layouts.dashboard')
@section('content')
@include('include.header')
<?php $publicPath = url('/'); ?>

          <div id="page-wrapper" >
            <div id="page-inner">
                <div class="row">
                    <div class="col-md-12">
                     <h2>Admin Dashboard</h2>   
                        
                       
                    </div>
                </div>
                 <!-- /. ROW  -->
                 <hr />
                                 
            <div class="row">
              
                <div class="col-md-12 col-sm-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                        </div>
                        <div class="panel-body">
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum tincidunt est vitae ultrices accumsan. Aliquam ornare lacus adipiscing, posuere lectus et, fringilla augue.</p>
							<p> <a  href="{{$publicPath.'/settings'}}"> Settings</a></p>
							<p> <a  href="{{$publicPath.'/survey'}}"> Survey</a></p>
							<p> <a  href="{{$publicPath.'/users'}}"> Users</a></p>
                        </div>
                        <div class="panel-footer">
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
  