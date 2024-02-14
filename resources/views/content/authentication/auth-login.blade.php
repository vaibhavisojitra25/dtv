@extends('layouts/fullLayoutMaster')

@section('title', 'Login Page')
@section('vendor-style')
<link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/toastr.min.css')) }}">

@endsection
@section('page-style')
  {{-- Page Css files --}}
  <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-validation.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('css/base/pages/authentication.css')) }}">

  <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-toastr.css')) }}">
  <style>
    .semi-dark{
      background-color: #283046;
    }
    .semi-dark h4, .semi-dark p, .semi-dark label{
      color: #e0e2e6!important;
    }
    .auth-wrapper{
      background-image: url('images/background.png');
      background-repeat: no-repeat;
      background-size: 100% 100%;
    }
    #frm-result{
      color: #ea5455;
      width: 100%;
    font-size: 0.857rem;
    }
  </style>
@endsection
@section('content')
<div class="auth-wrapper auth-basic px-2">
  <div class="auth-inner my-2">
    <!-- Login basic -->
    <div class="card mb-0">
      <div class="card-body semi-dark">
        <a href="#" class="brand-logo">
            <img src="{{!empty($siteSetting->logo_light) ? url('images/logo/'. $siteSetting->logo_light) : url('images/logo/logo_wide.png')}}" alt="Purple IPTV" width="300">
          <!-- <h2 class="brand-text text-primary ms-1">Purple IPTV</h2> -->
        </a>

        <h4 class="card-title mb-1">Welcome to Purple IPTV! 👋</h4>
        <p class="card-text mb-2">Please sign-in to your account</p>

        <form action="{{route('login.submit')}}" id="auth-login-form" method="post">
        {{csrf_field()}}
          <div class="mb-1">
            <label for="login-email" class="form-label">Email*</label>
            <input
              type="text"
              class="form-control"
              id="login-email"
              name="email"
              placeholder="Enter Email"
              aria-describedby="login-email"
              tabindex="1"
              autofocus
            />
          </div>

          <div class="mb-1">
            <div class="d-flex justify-content-between">
              <label class="form-label" for="login-password">Password*</label>
              <a href="{{url('forgot-password')}}">
                <small>Forgot Password?</small>
              </a>
            </div>
            <div class="input-group input-group-merge form-password-toggle">
              <input
                type="password"
                class="form-control form-control-merge"
                id="login-password"
                name="password"
                tabindex="2"
                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                aria-describedby="login-password"
              />
              <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
            </div>
          </div>
          <div class="mb-1">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="remember" id="remember-me" tabindex="3" />
              <label class="form-check-label" for="remember-me"> Remember Me </label>
            </div>
          </div>
          <!-- <div class="input-group">
            {!! NoCaptcha::renderJs('en', false, 'recaptchaCallback') !!}
            {!! NoCaptcha::display() !!}
          </div> -->
          <input type="hidden" class="hiddenRecaptcha required" name="hiddenRecaptcha" id="hiddenRecaptcha">

          <div id="frm-result"></div>
          <button class="btn btn-primary w-100 mt-2" tabindex="4" id="frm-signup">Sign in</button>
        </form>

        <p class="text-center mt-2" style="{{!empty($siteSetting) && $siteSetting->is_signup == 2 ? 'display: none' : '' }}">
          <span>New on our platform?</span>
          <a href="{{url('register')}}">
            <span>Create an account</span>
          </a>
        </p>

      </div>
    </div>
    <!-- /Login basic -->
  </div>
</div>
@endsection

@section('vendor-script')
<script src="{{asset(mix('vendors/js/forms/validation/jquery.validate.min.js'))}}"></script>
<script src="{{ asset(mix('vendors/js/forms/validation/additional-methods.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/extensions/toastr.min.js')) }}"></script>

@endsection

@section('page-script')

<script type="text/javascript">
function recaptchaCallback() {
 var res = $('#g-recaptcha-response').val();
    if (res == "" || res == undefined || res.length == 0){
      $('#hiddenRecaptcha').valid();
    }else{
      $('#hiddenRecaptcha-error').hide();
    }
};
$( document ).ready(function() {
  var iframe = $('iframe').contents();

$(document).on('click','iframe#recaptcha-anchor',function(){
  console.log(1);
  $('#hiddenRecaptcha-error').hide();

});
})

</script>
<script src="{{asset(mix('js/scripts/pages/auth-login.js'))}}"></script>
<script src="{{ asset(mix('js/scripts/extensions/ext-component-toastr.js')) }}"></script>
@endsection
