@if(count($counties))
    {!! Form::select($input_name,array_replace(['' => 'Select County '],$counties), null ,['id' => $input_id,'class' => 'form-control', 'autocomplete' => 'off']) !!}
@else
    {!! Form::select($input_name,array_replace(['' => 'Select County '],$counties), null ,['id' => $input_id,'class' => 'form-control', 'autocomplete' => 'off', 'disabled'=>'disabled']) !!}
@endif
<span class="icon error error_icon"><span class="tooltiptext"></span></span>
