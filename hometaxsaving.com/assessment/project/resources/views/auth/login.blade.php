@extends('layouts.app')
@section('pageTitle', 'Login') 
@section('css')

<link href="{{ asset('/project/resources/assets/customer/css/login.css') }}" rel="stylesheet">
<link href="{{ asset('/project/resources/assets/customer/css/custom_common.css') }}" rel="stylesheet">
@endsection
@section('content')
<div class="container">
            <div class="inner-free-regs">
               <h4>
                  FREE REGISTRATION
               </h4>
               <p>
                  Register today and realize a number of benefits from HomeTaxSavings™!
               </p>
               <ul>
                  <li>
                      Your free membership with HomeTaxSavings.com™ will ensure you don’t miss out on the opportunity to challenge your assessed value and save money.
                  </li>
                  <li>
                    Even if your assessment notice gets lost in the mail, we will notify when your new assessed value is issued along with advanced warning of the upcoming appeal deadline.
                  </li>
                  <li>
                     Periodic updates on local market conditions in the housing market and other relevant assessment issues and filing requirements.
                  </li>
                  <li>
                     Tips on when it would be advantageous to request the local assessor come to your home for an inspection. (For example, your property is in need of major repairs and renovation which should affect the overall value and what you are paying taxes on)
                  </li>
               </ul>
            </div>
            <div class="inner-login-regs">

                @if (count($errors) > 0)
                    <span class="help-block">
                        <strong>{{ $errors->first('session_expiry_message') }}</strong>
                    </span>
                @endif

               <h4>LOGIN HERE</h4>
               <form class="outer-login-regs" role="form" id="login_form" method="POST" action="{{ url('/login') }}">
                        {{ csrf_field() }}
                  <div class="login-form">
                     <label>
                        Email <span>*</span><br>
                     </label>
                     <input id="email" type="email" placeholder="Enter Email" name="email" value="{{ old('email') }}">
                    @if ($errors->has('email'))
                        <span class="help-block">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
                  </div>
                    <div class="login-form">
                     <label>
                       Password <span>*</span><br>
                     </label>
                     <input id="password" type="password" placeholder="Enter Password" name="password">
                    @if ($errors->has('password'))
                        <span class="help-block">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif
                  </div>
                  <div class="forgot-pass">
                     <label class="checkbox-div">Remember me
                     <input type="checkbox" name="remember">
                     <span class="checkmark"></span>
                    </label>
                    <div class="forgot-pass-button">
                       <a href="{{ url('/password/reset') }}">Forgot Password?</a>
                    </div>
                  </div>
                  <div class="login-button">
                     <input type="submit" name="login" value="LOGIN">
                  </div>
               </form>
            </div>
            <div class="third_grid">
               <img src="{{ asset('/project/resources/assets/customer/css/images/login-image.png') }}" alt="login-image">
            </div>
         </div><!--container-->
      </div><!--outer-free-regs-->
@endsection
@section('js')
<script src="{{ asset('/project/resources/assets/customer/js/jquery.validate.new.js') }}"></script>

<script>
$("#login_form").validate({
      rules: {
            email: {
                required: true,
                email: true
            },
            password: {
                required: true
            }
        },
        messages: {
            email: {
                required: "This email field is required.",
            },
            password: {
                required: "This password field is required."
            }
        }
  });

</script>
@endsection