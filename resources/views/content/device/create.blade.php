@extends('layouts/contentLayoutMaster')

@section('title', 'Add Device')

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">

@endsection
@section('page-style')
    {{-- Page css files --}}
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-validation.css')) }}">
    <style>
    .select2:before {
    content: "";
    position: absolute;
    right: 7px;
    top: 42%;
    border-top: 5px solid #888;
    border-left: 4px solid transparent;
    border-right: 4px solid transparent;
}
</style>
@endsection

@section('content')
    <section id="ajax-datatable">
        <div class="row">
            @if ($flag == 0)
                <div class="col-md-12 col-12 device_add">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Add Device</h4>
                            <a href="{{ url()->previous() }}" title="Go Back" class="btn btn-primary me-1 waves-effect waves-float waves-light">Back</a>
                        </div>
                        <div class="card-body">

                            <form class="form form-vertical" method="post" id="addDeviceForm">
                                {{ csrf_field() }}
                                <div class="row">
                                    <div class="col-6">
                                        <div class="mb-1">
                                            <label class="form-label" for="device_title">Device Title<span class="text-danger">*</span></label>
                                            <input type="text" id="device_title" class="form-control"
                                                placeholder="Device Title" name="device_title">
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="mb-1">
                                            <label class="form-label" for="plan_id">Select Plan<span class="text-danger">*</span></label>
                                            <select id="plan_id" class="form-select" name="plan_id">
                                                <option value="">--Select--</option>
                                                @if($plan_data)
                                                @foreach(array_reverse($plan_data) as $value)
                                                @if (Auth::user()->user_type == 3)
                                                   @php $price = $value['meta_data']['reseller_credit']; @endphp
                                                @else
                                                    @php $price = $value['price']; @endphp
                                                @endif
                                                <option value="{{$value['id']}}" data-amount="{{$price}}" data-trial_amount="@if(isset($value['trial_amount'])){{$value['trial_amount']}}@endif" data-is_free="@if(isset($value['meta_data']['is_free'])){{$value['meta_data']['is_free']}}@else{{0}}@endif">{{$value['plan_name']}} ( @if($value['billing_cycle']=='onetime'){{$value['currency_symbol']}}{{$price}}/{{'onetime'}}@else {{$value['currency_symbol']}}{{$price}}/{{$value['billing_period_num']}} @if($value['billing_period'] == 'm'){{'month'}}@elseif($value['billing_period'] == 'w'){{'week'}}@else{{'year'}}@endif @endif)</option>
                                                @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="mb-1">
                                            <label class="form-label" for="mac_id">Mac ID</label>
                                            <input type="text" id="mac_id" class="form-control"
                                                placeholder="Mac ID" name="mac_id">
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="mb-1">
                                            <label class="form-label" for="mac_key">Mac Key</label>
                                            <input type="text" id="mac_key" class="form-control"
                                                placeholder="Mac Key" name="mac_key">
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="row">
                                            <div class="col-10">
                                                <label class="form-label" for="playlist_id">Playlist<span class="text-danger">*</span></label>
                                                <select id="playlist_id" class="playlist_id form-select" name="playlist_id[]" multiple="multiple">
                                                    <option disabled>--Select--</option>
                                                    @if($playlist)
                                                    @foreach($playlist as $value)
                                                    <option value="@if(isset($value['id'])){{$value['id']}}@else{{$value['unique_id']}}@endif">{{$value['playlist_name']}}</option>
                                                    @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="col-2">
                                                <div class="mt-2">
                                                    <label class="form-label"></label>
                                                    <a data-bs-toggle="modal" data-bs-target="#addUpdatePlaylistMdl" class="btn btn-primary waves-effect waves-float waves-light addPlaylist" data-playlist_limit="{{$is_limit}}" title="Add New Playlist">
                                                        <i data-feather='plus'></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="mb-1">
                                            <label class="form-label" for="note">Note</label>
                                            <textarea id="note" class="form-control" name="note"></textarea>
                                        </div>
                                    </div>
                                    @if(Auth::user()->user_type == 3)
                                    <div class="col-6">
                                        <div class="mb-1">
                                            <label class="form-label" for="is_code_auto_renew">Is Code Auto Renew</label>
                                            <br>
                                            <span class="btnOn">
                                                <label class="switch">
                                                    <input type="checkbox"  name="is_code_auto_renew" class="is_code_auto_renew" id="is_code_auto_renew"/>
                                                    <span class="slider round"></span>
                                                </label>
                                            </span>
                                        </div>
                                    </div>
                                    @endif
                                    <div class="col-12">
                                        <input type="hidden" id="device_limit" value="{{$setting->device_limit}}">
                                        <input type="hidden" id="free_devices" value="{{$free_devices}}">
                                        <input type="hidden" id="user_credit" value="{{$user->credits}}">
                                        <input type="hidden" id="is_unlimited_credit" value="{{$user->is_unlimited_credit}}">
                                        <input type="hidden" id="user_id" value="{{Auth::user()->user_id}}">
                                        <input type="hidden" id="user_type" value="{{Auth::user()->user_type}}">
                                        <input type="hidden" id="customer_id" value="{{Auth::user()->customer_id}}">
                                        <button style="display:none;" class="btn w-100 btn-primary mt-2 upgradePlan" data-bs-toggle="modal" data-bs-target="#upgradePlanMdl" data-customer_id="{{Auth::user()->customer_id}}" data-user_id="{{Auth::user()->user_id}}" data-plan_id="" data-price="" data-trial_amount="">Upgrade</button>
                                        <button
                                            class="btn btn-primary me-1 waves-effect waves-float waves-light">Submit</button>
                                        <button type="reset" class="btn btn-outline-secondary waves-effect ResetForm">Reset</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
            <div class="col-md-6 col-12 device_code" style="@if(isset($code)){{'display:block;'}}@else{{'display:none;'}}@endif">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <table class="table table-cart">
                                    <tbody valign="middle">
                                        <tr>
                                            <td>
                                                <h4><span data-feather="lock"></span> <strong>Activation code</strong> </h4>
                                                <h2><span id="activation-code"
                                                        class="activation-code-active text-success">@if(isset($code)){{$code}}@endif</span></h2>
                                                <p id="active-message" class="activation-code-active text-success">Code
                                                    generated successfully.</p>
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr id="expiration-view" style="">
                                            <td>
                                                <h4><span data-feather="clock"></span> <strong>Valid Until</strong> <span
                                                        id="expiration-date">@if(isset($expire_date)){{$expire_date}}@endif</span> </h4>
                                                <!-- <p id="expiration-diff">@if(isset($expire_diff)){{$expire_diff}}@endif hours left</p> -->
                                            </td>
                                            <td></td>
                                        </tr>

                                    </tbody>

                                </table>
                            </div>
                        </div>
                        <tr>
                            <a href="{{ url('device/list/0') }}" title="Go Back"
                                class="btn btn-primary me-1 waves-effect waves-float waves-light">Back to
                                Device List</a>
                        </tr>
                    </div>
                </div>
            </div>

        </div>
    </section>
    @include('content/_partials/_modals/modal-add-new-cc')
    @include('content/_partials/_modals/modal-addupdate-playlist')
@endsection

@section('vendor-script')
    {{-- vendor files --}}
    <script src="{{ asset('vendors/js/forms/validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset(mix('vendors/js/forms/validation/additional-methods.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
@endsection

@section('page-script')
<script src="{{ asset(mix('js/scripts/pages/modal-addupdate-playlist.js')) }}"></script>

    {{-- Page js files --}}
    <script>
        jQuery(document).ready(function () {
            $(".playlist_id").wrap('<div class="position-relative"></div>');
            $(".playlist_id").select2({
                dropdownAutoWidth: true,
                maximumSelectionLength: 10,
                width: '100%',
                dropdownParent: $(".playlist_id").parent()
            });
            $(document).on('change', '#dns_id', function(){
                if($(this).val()){
                    $("#dns").val("");
                    $("#dns").attr('disabled','disabled');
                }else{
                    $("#dns").removeAttr('disabled');
                }
            });
            $(document).on('keyup', '#dns', function(){
                if($(this).val()){
                    $("#dns_id").val("");
                    $("#dns_id").attr('disabled','disabled');
                }else{
                    $("#dns_id").removeAttr('disabled');
                }
            });
        });
        $(function() {
            ('use strict');
            $(document).on('click',".ResetForm",function(){
                $(".playlist_id").select2("destroy").val('').select2();
                validator.resetForm();
                $('#addDeviceForm').find(".error").removeClass("error");
            });
            $(".device_code").hide();

            jQuery.validator.addMethod("specialChars", function( value, element ) {
                var regex = new RegExp("^[a-zA-Z0-9]+$");
                var key = value;

                if (!regex.test(key)) {
                return false;
                }
                return true;
            }, "please use only alphanumeric or alphabetic characters");

           var validator = $('#addDeviceForm').validate({
                rules: {
                    'device_title': {
                        required: true
                    },
                    'duration': {
                        required: true
                    },
                    'plan_id': {
                        required: true
                    },
                    'mac_id':{
                        nowhitespace: true,
                        alphanumeric: true
                    },
                    'mac_key':{
                        nowhitespace: true,
                        alphanumeric: true
                    },
                    'playlist_id[]': {
                        required: true
                    },
                },
                messages: {
                    'device_title': {
                        required: '*Please Enter Device Title'
                    },
                    'duration': {
                        required: '*Please Select Duration'
                    },
                    'plan_id': {
                        required: '*Please Select Plan'
                    },
                    'mac_id': {
                        nowhitespace: 'Please Remove Space'
                    },
                    'mac_key': {
                        nowhitespace: 'Please Remove Space'
                    },
                    'playlist_id': {
                        required: '*Please Select Playlist'
                    },
                }
            });
            $(document).on('submit', '#addDeviceForm', function(e) {
                e.preventDefault();
                var device_limit = $("#device_limit").val();
                var free_devices = $("#free_devices").val();

                var credits = $("#user_credit").val();
                var is_unlimited_credit = $("#is_unlimited_credit").val();
                var plan_amount = $("#plan_id").find(':selected').attr('data-amount');
                var is_free = $("#plan_id").find(':selected').attr('data-is_free');
                var is_lifetime = $("#plan_id").find(':selected').attr('data-is_lifetime');
                var trial_amount = $("#plan_id").find(':selected').attr('data-trial_amount');
                var plan_id = $("#plan_id").find(':selected').val();
                $(".upgradePlan").attr('data-plan_id',plan_id);
                $(".upgradePlan").attr('data-price',plan_amount);
                $(".upgradePlan").attr('data-trial_amount',trial_amount);
                var user_id = $('#user_id').val();
                var auth_user_id = $(this).find('#auth_user_id').val();
                if(is_unlimited_credit == 1){
                    var html = 'Your Credit is : <b>Unlimted</b>';
                    html += '<br>Your Plan Total Amount : <b>{{env('CURRENCY')}}'+parseInt(plan_amount)+'</b>';
                    Swal.fire({
                        title: 'Unlimted Credits',
                        html: html,
                        icon: 'warning',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Submit',
                    }).then((result) => {
                        if (result.isConfirmed == true) {
                            addDevice(1);
                        }else{
                            return false;
                        }
                    })
                }else if(plan_amount > 0 && parseInt(credits) >= parseInt(plan_amount)){
                    var remainig_credits = parseInt(credits)-parseInt(plan_amount);
                    var html = 'Your Total Credit : <b>'+parseInt(credits)+'</b>';
                    html += '<br>Your Plan Total Amount : <b>{{env('CURRENCY')}}'+parseInt(plan_amount)+'</b>';
                    html += '<br>Your Remaining Credit after Purchase this Plan : <b>'+remainig_credits+'</b>';
                    Swal.fire({
                        title: 'Remaining Credits',
                        html: html,
                        icon: 'warning',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Submit',
                    }).then((result) => {
                        if (result.isConfirmed == true) {
                            addDevice(1);
                        }else{
                            return false;
                        }
                    })
                }else if(plan_amount == 0 && is_free == 1){
                    if(parseInt(free_devices) >= parseInt(device_limit)){
                        var html = 'Your Free Plan Device Limit is Over. Please Select another Plan';
                        Swal.fire({
                            title: 'Limit Reached',
                            html: html,
                            icon: 'warning',
                            showCancelButton: false,
                            cancelButtonColor: '#d33',
                            cancelButtonText: 'Ok',
                        }).then((result) => {
                            if (result.isConfirmed == true) {
                                return true;
                            }
                        })
                    }else{
                        if($("#user_type").val() == 4){
                            var html = 'Your don\'t have enough Credit to Purchase this Plan';
                            html += '<br>Your Total Credit : <b>'+parseInt(credits)+'</b>';
                            html += '<br>Your Plan Total Amount : <b>{{env('CURRENCY')}}'+parseInt(plan_amount)+'</b>';
                            Swal.fire({
                                title: 'Not Enough Credits',
                                html: html,
                                icon: 'warning',
                                showconfirmButton: false,
                                cancelButtonColor: '#d33',
                            })

                        }else{
                            $(".upgradePlan").click();
                        }
                    }
                }else{
                    if($("#user_type").val() == 4 || $("#user_type").val() == 3){
                        var html = 'Your don\'t have enough Credit to Purchase this Plan';
                        html += '<br>Your Total Credit : <b>'+parseInt(credits)+'</b>';
                        html += '<br>Your Plan Total Amount : <b>{{env('CURRENCY')}}'+parseInt(plan_amount)+'</b>';
                        if($("#user_type").val() == 3){
                            html += '<br>Purchase More Credit by click on button';
                            html += '<br><br><a href="{{route('credits/list')}}" title="Credit" class="btn btn-primary me-1 waves-effect waves-float waves-light">Purchase Credits</a>';
                        }
                        Swal.fire({
                            title: 'Not Enough Credits',
                            html: html,
                            icon: 'warning',
                            showConfirmButton: false,
                            showcancelButton: false,
                        })

                    }else{
                        $(".upgradePlan").click();
                    }
                }
            });

            $('#upgradePlanMdl').on('hidden.bs.modal', function(e) {
                $("#upgradePlanForm")[0].reset();
                $('.plan_id').val("");
                $('.user_id').val("");
                $('.customer_id').val("");
                $('.price').val("");
                $('.trial_amount').val("");
                var validator = $("#upgradePlanForm").validate();
                validator.resetForm();
            });

            $(document).on("click", ".upgradePlan", function() {
                $('.user_id').val($(this).attr('data-user_id'));
                $('.plan_id').val($(this).attr('data-plan_id'));
                $('.customer_id').val($(this).attr('data-customer_id'));
                $('.price').val($(this).attr('data-price'));
                $('.trial_amount').val($(this).attr('data-trial_amount'));
                var credits = $("#user_credit").val();
                var plan_amount = $("#plan_id").find(':selected').attr('data-amount');
                var html = 'Your Total Credit <b>'+credits+'</b> is less than your plan amount <b>{{env('CURRENCY')}}'+parseInt(plan_amount)+'</b>';
                $(".credits_div").html(html);
                var plan_id = $(this).attr('data-plan_id');
                var customer_id = $(this).attr('data-customer_id');
                var formdata = new FormData($("#addDeviceForm")[0]);
                formdata.append('plan_id',plan_id);
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('getCheckoutLink') }}",
                    type: "POST",
                    data: formdata,
                    dataType: "json",
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(data) {
                        $(".checkout_link").html('<a class="btn btn-primary me-1 mt-1" href="'+data.data[0].checkout_page+'?customer_id='+customer_id+'">Click here to subscribe</a>');
                    },
                    error: function(data) {
                    }
                });
            });

            $('#upgradePlanForm').validate({
                rules: {
                    'coupon_code': {
                        required: true,
                        remote: {
                            url: '{{ route("CheckCouponCode") }}',
                            type: "post",
                            data: {
                                coupon_code: function () { return $("#coupon_code").val(); },
                                plan_id: function () { return $("#plan_id").val(); },
                            }
                        }
                    },
                },
                messages: {
                    'coupon_code': {
                        required: '*Please Enter Coupon Code',
                        remote: "Coupon Code is not valid for this plan"
                    },
                }
            });


            $(document).on('submit', '#upgradePlanForm', function (e) {
                e.preventDefault();
                addDevice();
            });

            function addDevice(credit_history_id=0){
                var formdata = new FormData($("#addDeviceForm")[0]);
                formdata.append('flag',1);
                var user_id = $('#user_id').val();
                $.ajax({
                    url: "{{ route('device.store') }}",
                    type: 'POST',
                    data: formdata,
                    dataType: "json",
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(data) {
                        $('.loader').hide();
                        if (data.success == 1) {
                            $(".device_id").val(data.device_id);
                            // $(".device_code").show();
                            // $(".device_add").hide();
                            // $("#activation-code").text(data.code);
                            $('#upgradePlanMdl').modal('hide');
                            if(credit_history_id==0){
                                var formdata1 = new FormData($("#upgradePlanForm")[0]);
                                $.ajax({
                                    url: "{{ route('upgradePlan') }}",
                                    type: "POST",
                                    data: formdata1,
                                    dataType: "json",
                                    contentType: false,
                                    cache: false,
                                    processData: false,
                                    success: function(data3) {
                                        if (data3.success == 1) {
                                            window.location.href = '{{url("device/list")}}/'+data.device_id;
                                            toastr.success('Device created.');
                                        }else{
                                            toastr.error(data3.message);
                                        }
                                    },
                                    error: function(data3) {

                                    }
                                });
                            }else{
                                if(credit_history_id==1){
                                    var credits = $("#user_credit").val();
                                    var is_unlimited_credit = $("#is_unlimited_credit").val();
                                    var plan_amount = $("#plan_id").find(':selected').attr('data-amount');
                                    var plan_id = $("#plan_id").find(':selected').val();
                                    var user_id = $('#user_id').val();
                                    var auth_user_id = $(this).find('#auth_user_id').val();
                                    var remainig_credits = 0;
                                    if(is_unlimited_credit == 0){
                                        var remainig_credits = parseInt(credits)-parseInt(plan_amount);
                                    }

                                    $.ajax({
                                        headers: {
                                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                        },
                                        url: '{{ route("updateUserCredits") }}',
                                        type: 'POST',
                                        data: {'flag':0,'credits':remainig_credits,'is_unlimited_credit':is_unlimited_credit,'plan_amount':plan_amount,'user_id':user_id,'auth_user_id':user_id,'plan_id':plan_id,'device_id':data.device_id},
                                        dataType: "json",
                                        cache: false,
                                        success: function (data1) {
                                            if (data1.success == 1) {
                                                var plan_id = $("#plan_id").find(':selected').val();
                                                var customer_id = $("#customer_id").val();
                                                $.ajax({
                                                    url: "{{ route('upgradePlan') }}",
                                                    type: "POST",
                                                    data: {'flag':1,'user_id':user_id,'plan_id':plan_id,'device_id':data.device_id,'customer_id':customer_id,'is_code_auto_renew':$("#is_code_auto_renew").val()},
                                                    dataType: "json",
                                                    cache: false,
                                                    success: function(data2) {
                                                        if (data2.success == 1) {
                                                            window.location.href = '{{url("device/list")}}/'+data.device_id;
                                                            toastr.success('Device created.');
                                                        }else{
                                                            toastr.error(data2.message);
                                                        }
                                                    },
                                                    error: function(data2) {

                                                    }
                                                });

                                            } else {
                                                toastr.error(data1.message);
                                            }
                                        },
                                        error: function (jqXHR, textStatus, errorThrown) {
                                            alert(errorThrown);
                                        }
                                    });
                                }

                            }
                            // window.location.href = "{{ route('device/list') }}";
                        } else {
                            toastr.error(data.message);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        alert(errorThrown);
                    }
                });
            }

            $(document).on('submit', '#addUpdatePlaylistForm', function (e) {
                e.preventDefault();
                var formdata = new FormData($("#addUpdatePlaylistForm")[0]);
                if($("#hide_playlist_id").val()){
                    var url = '{{ route("playlistUpdate") }}';
                }else{
                    var url = '{{ route("playlist.store") }}';
                }
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formdata,
                    dataType: "json",
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function (data) {
                        $('.loader').hide();
                        $('#addUpdatePlaylistMdl').modal('hide');
                        if (data.success == 1) {
                            toastr.success(data.message);
                            $('.playlist_id').append($("<option></option>").attr("value", data.playlist.id).text(data.playlist.playlist_name));
                            var selectedItems = $(".playlist_id").select2("val");
                            if((selectedItems).length > 0){
                                selectedItems.push(data.playlist.id);
                                $(".playlist_id").val(selectedItems).trigger('change');;
                            }else{
                                var selectedItems = [];
                                selectedItems.push(data.playlist.id);
                                $(".playlist_id").val(selectedItems).trigger('change');;
                            }
                            $(".addPlaylist").attr('data-playlist_limit',data.limit);
                        } else {
                            toastr.error(data.message);
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        alert(errorThrown);
                    }
                });
            });

        });
    </script>
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
@endsection
