
					<div class="form-group">
						{!! Form::label('name', 'Name:', ['class' => 'col-md-2 control-label']) !!}
						<div class="col-md-10">
						{!! Form::text('name', null, ['class' => 'form-control']) !!}
						{!! Form::hidden('id', $user->id) !!}

						</div>
					</div>

					<div class="form-group">
						{!! Form::label('email', 'Email:', ['class' => 'col-md-2 control-label']) !!}
						<div class="col-md-10">
						{!! Form::text('email', null, ['class' => 'form-control']) !!}
						</div>
					</div>
					
					
					<div class="form-group">
						{!! Form::label('password', 'Password:', ['class' => 'col-md-2 control-label']) !!}
						<div class="col-md-10">
						{!! Form::password('password', null, ['class' => 'form-control']) !!}
						</div>
					</div>
					
					<div class="form-group">
						{!! Form::label('confirm_password', 'Confirm Password:', ['class' => 'col-md-2 control-label']) !!}
						<div class="col-md-10">
						{!! Form::password('confirm_password', null, ['class' => 'form-control'])
						!!}
						</div>
					</div>
			<div class="form-group">
            <div class="col-md-10 col-md-offset-2">
                {!! Form::submit('Save', ['class' => 'btn btn-default btn-info'] ) !!}
            </div>
        </div>
				

					{!! Form::close() !!}