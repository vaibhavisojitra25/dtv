@extends('layouts/contentLayoutMaster')

@section('title', 'Add New Coupon')

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/buttons.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">
@endsection
@section('page-style')
    {{-- Page css files --}}
    {{-- Page Css files --}}
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
    <section id="ajax-datatable">
        <div class="row">
            <div class="col-md-6 col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            @if ($flag == 'create')
                                Add Coupon
                            @elseif($flag == 'show')
                                Show Coupon
                            @else
                                Edit Coupon
                            @endif
                        </h4>
                    </div>
                    <div class="card-body">
                        @if (isset($r->id))
                            <form class="form form-vertical" method="put" autocomplete="off" id="updateCouponForm">
                            @else
                                <form class="form form-vertical" method="post" id="addCouponForm">
                        @endif
                        @csrf
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-1">
                                    <label class="form-label" for="device_title">Coupon Name<span class="text-danger">*</span></label>
                                    <input type="text" id="coupon_name" class="form-control" placeholder="Coupon Name"
                                        name="coupon_name" value="{{ old('name', $r->name) }}"
                                        {{ $flag == 'show' ? 'disabled' : '' }}>
                                </div>
                            </div>
                            @if ($flag != 'show')
                                <div class="col-12">
                                    <div class="">
                                        <label class="form-label" for="device_id">Plan<span class="text-danger">*</span></label>
                                        <select class="select2 form-select" name="plan_id" id="plan_id">
                                            <option value="">Select Plan</option>
                                            @foreach (array_reverse($planData) as $plan)
                                                @if ($r->plan_id == $plan['plan_code'])
                                                    <option selected value="{{ $plan['id'] }}">
                                                        {{ $plan['plan_code'] }}
                                                    </option>
                                                @else
                                                    <option value="{{ $plan['id'] }}">{{ $plan['plan_code'] }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @else
                                <div class="col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="device_title">Plan</label>
                                        <input type="text" class="form-control" value="{{ $r->plan_code }}" disabled>
                                    </div>
                                </div>
                            @endif
                            <div class="col-12">
                                <div class="mt-1 mb-1 d-flex">
                                    <div class="form-check pe-5">
                                        <input class="form-check-input" type="radio" name="is_limit" value="1"
                                            id="limit" {{ $r->limit != -1 ? 'checked' : '' }}
                                            onclick="showLimitInput();" {{ $flag != 'create' ? 'disabled' : '' }}>
                                        <label class="form-check-label" for="limit">
                                            Limit
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" value="0" name="is_limit"
                                            id="no-limit" {{ $r->limit == -1 ? 'checked' : '' }}
                                            onclick="showCodeInput();" {{ $flag != 'create' ? 'disabled' : '' }}>
                                        <label class="form-check-label" for="no-limit">
                                            No limit
                                        </label>
                                    </div>
                                </div>
                            </div>
                            @if ($r->limit != -1)
                                <div class="col-12" id="limit-div">
                                    <div class="mb-1">
                                        <label class="form-label" for="device_title">Enter Limit<span class="text-danger">*</span></label>
                                        <input type="text" id="limit" class="form-control" placeholder="Enter limit"
                                            name="limit" value="{{ $r->limit }}"
                                            {{ $flag != 'create' ? 'disabled' : '' }}>
                                    </div>
                                </div>
                            @endif

                            @if ($flag == 'create' || $r->limit == -1)
                                <div class="col-12" id="code-div">
                                    <div class="mb-1">
                                        <label class="form-label" for="device_title">Coupon code</label>
                                        <input type="text" id="code" class="form-control"
                                            value="{{ $r->limit == -1 ? $r->code : '' }}" disabled>
                                        <input type="hidden" id="code1" class="form-control" name="code">
                                    </div>
                                </div>
                            @endif
                            @if ($flag != 'show')
                                <div class="col-12">
                                    @if ($flag == 'create')
                                        <button
                                            class="btn btn-primary me-1 waves-effect waves-float waves-light">Submit</button>
                                    @endif
                                    @if ($flag == 'edit')
                                        <input type="hidden" id="update_id" value="{{ $r->id }}">
                                        <button
                                            class="btn btn-primary me-1 waves-effect waves-float waves-light">Update</button>
                                    @endif
                                    <button type="reset" class="btn btn-outline-secondary waves-effect">Reset</button>
                                </div>
                            @endif
                        </div>
                        </form>
                    </div>
                </div>
            </div>
            @if ($flag == 'show' && $r->limit != -1)
                <div class="col-md-6 col-12">
                    <div class="card">
                        <div class="card-header border-bottom">
                            <h4 class="card-title">Coupon List</h4>
                        </div>
                        <div class="card-datatable">
                            <table class="datatables-ajax table table-responsive">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Used</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $couponsCount = \App\Models\Coupon::where('parent_id', $r->id)->count();
                                        $coupons = \App\Models\Coupon::where('parent_id', $r->id)->get();
                                    @endphp
                                    @if (!empty($coupons))
                                        @php
                                            $usedCound = \App\Models\Coupon::where('parent_id', $r->id)
                                                ->where('is_used', 1)
                                                ->count();
                                            $not_allowed = 0;
                                            if ($couponsCount == $usedCound) {
                                                $not_allowed = 1;
                                            }
                                        @endphp
                                        @foreach ($coupons as $item)
                                            <tr>
                                                <td>{{ $item->code }}</td>
                                                <td>{{ $item->is_used }}</td>
                                                <td>
                                                    <?php
                                                    if($not_allowed == 0){
                                        if ($item->status == 0) {
                                        ?>


                                                    <span class="btnOn">
                                                        <label class="switch">
                                                            <input type="checkbox" value="{{ $item->id }}"
                                                                class="chkStatus" />
                                                            <span class="slider round"></span>
                                                        </label>
                                                    </span>


                                                    <?php } else {
                                        ?>
                                                    <span class="btnOn">
                                                        <label class="switch">
                                                            <input type="checkbox" checked value="{{ $item->id }}"
                                                                class="chkStatus" />
                                                            <span class="slider round"></span>
                                                        </label>
                                                    </span>
                                                    <?php }}else{ ?>
<p>Not Allowed</p>
                                                        <?php }?>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>

@endsection

@section('vendor-script')
    {{-- vendor files --}}
    <script src="{{ asset('vendors/js/forms/validation/jquery.validate.min.js') }}"></script>
<script src="{{ asset(mix('vendors/js/forms/validation/additional-methods.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>

    <script src="{{ asset(mix('vendors/js/tables/datatable/jquery.dataTables.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.bootstrap5.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.responsive.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/responsive.bootstrap5.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.buttons.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.html5.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.print.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/pickers/flatpickr/flatpickr.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/polyfill.min.js')) }}"></script>
@endsection

@section('page-script')
    {{-- Page js files --}}
    <script>
        $('.datatables-ajax').DataTable({
            dom: '<"card-header border-bottom p-1"<"head-label"><"dt-action-buttons text-end"B>><"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        buttons: [
        {
          extend: 'collection',
          className: 'btn btn-outline-secondary dropdown-toggle me-2',
          text: feather.icons['share'].toSvg({ class: 'font-small-4 me-50' }) + 'Export',
          buttons: [
            {
              extend: 'csv',
              text: feather.icons['file-text'].toSvg({ class: 'font-small-4 me-50' }) + 'Csv',
              className: 'dropdown-item',
              exportOptions: { columns: [0,1] }
            },
          ],
          init: function (api, node, config) {
            $(node).removeClass('btn-secondary');
            $(node).parent().removeClass('btn-group');
            setTimeout(function () {
              $(node).closest('.dt-buttons').removeClass('btn-group').addClass('d-inline-flex');
            }, 50);
          }
        },
      ]  });
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        <?php if($flag == 'create'){
        ?>
        showLimitInput();
        <?php } ?>

        function showLimitInput() {
            document.getElementById('limit-div').style.display = 'block';
            document.getElementById('code-div').style.display = 'none';
        }

        function showCodeInput() {
            document.getElementById('limit-div').style.display = 'none';
            document.getElementById('code-div').style.display = 'block';
            <?php if($flag == 'create'){ ?>
            $.ajax({
                url: '{{ route('generateCouponCode') }}',
                type: 'GET',
                success: function(data) {
                    var s = document.getElementById('code');
                    s.value = data.code;
                    var d = document.getElementById('code1');
                    d.value = data.code;
                },
            });
            <?php }?>
        }
        $(function() {

            ('use strict');
            $('#addCouponForm').validate({
                rules: {
                    'coupon_name': {
                        required: true
                    },
                    'plan_id': {
                        required: true
                    },
                    'limit': {
                        required: {
                            depends: function() {
                                // text box only required when checkbox is unchecked
                                return $('#limit').is(':checked');
                            }
                        }
                    },
                    'limit': {
                        required: {
                            depends: function() {
                                // text box only required when checkbox is unchecked
                                return $('#limit').is(':checked');
                            }
                        }
                    },
                },
                messages: {
                    'coupon_name': {
                        required: '*Please Enter Coupon Name'
                    },
                    'plan_id': {
                        required: '*Please Select Plan'
                    },
                    'limit': {
                        required: '*Coupon limit is required'
                    },
                }
            });
            $('#updateCouponForm').validate({
                rules: {
                    'coupon_name': {
                        required: true
                    },
                    'plan_id': {
                        required: true
                    },
                },
                messages: {
                    'coupon_name': {
                        required: '*Please Enter Coupon Name'
                    },
                    'plan_id': {
                        required: '*Please Select Plan'
                    },
                }
            });
            $(document).on('submit', '#addCouponForm', function(e) {
                e.preventDefault();
                var formdata = new FormData($("#addCouponForm")[0]);
                $.ajax({
                    url: '{{ route('coupon.store') }}',
                    type: 'POST',
                    data: formdata,
                    dataType: "json",
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(data) {
                        $('.loader').hide();
                        if (data.success == 1) {
                            toastr.success('Coupon created');
                            window.location.href = "{{ route('coupon/list') }}";
                        } else {
                            toastr.error(data.message);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        alert(errorThrown);
                    }
                });

            });

            $(document).on('submit', '#updateCouponForm', function(e) {
                e.preventDefault();
                var formdata = new FormData($("#updateCouponForm")[0]);
                var id = $('#update_id').val();
                var coupon_name = $('#coupon_name').val();
                var plan_id = $('#plan_id').val();
                var url = '{{ route('coupon.update', ':id') }}';
                url = url.replace(':id', id);
                $.ajax({
                    url: url,
                    type: 'PUT',
                    data: {
                        coupon_name: coupon_name,
                        plan_id: plan_id
                    },
                    success: function(data) {
                        $('.loader').hide();
                        if (data.success == 1) {
                            toastr.success('Coupon updated');
                            window.location.href = "{{ route('coupon/list') }}";
                        } else {
                            toastr.error(data.message);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        alert(errorThrown);
                    }
                });

            });

        });

        $(document).on('click', '.chkStatus', function(e) {
            var id = this.value;
            var closeInSeconds = 2;
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, change it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('coupon/change_coupon_status') }}/" + id,
                        type: "POST",
                        data: {
                            has_limit: 1
                        },
                        dataType: "json",
                        success: function(data) {
                            if (data.active) {
                                Swal.fire({
                                    icon: 'success',
                                    title: "Done!",
                                    text: "Coupon Activated",
                                    showConfirmButton: false,
                                    timer: 2000
                                });

                            } else {
                                Swal.fire({
                                    icon: 'success',
                                    title: "Done!",
                                    text: "Coupon Suspended",
                                    showConfirmButton: false,
                                    timer: 2000
                                });

                            }
                        }
                    });

                }
            })
        });
    </script>
@endsection
