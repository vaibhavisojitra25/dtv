@extends('layouts/fullLayoutMaster')

@section('title', 'Forgot Password')

@section('page-style')
  {{-- Page Css files --}}
  <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-validation.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('css/base/pages/authentication.css')) }}">

  <style>
    .semi-dark{
      background-color: #283046;
    }
    .semi-dark h4, .semi-dark p, .semi-dark label{
      color: #e0e2e6!important;
    }
  </style>
@endsection

@section('content')
<div class="auth-wrapper auth-basic px-2">
  <div class="auth-inner my-2">
    <!-- Forgot Password basic -->
    <div class="card mb-0">
      <div class="card-body semi-dark">
        <a href="#" class="brand-logo">
        <img src="{{!empty($siteSetting->logo_light) ? url('images/logo/'. $siteSetting->logo_light) : url('images/logo/logo_wide.png')}}" alt="Purple IPTV" width="300">
        </a>

        <h4 class="card-title mb-1">Forgot Password? 🔒</h4>
        <p class="card-text mb-2">Enter your email and we'll send you instructions to reset your password</p>

        <form class="auth-forgot-password-form mt-2" action="{{route('send-reset-link')}}" method="post">
        {{csrf_field()}}
          <div class="mb-1">
            <label for="forgot-password-email" class="form-label">Email*</label>
            <input
              type="text"
              class="form-control"
              id="forgot-password-email"
              name="email"
              placeholder="Enter Email"
              aria-describedby="forgot-password-email"
              tabindex="1"
              autofocus
            />
          </div>
          <button class="btn btn-primary w-100" tabindex="2">Send reset link</button>
        </form>

        <p class="text-center mt-2">
          <a href="{{url('login')}}"> <i data-feather="chevron-left"></i> Back to login </a>
        </p>
      </div>
    </div>
    <!-- /Forgot Password basic -->
  </div>
</div>
@endsection

@section('vendor-script')
<script src="{{asset(mix('vendors/js/forms/validation/jquery.validate.min.js'))}}"></script>
<script src="{{ asset(mix('vendors/js/forms/validation/additional-methods.min.js')) }}"></script>
@endsection

@section('page-script')
<script src="{{asset(mix('js/scripts/pages/auth-forgot-password.js'))}}"></script>
@endsection
