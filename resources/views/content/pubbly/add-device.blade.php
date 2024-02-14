@extends('layouts/fullLayoutMaster')

@section('title', 'Payment Page')
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
    .auth-wrapper.auth-basic .auth-inner {
        max-width: 800px;
    }
    .auth-wrapper{
      background-image: url('images/background.png');
      background-repeat: no-repeat;
      background-size: 100% 100%;
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

        <p class="card-text mb-2 text-center">Smart TVs and supported devices can be activated after a payment for each TV/device. </p>
        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6">
                <form id="addDeviceForm" method="post" class="form form-horizontal">
                {{csrf_field()}}
                <div class="row">
                    <div class="col-12">
                    <div class="mb-1 row">
                        <div class="col-sm-3">
                        <label class="col-form-label" for="mac_id">Mac ID</label>
                        </div>
                        <div class="col-sm-9">
                        <input type="text" id="mac_id" class="form-control" name="mac_id" placeholder="Mac ID" />
                        </div>
                    </div>
                    </div>
                    <div class="col-12">
                    <div class="mb-1 row">
                        <div class="col-sm-3">
                        <label class="col-form-label" for="mac_key">Mac Key</label>
                        </div>
                        <div class="col-sm-9">
                        <input type="text" id="mac_key" class="form-control" name="mac_key" placeholder="Mac Key" />
                        </div>
                    </div>
                    </div>
                    <div class="col-12">
                    <div class="mb-1 row">
                        <div class="col-sm-3">
                        <label class="col-form-label" for="email">Email</label>
                        </div>
                        <div class="col-sm-9">
                        <input type="email" id="email" class="form-control" name="email" placeholder="Email" />
                        </div>
                    </div>
                    </div>

                    <div class="col-12">
                    <div class="mb-1 row">
                        <div class="col-sm-3">
                        <label class="col-form-label" for="plan_id">Select Plan</label>
                        </div>
                        <div class="col-sm-9">
                        <select id="plan_id" class="form-select" name="plan_id">
                            <option value="">--Select--</option>
                            @if($plan_data)
                            @foreach(array_reverse($plan_data) as $value)
                            <option value="{{$value['id']}}" data-amount="{{$value['price']}}" data-trial_amount="@if(isset($value['trial_amount'])){{$value['trial_amount']}}@endif">{{$value['plan_name']}}</option>
                            @endforeach
                            @endif
                        </select>
                        </div>
                    </div>
                    </div>
                  
                    <div class="col-sm-9 offset-sm-2">
                    <div class="mb-1">
                        <div class="form-check">
                        {!! NoCaptcha::renderJs('en', false, 'recaptchaCallback') !!}
                        {!! NoCaptcha::display() !!}
                        </div>
                        <input type="hidden" class="hiddenRecaptcha required" name="hiddenRecaptcha" id="hiddenRecaptcha">

                    </div>
                    </div>
                    <div class="col-sm-12 offset-sm-3">
                        <button class="btn btn-primary me-1 col-md-7" id="btnPay">Pay with Pabbly</button>
                        <a class="btn btn-primary me-1" href="{{url('login')}}">Login</a>

                    </div>
     
                </div>
                </form>
            </div>
        <div class="col-md-3"></div>

        </div>

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
        $('#hiddenRecaptcha-error').hide();
    });

    $(document).on('submit', '#addDeviceForm', function(e) {
        e.preventDefault();
        var formdata = new FormData($("#addDeviceForm")[0]);
        $("#btnPay").attr('disabled',true);
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ route('getCheckoutLinkForActivation') }}",
            type: "POST",
            data: formdata,
            dataType: "json",
            contentType: false,
            cache: false,
            processData: false,
            success: function(data) {
                window.location.href = data.data[0].checkout_page+'?customer_id='+data.customer_id;
                $("#btnPay").attr('disabled',false);

            },
            error: function(data) {
            }
        });
    });
});


</script>
<script src="{{asset(mix('js/scripts/pages/add-device.js'))}}"></script>
<script src="{{ asset(mix('js/scripts/extensions/ext-component-toastr.js')) }}"></script>
@endsection
