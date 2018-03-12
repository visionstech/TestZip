@extends('layouts.dashboard')
@section('css')
<link href="{{ asset('/project/resources/assets/customer/css/datatables.css') }}" rel="stylesheet">
<link href="{{ asset('/project/resources/assets/customer/css/datatables.min.css') }}" rel="stylesheet">
@stop
@section('content')
<?php $publicPath = url('/'); ?>
        <div id="page-wrapper" >
            <div id="page-inner">
                <!-- <div class="row">
                    <div class="col-md-12">
                       <h2></h2>   
                   </div>
               </div> -->

               <!-- <div class="outer-search-list">
                <div class="top-search-list">
                    <ul>
                        <li>
                           <a class="dashboard-search" href="#">Search List</a>
                       </li>
                       
                   </ul>
               </div>  
           </div>  -->                     
           @if(Session::has('flash_message'))
           <div class="alert alert-success">
            {{ Session::get('flash_message') }}
        </div>
        @endif
        
        
        <div class="row" >
            <div class="col-md-12 col-sm-12 col-xs-12">
             
                <div class="panel panel-default">
                    <div class="panel-heading">
                     Home Assessment Members
                 </div>
                 <div class="panel-body">
                    <div class="table-responsive">
                        <table id="example" class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    
                                    <th>Name</th>
                                    <th>Email</th>
                  <th>Phone</th>
                                    <th>Registered On</th>
                                </tr>
                            </thead>
                            <tbody>
                                
                                @foreach($memberList as $member)
                                <tr>
                                    <td><a href="./member/{{ $member->id }}"> 
                                        {{ $member->first_name." ".$member->last_name }} </a></td>
                                        <td>{{ $member->email }} </td>
                    <td>{{ $member->mobile_number }} </td>
                                        <td>{{ date('m/d/Y', strtotime($member->created_at)) }} 
                                        </td>
                                        
                                    </tr>
                                    @endforeach   


                                </tbody>
                            </table>
                            <div class="text-center">
                                {{ $memberList->links() }}
                            </div>
                            
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
        <!-- /. ROW  -->
        
    </div>
    
</div>

<!-- /. PAGE WRAPPER  -->
</div>
@stop
@section('js')
<script type="text/javascript" src="{{ asset('/project/resources/assets/customer/js/datatables.js') }}"></script>
<script type="text/javascript" src="{{ asset('/project/resources/assets/customer/js/datatables.min.js') }}"></script>
<script type="text/javascript">
   jQuery(document).ready(function() {
       jQuery('#example').dataTable({
          //  "scrollX": true,
           "autoWidth": false
      });
   } );
</script>
@stop

