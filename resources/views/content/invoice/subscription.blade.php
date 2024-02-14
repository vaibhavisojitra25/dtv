@extends('layouts/contentLayoutMaster')

@section('title', 'Subscription List')

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/flatpickr/flatpickr.min.css')) }}">
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
                            <label class="form-label" for="status"> Filter by Status</label>
                            <select id="status" class="form-select text-capitalize mb-md-0 mb-2xx">
                                <option value="all"> Select Status </option>
                                <option value="Live" class="text-capitalize">Live</option>
                                <option value="Pending" class="text-capitalize">Pending	</option>
                                <option value="Trial" class="text-capitalize">Trial	</option>
                                <option value="Cancelled" class="text-capitalize">Cancelled	</option>
                                <option value="Expired" class="text-capitalize">Expired</option>
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
                                    <th> Date</th>
                                    <th>Email</th>
                                    <th>Plan</th>
                                    <th>Last/Next Billing Date</th>
                                    <th>Revenu</th>
                                    <th>Status</th>
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
    <script src="{{ asset(mix('vendors/js/pickers/flatpickr/flatpickr.min.js')) }}"></script>
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
            dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            ajax: {
                url: "{{ route('subscription') }}",
                data: function(data) {
                    data.status = $("#status").val();
                    data.startdate = $("#startdate").val();
                    data.enddate = $("#enddate").val();
                }
            },
            columns: [{
                    data: 'date',
                    name: 'date',
                    sClass: "align-middle table-image",
                    orderable: "DESC",
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
                    data: 'billing_date',
                    name: 'billing_date',
                    sClass: "align-middle"
                },
                {
                    data: 'revenu',
                    name: 'revenu',
                    sClass: "align-middle"
                }, 
                {
                    data: 'status',
                    name: 'status',
                    sClass: "align-middle"
                },
            ],
            language: {
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
                    console.log(dateStr);
                    if(dateStr.length > 1){
                        var startDate = dateStr[0];
                        $("#startdate").val(startDate);
                        var endDate = dateStr[1];
                        $("#enddate").val(endDate);
                    }else{
                        $("#startdate").val("");
                        $("#enddate").val("");
                    }
                    tableData.ajax.reload();
                }
            });
            $(document).on('click','#clearFilter',function() {
                $("#startdate").val("");
                $("#enddate").val("");
                $("#status").val("all");
                $flatpickr.clear();
                tableData.ajax.reload();
            });
            
        });
    </script>

@endsection
