@extends('layouts/contentLayoutMaster')

@section('title', 'Manage Profile')

@section('vendor-style')
  <!-- vendor css files -->
  <link rel='stylesheet' href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
  <link rel='stylesheet' href="{{ asset(mix('vendors/css/animate/animate.min.css')) }}">
  <link rel='stylesheet' href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">
@endsection
@section('page-style')
  <!-- Page css files -->
  <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-sweet-alerts.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-validation.css')) }}">
@endsection

@section('content')
<div class="row">
  <div class="col-12">
    <ul class="nav nav-pills mb-2">
      <!-- Account -->
      <li class="nav-item">
        <a class="nav-link active" href="{{asset('my-profile')}}">
          <i data-feather="user" class="font-medium-3 me-50"></i>
          <span class="fw-bold">My Profile</span>
        </a>
      </li>
      <!-- security -->
      <li class="nav-item">
        <a class="nav-link" href="{{asset('change-password')}}">
          <i data-feather="lock" class="font-medium-3 me-50"></i>
          <span class="fw-bold">Change Password</span>
        </a>
      </li>
      <!-- @if(Auth::user()->user_type == 2)
      <li class="nav-item">
        <a class="nav-link" href="{{asset('account-settings-billing')}}">
          <i data-feather="bookmark" class="font-medium-3 me-50"></i>
          <span class="fw-bold">Billings &amp; Plans</span>
        </a>
      </li>
    @endif -->
    </ul>

    <!-- profile -->
    <div class="card">
      <div class="card-header border-bottom">
        <h4 class="card-title">Profile Details</h4>
      </div>
      <div class="card-body py-2 my-25">
        <!-- form -->
        <form class="" action="{{route('updateProfile')}}" id="update-profile-form" method="post" enctype='multipart/form-data'>
        {{csrf_field()}}
        <!-- header section -->
        <div class="d-flex">
          <a href="#" class="me-25">
            <img
              src="{{ (Auth::user() && Auth::user()->profile_picture) ? url('uploads/profile_pictures/'.Auth::user()->profile_picture) : asset('images/portrait/small/avatar-s-11.jpg') }}"
              id="account-upload-img"
              class="uploadedAvatar rounded me-50"
              alt="profile image"
              height="100"
              width="100"
            />
          </a>
          <!-- upload and reset button -->
          <div class="d-flex align-items-end mt-75 ms-1">
            <div>
              <label for="account-upload" class="btn btn-sm btn-primary mb-75 me-75">Upload</label>
              <input type="file" id="account-upload" name="profile_picture" hidden accept="image/*" />
              <button type="button" id="account-reset" class="btn btn-sm btn-outline-secondary mb-75">Reset</button>
              <p class="mb-0">Allowed file types: png, jpg, jpeg.</p>
            </div>
          </div>
          <!--/ upload and reset button -->
        </div>
        <!--/ header section -->

   
          <div class="row mt-2 pt-50">
            <div class="col-12 col-sm-6 mb-1">
              <label class="form-label" for="first_name">First Name</label>
              <input
                type="text"
                class="form-control"
                id="first_name"
                name="first_name"
                placeholder="Enter Firstname"
                value="{{Auth::user()->first_name}}"
                data-msg="Please enter first name"
              />
            </div>
            <div class="col-12 col-sm-6 mb-1">
              <label class="form-label" for="last_name">Last Name</label>
              <input
                type="text"
                class="form-control"
                id="last_name"
                name="last_name"
                placeholder="Enter Lastname"
                value="{{Auth::user()->last_name}}"
                data-msg="Please enter last name"
              />
            </div>
            <div class="col-12 col-sm-6 mb-1">
              <label class="form-label" for="accountEmail">Email</label>
              <input
                type="email"
                class="form-control"
                id="accountEmail"
                name="email"
                placeholder="Email"
                value="{{Auth::user()->email}}" @if(Auth::user()->user_type == 2){{"readonly"}}@endif
              />
            </div>
    
            <div class="col-12 col-sm-6 mb-1">
              <label class="form-label" for="phone_no">Phone Number</label>
              <input
                type="text"
                class="form-control account-number-mask"
                id="phone_no"
                name="phone_no"
                maxlength="14" 
                placeholder="Phone Number"
                value="{{Auth::user()->phone_no}}"
              />
            </div>

            <div class="col-12">
              <button class="btn btn-primary mt-1 me-1">Save changes</button>
              <a href="/dashboard" class="btn btn-outline-secondary mt-1">Discard</a>
            </div>
          </div>
      </div>

      </form>
        <!--/ form -->
    </div>

    <!-- deactivate account  -->
    <!-- <div class="card">
      <div class="card-header border-bottom">
        <h4 class="card-title">Delete Account</h4>
      </div>
      <div class="card-body py-2 my-25">
        <div class="alert alert-warning">
          <h4 class="alert-heading">Are you sure you want to delete your account?</h4>
          <div class="alert-body fw-normal">
            Once you delete your account, there is no going back. Please be certain.
          </div>
        </div>

        <form id="formAccountDeactivation" class="validate-form" onsubmit="return false">
          <div class="form-check">
            <input
              class="form-check-input"
              type="checkbox"
              name="accountActivation"
              id="accountActivation"
              data-msg="Please confirm you want to delete account"
            />
            <label class="form-check-label font-small-3" for="accountActivation">
              I confirm my account deactivation
            </label>
          </div>
          <div>
            <button type="submit" class="btn btn-danger deactivate-account mt-1">Deactivate Account</button>
          </div>
        </form>
      </div>
    </div> -->
    <!--/ profile -->
  </div>
</div>
@endsection

@section('vendor-script')
  <!-- vendor files -->
  <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/forms/validation/jquery.validate.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/forms/validation/additional-methods.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/forms/cleave/cleave.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/forms/cleave/addons/cleave-phone.us.js')) }}"></script>
@endsection
@section('page-script')
  <!-- Page js files -->
  <script src="{{ asset(mix('js/scripts/pages/page-account-settings-account.js')) }}"></script>
@endsection
