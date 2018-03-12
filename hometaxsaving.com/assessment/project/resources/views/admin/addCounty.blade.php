@extends('layouts.dashboard')
@section('content')
           
        <div id="page-wrapper" >
            <div id="page-inner">
                <div class="row">
                    <div class="col-md-12">
                    
					
					<p class="lead">Add County</p>
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
					
                    {!! Form::model($county, ['action' => 'HomeController@saveCounty','class'=> 'form-horizontal']) !!}
					
					
					<div class="form-group">
						{!! Form::label('state_id', 'State:', ['class' => 'col-md-2 control-label']) !!}
						<div class="col-md-10">
							{!! Form::select('state_id', $stateList, null, ['class' => 'form-control']) !!} 
						</div>
						
					</div>

					<div class="form-group">
						{!! Form::label('county_name', 'County:', ['class' => 'col-md-2 control-label']) !!}
						<div class="col-md-10">
							{!! Form::text('county_name', null, ['class' => 'form-control']) !!}
						</div>
					</div>
					
					<div class="form-group">
						{!! Form::label('date_of_value', 'Date of Value:', ['class' => 'col-md-2 control-label']) !!}
						<div class="col-md-10">
							{!! Form::date('date_of_value', \Carbon\Carbon::now()) !!}
						</div>
					</div>
					
					<div class="form-group">
						{!! Form::label('notice_date', 'Notice Date:', ['class' => 'col-md-2 control-label']) !!}
						<div class="col-md-10">
							{!! Form::date('notice_date', \Carbon\Carbon::now()) !!}
						</div>
					</div>
					
					<div class="form-group">
						{!! Form::label('appeal_deadline_date', 'Appeal Deadline Date:', ['class' => 'col-md-2 control-label']) !!}
						<div class="col-md-10">
							{!! Form::date('appeal_deadline_date', \Carbon\Carbon::now()) !!}
						</div>
					</div>
					
					<div class="form-group">
						{!! Form::label('county_link', 'County link:', ['class' => 'col-md-2 control-label']) !!}
						<div class="col-md-10">
							{!! Form::text('county_link', null, ['class' => 'form-control']) !!}
						</div>
					</div>

					<div class="form-group">
            <div class="col-md-10 col-md-offset-2">
			
                {!! Form::submit('Save', ['class' => 'btn btn-default btn-info', 'name'=> 'save', 'value' => 'save'] ) !!}
            </div>
        </div>

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
			
	<script type="text/javascript">
    $(document).ready(function() {
        $('select[name="state_id"]').on('change', function() {
            var stateID = $(this).val();
            if(stateID) {
                $.ajax({
                    url: '/county/add/ajax/'+stateID,
                    type: "GET",
                    dataType: "json",
                    success:function(data) {

                        
                        $('select[name="county_id"]').empty();
                        $.each(data, function(key, value) {
                            $('select[name="county_id"]').append('<option value="'+ key +'">'+ value +'</option>');
                        });

                    }
                });
            }else{
                $('select[name="county_id"]').empty();
            }
        });
    });
	</script>
  