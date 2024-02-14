@extends('layouts/contentLayoutMaster')

@section('title', 'Setting')

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
            <!-- profile -->
            <div class="card">
                <div class="card-header border-bottom">
                    <h4 class="card-title">Update Limit</h4>
                </div>
                <div class="card-body py-2 my-25">
                    <!-- form -->
                    <form class="" action="{{ route('update_setting') }}" id="update-profile-form"
                        method="post" enctype='multipart/form-data'>
                        {{ csrf_field() }}
                        <!-- header section -->
                        <div class="row">
                            <div class="row mb-1">
                                <div class="col-sm-6">
                                    <label class="form-label" for="playlist_limit">Playlist limit</label>
                                    <input type="number" class="form-control" id="playlist_limit" name="playlist_limit"
                                        value="{{ $setting->playlist_limit ?? '' }}"
                                        placeholder="Playlist limit" data-msg="Please Enter Playlist limit" />
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label" for="device_limit">Device limit (For Free Plan)</label>
                                    <input type="number" class="form-control" id="device_limit" name="device_limit"
                                        value="{{ $setting->device_limit ?? '' }}"
                                        placeholder="Device limit" data-msg="Please Enter Device limit" />
                                </div>
                            </div>

                            <div class="col-12">
                                <input type="hidden" name="flag" value="1">
                                <button class="btn btn-primary mt-1 me-1">Save</button>

                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header border-bottom">
                    <h4 class="card-title">Update Admin Email</h4>
                </div>
                <div class="card-body py-2 my-25">
                    <!-- form -->
                    <form class="" action="{{ route('update_setting') }}" id="update-profile-form"
                        method="post" enctype='multipart/form-data'>
                        {{ csrf_field() }}
                        <!-- header section -->
                        <div class="row">
                            <div class="row mb-1">
                                <div class="col-sm-6">
                                    <label class="form-label" for="first_name">Admin Email</label>
                                    <input type="text" class="form-control" id="admin_email" name="admin_email"
                                        value="{{ $setting->admin_email ?? '' }}"
                                        placeholder="Admin Email" data-msg="Please Enter Admin Email" />
                                </div>
                            </div>

                            <div class="col-12">
                            <input type="hidden" name="flag" value="2">
                                <button class="btn btn-primary mt-1 me-1">Save</button>

                            </div>
                        </div>
                    </form>
                </div>
            </div>

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
@endsection
