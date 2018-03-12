@extends('layouts.app')
@section('title')
    @section('pageTitle', 'RESET PASSWORD')
@endsection
@section('css')
<link href="{{ asset('/project/resources/assets/customer/css/login.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('/project/resources/assets/customer/css/custom_common.css') }}" rel="stylesheet">

@endsection
@section('content')
<div class="container forgot-password">
    <div class="row">
        <div class="col-md-8 col-md-offset-2 ">
            <div class="panel panel-default">
                <div class="inner-login-regs">
                    <h4>Reset Password</h4>

                    <div class="panel-body">
                        <form class="form-horizontal" role="form" id="reset_password_form" method="POST" action="{{ url('/password/reset') }}">
                            {{ csrf_field() }}

                            <input type="hidden" name="token" value="{{ $token }}">

                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <div class="login-form">
                                    <label for="email" class="col-md-4 control-label">E-Mail Address <span>*</span></label>

                                    <div class="col-md-6">
                                        <input id="email" type="email" class="form-control" name="email" value="{{ $email or old('email') }}">

                                        @if ($errors->has('email'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                <div class="login-form">
                                    <label for="password" class="col-md-4 control-label">Password <span>*</span></label>

                                    <div class="col-md-6">
                                        <input id="password" type="password" class="form-control" name="password">

                                        @if ($errors->has('password'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                                <div class="login-form">
                                    <label for="password-confirm" class="col-md-4 control-label">Confirm Password <span>*</span></label>
                                    <div class="col-md-6">
                                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation">

                                        @if ($errors->has('password_confirmation'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('password_confirmation') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-4">
                                    <div class="login-button">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-btn fa-refresh"></i> Reset Password
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.js"></script>

<script>
    $("#reset_password_form").validate({
      rules: {
        email: {
            required: true,
            email: true
        },
        password:{
          required: true,
          minlength: 6
      },
      password_confirmation:{
          required: true,
          equalTo: "#password"
      }
  }
});
</script>
@endsection