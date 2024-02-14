@extends('layouts/contentLayoutMaster')

@section('title', 'System Settings')

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
    <style>
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;

        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #e74c3c;
            -webkit-transition: .4s;
            transition: .4s;

        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
        }

        input:checked+.slider {
            background-color: #29c75f;

        }

        input:focus+.slider {
            box-shadow: 0 0 1px #2196F3;
        }

        input:checked+.slider:before {
            -webkit-transform: translateX(26px);
            -ms-transform: translateX(26px);
            transform: translateX(26px);
        }

        /* Rounded sliders */
        .slider.round {
            border-radius: 34px;
        }

        .slider.round:before {
            border-radius: 50%;
        }

        .on_off {
            margin-top: 10px;
        }

        .btn-sm {
            margin-right: 10px !important;
        }

        .table>tbody>tr>td {
            vertical-align: middle;
        }

        .btn-md {
            width: 200px;
            background-color: #fb2736 !important;
            color: white;
            font-size: 18px;
        }
    </style>
@endsection
@section('content')
    <div class="row">
                <div class="row match-height">
                    <!-- Filled Tabs starts -->
                    <div class="col-xl-12 col-lg-12">
                        <div class="card">
                            {{-- <div class="card-header">
                        <h4 class="card-title">Filled</h4>
                      </div> --}}
                            <div class="card-body">
                                <!-- Nav tabs -->
                                <ul class="nav nav-tabs nav-fill" id="myTab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link  {{ empty($tabName) || $tabName == 'site-setting' ? 'active' : '' }}"
                                            id="site-setting" data-bs-toggle="tab" href="#site-setting-section"
                                            role="tab" aria-controls="home-fill" aria-selected="true">Site Setting</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ $tabName == 'email-setting' ? 'active' : '' }}"
                                            id="email-setting" data-bs-toggle="tab" href="#email-setting-section"
                                            role="tab" aria-controls="profile-fill" aria-selected="false">Email
                                            Setting</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ $tabName == 'captcha-setting' ? 'active' : '' }}"
                                            id="captcha-setting" data-bs-toggle="tab" href="#captcha-setting-section"
                                            role="tab" aria-controls="messages-fill" aria-selected="false">Captcha
                                            Setting</a>
                                    </li>
                                </ul>

                                <!-- Tab panes -->
                                <div class="tab-content pt-1">
                                    <div class="tab-pane {{ empty($tabName) || $tabName == 'site-setting' ? 'active' : '' }}"
                                        id="site-setting-section" role="tabpanel" aria-labelledby="site-setting">
                                        <form class="" action="{{ route('updateSiteSetting') }}"
                                            id="update-profile-form" method="post" enctype='multipart/form-data'>
                                            {{ csrf_field() }}
                                            <!-- header section -->
                                            <div class="row">
                                                <div class="row mb-1">
                                                    <div class="col-sm-4">
                                                        <label for="formFile" class="form-label">Logo dark</label>
                                                        <input class="form-control" type="file" id=""
                                                            accept=".jpg, .jpeg, .png" name="logo_dark"
                                                            onchange="readURL1(this);" /><br>
                                                        <img id="blah1"
                                                            src="{{ !empty($siteSetting->logo_dark) ? $siteSetting->logo_dark_formatted : url('images/logo/default-logo.png') }}"
                                                            name=""
                                                            style="width: 100%;
                                                            height: 100px; border: 2px solid #E5E5E5;" />
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <label for="formFile" class="form-label">Logo Light</label>
                                                        <input class="form-control" type="file" id=""
                                                            accept=".jpg, .jpeg, .png" name="logo_light"
                                                            onchange="readURL2(this);" /><br>
                                                        <img id="blah2"
                                                            src="{{ !empty($siteSetting->logo_light) ? $siteSetting->logo_light_formatted : url('images/logo/default-logo.png') }}"
                                                            name=""
                                                            style="width: 100%;
                                                            height: 100px; border: 2px solid #E5E5E5;" />
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <label for="formFile" class="form-label">Favicon</label>
                                                        <input class="form-control" type="file" id=""
                                                            accept=".jpg, .jpeg, .png" name="favicon"
                                                            onchange="readURL3(this);" /><br>
                                                        <img id="blah3"
                                                            src="{{ !empty($siteSetting->favicon) ? $siteSetting->favicon_formatted : url('images/logo/default-logo.png') }}"
                                                            name=""
                                                            style="width: 100%;
                                                            height: 100px; border: 2px solid #E5E5E5;" />
                                                    </div>
                                                </div>
                                                <div class="row mb-0">
                                                    <div class="col-sm-4">
                                                        <label class="form-label">Title Text</label>
                                                        <input class="form-control" type="text" id=""
                                                            name="title_text"
                                                            value="{{ $siteSetting->title_text ?? '' }}" /><br>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <label class="form-label">Footer Text</label>
                                                        <input class="form-control" type="text" id=""
                                                            name="footer_text"
                                                            value="{{ $siteSetting->footer_text ?? '' }}" /><br>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <label class="form-label">Signup Allowed</label><br>
                                                        <?php
                                                        if(!empty($siteSetting->is_signup))
                                                        {
                                                            $is_signup = $siteSetting->is_signup;
                                                        }else{
                                                            $is_signup = 1;
                                                        }

                                                        if ($is_signup == 2) {
                                                        ?>
                                                        <span class="btnOn">
                                                            <label class="switch">
                                                                <input type="checkbox"
                                                                    class="chkSignupStatus" />
                                                                <span class="slider round"></span>
                                                            </label>
                                                        </span>
                                                        <?php } else {
                                                        ?>
                                                        <span class="btnOn">
                                                            <label class="switch">
                                                                <input type="checkbox" checked class="chkSignupStatus" />
                                                                <span class="slider round"></span>
                                                            </label>
                                                        </span>
                                                        <?php }?><br>
                                                    </div>

                                              

                                                </div>
                                                <div class="row mb-1">

                                                    <div class="col-sm-4">
                                                        <label class="form-label">Activation Device Allowed</label><br>
                                                        <?php
                                                        if(!empty($siteSetting->is_activation))
                                                        {
                                                            $is_activation = $siteSetting->is_activation;
                                                        }else{
                                                            $is_activation = 1;
                                                        }

                                                        if ($is_activation == 2) {
                                                        ?>
                                                        <span class="btnOn">
                                                            <label class="switch">
                                                                <input type="checkbox"
                                                                    class="chkActivationStatus" />
                                                                <span class="slider round"></span>
                                                            </label>
                                                        </span>
                                                        <?php } else {
                                                        ?>
                                                        <span class="btnOn">
                                                            <label class="switch">
                                                                <input type="checkbox" checked class="chkActivationStatus" />
                                                                <span class="slider round"></span>
                                                            </label>
                                                        </span>
                                                        <?php }?><br>
                                                    </div>

                                                    <div class="col-sm-4">
                                                        <label class="form-label">Help URL</label>
                                                        <input class="form-control" type="text" id=""
                                                            name="help_url"
                                                            value="{{ $siteSetting->help_url ?? '' }}" /><br>
                                                    </div>
                                                </div>


                                                <div class="col-12">
                                                    <input type="hidden" id="is_signup" name="is_signup" >
                                                    <input type="hidden" id="is_activation" name="is_activation" >
                                                    <button class="btn btn-primary mt-1 me-1">Save</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="tab-pane {{ $tabName == 'email-setting' ? 'active' : '' }}"
                                        id="email-setting-section" role="tabpanel" aria-labelledby="profile-tab-fill">
                                        <form class="" action="{{ route('updateEmailSetting') }}"
                                            id="update-profile-form" method="post" enctype='multipart/form-data'>
                                            {{ csrf_field() }}
                                            <!-- header section -->
                                            <div class="row">
                                                <div class="row mb-1">
                                                    <div class="col-sm-4">
                                                        <label class="form-label">Mail Driver</label>
                                                        <input type="text" class="form-control" name="mail_driver"
                                                            value="{{ $mailSetting->mail_driver ?? '' }}"
                                                            placeholder="Mail Driver"
                                                            data-msg="Please Enter Mail Driver" />
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <label class="form-label">Mail Host</label>
                                                        <input type="text" class="form-control" name="mail_host"
                                                            value="{{ $mailSetting->mail_host ?? '' }}"
                                                            placeholder="Mail Host" data-msg="Please Enter Mail Host" />
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <label class="form-label">Mail Port</label>
                                                        <input type="text" class="form-control" name="mail_port"
                                                            value="{{ $mailSetting->mail_port ?? '' }}"
                                                            placeholder="Mail Port" data-msg="Please Enter Mail Port" />
                                                    </div>
                                                </div>

                                                <div class="row mb-1">
                                                    <div class="col-sm-4">
                                                        <label class="form-label">Mail Username</label>
                                                        <input type="text" class="form-control" name="mail_username"
                                                            value="{{ $mailSetting->mail_username ?? '' }}"
                                                            placeholder="Mail Username"
                                                            data-msg="Please Enter Mail Username" />
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <label class="form-label">Mail Password</label>
                                                        <input type="text" class="form-control" name="mail_password"
                                                            value="{{ $mailSetting->mail_password ?? '' }}"
                                                            placeholder="Mail Password"
                                                            data-msg="Please Enter Mail Password" />
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <label class="form-label">Mail Encryption</label>
                                                        <input type="text" class="form-control" name="mail_encryption"
                                                            value="{{ $mailSetting->mail_encryption ?? '' }}"
                                                            placeholder="Mail Encryption"
                                                            data-msg="Please Enter Mail Encryption" />
                                                    </div>
                                                </div>

                                                <div class="row mb-1">
                                                    <div class="col-sm-4">
                                                        <label class="form-label">Mail From Address</label>
                                                        <input type="text" class="form-control"
                                                            name="mail_from_address"
                                                            value="{{ $mailSetting->mail_from_address ?? '' }}"
                                                            placeholder="Mail From Address"
                                                            data-msg="Please Enter Mail From Address" />
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <label class="form-label">Mail From Name</label>
                                                        <input type="text" class="form-control" name="mail_from_name"
                                                            value="{{ $mailSetting->mail_from_name ?? '' }}"
                                                            placeholder="Mail Mail From Name"
                                                            data-msg="Please Enter Mail From Name" />
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <button class="btn btn-primary mt-1 me-1">Save</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="tab-pane {{ $tabName == 'captcha-setting' ? 'active' : '' }}"
                                        id="captcha-setting-section" role="tabpanel" aria-labelledby="messages-tab-fill">
                                        <form class="" action="{{ route('updateCaptchaSetting') }}"
                                            id="update-profile-form" method="post" enctype='multipart/form-data'>
                                            {{ csrf_field() }}
                                            <!-- header section -->
                                            <div class="row">
                                                <div class="row mb-1">
                                                    <div class="col-sm-4">
                                                        <label class="form-label" for="first_name">Captcha Key</label>
                                                        <input type="text" class="form-control" name="captcha_Key"
                                                            value="{{ $captchaSetting->captcha_Key ?? '' }}"
                                                            placeholder="Captcha Key"
                                                            data-msg="Please Enter Captcha Key" />
                                                    </div>
                                                </div>
                                                <div class="row mb-1">
                                                    <div class="col-sm-4">
                                                        <label class="form-label" for="first_name">Captcha Secret</label>
                                                        <input type="text" class="form-control" name="captcha_secret"
                                                            value="{{ $captchaSetting->captcha_secret ?? '' }}"
                                                            placeholder="Captcha Secret"
                                                            data-msg="Please Enter Captcha Secret" />
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <button class="btn btn-primary mt-1 me-1">Save</button>

                                                </div>
                                            </div>
                                        </form>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Filled Tabs ends -->
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
        <script>
            //redirect to specific tab
            $(document).on('click', '.chkSignupStatus', function (e) {
                var status = $(this).prop('checked') == true ? 1 : 2;
                $("#is_signup").val(status);
            });
            $(document).on('click', '.chkActivationStatus', function (e) {
                var status = $(this).prop('checked') == true ? 1 : 2;
                $("#is_activation").val(status);
            });
        </script>
        <!-- Page js files -->
        <script>
            function readURL1(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#blah1').attr('src', e.target.result);
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }

            function readURL2(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#blah2').attr('src', e.target.result);
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }

            function readURL3(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#blah3').attr('src', e.target.result);
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }
        </script>
    @endsection
