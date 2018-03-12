@extends('layouts.dashboard')
@section('content')
           
        <div id="page-wrapper" >
            <div id="page-inner">
                <div class="row">
                    <div class="col-md-12">
                    <h3>Welcome, {{ Auth::user()->name }}</h3>   
					
					<p class="lead">Edit county</p>
					@if(Session::has('flash_message'))
						<div class="alert alert-success">
						{{ Session::get('flash_message') }}
						</div>
					@endif
					<hr>
                     {!! Form::model($county, ['action' => 'HomeController@updateCounty','class'=> 'form-horizontal']) !!}

					<div class="form-group">
						{!! Form::label('state_id', 'State:', ['class' => 'col-md-2 control-label']) !!}
						<div class="col-md-10">
							{!! Form::select('state_id', $stateList, $county->state_id, ['class' => 'form-control']) !!} 
						</div>
					</div>

					<div class="form-group">
						{!! Form::label('county_name', 'County:', ['class' => 'col-md-2 control-label']) !!}
						<div class="col-md-10">
							{!! Form::text('county_name', $county->county_name, ['class' => 'form-control']) !!}
							{!! Form::hidden('county_id', $county->county_id) !!}

						</div>
					</div>
					
					
					<div class="form-group">
						{!! Form::label('date_of_value', 'Date of value:', ['class' => 'col-md-2 control-label']) !!}
						<div class="col-md-10">
							{!! Form::date('date_of_value', $county->date_of_value, ['class' => 'form-control']) !!}
						</div>
					</div>
					<div class="form-group">
						{!! Form::label('notice_date', 'Notice Date:', ['class' => 'col-md-2 control-label']) !!}
						<div class="col-md-10">
							{!! Form::date('notice_date', $county->notice_date, ['class' => 'form-control']) !!}
						</div>
					</div>
					<div class="form-group">
						{!! Form::label('appeal_deadline_date', 'Appeal Deadline Date:', ['class' => 'col-md-2 control-label']) !!}
						<div class="col-md-10">
							{!! Form::date('appeal_deadline_date', $county->appeal_deadline_date, ['class' => 'form-control']) !!}
						</div>
					</div>
					
					
					<div class="form-group">
						{!! Form::label('county_link', 'County link:', ['class' => 'col-md-2 control-label']) !!}
						<div class="col-md-10">
							{!! Form::text('county_link', $county->county_link, ['class' => 'form-control']) !!}
						</div>
					</div>

<div class="text-right">
					{!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}

					{!! Form::close() !!}
</div>
                    </div>
                </div>
                 <!-- /. ROW  -->
                 <hr />
               
    </div>
             <!-- /. PAGE INNER  -->
            </div>
         <!-- /. PAGE WRAPPER  -->
        </div>
		    @stop
  