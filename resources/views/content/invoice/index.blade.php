@extends('layouts/contentLayoutMaster')

@section('title', 'Invoice List')

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/animate/animate.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}"> 
     <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/pickadate/pickadate.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/flatpickr/flatpickr.min.css')) }}">

@endsection
@section('page-style')
    {{-- Page css files --}}
    <link rel="stylesheet" type="text/css" href="{{ asset('css/base/plugins/forms/pickers/form-flat-pickr.css') }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-sweet-alerts.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/pickers/form-flat-pickr.css')) }}">
<link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/pickers/form-pickadate.css')) }}">

@endsection

@section('content')
    <section id="ajax-datatable">
        <div class="row">
            <div class="col-12">

            <div class="card">
                <div class="card-body border-bottom">
                    <h4 class="card-title">Search & Filter</h4>
                    <div class="row">
                        <div class="col-md-4">
                            <label for="status"> Filter by Status</label>
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
                <input type="hidden" class="form-control" id="user_id" value="@if(Auth::user()->user_type == 2){{Auth::user()->user_id}}@endif">
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
        </div>
    </section>

    <!-- Dashboard Ecommerce ends -->
@endsection

@section('vendor-script')
    {{-- vendor files --}}
    <script src="{{ asset(mix('vendors/js/tables/datatable/jquery.dataTables.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.bootstrap5.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.responsive.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/responsive.bootstrap5.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/polyfill.min.js')) }}"></script>

    <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.date.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.time.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/pickers/pickadate/legacy.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/pickers/flatpickr/flatpickr.min.js')) }}"></script>

@endsection

@section('page-script')
    {{-- Page js files --}}

    <script>
        var tableData = $('.datatables-ajax').DataTable({
            stateSave: true,
            processing: true,
            serverSide: true,
            paging: true,
            deferRender: true,
            dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            ajax: {
                url: "{{ route('invoice/list') }}",
                data: function(data) {
                    data.status = $("#status").val();
                    data.startdate = $("#startdate").val();
                    data.enddate = $("#enddate").val();
                    data.user_id = $("#user_id").val();
                }
            },
            columns: [{
                    data: 'starts_at',
                    name: 'starts_at',
                    sClass: "align-middle table-image",
                    orderable: "DESC",
                },
                {
                    data: 'email',
                    name: 'email',
                    sClass: "align-middle"
                },
                {
                    data: 'plan_id',
                    name: 'plan_id',
                    sClass: "align-middle",
                    
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
                    sClass: "align-middle",
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
