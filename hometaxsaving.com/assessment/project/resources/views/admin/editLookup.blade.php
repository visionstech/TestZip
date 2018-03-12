@extends('layouts.dashboard')
@section('content')
           
        <div id="page-wrapper" >
            <div id="page-inner">
                <div class="row">
                    <div class="col-md-12">
                    <h3>Welcome, {{ Auth::user()->name }}</h3>   
					
					<p class="lead">Edit item</p>
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
                 {!! Form::model($lookup, ['action' => 'HomeController@updateLookup','class'=> 'form-horizontal']) !!}
					
					<div class="form-group">
						{!! Form::label('lookup_type_id', 'Lookup Type:', ['class' => 'col-md-2 control-label']) !!}
						<div class="col-md-10">
							{!! Form::select('lookup_type_id', $lookupTypeList, null, ['class' => 'form-control','id' => 'editLookupTypeId']) !!} 
							{!! Form::hidden('lookup_id', $lookup->lookup_id) !!}

						</div>
					</div>
					<div class="form-group">
						{!! Form::label('lookup_name', 'Name:', ['class' => 'col-md-2 control-label']) !!}
						<div class="col-md-10">
							{!! Form::text('lookup_name', $lookup->name, ['class' => 'form-control']) !!}
						</div>

					</div>
					
					<div class="form-group">
						{!! Form::label('lookup_desc', 'Description:', ['class' => 'col-md-2 control-label']) !!}
						<div class="col-md-10">
							{!! Form::text('lookup_desc', $lookup->description, ['class' => 'form-control']) !!}
						</div>

					</div>

					<div class="form-group">
						{!! Form::label('lookup_value', 'Value 1:', ['class' => 'col-md-2 control-label']) !!}
						<div class="col-md-10">
							{!! Form::text('lookup_value', $lookup->value, ['class' => 'form-control']) !!}
						</div>
					</div>
					
					<div class="form-group">
						{!! Form::label('lookup_value1', 'Value 2:', ['class' => 'col-md-2 control-label']) !!}
						<div class="col-md-10">
							{!! Form::text('lookup_value1', $lookup->value1, ['class' => 'form-control']) !!}
						</div>
					</div>
					
					<div class="form-group">
						{!! Form::label('lookup_value2', 'Value 3:', ['class' => 'col-md-2 control-label']) !!}
						<div class="col-md-10">
							{!! Form::text('lookup_value2', $lookup->value2, ['class' => 'form-control']) !!}
						</div>
					</div>
					
					<div class="form-group">
						{!! Form::label('parent_lookup_id', 'Parent Lookup:', ['class' => 'col-md-2 control-label']) !!}
						<div class="col-md-10">
							{!! Form::select('parent_lookup_id', $lookupList, null, ['placeholder' => 'Please select...','class' => 'form-control','id' => 'lookupId']) !!} 
						</div>
					</div>
					
					<div class="form-group">
						{!! Form::label('display_order', 'Display order:', ['class' => 'col-md-2 control-label']) !!}
						<div class="col-md-10">
							{!! Form::text('display_order', $lookup->display_order, ['class' => 'form-control']) !!}
						</div>
					</div>
<div class="text-right">
					{!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}

					{!! Form::close() !!} </div>
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
  