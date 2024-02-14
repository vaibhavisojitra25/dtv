@extends('layouts/contentLayoutMaster')

@section('title', 'Subscription Plan')

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
        <div class="card-header border-bottom">
          <h4 class="card-title">Plan List</h4>
          <!-- <div class="col-md-4 pull-left">
            <select id="status" class="form-select text-capitalize mb-md-0 mb-2xx">
              <option value=""> Select Status </option>
              <option value="1" class="text-capitalize">Active</option>
              <option value="2" class="text-capitalize">Inactive</option>
            </select>
          </div> -->
          <div class="pull-right">
                <a href="{{route('plan.create')}}" class="btn btn-primary waves-effect waves-float waves-light">
                    Create New Plan
                </a>
            </div>

        </div>

        <div class="card-datatable">
          <table class="datatables-ajax table table-responsive users-list">
            <thead>
              <tr>
                <th>Plan Name</th>
                <th>Amount</th>
                <th>Billing Cycle</th>
                <th>Is For Reseller</th>
                <th>Is Credit</th>
                <th>Credit Amount</th>
                <th>Status</th>
                <th>Action</th>
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
            url: "{{route('subscription/plan')}}",
            // data: function(data){
            //   // data.status =  $("#status").val();
            // }
        },
        columns: [
            {data: 'plan_name', name: 'plan_name',sClass: "align-middle table-image"},
            {data: 'amount', name: 'amount', sClass: "align-middle"},
            {data: 'billing_cycle', name: 'billing_cycle', sClass: "align-middle"},
            {data: 'is_for_reseller', name: 'is_for_reseller', sClass: "align-middle"},
            {data: 'is_credit', name: 'is_credit', sClass: "align-middle"},
             {data: 'credit_amount', name: 'credit_amount', sClass: "align-middle"},
            {data: 'status', name: 'status', sClass: "align-middle"},
            {data: 'action', name: 'action', orderable: false, searchable: false, sClass: "align-middle no-wrap"},
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

    $('#status').change(function(){
        $('.datatables-ajax').DataTable().ajax.reload(null, false);
      });
    jQuery(document).ready(function () {


    });
</script>

<script>
  $(document).on('click', '.chkStatus', function (e) {
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });
      var id = $(this).attr('data-id');
      var name = $(this).attr('data-name');
      var status = $(this).val();
      $this = $(this);
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
          url: "{{ url('change_plan_status') }}",
          data:{'id':id,'status':status,'name':name},
          type: "POST",
          dataType: "json",
          success: function(data) {
            if (data.success == 0) {
              Swal.fire({
                  icon: 'error',
                  title: "Error!",
                  text: 'Error While Update',
                  showConfirmButton: false,
                  timer: 2000
              });
            }else{
              if (data.active) {
                $this.val(1);
                  Swal.fire({
                      icon: 'success',
                      title: "Done!",
                      text: data.active,
                      showConfirmButton: false,
                      timer: 2000
                  });
              } else {
                $this.val(0);
                  Swal.fire({
                      icon: 'success',
                      title: "Done!",
                      text: data.suspend,
                      showConfirmButton: false,
                      timer: 2000
                  });
              }
            }
          }
      });

    }else{
      if ($(this).prop('checked')==true){
            $(this).prop('checked',false);
          }else{
            $(this).prop('checked',true);
          }
    }
    })

  })
</script>
@endsection
