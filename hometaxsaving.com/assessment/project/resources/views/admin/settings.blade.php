@extends('layouts.dashboard')
@section('content')
   
        <div id="page-wrapper" >
            <div id="page-inner">
                <div class="row">
                    <div class="col-md-12">
                     <h2>Settings</h2>   
                        
                    </div>
                </div>              
                @if(Session::has('flash_message'))
						<div class="alert alert-success">
						{{ Session::get('flash_message') }}
						</div>
				@endif
                <hr>
             
                <div class="row" >
                    <div class="col-md-12 col-sm-12 col-xs-12">
               
                    <div class="panel panel-default">
                        <div class="panel-heading">
                           Manage configurable items
                        </div>
                        <div class="panel-body">
                            <div class="inline">{{ Html::link('/lookups', 'Manage Lookups') }}</div>
						<div class="inline">{{ Html::link('/jurisdiction', 'Manage Jurisdictions') }}</div>
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