@extends('layouts/contentLayoutMaster')

@section('title', 'Device List')

@section('vendor-style')
  {{-- vendor css files --}}
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap5.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/flatpickr/flatpickr.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/animate/animate.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
  <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>

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
          <h4 class="card-title">Device List</h4>
          @if (Auth::user()->user_type != 1)
          <div class="pull-right">
                <a href="{{route('device.create')}}" class="btn btn-primary waves-effect waves-float waves-light">
                    Add New Device
                </a>
                <button class="btn btn-danger delete_all">Delete All Selected</button>

            </div>

          @endif

        </div>
        @if (Auth::user()->user_type == 1)
            <div class="card-body border-top pt-2">
            <h4 class="card-title">Search & Filter</h4>
            <div class="row">
              <div class="col-md-3">
                    <label for="added_by"> Filter by User</label>
                        <select id="added_by" class="form-select user-select">
                            <option value=""> Select User </option>
                            @foreach($users as $value)
                            <option value="{{$value->user_id}}"> {{$value->first_name}} {{$value->last_name}} </option>
                            @endforeach
                        </select>
                </div>

                <div class="col-md-3">
                    <label for="status"> Filter by Status</label>
                        <select id="status" class="form-select text-capitalize mb-md-0 mb-2xx">
                            <option value=""> Select Status </option>
                            <option value="1"> Active </option>
                            <option value="0"> Expired </option>
                        </select>
                </div>
                <div class="col-md-2">
                    <button id="clearFilter" class="btn btn-primary waves-effect waves-float  waves-light mt-2">  Clear Filter </button>
                </div>
                <div class="col-md-4">
                  <button class="btn btn-danger mt-2  float-end delete_all">Delete All Selected</button>
                </div>
            </div>
          </div>
          @endif

        <input type="hidden" id="user_type" value="{{Auth::user()->user_type}}">
        <input type="hidden" id="device_id" value="@if(isset($device_id)){{$device_id}}@endif">

        <div class="card-datatable">
          <div class="p-1" style="width: 100%;">

            <div class="table-responsive">
              <table class="datatables-ajax table">
                <thead>
                  <tr>
                    <th></th>
                    <th class="all" width="50px"><input type="checkbox"  id="select-all-checkbox"></th>
                    <th class="all">Code</th>
                    @if (Auth::user()->user_type == 1)
                    <th class="all">Created at</th>
                    @endif
                    @if (Auth::user()->user_type != 1)
                    <th class="none">Device Type</th>
                    <th class="none">IP Address</th>
                    <th class="none">Mac Address</th>
                    @endif
                    <th class="none">Platform</th>
                    <th class="@if(Auth::user()->user_type == 1){{'none'}}@else{{'all'}}@endif">App Name</th>
                    <th class="@if(Auth::user()->user_type == 1){{'none'}}@else{{'all'}}@endif">Device Title</th>
                    <th class="all">Expire Date</th>
                    <th class="all">Is Cloud Sync</th>
                    <th class="all">Is Code Auto Renew</th>
                    @if (Auth::user()->user_type == 1)
                    <th class="all">Added by</th>
                    @endif
                    <th class="all">Code Status</th>
                    <th class="all">Device Status</th>
                    <th class="action-btn all">Action</th>
                  </tr>
                </thead>
              </table>
              </div>
           </div>
        </div>
      </div>
    </div>
  </div>
</section>
@include('content/_partials/_modals/modal-device-details')
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
  <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>

@endsection

@section('page-script')
  {{-- Page js files --}}
  <script>
if($('#user_type').val() == 1){
      var tableData = $('.datatables-ajax').DataTable({
        responsive: {
          type: 'column'
        },
        stateSave: true,
        processing: true,
        serverSide: true,
        ordering: false,
        paging: true,
        deferRender: true,
        dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        ajax: {
          headers: {
           'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: "{{url('device/list/0')}}",
          data: function(data) {
                  data.added_by = $("#added_by").val();
                  data.status = $("#status").val();
              }
        },
        // "columnDefs": [
        //   { "width": "20%", "targets": 7 }
        // ],
        columns: [
          {
                "className":      'details-control',
                "orderable":      false,
                "data":           null,
                "defaultContent": '',
          },
            { data: "checkbox", className: 'select-checkbox', orderable:false, searchable:false},
            {data: 'code', name: 'code',sClass: "align-middle"},
            {data: 'created_at', name: 'created_at',sClass: "align-middle"},
            {data: 'platform', name: 'platform', sClass: "align-middle"},
            {data: 'app_name', name: 'app_name', sClass: "align-middle"},
            {data: 'device_title', name: 'device_title', sClass: "align-middle"},
            {data: 'expire_date', name: 'expire_date', sClass: "align-middle"},
            {data: 'is_cloud_sync', name: 'is_cloud_sync', sClass: "align-middle"},
            {data: 'is_code_auto_renew', name: 'is_code_auto_renew', sClass: "align-middle"},
            {data: 'added_by', name: 'added_by', sClass: "align-middle"},
            {data: 'status', name: 'status', sClass: "align-middle"},
            {data: 'is_active', name: 'is_active', sClass: "align-middle"},
            {data: 'action', name: 'action',sClass: "align-middle no-wrap"},
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
  }else{
    var tableData = $('.datatables-ajax').DataTable({
        responsive: {
          type: 'column'
        },
        processing: true,
        serverSide: true,
        paging: true,
        deferRender: true,
        dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        ajax: {
            url: "{{url('device/list/0')}}",
        },
        // "columnDefs": [
        //   { "width": "20%", "targets": 7 }
        // ],
        columns: [
          {
                "className":      'details-control',
                "orderable":      false,
                "data":           null,
                "defaultContent": '',
          },
          { data: "checkbox", orderable:false, searchable:false},
            {data: 'code', name: 'code',sClass: "align-middle"},
            {data: 'device_type', name: 'device_type', sClass: "align-middle"},
            {data: 'ip_address', name: 'ip_address', sClass: "align-middle"},
            {data: 'mac_address', name: 'mac_address', sClass: "align-middle"},
            {data: 'platform', name: 'platform', sClass: "align-middle"},
            {data: 'app_name', name: 'app_name', sClass: "align-middle"},
            {data: 'device_title', name: 'device_title', sClass: "align-middle"},
            {data: 'expire_date', name: 'expire_date', sClass: "align-middle"},
            {data: 'is_cloud_sync', name: 'is_cloud_sync', sClass: "align-middle"},
            {data: 'is_code_auto_renew', name: 'is_code_auto_renew', sClass: "align-middle"},
            {data: 'status', name: 'status', sClass: "align-middle"},
            {data: 'is_active', name: 'is_active', sClass: "align-middle"},
            {data: 'action', name: 'action',sClass: "align-middle no-wrap"},
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
  }


    jQuery(document).ready(function () {
      $(".user-select").select2().on('select2:open', function(e){
          $('.select2-search__field').attr('placeholder', 'Search User');
      });

      $('#select-all-checkbox').on('click', function(){
        var rows = tableData.rows({ 'search': 'applied' }).nodes();
        $('.device_checkbox', rows).prop('checked', this.checked);
    });
    $('.table tbody').on('change', '.device_checkbox', function(){
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
      var device_id = $("#device_id").val();
      if(device_id){
        $.ajax({
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ url('device/get_device_code') }}/" + device_id,
            type: "POST",
            dataType: "json",
            success: function(data) {
              if (data.data) {
                $("#activation-code").text(data.data.code);
                $("#expiration-date").text(data.data.expire_date);
                // $("#expiration-diff").text(data.data.expire_diff + ' hours left');
                $("#deviceCodeMdl").modal('show');
              }
            }
        });
      }
      $('#deviceCodeMdl').on('hidden.bs.modal', function () {
         window.location.href = '{{url("device/list/0")}}';
      });

      $(document).on('click', '.delete_all', function(){
        var id = [];
        var closeInSeconds = 2;
        $('.device_checkbox:checked').each(function(){
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
                      url:"{{ url('device/deleteAll') }}",
                      method:"POST",
                      data:{ids:id},
                      dataType: "json",
                      success:function(data)
                      {
                        if (data.success == 1) {
                            Swal.fire({
                                icon: 'success',
                                title: "Done!",
                                text: "Device Deleted",
                                showConfirmButton: false,
                                timer: 2000
                            });
                            tableData.ajax.reload();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: "Error!",
                                text: "Error while Device Delete",
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

      $(document).on('click', '.changeDeviceCodeStatus', function (e) {
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
                  url: "{{ url('device/change_device_code_status') }}/" + id,
                  type: "POST",
                  dataType: "json",
                  success: function(data) {
                    if (data.enable) {
                        Swal.fire({
                            icon: 'success',
                            title: "Done!",
                            text: "Device Active",
                            showConfirmButton: false,
                            timer: 2000
                        });
                        tableData.ajax.reload();
                    } else {
                        Swal.fire({
                            icon: 'success',
                            title: "Done!",
                            text: "Device Inactive",
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

      });

      $(document).on('click', '.activeDeactiveDevice', function (e) {
        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });
          var id = this.value;
          var closeInSeconds = 2;

          $.ajax({
                  url: "{{ url('device/active_deactive_device') }}/" + id,
                  type: "POST",
                  dataType: "json",
                  success: function(data) {
                    if (data.enable) {
                        Swal.fire({
                            icon: 'success',
                            title: "Done!",
                            text: "Device Activate Successfully",
                            showConfirmButton: false,
                            timer: 2000
                        });
                        tableData.ajax.reload();
                    } else {
                        Swal.fire({
                            icon: 'success',
                            title: "Done!",
                            text: "Device Deactivate Successfully",
                            showConfirmButton: false,
                            timer: 2000
                        });
                        tableData.ajax.reload();
                    }
                  }
              });
        //   Swal.fire({
        //       title: 'Are you sure?',
        //       text: "You won't be able to revert this!",
        //       icon: 'warning',
        //       showCancelButton: true,
        //       confirmButtonColor: '#3085d6',
        //       cancelButtonColor: '#d33',
        //       confirmButtonText: 'Yes, change it!'
        //   }).then((result) => {
        //       if (result.isConfirmed) {


        //     }else{
        //       if ($(this).prop('checked')==true){
        //         $(this).prop('checked',false);
        //       }else{
        //         $(this).prop('checked',true);
        //       }
        //     }
        // })

      });

      $(document).on('click', '.changeCloudStatus', function (e) {
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
                  url: "{{ url('device/change_cloud_status') }}/" + id,
                  type: "POST",
                  dataType: "json",
                  success: function(data) {
                    if (data.enable) {
                        Swal.fire({
                            icon: 'success',
                            title: "Done!",
                            text: "Device Cloud Sync Enable",
                            showConfirmButton: false,
                            timer: 2000
                        });
                        tableData.ajax.reload();
                    } else {
                        Swal.fire({
                            icon: 'success',
                            title: "Done!",
                            text: "Device Cloud Sync Disable",
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

      });

      $(document).on('click', '.changeCodeAutoRenew', function (e) {
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
                  url: "{{ url('device/change_code_renew_status') }}/" + id,
                  type: "POST",
                  dataType: "json",
                  success: function(data) {
                    if (data.enable) {
                        Swal.fire({
                            icon: 'success',
                            title: "Done!",
                            text: "Device Code Auto Renew Enable",
                            showConfirmButton: false,
                            timer: 2000
                        });
                        tableData.ajax.reload();
                    } else {
                        Swal.fire({
                            icon: 'success',
                            title: "Done!",
                            text: "Device Code Auto Renew Disable",
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

      });
    });
      function renewCode(url, token) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'you want to renew code?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, renew it!',
            customClass: {
                confirmButton: 'btn btn-primary',
                cancelButton: 'btn btn-outline-danger ms-1'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    data: {
                        _method: 'POST',
                        _token: token
                    },
                    url: url,
                    success: function (data) {
                        if(data.success == 1){
                              Swal.fire({
                                icon: 'success',
                                title: 'Renew Code!',
                                text: 'Your device code has been renewd.',
                                showConfirmButton: false,
                                timer: 2000
                              });
                        }else{
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Error While Renew Code.',
                                // customClass: {
                                //   confirmButton: 'btn btn-danger'
                                // }
                                showConfirmButton: false,
                                timer: 2000
                              });
                        }
                        tableData.ajax.reload();
                        // setTimeout(function () {
                        //     window.location.reload();
                        // }, 500);
                    },
                    error: function (data) {
                        toastr.error('Something went wrong');
                    }
                });
            }
        });
    }
    jQuery(document).ready(function () {

        $(document).on('change','#added_by',function() {
            tableData.ajax.reload();
        });
        $(document).on('change','#status',function() {
            tableData.ajax.reload();
        });
        $(document).on('click','#clearFilter',function() {
          $(".user-select").select2("destroy").val('').select2();
            $("#status").val("");
            tableData.ajax.reload();
        });

      });
</script>
@endsection
