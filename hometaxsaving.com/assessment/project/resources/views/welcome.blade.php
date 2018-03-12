@extends('layouts.dashboard')
@section('content')
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
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Lorem ipsum dolor
                        </div>
                        <div class="panel-body">
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum tincidunt est vitae ultrices accumsan. Aliquam ornare lacus adipiscing, posuere lectus et, fringilla augue.</p>
                             <div class="inline"><a  href="{{$publicPath.'/settings'}}"> Settings</a>   </div>
                             <div class="inline"> <a  href="http://survey.sismithllc.com/wp-admin/"> Survey</a>   </div>
                             <div class="inline"> <a  href="{{$publicPath.'/users'}}"> Users</a>   </div>
                        </div>
                        
                    </div>
                </div>
              
            </div>
                   <!-- /. ROW  -->
                    

    </div>
             <!-- /. PAGE INNER  -->
            </div>
         
</div>
          

		    @stop
  
