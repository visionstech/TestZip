@extends('layouts.app')
@section('title')
    @section('pageTitle', 'Change Password')
@endsection
@section('css')
<link href="{{ asset('/project/resources/assets/customer/css/password-style.css') }}" rel="stylesheet">
<link href="{{ asset('/project/resources/assets/customer/css/custom_common.css') }}" rel="stylesheet">
@endsection
@section('content')
            <div class="outer-search-list">
               <div class="top-search-list">
                  <ul>
                    <li>
                     <a href="#"><span>Change Password</span></a>
                    </li>
                     <li>
                        <a href="{{ url('/search-address') }}">START NEW SEARCH</a>
                     </li>
                  </ul>
               </div>
               <div class="search-list">
                  <div class="main-form">
                      @if (session('error'))
                        <div class="alert alert-danger">
                          {{ session('error') }}
                        </div>
                      @endif
                      @if (session('success'))
                        <div class="alert alert-success">
                          {{ session('success') }}
                        </div>
                      @endif
                      <form method="POST" id="change_password_form" action="{{ route('changePassword') }}">
                        {{ csrf_field() }}
                          <div class="form-label">
                          <label>Current Password <span>*</span><br>
                          </label>
                          <div class="inner-assessment-form-input-password"> 
                          <input id="current-password" placeholder="Enter Current Password" type="password" name="current-password" required > <br>
                          @if ($errors->has('current-password'))
                          <span class="help-block">
                            <strong>{{ $errors->first('current-password') }}</strong>
                          </span>
                          @endif
                      </div>
                    </div>
                      <div class="form-label">
                          <label>
                             New Password <span>*</span><br>
                          </label>
                          <div class="inner-assessment-form-input-password">
                           <input id="new-password" type="password" placeholder="Enter New Password" name="new-password" required>
                            @if ($errors->has('new-password'))
                            <span class="help-block">
                              <strong>{{ $errors->first('new-password') }}</strong>
                            </span>
                            @endif
                      </div>
                    </div>
                      <div class="form-label">
                          <label>
                              Re-Enter New Password <span>*</span>
                          </label>
                          <div class="inner-assessment-form-input-password">
                           <input id="new-password-confirm" type="password"  placeholder="Re-Enter New Password" name="new-password_confirmation" required>
                      </div>
                    </div>
                      <div class="form-button">
                          <button type="submit" class="btn">
                            CHANGE PASSWORD
                          </button>
                      </div>
                              </form>
                          
              
                      </div>
               </div>
            </div>
@endsection
@section('js')

<script src="{{ asset('/project/resources/assets/customer/js/jquery.validate.new.js') }}"></script>

<script>
$("#change_password_form").validate({
      rules: {
            "current-password":{
              required: true

            },
            "new-password":{
              required: true,
              minlength: 6,
              
            },
            "new-password_confirmation":{
              required: true,
              equalTo : "#new-password"
            }
        }
  });
</script>
@endsection