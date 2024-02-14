@extends('layouts/fullLayoutMaster')

@section('title', 'Reset Password')

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
    <!-- Reset Password basic -->
    <div class="card mb-0">
      <div class="card-body semi-dark">
        <a href="#" class="brand-logo">
        <img src="{{asset('images/logo/logo_wide.png')}}" alt="Purple IPTV" width="300">
        </a>

        <h4 class="card-title mb-1">Reset Password 🔒</h4>
        <p class="card-text mb-2">Your new password must be different from previously used passwords</p>

        <form class="auth-reset-password-form mt-2" action="{{route('resetPassword')}}" method="post">
        {{csrf_field()}}
        <div class="mb-1">
          <div class="d-flex justify-content-between">
            <label class="form-label" for="reset-password-new">Email*</label>
          </div>
          <div class="input-group input-group-merge form-password-toggle">
            <input
              type="email"
              class="form-control form-control-merge"
              id="email"
              name="email"
              placeholder="Email"
              aria-describedby="email"
              tabindex="1"
              autofocus
            />
          </div>
        </div>
          <div class="mb-1">
            <div class="d-flex justify-content-between">
              <label class="form-label" for="reset-password-new">New Password*</label>
            </div>
            <div class="input-group input-group-merge form-password-toggle">
              <input
                type="password"
                class="form-control form-control-merge"
                id="reset-password-new"
                name="new_password"
                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                aria-describedby="reset-password-new"
                tabindex="1"
                autofocus
              />
              <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
            </div>
          </div>
          <div class="mb-1">
            <div class="d-flex justify-content-between">
              <label class="form-label" for="reset-password-confirm">Confirm Password*</label>
            </div>
            <div class="input-group input-group-merge form-password-toggle">
              <input
                type="password"
                class="form-control form-control-merge"
                id="reset-password-confirm"
                name="reset-password-confirm"
                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                aria-describedby="reset-password-confirm"
                tabindex="2"
              />
              <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
            </div>
          </div>
          {{-- <input type="hidden" name="user_id" value="{{$user->user_id}}"> --}}
          <button class="btn btn-primary w-100" tabindex="3">Set New Password</button>
        </form>

        <p class="text-center mt-2">
          <a href="{{url('login')}}"> <i data-feather="chevron-left"></i> Back to login </a>
        </p>
      </div>
    </div>
    <!-- /Reset Password basic -->
  </div>
</div>
@endsection

@section('vendor-script')
<script src="{{asset(mix('vendors/js/forms/validation/jquery.validate.min.js'))}}"></script>
<script src="{{ asset(mix('vendors/js/forms/validation/additional-methods.min.js')) }}"></script>
@endsection

@section('page-script')
<script src="{{asset(mix('js/scripts/pages/auth-reset-password.js'))}}"></script>
@endsection
