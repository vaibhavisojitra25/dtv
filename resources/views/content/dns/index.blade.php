@extends('layouts/contentLayoutMaster')

@section('title', 'DNS')

@section('vendor-style')
  {{-- vendor css files --}}
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap5.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/flatpickr/flatpickr.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/animate/animate.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">

@endsection
@section('page-style')
  {{-- Page css files --}}
  <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-validation.css')) }}">
  <link rel="stylesheet" href="{{asset(mix('css/base/plugins/extensions/ext-component-sweet-alerts.css'))}}">
@endsection

@section('content')
<section id="ajax-datatable">

  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header border-bottom">
          <h4 class="card-title">DNS List</h4>
          <div class="pull-right">
            <a data-bs-toggle="modal" data-bs-target="#addUpdateDNSMdl" class="btn btn-primary waves-effect waves-float waves-light addDNS">
                Add New DNS
            </a> 
            <button class="btn btn-danger delete_all" data-type="1">Delete All Selected</button> 
            </div>
        </div>
        <div class="card-body">
            <div class="card-datatable">
                <div class="table-responsive">
                    <table class="datatables-dns table">
                    <thead>
                        <tr>
                        <th width="50px"><input type="checkbox" id="select-dns-all-checkbox"></th>  
                        <th>URL</th>
                        <th>Action</th> 
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

@include('content/_partials/_modals/modal-addupdate-dns')

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
  <script src="{{asset('vendors/js/forms/validation/jquery.validate.min.js')}}"></script>
  <script src="{{ asset(mix('vendors/js/forms/validation/additional-methods.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>

@endsection

@section('page-script')
  {{-- Page js files --}}
<script>
    var tableData = $('.datatables-dns').DataTable({
        stateSave: true,
        processing: true,
        serverSide: true,
        autoWidth: false,
        paging: true,
        deferRender: true,
        dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        ajax: {
            url: "{{route('dns/list')}}",
            data: function ( data ) {
            },
        },
        columns: [
            // {data: 'code', name: 'code', orderable: false, searchable: false, sClass: "align-middle table-image"},
            { data: "checkbox", orderable:false, searchable:false},
            {data: 'dns_url', name: 'dns_url', sClass: "align-middle"},
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

    jQuery(document).ready(function () {
  
      $('#select-dns-all-checkbox').on('click', function(){
        var rows = tableData.rows({ 'search': 'applied' }).nodes();
        $('.dns_checkbox', rows).prop('checked', this.checked);
      });
        $('.table tbody').on('change', '.dns_checkbox', function(){
          // If checkbox is not checked
          if(!this.checked){
            var el = $('#select-dns-all-checkbox').get(0);
            // If "Select all" control is checked and has 'indeterminate' property
            if(el && el.checked && ('indeterminate' in el)){
                // Set visual state of "Select all" control
                // as 'indeterminate'
                el.indeterminate = true;
            }
          }
      });

      $('#addUpdateDNSMdl').on('hidden.bs.modal', function(e) {
        $("#dns_id").val("");
          $("#addUpdateDNSForm")[0].reset();
          var validator = $("#addUpdateDNSForm").validate();
          validator.resetForm();
      });

      $(document).on('click',".updateDNS",function(){
        $("#mdlTitle").text('Edit DNS');
        $('#dns_id').val($(this).attr('data-id'));
        $('#dns_url').val($(this).attr('data-dns_url'));

      });

    var message = '';

    var validator = $('#addUpdateDNSForm').validate({
        rules: {
            'dns_url': {
                required: true,
                url: true,
                nowhitespace: true,
                remote: {
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '{{ url("checkExistDNS") }}',
                    type: "post",
                    data: {
                        dns_url: function () { return $("#dns_url").val(); },
                        dns_id: function () { return $("#dns_id").val(); },
                    }
                }
            },
        },
        messages: {
            'dns_url': {
                required: '*Please Enter DNS',
                nowhitespace: 'Please Remove Space',
                remote: 'DNS already exist'
            },
        }
    });

      $(document).on('submit', '#addUpdateDNSForm', function (e) {
        e.preventDefault();
        var formdata = new FormData($("#addUpdateDNSForm")[0]);
        if($("#dns_id").val()){
          var url = '{{ route("dnsUpdate") }}';
        }else{
          var url = '{{ route("dns.store") }}';
        }
        $.ajax({
            url: url,
            type: 'POST',
            data: formdata,
            dataType: "json",
            contentType: false,
            cache: false,
            processData: false,
            success: function (data) {
                $('.loader').hide();
                $('#addUpdateDNSMdl').modal('hide');
                if (data.success == 1) {
                    toastr.success(data.message);
                    tableData.ajax.reload();
                } else {
                    toastr.error(data.message);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert(errorThrown);
            }
        }); 
    });
    
    });
    jQuery(document).ready(function () {
   
      $(document).on('click', '.delete_all', function(){
        var id = [];
        var closeInSeconds = 2;
        var is_playlist = 0;
        $('.dns_checkbox:checked').each(function(){
            if($(this).attr('data-is_playlist') == 0){
              id.push($(this).val());
            }
            if($(this).attr('data-is_playlist') == 1){
              is_playlist = 1;
            }
          });
        if(id.length > 0)
        {
          var type = $(this).attr('data-type');
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
                      url:"{{ url('dns/deleteAll') }}",
                      method:"POST",
                      data:{ids:id,type:type},
                      dataType: "json",
                      success:function(data)
                      {
                        if (data.success == 1) {

                          if(is_playlist == 1){
                            Swal.fire({
                              title: 'This DNS cannot be deleted because it is used in the playlist.',
                              text: 'If you want to delete it then first you will have to change the DNS from the playlist.',
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
                              text: "DNS Deleted",
                              showConfirmButton: false,
                              timer: 2000
                          });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: "Error!",
                                text: "Error while DNS Delete",
                                showConfirmButton: false,
                                timer: 2000
                            });
                        }

                        tableData.ajax.reload();
                    }
                  });
                }
              })  
            }
          else if(is_playlist == 1){
            Swal.fire({
              title: 'This DNS cannot be deleted because it is used in the playlist.',
              text: 'If you want to delete it then first you will have to change the DNS from the playlist.',
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
            toastr.error('Please select atleast one checkbox');
          }      
      });
    });
</script>
@endsection


