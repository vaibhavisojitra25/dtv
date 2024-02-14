@extends('layouts/fullLayoutMaster')

@section('title', 'Register Page')
@section('vendor-style')
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/toastr.min.css')) }}">

@endsection
@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-validation.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/pages/authentication.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-toastr.css')) }}">

    <style>
        .semi-dark {
            background-color: #283046;
        }

        .semi-dark h4,
        .semi-dark p,
        .semi-dark label {
            color: #e0e2e6 !important;
        }

        .auth-wrapper {
            background-image: url('images/background.png');
            background-repeat: no-repeat;
            background-size: 100% 100%;
        }

        .validate-error {
            color: #ea5455;
            width: 100%;
            font-size: 0.857rem;
        }
    </style>
@endsection

@section('content')
    <div class="auth-wrapper auth-basic px-2">
        <div class="auth-inner my-2">
            <!-- Register basic -->
            <div class="card mb-0">
                <div class="card-body semi-dark">
                    <a href="#" class="brand-logo">
                        <img src="{{ !empty($siteSetting->logo_light) ? url('images/logo/' . $siteSetting->logo_light) : url('images/logo/logo_wide.png') }}"
                            alt="Purple IPTV" width="300">
                    </a>

                    <h4 class="card-title mb-1">Adventure starts here 🚀</h4>
                    <p class="card-text mb-2">Make your app management easy and fun!</p>

                    <form class="mt-2" action="{{ route('register.submit') }}" id="auth-register-form" method="post">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="mb-1 col-md-6 col-12">
                                <label for="first_name" class="form-label">Firstname*</label>
                                <input type="text" class="form-control" id="first_name" name="first_name"
                                    placeholder="Firstname" aria-describedby="first_name" tabindex="1" autofocus />
                            </div>
                            <div class="mb-1 col-md-6 col-12">
                                <label for="last_name" class="form-label">Lastname*</label>
                                <input type="text" class="form-control" id="last_name" name="last_name"
                                    placeholder="Lastname" aria-describedby="last_name" tabindex="2" autofocus />
                            </div>
                        </div>
                        <div class="mb-1">
                            <label for="email" class="form-label">Email*</label>
                            <input type="text" class="form-control" id="email" name="email" placeholder="Email"
                                aria-describedby="email" tabindex="3" />
                            <div class="validate-error" id="email-error"></div>
                        </div>

                        <div class="mb-1">
                            <label for="password" class="form-label">Password*</label>

                            <div class="input-group input-group-merge form-password-toggle">
                                <input type="password" class="form-control form-control-merge" id="password"
                                    name="password"
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                    aria-describedby="password" tabindex="4" />
                                <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                            </div>
                        </div>

                        <div class="mb-1">
                            <label for="confirm-password" class="form-label">Confirm Password*</label>

                            <div class="input-group input-group-merge form-password-toggle">
                                <input type="password" class="form-control form-control-merge" id="confirm-password"   onpaste="return false;"
                                    name="confirm-password"
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                    aria-describedby="confirm-password" tabindex="5" />
                                <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                            </div>
                        </div>
                        <div class="input-group">
                            {!! NoCaptcha::renderJs('en', false, 'recaptchaCallback') !!}
                            {!! NoCaptcha::display() !!}
                        </div>
                        <input type="hidden" class="hiddenRecaptcha required" name="hiddenRecaptcha" id="hiddenRecaptcha">
                        <div class="validate-error" id="frm-result"></div>
                        <button class="btn btn-primary w-100 mt-2" tabindex="5">Sign up</button>
                    </form>

                    <p class="text-center mt-2">
                        <span>Already have an account?</span>
                        <a href="{{ url('login') }}">
                            <span>Sign in instead</span>
                        </a>
                    </p>
                </div>
            </div>
            <!-- /Register basic -->
        </div>
    </div>
@endsection

@section('vendor-script')
    <script src="{{ asset('vendors/js/forms/validation/jquery.validate.min.js') }}"></script>
<script src="{{ asset(mix('vendors/js/forms/validation/additional-methods.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/toastr.min.js')) }}"></script>

@endsection

@section('page-script')
    <script type="text/javascript">
       function recaptchaCallback() {
            var res = $('#g-recaptcha-response').val();
            console.log(1);
            if (res == "" || res == undefined || res.length == 0){
            $('#hiddenRecaptcha').valid();
            }else{
            $('#hiddenRecaptcha-error').hide();
            }
        };
        // const validateEmail = (email) => {
        //     return email.match(
        //         /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
        //     );
        // };
        // $("#email").keyup(function() {
        //     email = $('#email').val();
        //     if (!validateEmail(email)) {
        //         $("#email-error").text("Please enter valid email");
        //         return false;
        //     } else if (validateEmail(email)) {
        //         $("#email-error").text("");
        //         return true;
        //     }
        // });
        // $('#auth-register-form').submit(function(e) {
        //     email = $('#email').val();
        //     if (!validateEmail(email)) {
        //         $("#email-error").text("Please enter valid email");
        //         return false;
        //     }
        // });

    </script>
    <script src="{{ asset('js/scripts/pages/auth-register.js') }}"></script>
    <script src="{{ asset(mix('js/scripts/extensions/ext-component-toastr.js')) }}"></script>
@endsection
