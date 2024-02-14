@extends('layouts/contentLayoutMaster')

@section('title', 'Billing & Plans')

@section('vendor-style')
  <!-- vendor css files -->
  <link rel='stylesheet' href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
  <link rel='stylesheet' href="{{ asset(mix('vendors/css/animate/animate.min.css')) }}">
  <link rel='stylesheet' href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">
  <link rel='stylesheet' href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
  <link rel='stylesheet' href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap5.min.css')) }}">
  <link rel='stylesheet' href="{{ asset(mix('vendors/css/tables/datatable/buttons.bootstrap5.min.css')) }}">
  <link rel='stylesheet' href="{{ asset(mix('vendors/css/tables/datatable/rowGroup.bootstrap5.min.css')) }}">
@endsection
@section('page-style')
  <!-- Page css files -->
  <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-sweet-alerts.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-validation.css')) }}">
@endsection

@section('content')
<section class="app-user-view-account">
<div class="row">
  <div class="col-12">
    <ul class="nav nav-pills mb-2">
      <!-- Account -->
      <li class="nav-item">
        <a class="nav-link" href="{{asset('my-profile')}}">
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
      @if(Auth::user()->user_type == 2)
      <!-- billing and plans -->
      <li class="nav-item">
        <a class="nav-link active" href="{{asset('account-settings-billing')}}">
          <i data-feather="bookmark" class="font-medium-3 me-50"></i>
          <span class="fw-bold">Billings &amp; Plans</span>
        </a>
      </li>
    @endif
 
    </ul>

    <!-- billing and plans  -->

    <!-- current plan -->
    <div class="row">
      @if($userSubscription)
      <div class="col-md-6">
        <div class="card  border-success">
          <div class="card-header border-bottom">
            <h4 class="card-title">Current plan</h4>
          </div>
          <div class="card-body my-2 py-25">
            <div class="row">
              <div class="col-md-12">

              <div class="d-flex justify-content-between align-items-start">
                <span class="badge bg-light-primary" style="font-size: 18px;">{{$userSubscription['plan']['plan_name']}}</span>
                <div class="d-flex justify-content-center">
                  <sup class="h5 pricing-currency text-primary mt-1 mb-0">{{$userSubscription['currency_symbol']}}</sup>
                  <span class="fw-bolder display-5 mb-0 text-primary">{{$userSubscription['amount']}}</span>
                  <sub class="pricing-duration font-small-4 ms-25 mt-auto mb-2">/@if($userSubscription['plan']['billing_period']=='m'){{'Month'}}@else{{'Year'}}@endif</sub>
                </div>
              </div>
                    
                @if( $userSubscription['status'] =='trial' )
                  <div class="mb-2 pb-30">
                  <p class="card-text">{{$userSubscription['trial_days']}} {{$userSubscription['plan']['trial_type']}} FREE trial!</p>
                  </div>
                @endif


                <div class="mb-2 pb-30">
                    @if($userSubscription['activation_date'])
                      Activate at <strong>{{\Carbon\Carbon::parse($userSubscription['activation_date'])->format('M d, Y')}}</strong>
                    @endif
                  </div>

                <div class="mb-2 pb-30">
                  @if($userSubscription['next_billing_date'])
                  Next Billing Date <strong>{{\Carbon\Carbon::parse($userSubscription['next_billing_date'])->format('M d, Y')}}</strong>
                  @else
                  Expire at <strong>{{\Carbon\Carbon::parse($userSubscription['expiry_date'])->format('M d, Y')}}</strong>
                  @endif
                </div>
                @if( $userSubscription['status'] =='trial' )
                  <div class="plan-statistics pt-1">
                    <div class="d-flex justify-content-between">
                      <h5 class="fw-bolder">Devices</h5>
                      <h5 class="fw-bolder">{{$trial_remain_device}} of {{$userSubscription['trial_device_limit']}} </h5>
                    </div>
                    <div class="progress">
                    <?php $percentage = ($userSubscription['trial_remain_device_limit'] / $userSubscription['trial_device_limit']) * 100?>
                    <div
                      class="progress-bar" style="width:{{$percentage}}%"
                      role="progressbar"
                      aria-valuenow="{{$userSubscription['trial_remain_device_limit']}}"
                      aria-valuemin="0"
                      aria-valuemax="100"
                    ></div>
                    </div>
                    <p class="mt-50">{{$trial_remain_device}} device remaining</p>
                  </div>
                @else
                <div class="plan-statistics pt-1">
                    <div class="d-flex justify-content-between">
                      <h5 class="fw-bolder">Devices</h5>
                      <h5 class="fw-bolder">{{$remain_device}} of {{$userSubscription['device_limit']}} </h5>
                    </div>
                    <div class="progress">
                    <?php $percentage = ($userSubscription['remaining_device_limit'] / $userSubscription['device_limit']) * 100?>
                    <div
                      class="progress-bar" style="width:{{$percentage}}%"
                      role="progressbar"
                      aria-valuenow="{{$userSubscription['remaining_device_limit']}}"
                      aria-valuemin="0"
                      aria-valuemax="100"
                    ></div>
                    </div>
                    <p class="mt-50">{{$remain_device}} device remaining</p>
                  </div>
                @endif
                <div class="col-12">
                <a class="btn btn-primary me-1 mt-1" href="{{ route('subscription/plan')}}">
                    Upgrade Plan </a>
                  <!-- <button class="btn btn-primary me-1 mt-1" data-bs-toggle="modal" data-bs-target="#pricingModal">
                    Upgrade Plan
                  </button> -->
                  @if(empty($userUpcomingSubscription->canceled_date))
                  <button class="btn btn-outline-danger cancelSubscription mt-1" data-subscription_id="@if($userSubscription){{$userSubscription['subscription_id']}}@endif">Cancel Subscription</button>
                  @else
                  <p class="mt-50">You have canceled your current subscription plan</p>
                  @endif
              </div>
              </div>
              
            </div>
        </div>
      </div>
      </div>
      @endif
      @if(!empty($userUpcomingSubscription['data']))
      <div class="col-md-6">
        <div class="card border-primary">
          <div class="card-header border-bottom">
            <h4 class="card-title">Scheduled Details</h4>
          </div>
          <div class="card-body my-2 py-25">
            <div class="row">
              <div class="col-md-12">

                <div class="d-flex justify-content-between align-items-start">
                  <span class="badge bg-light-primary" style="font-size: 18px;">{{$userUpcomingSubscription['data']['new_plan']['plan_name']}}</span>
                  <div class="d-flex justify-content-center">
                    <sup class="h5 pricing-currency text-primary mt-1 mb-0">{{$userUpcomingSubscription['data']['currency_symbol']}}</sup>
                    <span class="fw-bolder display-5 mb-0 text-primary">{{$userUpcomingSubscription['data']['new_plan']['price']}}</span>
                    <sub class="pricing-duration font-small-4 ms-25 mt-auto mb-2">/@if($userUpcomingSubscription['data']['new_plan']['billing_period']=='m'){{'Month'}}@else{{'Year'}}@endif</sub>
                  </div>
                </div>
                @if( $userUpcomingSubscription['data']['new_plan']['trial_period'] > 0 )
                  <div class="mb-2 pb-30">
                  <p class="card-text">{{$userUpcomingSubscription['data']['new_plan']['trial_days']}} {{$userUpcomingSubscription['data']['new_plan']['plan']['trial_type']}} FREE trial!</p>
                  </div>
                @endif
                <div class="mb-2 pb-30">
                  Scheduled at <strong>{{\Carbon\Carbon::parse($userUpcomingSubscription['data']['scheduled_date'])->format('M d, Y')}}</strong>
                </div>

                <div class="plan-statistics pt-1">
                  <div class="d-flex justify-content-between">
                    <h5>Devices Limit <strong>{{$userUpcomingSubscription['data']['new_plan']['meta_data']['device_limit']}}</strong></h5>
                  </div>
                  <!-- <div class="progress">
                  <?php //$percentage = ($userUpcomingSubscription['data']['new_plan']['meta_data']['device_limit'] / $userUpcomingSubscription['data']['new_plan']['meta_data']['device_limit']['meta_data']['device_limit']) * 100?>
                    <div
                      class="progress-bar" style="width:{{$percentage}}%"
                      role="progressbar"
                      aria-valuenow="{{$userUpcomingSubscription['data']['new_plan']['meta_data']['device_limit']}}"
                      aria-valuemin="0"
                      aria-valuemax="100"
                    ></div>
                  </div>
                  <p class="mt-50">{{$userUpcomingSubscription['data']['new_plan']['meta_data']['device_limit']}} device remaining until your plan requires update</p> -->
                </div>
              </div>
              
            </div>
        </div>
      </div>
      @endif
      @if(empty($userSubscription))
      <div class="card border-success">
        <div class="card-header border-bottom">
          <h4 class="card-title">Current plan</h4>
        </div>
        <div class="card-body my-2 py-25">
          <div class="row">
            <div class="col-md-6">

            <div class="d-flex justify-content-between align-items-start">
                <span class="badge bg-light-primary" style="font-size: 18px;">Free</span>
                <span>A simple start for everyone</span>
              </div>

              <div class="plan-statistics pt-1">
                <div class="d-flex justify-content-between">
                  <h5 class="fw-bolder">Devices</h5>
                  <h5 class="fw-bolder">{{$remain_device}} of 1</h5>
                </div>
                <div class="progress">
               <?php $percentage = ($remain_device / 1) * 100?>
                  <div
                  class="progress-bar" style="width:{{$percentage}}%"
                      role="progressbar"
                      aria-valuenow="{{$remain_device}}"
                      aria-valuemin="0"
                      aria-valuemax="100"
                  ></div>
                </div>
                <p class="mt-50">{{$remain_device}} device remaining until your plan requires update</p>
              </div>
            </div>
            <div class="col-md-6">
              <div class="alert alert-warning mb-2" role="alert">
                <h6 class="alert-heading">We need your attention!</h6>
                <div class="alert-body fw-normal">your plan requires update</div>
              </div>
            </div>
            <div class="col-12">
              <a class="btn btn-primary me-1 mt-1" href="{{ route('subscription/plan')}}">
                  Upgrade Plan </a>
                <!-- <button class="btn btn-primary me-1 mt-1" data-bs-toggle="modal" data-bs-target="#pricingModal">
                  Upgrade Plan
                </button> -->
                <!-- <button class="btn btn-outline-danger cancelSubscription mt-1">Cancel Subscription</button> -->
            </div>
          </div>
        </div>
      </div>
      @endif
      </div>
    <!-- / current plan -->

    <!--/ billing and plans -->
  </div>
</div>
</section>
@include('content/_partials/_modals/modal-pricing')
@include('content/_partials/_modals/modal-edit-cc')
@endsection

@section('vendor-script')
  <!-- vendor files -->
  <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/forms/validation/jquery.validate.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/forms/validation/additional-methods.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/forms/cleave/cleave.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/forms/cleave/addons/cleave-phone.us.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/extensions/moment.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/jquery.dataTables.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.bootstrap5.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.responsive.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/responsive.bootstrap5.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.buttons.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/jszip.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/pdfmake.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/vfs_fonts.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.html5.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.print.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.rowGroup.min.js')) }}"></script>
@endsection
@section('page-script')
  <!-- Page js files -->
  <script src="{{ asset(mix('js/scripts/pages/page-pricing.js')) }}"></script>
  <script src="{{ asset(mix('js/scripts/pages/modal-add-new-cc.js')) }}"></script>
  <script src="{{ asset(mix('js/scripts/pages/modal-edit-cc.js')) }}"></script>
  <script src="{{ asset(mix('js/scripts/pages/page-account-settings-billing.js')) }}"></script>


  <script>
         var tableData = $('.datatables-ajax').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('invoice/list') }}",
                data: function(data) {
                    data.user_id = "{{Auth::user()->user_id}}";
                }
            },
            columns: [{
                    data: 'invoice_number',
                    name: 'invoice_number',
                    sClass: "align-middle table-image"
                },
                {
                    data: 'email',
                    name: 'email',
                    sClass: "align-middle"
                },
                {
                    data: 'plan_name',
                    name: 'plan_name',
                    sClass: "align-middle"
                },
                {
                    data: 'amount',
                    name: 'amount',
                    sClass: "align-middle"
                },
              
                {
                    data: 'activation_date',
                    name: 'activation_date',
                    sClass: "align-middle"
                },
                {
                    data: 'expiry_date',
                    name: 'expiry_date',
                    sClass: "align-middle"
                },
                {
                    data: 'status',
                    name: 'status',
                    sClass: "align-middle"
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    sClass: "align-middle no-wrap"
                }
            ],
            dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            orderCellsTop: true,
            language: {
                search: 'Search',
                searchPlaceholder: 'Search..',
                paginate: {
                    // remove previous & next text from pagination
                    previous: '&nbsp;',
                    next: '&nbsp;'
                }
            },
            "drawCallback": function(settings) {
                feather.replace();
            }
        });
        jQuery(document).ready(function() {

          $(document).on('click', '.cancelSubscription', function (e) {
            var subscription_id = $(this).attr('data-subscription_id');
                Swal.fire({
                  text: 'Are you sure you would like to cancel your subscription?',
                  icon: 'warning',
                  showCancelButton: true,
                  confirmButtonText: 'Yes',
                  customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-outline-danger ms-1'
                  },
                  buttonsStyling: false
                }).then(function (result) {

                  if (result.isConfirmed) {
                      $.ajax({
                          url: "{{ route('canceledSubscription') }}",
                          type: "POST",
                          dataType: "json",
                          data:{"subscription_id":subscription_id,  _token: "{{csrf_token()}}"},
                          success: function(data) {
                            if (data.sucess == 1) {
                                Swal.fire({
                                    icon: 'success',
                                    title: "Done!",
                                    text: "Your suscription would be cancelled at the end of the term",
                                    showConfirmButton: false,
                                    timer: 2000
                                });
                                location.reload()
                            } else {
                                Swal.fire({
                                    icon: 'success',
                                    title: "Done!",
                                    text: "Erroe While Cancelled subscription!!",
                                    showConfirmButton: false,
                                    timer: 2000
                                });
                                location.reload()
                            }
                          }
                      }); 
                            
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                      title: 'Cancelled',
                      text: 'Unsubscription Cancelled!!',
                      icon: 'error',
                      showConfirmButton: false,
                                    timer: 2000
                    });
                  }

                  // if (result.value) {
                  //   Swal.fire({
                  //     icon: 'success',
                  //     title: 'Unsubscribed!',
                  //     text: 'Your subscription cancelled successfully.',
                  //     customClass: {
                  //       confirmButton: 'btn btn-success'
                  //     }
                  //   });
                  // } else if (result.dismiss === Swal.DismissReason.cancel) {
                  //   Swal.fire({
                  //     title: 'Cancelled',
                  //     text: 'Unsubscription Cancelled!!',
                  //     icon: 'error',
                  //     customClass: {
                  //       confirmButton: 'btn btn-success'
                  //     }
                  //   });
                  // }
                });
            });

            $(document).on('click', '.deleteSubscription', function (e) {
            var subscription_id = $(this).attr('data-subscription_id');
            var id = $(this).attr('data-id');
                Swal.fire({
                  text: 'Are you sure you would like to delete your subscription?',
                  icon: 'warning',
                  showCancelButton: true,
                  confirmButtonText: 'Yes',
                  customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-outline-danger ms-1'
                  },
                  buttonsStyling: false
                }).then(function (result) {

                  if (result.isConfirmed) {
                      $.ajax({
                          url: "{{ route('deleteSubscription') }}",
                          type: "POST",
                          dataType: "json",
                          data:{"subscription_id":subscription_id,"id":id,  _token: "{{csrf_token()}}"},
                          success: function(data) {
                            if (data.sucess == 1) {
                                Swal.fire({
                                    icon: 'success',
                                    title: "Done!",
                                    text: "Your suscription deleted successfully",
                                    showConfirmButton: false,
                                    timer: 2000
                                });
                                location.reload()
                            } else {
                                Swal.fire({
                                    icon: 'success',
                                    title: "Done!",
                                    text: "Erroe While delete subscription!!",
                                    showConfirmButton: false,
                                    timer: 2000
                                });
                                location.reload()
                            }
                          }
                      }); 
                            
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                      title: 'Cancelled',
                      text: 'Your suscription not deleted!!',
                      icon: 'error',
                      showConfirmButton: false,
                                    timer: 2000
                    });
                  }

                  // if (result.value) {
                  //   Swal.fire({
                  //     icon: 'success',
                  //     title: 'Unsubscribed!',
                  //     text: 'Your subscription cancelled successfully.',
                  //     customClass: {
                  //       confirmButton: 'btn btn-success'
                  //     }
                  //   });
                  // } else if (result.dismiss === Swal.DismissReason.cancel) {
                  //   Swal.fire({
                  //     title: 'Cancelled',
                  //     text: 'Unsubscription Cancelled!!',
                  //     icon: 'error',
                  //     customClass: {
                  //       confirmButton: 'btn btn-success'
                  //     }
                  //   });
                  // }
                });
            });

        });
    </script>
@endsection
