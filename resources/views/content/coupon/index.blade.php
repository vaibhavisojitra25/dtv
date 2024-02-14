@extends('layouts/contentLayoutMaster')

@section('title', 'Coupons List')

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
          <h4 class="card-title">Coupon List</h4>

          <div class="pull-right">
                <a href="{{route('coupon.create')}}" class="btn btn-primary waves-effect waves-float waves-light">
                    Add New Coupon
                </a>
                <button class="btn btn-danger ms-1 float-end delete_all">Delete All Selected</button>  

            </div>
        </div>
        <div class="card-datatable">
          <table class="datatables-ajax table table-responsive">
            <thead>
              <tr>
              <th width="50px"><input type="checkbox" id="select-all-checkbox"></th>  
                <th>Name</th>
                <th>Code</th>
                <th>Limit</th>
                <th>Used</th>
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
          ajax: {
            url: "{{ route('coupon/list' )}}",
              data: function(data) {
                  //   data.status =  $("#status").val();a
              }
          },
          columns: [
            { data: "checkbox", orderable:false, searchable:false},
            {data: 'name', name: 'name', orderable: false, searchable: false, sClass: "align-middle table-image"},
            {data: 'code', name: 'code', sClass: "align-middle"},
            {data: 'limit', name: 'limit', sClass: "align-middle"},
            {data: 'used', name: 'used', sClass: "align-middle"},
            {data: 'status', name: 'status', sClass: "align-middle"},
            {data: 'action', name: 'action', orderable: false, searchable: false, sClass: "align-middle no-wrap"},
        ],
          dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
          "drawCallback": function( settings ) {
          feather.replace();
      }
      });

      $('#select-all-checkbox').on('click', function(){
        var rows = tableData.rows({ 'search': 'applied' }).nodes();
        $('.coupon_checkbox', rows).prop('checked', this.checked);
      });
      $('.table tbody').on('change', '.coupon_checkbox', function(){
        // If checkbox is not checked
        if(!this.checked){
          var el = $('#select-all-checkbox').get(0);
          // If "Select all" control is checked and has 'indeterminate' property
          if(el && el.checked && ('indeterminate' in el)){
              // Set visual state of "Select all" control
              // as 'indeterminate'
              el.indeterminate = true;
          }
        }
    });

      $('#status').change(function() {
          $('.datatables-ajax').DataTable().ajax.reload(null, false);
      });
      $(document).on('click', '.delete_all', function(){
        var id = [];
        var closeInSeconds = 2;
        $('.coupon_checkbox:checked').each(function(){
              id.push($(this).val());
          });
        if(id.length > 0)
        {
          $.ajaxSetup({
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
          });
          Swal.fire({
              title: 'Are you sure?',
              text: "You won't be able to revert this!",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Yes, delete it!'
          }).then((result) => {
              if (result.isConfirmed) {
              
                  $.ajax({
                      url:"{{ url('coupon/deleteAll') }}",
                      method:"POST",
                      data:{ids:id},
                      dataType: "json",
                      success:function(data)
                      {
                        if (data.success == 1) {
                            Swal.fire({
                                icon: 'success',
                                title: "Done!",
                                text: "Coupon Deleted",
                                showConfirmButton: false,
                                timer: 2000
                            });
                            tableData.ajax.reload();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: "Error!",
                                text: "Error while Coupon Delete",
                                showConfirmButton: false,
                                timer: 2000
                            });
                            tableData.ajax.reload();
                        }
                      }
                  });
                }
              })  
            }
          else
          {
            toastr.error('Please select atleast one checkbox');
          }      
      });

  $(document).on('click', '.chkStatus', function (e) {
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });
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
          
          dataType: "json",
          success: function(data) {
            if (data.success == 0) {
              Swal.fire({
                    icon: 'error',
                    title: "Error!",
                    text: "Error While Update",
                    showConfirmButton: false,
                    timer: 2000
                });
            }else{
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
                      text: "Coupon Deactivated",
                      showConfirmButton: false,
                      timer: 2000
                  });
                  
              }
            }
          }
      }); 
             
    }
    else{
          if ($(this).prop('checked')==true){ 
            $(this).prop('checked',false);
          }else{
            $(this).prop('checked',true);
          }
        }
    })
      
  });
</script>
@endsection


