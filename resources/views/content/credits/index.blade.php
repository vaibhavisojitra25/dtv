@extends('layouts/contentLayoutMaster')

@section('title', 'Credits History')

@section('vendor-style')
  {{-- vendor css files --}}
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap5.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/flatpickr/flatpickr.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/animate/animate.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">

@endsection
@section('page-style')
  {{-- Page css files --}}
  <link rel="stylesheet" type="text/css" href="{{asset('css/base/plugins/forms/pickers/form-flat-pickr.css')}}">
  <link rel="stylesheet" href="{{asset(mix('css/base/plugins/extensions/ext-component-sweet-alerts.css'))}}">

@endsection

@section('content')
<section id="ajax-datatable">

  <div class="row">
    <div class="col-12">
      <div class="card">
        
        <div class="card-header">
          @if(Auth::user()->user_type != 1)<h3 class="card-title fw-bolder mb-75">Total Remaining Credit - @if(Auth::user()->is_unlimited_credit == 1){{'Unlimited'}}@else{{ Auth::user()->credits }}@endif</h3>

            <div class="pull-right">
                <a data-bs-toggle="modal" data-bs-target="#addCreditsMdl" class="btn btn-primary waves-effect waves-float waves-light">
                    Add Credit
                </a>
            </div>
            @endif

        </div>
        
        <div class="card-body border-top pt-2">
            <h4 class="card-title">Search & Filter</h4>
            <div class="row">
              <div class="col-md-3">
                    <label for="added_by"> Filter by Credited/Deducted By</label>
                        <select id="added_by" class="form-select text-capitalize mb-md-0 mb-2xx">
                            <option value=""> Select User </option>
                            @foreach($users as $value)
                            <option value="{{$value->user_id}}"> {{$value->first_name}} {{$value->last_name}} </option>
                            @endforeach
                        </select>
                </div>

                <div class="col-md-3">
                    <label for="credited_to"> Filter by Credited To</label>
                        <select id="credited_to" class="form-select text-capitalize mb-md-0 mb-2xx">
                            <option value=""> Select User </option>
                            @foreach($users as $value)
                            <option value="{{$value->user_id}}"> {{$value->first_name}} {{$value->last_name}} </option>
                            @endforeach
                        </select>
                </div>
                <div class="col-md-2">
                    <label>Filter By Date</label>
                    <input
                      type="text"
                      id="fp-date-time"
                      class="form-control change_data"
                      placeholder="YYYY-MM-DD"
                    />
                        <input type="hidden" class="form-control" id="startdate">
                </div>

                <div class="col-md-2">
                    <label for="is_credited"> Filter by Status</label>
                        <select id="is_credited" class="form-select text-capitalize mb-md-0 mb-2xx">
                            <option value=""> Select Status </option>
                            <option value="1"> Credited </option>
                            <option value="0"> Deduct </option>
                        </select>
                </div>
                <div class="col-md-2"> 
                    <button id="clearFilter" class="btn btn-primary waves-effect waves-float  waves-light mt-2">  Clear Filter </button>
                </div>

            </div>
          </div>
        <div class="card-datatable">
          <table class="datatables-ajax table table-responsive" style="width:100%">
            <thead>
              <tr>
                <th>Credits</th>
                <th>Status</th>
                <th>Credited/Deduct By</th>
                <th>Credited To</th>
                <th>Device Code</th>
                <th>Plan</th>
                <th>Date</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </div>
  </div>
</section>
@include('content/_partials/_modals/modal-add-credits')
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

@endsection

@section('page-script')
  {{-- Page js files --}}
  <script>

var tableData = $('.datatables-ajax').DataTable({
        stateSave: true,
        processing: true,
        serverSide: true,
        ordering: false,
        dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        ajax: {
            url: "{{route('credits/list')}}",
            data: function(data) {
                    data.startdate = $("#startdate").val();
                    data.added_by = $("#added_by").val();
                    data.credited_to = $("#credited_to").val();
                    data.is_credited = $("#is_credited").val();
              }
        },
        // "columnDefs": [
        //   { "width": "20%", "targets": 7 }
        // ],
        columns: [
            {data: 'credits', name: 'credits',sClass: "align-middle table-image"},
            {data: 'is_credited', name: 'is_credited',sClass: "align-middle table-image"},
            {data: 'added_by', name: 'added_by',sClass: "align-middle table-image"},
            {data: 'credited_to', name: 'credited_to',sClass: "align-middle table-image"},
            {data: 'device_code', name: 'device_code', sClass: "align-middle"},
            {data: 'plan_name', name: 'plan_name', sClass: "align-middle"},
            {data: 'created_at', name: 'created_at', sClass: "align-middle"},
        ],
        language: {
          paginate: {
            // remove previous & next text from pagination
            previous: '&nbsp;',
            next: '&nbsp;'
          }
        },
        "drawCallback": function( settings ) {
          feather.replace();
      }
    });
    jQuery(document).ready(function () {
      
      $('#addCreditsMdl').on('hidden.bs.modal', function(e) {
          $("#addCredits")[0].reset();
          $('#plan_id').val("");
          var validator = $("#addCredits").validate();
          validator.resetForm();
      });

      $(document).on('submit', '#addCredits', function (e) {
            e.preventDefault();
            var plan_id = $("#plan_id").val();
            var customer_id = $("#customer_id").val();
            $.ajax({
                url: "{{ route('getCheckoutLink') }}",
                type: "POST",
                data: {plan_id:plan_id,flag:2},
                dataType: "json",
                cache: false,
                success: function(data) {
                  var url = data.data[0].checkout_page+'?customer_id='+customer_id;
                  window.location.href = url;
                },
                error: function(data) {
                }
            });
        });

        var $flatpickr = $('.change_data').flatpickr({
            // enableTime: true,
            setDate: new Date(),
            onChange: function(selectedDates, dateStr, instance) {
                $("#startdate").val(dateStr);
                tableData.ajax.reload();
            }
        });
        $(document).on('change','#credited_to',function() {
            tableData.ajax.reload();
        });
        $(document).on('change','#added_by',function() {
            tableData.ajax.reload();
        });
        $(document).on('change','#is_credited',function() {
            tableData.ajax.reload();
        });
        $(document).on('click','#clearFilter',function() {
            $("#startdate").val("");
            $("#credited_to").val("");
            $("#added_by").val("");
            $("#is_credited").val("");
            $flatpickr.clear();
            tableData.ajax.reload();
        });
        
      });
      $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

</script>
@endsection


