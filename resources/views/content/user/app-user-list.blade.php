@extends('layouts/contentLayoutMaster')

@section('title', 'User List')

@section('vendor-style')
  {{-- Page Css files --}}
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap5.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/buttons.bootstrap5.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/rowGroup.bootstrap5.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/animate/animate.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">
@endsection

@section('page-style')
  {{-- Page Css files --}}
  <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-validation.css')) }}">
  <link rel="stylesheet" href="{{asset(mix('css/base/plugins/extensions/ext-component-sweet-alerts.css'))}}">

@endsection

@section('content')
<!-- users list start -->
<section class="app-user-list">
  <div class="row">
  @if(Auth::user()->user_type == 3)
    <div class="col-lg-3 col-sm-6">
      <div class="card">
        <div class="card-body d-flex align-items-center justify-content-between">
          <div>
            <h3 class="fw-bolder mb-75">{{$total_subreseller}}</h3>
            <span>Total Sub Resellers </span>
          </div>
          <div class="avatar bg-light-primary p-50">
            <span class="avatar-content">
              <i data-feather="user" class="font-medium-4"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
    @endif
    @if(Auth::user()->user_type == 1)
    <div class="col-lg-4 col-sm-6">
      <div class="card">
        <div class="card-body d-flex align-items-center justify-content-between">
          <div>
            <h3 class="fw-bolder mb-75">{{$total_reseller}}</h3>
            <span>Total Resellers </span>
          </div>
          <div class="avatar bg-light-primary p-50">
            <span class="avatar-content">
              <i data-feather="user" class="font-medium-4"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-4 col-sm-6">
      <div class="card">
        <div class="card-body d-flex align-items-center justify-content-between">
          <div>
            <h3 class="fw-bolder mb-75">{{$total_subreseller}}</h3>
            <span>Total Sub Resellers </span>
          </div>
          <div class="avatar bg-light-primary p-50">
            <span class="avatar-content">
              <i data-feather="user" class="font-medium-4"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-4 col-sm-6">
      <div class="card">
        <div class="card-body d-flex align-items-center justify-content-between">
          <div>
            <h3 class="fw-bolder mb-75">{{$total_customer}}</h3>
            <span>Total Customer </span>
          </div>
          <div class="avatar bg-light-primary p-50">
            <span class="avatar-content">
              <i data-feather="user" class="font-medium-4"></i>
            </span>
          </div>
        </div>
      </div>
    </div>

    <div class="@if(Auth::user()->user_type == 1){{'col-lg-4'}}@else{{'col-lg-3'}}@endif col-sm-6">
      <div class="card">
        <div class="card-body d-flex align-items-center justify-content-between">
          <div>
            <h3 class="fw-bolder mb-75 verified_user">{{$verified_user}}</h3>
            <span>Verified Users</span>
          </div>
          <div class="avatar bg-light-warning p-50">
            <span class="avatar-content">
            <i data-feather="user-check" class="font-medium-4"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
    <div class="@if(Auth::user()->user_type == 1){{'col-lg-4'}}@else{{'col-lg-3'}}@endif col-sm-6">
      <div class="card">
        <div class="card-body d-flex align-items-center justify-content-between">
          <div>
            <h3 class="fw-bolder mb-75 active_user">{{$active_user}}</h3>
            <span>Active Users</span>
          </div>
          <div class="avatar bg-light-success p-50">
            <span class="avatar-content">
              <i data-feather="user-plus" class="font-medium-4"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
    <div class="@if(Auth::user()->user_type == 1){{'col-lg-4'}}@else{{'col-lg-3'}}@endif col-sm-6">
      <div class="card">
        <div class="card-body d-flex align-items-center justify-content-between">
          <div>
            <h3 class="fw-bolder mb-75 suspended_user">{{$suspended_user}}</h3>
            <span>Inactive Users</span>
          </div>
          <div class="avatar bg-light-danger p-50">
            <span class="avatar-content">
              <i data-feather="user-x" class="font-medium-4"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
    @endif
  </div>
  <!-- list and filter start -->
  <div class="card">
    <div class="card-body border-bottom">
      <h4 class="card-title">Search & Filter</h4>
      <div class="row">
        @if(Auth::user()->user_type == 1)
        <div class="col-md-4">
            <label class="form-label" for="user_type"> Filter by User Role</label>
                  <select id="user_type" class="form-select text-capitalize mb-md-0 mb-2xx">
                    <option value=""> Select Role </option>
                    <option value="2" class="text-capitalize">Customer</option>
                    <option value="3" class="text-capitalize">Reseller</option>
                    <option value="4" class="text-capitalize">Sub Reseller</option>
                  </select>
          </div>
          @endif
        <div class="col-md-4">
          <label class="form-label" for="status"> Filter by Status</label>
                  <select id="status" class="form-select text-capitalize mb-md-0 mb-2xx">
                    <option value=""> Select Status </option>
                    <option value="1" class="text-capitalize">Active</option>
                    <option value="2" class="text-capitalize">Inactive</option>
                  </select>
          </div>

          <div class="col-md-4">
          <label class="form-label" for="is_verified"> Filter by Verified</label>
                  <select id="is_verified" class="form-select text-capitalize mb-md-0 mb-2xx">
                    <option value=""> Select Is Verified </option>
                    <option value="1" class="text-capitalize">Verifed</option>
                    <option value="2" class="text-capitalize">Unverified</option>
                  </select>
          </div>

          </div>
    </div>
    <div class="card-datatable">
      <div class="me-3 mt-1" style="display: flow-root;">

      <div class="float-end">
            <button class="btn btn-danger ms-1  float-end delete_all">Delete All Selected</button>
          </div>
          <div class="float-end">
              <a href="{{route('users.create')}}" class="btn btn-primary waves-effect waves-float waves-light">
              @if(Auth::user()->user_type == 1) {{'Add User'}} @else {{'Add Sub Reseller'}} @endif
              </a>
          </div>
        </div>
      <table class="datatables-ajax table table-responsive users-list">
        <thead>
          <tr>
          <th width="50px"><input type="checkbox" id="select-all-checkbox"></th>
            <th>Name</th>
            <th>User Type</th>
            <th>Added By</th>
            <th>Contact</th>
            <th>Credit</th>
            <th>Is Verified</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
      </table>
    </div>

  </div>
  <!-- list and filter end -->
</section>
<!-- users list ends -->
@include('content/_partials/_modals/modal-update-credits')
@endsection

@section('vendor-script')
  {{-- Vendor js files --}}
  <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
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
  <script src="{{ asset(mix('vendors/js/forms/validation/jquery.validate.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/forms/validation/additional-methods.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/forms/cleave/cleave.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/forms/cleave/addons/cleave-phone.us.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/extensions/polyfill.min.js')) }}"></script>
@endsection

@section('page-script')
<script src="{{ asset(mix('js/scripts/extensions/ext-component-sweet-alerts.js')) }}"></script>
  {{-- Page js files --}}

  <script>
    var tableData;
  $(function () {
    tableData = $('.datatables-ajax').DataTable({
          stateSave: true,
          processing: true,
          serverSide: true,
          paging: true,
          deferRender: true,
          ajax: {
            url: "{{ route('users/list') }}",
            data: function(data){
              data.status =  $("#status").val();
              data.is_verified =  $("#is_verified").val();
              data.user_type =  $("#user_type").val();
            }
          },
          columns: [
            { data: "checkbox", orderable:false, searchable:false},
            {data: 'name', name: 'first_name', sClass: "align-middle"},
            {data: 'user_type', name: 'user_type', sClass: "align-middle"},
            {data: 'added_by', name: 'added_by', sClass: "align-middle"},
            {data: 'phone_no', name: 'phone_no', sClass: "align-middle"},
            {data: 'credits', name: 'credits', sClass: "align-middle"},
            {data: 'is_verified', name: 'is_verified', sClass: "align-middle"},
            {data: 'status', name: 'status', sClass: "align-middle"},
            {data: 'action', name: 'action', orderable: false, searchable: false, sClass: "align-middle no-wrap"},
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
        "drawCallback": function( settings ) {
          feather.replace();
      }
      });
      $('#user_type').change(function(){
        $('.datatables-ajax').DataTable().ajax.reload(null, false);
      });
      $('#status').change(function(){
        $('.datatables-ajax').DataTable().ajax.reload(null, false);
      });
      $('#is_verified').change(function(){
        $('.datatables-ajax').DataTable().ajax.reload(null, false);
      });

    });
    jQuery(document).ready(function () {
      $('#select-all-checkbox').on('click', function(){
        var rows = tableData.rows({ 'search': 'applied' }).nodes();
        $('.user_checkbox', rows).prop('checked', this.checked);
      });
      $('.table tbody').on('change', '.user_checkbox', function(){
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
      $('#updateCreditsMdl').on('hidden.bs.modal', function(e) {
            $("#updateUserCredits")[0].reset();
            $("#deductUserCredits")[0].reset();
            $('.user_id').val("");
            $('.credits').val("");
            var validator = $("#updateUserCredits").validate();
            validator.resetForm();
            $("#updateUserCredits").find('.error').removeClass("error");
            var validator1 = $("#deductUserCredits").validate();
            validator1.resetForm();
            $("#deductUserCredits").find('.error').removeClass("error");
        });
      $(document).on("keyup", ".credits1", function() {
            var validator1 = $("#deductUserCredits").validate();
            validator1.resetForm();
      });
      $(document).on("keyup", ".credits2", function() {
            var validator1 = $("#updateUserCredits").validate();
            validator1.resetForm();
      });
      $(document).on("click", ".updateCredits", function() {
          $('.user_id').val($(this).attr('data-user_id'));
          $('#max_deduct_credits').val($(this).attr('data-credits'));
      });

      $(document).on('click', '.delete_all', function(){
        var id = [];
        var closeInSeconds = 2;
        var is_reseller = 0;
        $('.user_checkbox:checked').each(function(){
            if($(this).attr('data-is_subreseller') == 0){
              id.push($(this).val());
            }
            if($(this).attr('data-is_subreseller') == 1){
              is_reseller = 1;
            }
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
                      url:"{{ url('user/deleteAll') }}",
                      method:"POST",
                      data:{ids:id},
                      dataType: "json",
                      success:function(data)
                      {
                        if (data.success == 1) {
                            if(is_reseller == 1){
                              Swal.fire({
                                title: 'You can not inactivate, delete or change the type of some reseller',
                                text: 'because there is a subreseller inside some reseller. First you have to change the user type of all the subreseller inside it, only then you can deactivate, delete or change its type.',
                                icon: 'warning',
                                showCancelButton: false,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Ok!!',
                                customClass: {
                                    confirmButton: 'btn btn-primary',
                                    cancelButton: 'btn btn-outline-danger ms-1'
                                },
                                buttonsStyling: false
                              }).then((result) => {
                                  if (result.isConfirmed) {
                                    if ($(this).prop('checked')==true){
                                      $(this).prop('checked',false);
                                    }else{
                                      $(this).prop('checked',true);
                                    }
                                      return false;
                                  }
                              });
                            }

                            Swal.fire({
                                icon: 'success',
                                title: "Done!",
                                text: "User Deleted",
                                showConfirmButton: false,
                                timer: 2000
                            });
                            tableData.ajax.reload();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: "Error!",
                                text: "Error while User Delete",
                                showConfirmButton: false,
                                timer: 2000
                            });
                            tableData.ajax.reload();
                        }
                      }
                  });
                }
              })
            }else if(is_reseller == 1){
              Swal.fire({
                title: 'You can not inactivate, delete or change the type of some reseller',
                text: 'because there is a subreseller inside some reseller. First you have to change the user type of all the subreseller inside it, only then you can deactivate, delete or change its type.',
                icon: 'warning',
                showCancelButton: false,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ok!!',
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-outline-danger ms-1'
                },
                buttonsStyling: false
              }).then((result) => {
                  if (result.isConfirmed) {
                    if ($(this).prop('checked')==true){
                      $(this).prop('checked',false);
                    }else{
                      $(this).prop('checked',true);
                    }
                      return false;
                  }
              });
            }
          else
          {
            toastr.error('Please select atleast one checkbox');
          }
      });
      var validator = $('#updateUserCredits').validate({
              rules: {
                'credits': {
                    required: true,
                    max: {
                        param: function (element) {
                          return parseInt($('#max_admin_credits').val())
                        },
                        depends: function (element) {
                          if($(".admin_user_type").val() != 1){ return true };
                        }
                    }
                  },
              },
              messages: {
                  'credits': {
                      required: 'Please Enter Credits',
                  },
              }
          });

          var validator1 = $('#deductUserCredits').validate({
              rules: {
                'credits': {
                    required: true,
                    max: {
                        param: function (element) {
                          return parseInt($('#max_deduct_credits').val())
                        }
                    }
                  },
              },
              messages: {
                  'credits': {
                      required: 'Please Enter Credits',
                  },
              }
          });

      $(document).on('submit', '#updateUserCredits', function (e) {
            e.preventDefault();
            $('.loader').show();
            var formdata = new FormData($("#updateUserCredits")[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route("updateUserCredits") }}',
                type: 'POST',
                data: formdata,
                dataType: "json",
                contentType: false,
                cache: false,
                processData: false,
                success: function (data) {
                    $('.loader').hide();
                    if (data.success == 1) {
                        toastr.success('Credits update');
                    } else {
                        toastr.error(data.message);
                    }
                    $("#max_admin_credits").val(data.data.auth_credit);
                    $(".credits1").attr('max',data.data.auth_credit);
                    $(".credits2").attr('max',data.data.auth_credit);
                    $('#updateCreditsMdl').modal('hide');
                    tableData.ajax.reload();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert(errorThrown);
                }
            });

        });


      $(document).on('submit', '#deductUserCredits', function (e) {
            e.preventDefault();
            $('.loader').show();
            var formdata = new FormData($("#deductUserCredits")[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route("updateUserCredits") }}',
                type: 'POST',
                data: formdata,
                dataType: "json",
                contentType: false,
                cache: false,
                processData: false,
                success: function (data) {
                    $('.loader').hide();
                    if (data.success == 1) {
                        toastr.success('Credits update');
                    } else {
                        toastr.error(data.message);
                    }

                    $('#updateCreditsMdl').modal('hide');
                        tableData.ajax.reload();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert(errorThrown);
                }
            });

        });

      });

</script>

<script>
  $(document).on('click', '.chkStatus', function (e) {
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });
      var id = this.value;
      var is_subreseller = $(this).attr('data-is_subreseller');
      if(is_subreseller == 1){
        Swal.fire({
            title: 'You can not inactivate, delete or change the type of this reseller',
            text: 'because there is a subreseller inside this reseller. First you have to change the user type of all the subreseller inside it, only then you can deactivate, delete or change its type.',
            icon: 'warning',
            showCancelButton: false,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ok!!',
            customClass: {
                confirmButton: 'btn btn-primary',
                cancelButton: 'btn btn-outline-danger ms-1'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
              if ($(this).prop('checked')==true){
                $(this).prop('checked',false);
              }else{
                $(this).prop('checked',true);
              }
                return false;
            }
        });
    }else{

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
              url: "{{ url('users/change_user_status') }}/" + id,
              type: "POST",
              dataType: "json",
              success: function(data) {
                if (data.active) {
                    Swal.fire({
                        icon: 'success',
                        title: "Done!",
                        text: "User Account Activated",
                        showConfirmButton: false,
                        timer: 2000
                    });
                    tableData.ajax.reload();
                    $(".active_user").text(data.active_user);
                    $(".suspended_user").text(data.suspended_user);

                } else {
                    Swal.fire({
                        icon: 'success',
                        title: "Done!",
                        text: "User Account Suspended",
                        showConfirmButton: false,
                        timer: 2000
                    });
                    tableData.ajax.reload();
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
    }

  });

  $(document).on('click', '.chkIsVerified', function (e) {
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
              url: "{{ url('users/verified_user') }}/" + id,
              type: "POST",
              dataType: "json",
              success: function(data) {
                if (data.active) {
                    Swal.fire({
                        icon: 'success',
                        title: "Done!",
                        text: "Your account has been successfully verified.",
                        showConfirmButton: false,
                        timer: 2000
                    });
                    $(".verified_user").text(data.verified_user);
                    tableData.ajax.reload();
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

  });

  function takeAccess(url, redirectTo, leaveUrl) {
  var tab;
  $.ajax({
    url: url,
    type: "GET",
    success: function (response) {
      tab = window.open(redirectTo);
      Swal.fire({
        closeOnClickOutside: false,
        title: "Access",
        text: "You have taken an access of other user, you want to leave?",
        icon: "warning",
        buttons: {
          cancel: false,
          confirm: true,
        }
      }).then((willDelete) => {
        if (willDelete) {
          tab.close();
          $.ajax({
            url: leaveUrl,
            type: "GET",
            dataType: 'json',
            success: function (response) {},
            error: function (err) {}
          });
        }
      });
    },
    error: function (err) {
      if (err.responseJSON.message) {
        toastr.error(err.responseJSON.message);
      } else {
        toastr.error('Something went wrong');
      }
    }
  });
}

</script>
@endsection
