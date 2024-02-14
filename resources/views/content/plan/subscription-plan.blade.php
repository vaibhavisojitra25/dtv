
@extends('layouts/contentLayoutMaster')

@section('title', 'Pricing')

@section('page-style')
{{-- Page Css files --}}
<link rel="stylesheet" type="text/css" href="{{asset('css/base/pages/page-pricing.css')}}">
<style>
  .plan_description{
    text-align: left;
    line-height: 33px;
    /* display: block; */
    /* padding: 0.75rem 1.25rem; */
    color: #050406;
  }
  .plan_description img{
    width: 100%;
  }
</style>
@endsection

@section('content')
<section id="pricing-plan">
  <!-- title text and switch button -->
  @if (Auth::user()->user_type != 1)
  <div class="d-flex justify-content-end">
    <a href="{{ url('credits/list') }}" title="Go Back" class="btn btn-primary me-1 waves-effect waves-float waves-light">Add Credits</a>
  </div>
  @endif
  @if (Auth::user()->user_type == 1)
  <div class="d-flex justify-content-end">
          <a href="{{route('plan.create')}}" class="btn btn-primary waves-effect waves-float waves-light">
              Create New Plan
          </a>
      </div>
      @endif
  <div class="text-center">
    <h1 class="mt-5">Pricing Plans</h1>


    <!-- <div class="d-flex align-items-center justify-content-center mb-5 pb-50">
      <h6 class="me-1 mb-0">Monthly</h6>
      <div class="form-check form-switch">
        <input type="checkbox" class="form-check-input" id="planSwitch" />
        <label class="form-check-label" for="planSwitch"></label>
      </div>
      <h6 class="ms-50 mb-0">Annually</h6>
    </div> -->
  </div>
  <!--/ title text and switch button -->

  <!-- pricing plan cards -->
  <div class="row pricing-card">
    <div class="col-12 col-sm-offset-2 col-sm-10 col-md-12 col-lg-offset-2 col-lg-10 mx-auto">
      <div class="row">
        <!-- basic plan -->

        @foreach ($planData as $plan)
        <div class="col-12 col-md-4 monthlyplan">

        <form class="form form-vertical" method="post" id="upgradePlanForm">
        {{ csrf_field() }}
          <div class="card basic-pricing text-center">
            <div class="card-body">
              <img src="{{asset('images/illustration/Pot1.svg')}}" class="mb-2 mt-5" alt="svg img" />
              <h3>{{$plan['plan_name']}}</h3>
              <!-- <p class="card-text">Credit Limit:</p> -->
              <div class="annual-plan">
                <div class="plan-price mt-2">
                @if(isset($plan['meta_data']['reseller_credit']))
                <span class="pricing-basic-value fw-bolder text-primary">{{$plan['meta_data']['reseller_credit']}} Credit</span>

                @else
                  <sup class="font-medium-1 fw-bold text-primary">{{env('CURRENCY')}}</sup>
                  <span class="pricing-basic-value fw-bolder text-primary">{{$plan['price']}}</span>
                  @endif
                  <!-- <sub class="pricing-duration text-body font-medium-1 fw-bold">/@if($plan['billing_period'] == 'm'){{'month'}}@elseif($plan['billing_period'] == 'w'){{'week'}}@else{{'year'}}@endif</sub> -->
                </div>
                <small class="annual-pricing d-none text-muted"></small>
              </div>
              @if($plan['trial_period'] > 0)
              <p class="card-text">{{$plan['trial_period']}} {{$plan['trial_type']}} FREE trial!</p>
              @endif
              <div class="plan_description">
                {!! $plan['plan_description'] !!}
              </div>
              @if($plan['is_purchased'] == 0 && isset($device_id))
                  <!-- <button class="btn w-100 btn-primary mt-2 upgradePlan" data-bs-toggle="modal" data-bs-target="#upgradePlanMdl" data-device_id="{{$device_id}}" data-customer_id="{{$customer_id}}" data-plan_id="{{$plan['id']}}" data-price="{{$plan['price']}}" data-trial_amount="@if(isset($plan['trial_amount'])){{$plan['trial_amount']}}@endif">Upgrade</button> -->

                  <input type="hidden" id="device_limit" value="{{$setting->device_limit}}">
                  <input type="hidden" id="free_devices" value="{{$free_devices}}">
                  <input type="hidden" id="user_credit" value="{{$user->credits}}">
                  <input type="hidden" id="user_id" name="user_id" value="{{$user->user_id}}">
                  <input type="hidden" id="auth_user_id" name="auth_user_id" value="{{Auth::user()->user_id}}">
                  <input type="hidden" id="user_type" name="user_type" value="{{$user->user_type}}">
                  <input type="hidden" id="customer_id" name="customer_id" value="{{$user->customer_id}}">
                  <input type="hidden" id="plan_id" name="plan_id" value="{{$plan['id']}}">
                  <input type="hidden" id="device_id" name="device_id" value="{{$device_id}}">
                  <input type="hidden" id="plan_amount" name="plan_amount" value="{{$plan['price']}}">
                  <input type="hidden" id="trial_amount" name="trial_amount" value="@if(isset($plan['trial_amount'])){{$plan['trial_amount']}}@endif">

                  <button style="display:none;" class="btn w-100 btn-primary mt-2 upgradePlan" data-bs-toggle="modal" data-bs-target="#upgradePlanMdl" data-device_id="{{$device_id}}" data-customer_id="{{$customer_id}}" data-plan_id="{{$plan['id']}}" data-user_id="{{$user->user_id}}" data-price="{{$plan['price']}}"  data-trial_amount="@if(isset($plan['trial_amount'])){{$plan['trial_amount']}}@endif">Upgrade</button>
                  @if( (array_key_exists("is_free",$plan['meta_data']) && $plan['meta_data']['is_free'] != 1) ||(!array_key_exists("is_free",$plan['meta_data']) ))                  <button class="btn btn-primary me-1 waves-effect waves-float waves-light">Upgrade</button>
                  @endif

              @endif
            </div>
          </div>
        </form>
        </div>

        @endforeach

        <!--/ basic plan -->

</section>
@include('content/_partials/_modals/modal-add-new-cc')
@endsection
@section('vendor-script')
{{-- vendor files --}}
<script src="{{asset('vendors/js/forms/validation/jquery.validate.min.js')}}"></script>
<script src="{{ asset(mix('vendors/js/forms/validation/additional-methods.min.js')) }}"></script>
<script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
@endsection

@section('page-script')
{{-- Page js files --}}
<script src="{{asset('js/scripts/pages/page-pricing.js')}}"></script>
<script>
   $(document).ready(function() {

    $(".yearlyplan").hide();
    $(document).on("click", "#planSwitch", function() {
        if($(this).is(":checked")) {
            $(".yearlyplan").show();
            $(".monthlyplan").hide();
        } else {
            $(".yearlyplan").hide();
            $(".monthlyplan").show();
        }
    });

    $(document).on('submit', '#upgradePlanForm', function(e) {
      e.preventDefault();
      var device_limit = $(this).find("#device_limit").val();
      var free_devices = $(this).find("#free_devices").val();

      var credits = $(this).find("#user_credit").val();
      var plan_amount = $(this).find("#plan_amount").val();
      var trial_amount = $(this).find("#trial_amount").val();
      var plan_id = $(this).find("#plan_id").val();
      var user_id = $(this).find('#user_id').val();
      var auth_user_id = $(this).find('#auth_user_id').val();
      $form = $(this);

      if(plan_amount > 0 && parseInt(credits) >= parseInt(plan_amount)){
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
                  $.ajax({
                      headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      },
                      url: '{{ route("updateUserCredits") }}',
                      type: 'POST',
                      data: {'flag':0,'credits':remainig_credits,'plan_amount':plan_amount,'user_id':user_id,'auth_user_id':auth_user_id,'plan_id':plan_id},
                      dataType: "json",
                      cache: false,
                      success: function (data) {
                          if (data.success == 1) {
                            console.log($form[0]);
                            var formdata1 = new FormData($form[0]);
                            $.ajax({
                                url: "{{ route('upgradePlan') }}",
                                type: "POST",
                                data: formdata1,
                                dataType: "json",
                                contentType: false,
                                cache: false,
                                processData: false,
                                success: function(data) {
                                    if (data.success == 1) {
                                        window.location.href = '{{url("device/list")}}';
                                        toastr.success('Plan upgraded.');
                                    }else{
                                        toastr.error(data.message);
                                    }
                                },
                                error: function(data) {

                                }
                            });

                          } else {
                              toastr.error(data.message);
                          }
                      },
                      error: function (jqXHR, textStatus, errorThrown) {
                          alert(errorThrown);
                      }
                  });
              }else{
                  return false;
              }
          })
      }else if(plan_amount == 0){
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
                $(this).find(".upgradePlan").click();
              }
          }
      }else{
          if($(this).find("#user_type").val() == 4){
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
            $(this).find(".upgradePlan").click();
          }
      }
  });

    $('#upgradePlanMdl').on('hidden.bs.modal', function(e) {
        $("#upgradePlanForm")[0].reset();
        $('.plan_id').val("");
        $('.device_id').val("");
        $('.customer_id').val("");
        $('.price').val("");
        $('.trial_amount').val("");
        var validator = $("#upgradePlanForm").validate();
        validator.resetForm();
    });

    $(document).on("click", ".upgradePlan", function() {
        $('.plan_id').val($(this).attr('data-plan_id'));
        $('.device_id').val($(this).attr('data-device_id'));
        $('.customer_id').val($(this).attr('data-customer_id'));
        $('.price').val($(this).attr('data-price'));
        $('.trial_amount').val($(this).attr('data-trial_amount'));
        var plan_id = $(this).attr('data-plan_id');
        var device_id = $(this).attr('data-device_id');
        var customer_id = $(this).attr('data-customer_id');
        $.ajax({
              url: "{{ route('getCheckoutLink') }}",
              type: "POST",
              data: {'flag':1,plan_id:plan_id,device_id:device_id,customer_id:customer_id},
              dataType: "json",
              cache: false,
              success: function(data) {
                $(".checkout_link").html('<a class="btn btn-primary me-1 mt-1" href="'+data.data[0].checkout_page+'?device_id='+device_id+'&customer_id='+customer_id+'" >Click here to subscribe</a>');
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
