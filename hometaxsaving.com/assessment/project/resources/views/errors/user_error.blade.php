@if($errors->any())
	<div class="alert alert-danger errorAlertMsgMain text-left">
		<!--<strong></strong> There were some problems with your input. here-->
		<ul class="errorAlertMsg">
			@foreach($errors->all() as $error)
			<li>{{ $error }}</li>
			@endforeach
		</ul>
	</div>
@else 
    <?php $errors = []; ?>
@endif
@if(Session::has('success'))
    <div class="alert alert-success"> 
       {{ Session::get('success') }} 
    </div> 
@endif
@if(Session::has('error')) 
    <div class="alert alert-danger"> 
        {{ Session::get('error')}} 
    </div> 
@endif