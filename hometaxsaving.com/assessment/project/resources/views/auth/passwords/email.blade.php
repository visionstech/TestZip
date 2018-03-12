@extends('layouts.app')
@section('pageTitle', 'RESET PASSWORD')
@section('css')
<link href="{{ asset('/project/resources/assets/customer/css/login.css') }}" rel="stylesheet" type="text/css">
@endsection
<!-- Main Content -->
@section('content')
<div class="container forgot-password">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="inner-login-regs">
                <div class="panel-heading"><h4>Reset Password</h4></div>
                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/password/email') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <div class="login-form">
                            <label for="email" class="col-md-4 control-label">E-Mail Address <span>*</span></label>

                            <div class="col-md-6">
                                <input id="email" type="email" placeholder="Enter Email" class="form-control" name="email" value="{{ old('email') }}">

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-btn fa-envelope"></i> Send Password Reset Link
                                </button>
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
