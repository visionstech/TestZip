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
					<hr>
                    {!! Form::model($item, ['action' => 'HomeController@updateItem']) !!}
					<div class="form-group">
						{!! Form::label('item_name', 'Name:', ['class' => 'control-label']) !!}
						{!! Form::text('item_name', $item->item_name, ['class' => 'form-control']) !!}
						{!! Form::hidden('item_id', $item->item_id) !!}
					</div>

					<div class="form-group">
						{!! Form::label('item_value', 'Value:', ['class' => 'control-label']) !!}
						{!! Form::text('item_value', $item->item_value, ['class' => 'form-control']) !!}
					</div>

					{!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}

					{!! Form::close() !!}
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
  