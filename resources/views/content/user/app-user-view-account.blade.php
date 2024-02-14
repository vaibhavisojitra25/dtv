@extends('layouts/contentLayoutMaster')

@section('title', 'User View')

@section('vendor-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/animate/animate.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/buttons.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/rowGroup.bootstrap5.min.css')) }}">

    <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/pickadate/pickadate.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/flatpickr/flatpickr.min.css')) }}">
@endsection

@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-validation.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-sweet-alerts.css')) }}">
@endsection

@section('content')

<section class="app-user-view-account">
  <div class="row">
    <!-- User Sidebar -->
    <div class="col-xl-4 col-lg-5 col-md-5 order-1 order-md-0">
      <!-- User Card -->
      <div class="card">
        <div class="card-body">
          <div class="user-avatar-section">
            <div class="d-flex align-items-center flex-column">
              <img
                class="img-fluid rounded mt-3 mb-2"
                src="{{ $user && $user->profile_picture? url('uploads/profile_pictures/' . $user->profile_picture): asset('images/portrait/small/avatar-s-11.jpg') }}"
                height="110"
                width="110"
                alt="User avatar"
              />
              <div class="user-info text-center">
                <h4>{{ $user->first_name }} {{ $user->last_name }}</h4>
                @php
                  if ($user->user_type == 2) {
                      $user_type = "<span class='badge badge-light-success rounded-pill'>User</span>";
                  } else if ($user->user_type == 3) {
                      $user_type = "<span class='badge badge-light-warning rounded-pill'>Reseller</span>";
                  } else if ($user->user_type == 4) {
                      $user_type = "<span class='badge badge-light-info rounded-pill'>SubReseller</span>";
                    } else {
                      $user_type = "<span class='badge badge-light-dark rounded-pill'>Admin</span>";
                    }
                @endphp
               {!! $user_type !!}
              </div>
            </div>
          </div>
     
          <h4 class="fw-bolder border-bottom pb-50 mb-1">Details</h4>
          <div class="info-container">
            <ul class="list-unstyled">
              <li class="mb-75">
                <span class="fw-bolder me-25">Email:</span>
                <span>{{ $user->email }}</span>
              </li>
              <li class="mb-75">
                <span class="fw-bolder me-25">Status:</span>
                @if($user->status == 1)
                <span class="badge bg-light-success">Active</span>
                @else
                <span class="badge bg-light-danger">Inactive</span>
                @endif
              </li>
              <li class="mb-75">
                <span class="fw-bolder me-25">Is Verified:</span>
                @if($user->is_verified == 1)
                <span class="badge bg-light-success">Verified</span>
                @else
                <span class="badge bg-light-danger">Unverified</span>
                @endif
              </li>
              <li class="mb-75">
                <span class="fw-bolder me-25">Contact:</span>
                <span>{{ $user->phone_no }}</span>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
      <!-- /User Card -->

      
      <!-- Plan Card -->
      {{-- @if($userSubscription)
      <div class="col-xl-4 col-lg-5 col-md-5 order-1 order-md-0">
      <div class="card border-primary">
        <div class="card-header border-bottom">
          <h4 class="card-title">Current plan</h4>
        </div>
        <div class="card-body my-2 py-25">
          <div class="d-flex justify-content-between align-items-start">
            <span class="badge bg-light-primary">{{$userSubscription['plan']['plan_name']}}</span>
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
          <ul class="ps-1 mb-2">
              <li class="mb-50">
              @if($userSubscription['activation_date'])
                      Activate at <strong>{{\Carbon\Carbon::parse($userSubscription['activation_date'])->format('M d, Y')}}</strong>
                    @endif
                  </li>
          <li class="mb-50">
          @if($userSubscription['next_billing_date'])
                  Next Billing Date <strong>{{\Carbon\Carbon::parse($userSubscription['next_billing_date'])->format('M d, Y')}}</strong>
                  @else
                  Active until <strong>{{\Carbon\Carbon::parse($userSubscription['expiry_date'])->format('M d, Y')}}</strong>
                  @endif
                  </li>
          </ul>

          @if( $userSubscription['status'] =='trial' )
            <div class="d-flex justify-content-between align-items-center fw-bolder mb-50">
              <span>Trial Devices</span>
              <span>{{$trial_remain_device}} of {{$userSubscription['trial_device_limit']}}</span>
            </div>
            <div class="progress mb-50" style="height: 8px">
              <?php $percentage = ($userSubscription['trial_remain_device_limit'] / $userSubscription['trial_device_limit']) * 100?>
                <div
                  class="progress-bar" style="width:{{$percentage}}%"
                  role="progressbar"
                  aria-valuenow="{{$userSubscription['remaining_device_limit']}}"
                  aria-valuemin="0"
                  aria-valuemax="100"
                ></div>
            </div>
            <span>{{$trial_remain_device}} device remaining</span>
          @else

          <div class="d-flex justify-content-between align-items-center fw-bolder mb-50">
            <span>Devices</span>
            <span>{{$remain_device}} of {{$userSubscription['device_limit']}}</span>
          </div>
          <div class="progress mb-50" style="height: 8px">
            <?php $percentage = ($userSubscription['remaining_device_limit'] / $userSubscription['device_limit']) * 100?>
              <div
                class="progress-bar" style="width:{{$percentage}}%"
                role="progressbar"
                aria-valuenow="{{$userSubscription['remaining_device_limit']}}"
                aria-valuemin="0"
                aria-valuemax="100" 
              ></div>
          </div>
          <span>{{$remain_device}} device remaining</span>
          @endif

          <div class="d-grid w-100 mt-2">
            <!-- <button class="btn btn-primary" data-bs-target="#upgradePlanModal" data-bs-toggle="modal">
              Upgrade Plan
            </button> -->
          </div>
        </div>
        </div>
      </div>
      @endif
      @if(!empty($userUpcomingSubscription['data']))
      <div class="col-xl-4 col-lg-5 col-md-5 order-1 order-md-0">
        <div class="card border-primary">
          <div class="card-header border-bottom">
            <h4 class="card-title">Scheduled Details</h4>
          </div>
          <div class="card-body my-2 py-25">
            <div class="d-flex justify-content-between align-items-start">
              <span class="badge bg-light-primary">{{$userUpcomingSubscription['data']['new_plan']['plan_name']}}</span>
              <div class="d-flex justify-content-center">
                <sup class="h5 pricing-currency text-primary mt-1 mb-0">{{$userUpcomingSubscription['data']['currency_symbol']}}</sup>
                <span class="fw-bolder display-5 mb-0 text-primary">{{$userUpcomingSubscription['data']['new_plan']['price']}}</span>
                <sub class="pricing-duration font-small-4 ms-25 mt-auto mb-2">/@if($userUpcomingSubscription['data']['new_plan']['billing_period']=='m'){{'Month'}}@else{{'Year'}}@endif</sub>
              </div>
            </div>
            <ul class="ps-1 mb-2">
              <li class="mb-50">Scheduled at <strong>{{\Carbon\Carbon::parse($userUpcomingSubscription['data']['scheduled_date'])->format('M d, Y')}} </strong></li>
            </ul>
            <div class="d-flex justify-content-between align-items-center fw-bolder mb-50">
              <span>Devices</span>
              <span>{{$userUpcomingSubscription['data']['new_plan']['meta_data']['device_limit']}}</span>
            </div>
            <div class="d-grid w-100 mt-2">
              <!-- <button class="btn btn-primary" data-bs-target="#upgradePlanModal" data-bs-toggle="modal">
                Upgrade Plan
              </button> -->
            </div>
          </div>
          </div>
      </div>
      @endif

      @if(empty($userSubscription))
      <div class="col-xl-4 col-lg-5 col-md-5 order-1 order-md-0">
        <div class="card border-primary">
          <div class="card-header border-bottom">
            <h4 class="card-title">Current plan</h4>
          </div>
          <div class="card-body my-2 py-25">
        <div class="d-flex justify-content-between align-items-start">
          <span class="badge bg-light-primary">Free</span>
          <span>A simple start for everyone</span>
        </div>
        <div class="d-flex justify-content-between align-items-center fw-bolder mt-3 mb-50">
          <span>Devices</span>
          <span>{{$remain_device}} of 1</span>
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
        <span>{{$remain_device}} device remaining</span>
        <div class="d-grid w-100 mt-2">
          <!-- <button class="btn btn-primary" data-bs-target="#upgradePlanModal" data-bs-toggle="modal">
            Upgrade Plan
          </button> -->
        </div>
        </div>
        </div>
      </div>
      @endif
    --}}
   
  
  </div>
  <div class="row">
    <!-- User Content -->
  <div class="col-xl-12 col-lg-7 col-md-7 order-0 order-md-1">
  
    <div class="card">
        <div class="card-body border-bottom">
            <h4 class="card-title">Search & Filter</h4>
            <div class="row">
                <div class="col-md-4">
                    <label class="form-label" for="status"> Filter by Status</label>
                    <select id="status" class="form-select text-capitalize mb-md-0 mb-2xx">
                        <option value=""> Select Status </option>
                        <option value="1" class="text-capitalize">Active</option>
                        <option value="4" class="text-capitalize">Expired</option>
                        <option value="2" class="text-capitalize">Upcoming</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label>Filter By Date</label>
                    <input
                        type="text"
                        id="fp-range"
                        class="form-control change_invoicedata"
                        placeholder="YYYY-MM-DD to YYYY-MM-DD"
                    />
                        <input type="hidden" class="form-control" id="startdate">
                        <input type="hidden" class="form-control" id="enddate">
                </div>
                <div class="col-md-4"> 
                    <button id="clearFilter" class="btn btn-primary waves-effect waves-float  waves-light mt-2">  Clear Filter </button>
                </div>

            </div>
        </div>
        <div class="card-datatable">
          <table class="datatables-ajax table table-responsive">
                    <thead>
                        <tr>
                          <th>Start Date</th>
                          <th>Email</th>
                          <th>Plan</th>
                          <th>Device Code</th>
                          <th>Amount</th>
                          <th>Activation Date</th>
                          <th>Expire Date</th>
                          <th>Status</th>
                          <th>Actions</th>
                        </tr>
                    </thead>
                </table>
        </div>
        
    </div>
            

    
  </div>
  <!--/ User Content -->
  </div>
</section>

@include('content/_partials/_modals/modal-edit-user')
@include('content/_partials/_modals/modal-upgrade-plan')
@endsection

@section('vendor-script')
    {{-- Vendor js files --}}
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/forms/cleave/cleave.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/forms/cleave/addons/cleave-phone.us.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/forms/validation/jquery.validate.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/forms/validation/additional-methods.min.js')) }}"></script>
    {{-- data table --}}
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
    <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/polyfill.min.js')) }}"></script>
@endsection

@section('page-script')
    {{-- Page js files --}}
    {{-- <script src="{{ asset(mix('js/scripts/pages/modal-edit-user.js')) }}"></script> --}}
    {{-- <script src="{{ asset(mix('js/scripts/pages/app-user-view-account.js')) }}"></script> --}}
    {{-- <script src="{{ asset(mix('js/scripts/pages/app-user-view.js')) }}"></script> --}}


    <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.date.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.time.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/pickers/pickadate/legacy.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/pickers/flatpickr/flatpickr.min.js')) }}"></script>


    <script>
         var tableData = $('.datatables-ajax').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
              url: "{{ route('invoice/list') }}",
                data: function(data) {
                    data.user_id = "{{$user->user_id}}";
                    data.status = $("#status").val();
                    data.startdate = $("#startdate").val();
                    data.enddate = $("#enddate").val();
                }
            },
            columns: [{
                    data: 'starts_at',
                    name: 'starts_at',
                    sClass: "align-middle table-image",
                },
                {
                    data: 'email',
                    name: 'email',
                    sClass: "align-middle"
                },
                {
                    data: 'plan_id',
                    name: 'plan_id',
                    sClass: "align-middle"
                },              
                {
                    data: 'device_id',
                    name: 'device_id',
                    sClass: "align-middle"
                },
                {
                    data: 'amount',
                    name: 'amount',
                    sClass: "align-middle",
                    width:'5%'
                },
                {
                    data: 'activation_date',
                    name: 'activation_date',
                    sClass: "align-middle"
                },
                {
                    data: 'expiry_date',
                    name: 'expiry_date',
                    sClass: "align-middle table-image",
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
                    sClass: "align-middle no-wrap",
                    width:'15%'
                },
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
        $(document).on('change','#status',function() {
            $('.datatables-ajax').DataTable().ajax.reload(null, false);
        });
        jQuery(document).ready(function() {

            var $flatpickr = $('.change_invoicedata').flatpickr({
                mode: 'range',
                setDate: new Date(),
                onChange: function(selectedDates, dateStr, instance) {
                    dateStr = dateStr.split("to ");
                    if(dateStr.length > 1){
                        var startDate = dateStr[0];
                        $("#startdate").val(startDate);
                        var endDate = dateStr[1];
                        $("#enddate").val(endDate);
                    }else{
                        $("#startdate").val(dateStr);
                        $("#enddate").val(dateStr);
                    }
                    tableData.ajax.reload();
                }
            });

            $(document).on('click','#clearFilter',function() {
                $("#startdate").val("");
                $("#enddate").val("");
                $("#status").val("");
                $flatpickr.clear();
                tableData.ajax.reload();
            });
        });
    </script>

@endsection
