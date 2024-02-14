@extends('layouts/contentLayoutMaster')

@section('title', 'Playlist')

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
  <style>
    .select2:before {
    content: "";
    position: absolute;
    right: 7px;
    top: 42%;
    border-top: 5px solid #888;
    border-left: 4px solid transparent;
    border-right: 4px solid transparent;
}
</style>
@endsection

@section('content')
<section id="ajax-datatable">
<div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header border-bottom">
          <h4 class="card-title">Assign Playlist To Device</h4>
        </div>
        <div class="card-body">
            <form class="form form-vertical" method="post" id="addPlaylistForm">
            {{csrf_field()}}
                <div class="row">
                    <div class="col-3">
                        <div class="mb-1">
                            <label class="form-label" for="mac_id">Mac ID</label>
                            <input type="text" id="mac_id" class="form-control" name="mac_id" readonly>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="mb-1">
                            <label class="form-label" for="mac_key">Mac Key</label>
                            <input type="text" id="mac_key" class="form-control" name="mac_key" readonly>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="mb-1">
                            <label class="form-label" for="playlist_id">Playlist</label>
                            <select id="playlist_id" class="playlist_id form-select" name="playlist_id[]" multiple="multiple">
                                <option value="" disabled>--Select--</option>
                                @if($playlist)
                                @foreach($playlist as $value)
                                <option value="@if(isset($value['id'])){{$value['id']}}@else{{$value['unique_id']}}@endif">{{$value['playlist_name']}}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-4">
                        <button class="btn btn-primary me-1 waves-effect waves-float waves-light mt-2">Add Playlist to Device</button>
                    </div>
                </div>
            </form>

            </div>
        </div>
    </div>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header border-bottom">
          <h4 class="card-title">Playlist List</h4>
        </div>
        <div class="card-datatable">
          <div class="me-3" style="display: flow-root;">
                <div class="float-end mt-1">
                    <!-- <a href="{{route('playlist.create')}}?type=1" class="btn btn-primary waves-effect waves-float waves-light">
                        Add New Playlist
                    </a> -->
                    <a data-bs-toggle="modal" data-bs-target="#addUpdatePlaylistMdl" data-playlist_limit="{{$is_limit}}" data-type="1" class="btn btn-primary waves-effect waves-float waves-light addPlaylist">
                        Add New Playlist
                    </a> 
                </div>
              </div>

                <table class="datatables-xtream table" style="width:100%">
                    <thead>
                        <tr>
                        <th>Name</th>
                        <th>URL</th>
                        <th>Username</th>
                        <th>Password</th>
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

@include('content/_partials/_modals/modal-addupdate-playlist')

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
    <script src="{{ asset(mix('js/scripts/pages/modal-addupdate-playlist.js')) }}"></script>

<script>
     jQuery(document).ready(function () {
        if($(".playlist_id").length() > 0){
            $(".playlist_id").wrap('<div class="position-relative"></div>');
            $(".playlist_id").select2({
                dropdownAutoWidth: true,
                maximumSelectionLength: 10,
                width: '100%',
                dropdownParent: $(".playlist_id").parent()
            });
        }
    });
    $(function() {
        "use strict";
        $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e){
            $($.fn.dataTable.tables(true)).DataTable()
            .columns.adjust();
        });
    });
    var xtreamTableData = $('.datatables-xtream').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        ajax: {
            url: "{{route('playlist/list')}}",
            data: function ( data ) {
            data.type=1;
            },
        },
        columns: [
            // {data: 'code', name: 'code', orderable: false, searchable: false, sClass: "align-middle table-image"},
            {data: 'playlist_name', name: 'playlist_name', sClass: "align-middle"},
            {data: 'dns', name: 'dns', sClass: "align-middle"},
            {data: 'username', name: 'username', sClass: "align-middle"},
            {data: 'password', name: 'password', sClass: "align-middle"},
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


    jQuery(document).ready(function () {
  
      $('#addUpdatePlaylistMdl').on('hidden.bs.modal', function(e) {
          $("#addUpdatePlaylistForm")[0].reset();
          var validator = $("#addUpdatePlaylistForm").validate();
          validator.resetForm();
      });


    var message = '';

    var validator = $('#addUpdatePlaylistForm').validate({
        rules: {
            'playlist_name': {
                required: true,
                nowhitespace: true
            },
            'type': {
                required: true
            },
            'dns': {
                required: function() {
                    if ($('#type').val() == 1) {
                        return true;
                    }
                    else {
                        return false;
                    }
                },
                url: true,
                nowhitespace: true
            },
            'username': {
                required: function() {
                    if ($('#type').val() == 1) {
                        return true;
                    }
                    else {
                        return false;
                    }
                }, 
                nowhitespace: true                  
            },
            'password': {
                required: function() {
                    if ($('#type').val() == 1) {
                        return true;
                    }
                    else {
                        return false;
                    }
                },
                nowhitespace: true
            },
            'm3u_url': {
                required: function() {
                    if ($('#type').val() == 2) {
                        return true;
                    }
                    else {
                        return false;
                    }
                },
                url: true,
                nowhitespace: true
            },
            'epg':{
               nowhitespace: true
            }
        },
        messages: {
            'playlist_name': {
                required: '*Please Enter Playlist name',
                nowhitespace: 'Please Remove Space'
            },
            'type': {
                required: '*Please Select Type'
            },
            'dns': {
                required: '*Please Enter DNS',
                nowhitespace: 'Please Remove Space'
            },
            'username': {
                required: '*Please Enter Username',
                remote: 'Username Already Exist',
                nowhitespace: 'Please Remove Space'
            },
            'password': {
                required: '*Please Enter Password',
                nowhitespace: 'Please Remove Space'
            },
            'm3u_url': {
                required: '*Please Enter M3u URL',
                nowhitespace: 'Please Remove Space'
            },
            'epg': {
                nowhitespace: 'Please Remove Space'
            }
        }
    });

      $(document).on('submit', '#addUpdatePlaylistForm', function (e) {
        e.preventDefault();
        var formdata = new FormData($("#addUpdatePlaylistForm")[0]);
        if($("#playlist_id").val()){
          var url = '{{ route("playlistUpdate") }}';
        }else{
          var url = '{{ route("playlist.store") }}';
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
                $('#addUpdatePlaylistMdl').modal('hide');
                if (data.success == 1) {
                    toastr.success(data.message);
                    $(".addPlaylist").attr('data-playlist_limit',data.limit);
                    xtreamTableData.ajax.reload();
                    m3uTableData.ajax.reload();
                    multiDnsTableData.ajax.reload();
                    
                } else {
                    toastr.error(data.message);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert(errorThrown);
            }
        }); 
    });
    
      $(document).on('click', '.changeStatus', function (e) {
        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });
      
          var id = this.value;
          var closeInSeconds = 2;
          Swal.fire({
              title: 'Are you sure?',
              text: "you want to change the status?",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Yes, change it!'
          }).then((result) => {
              if (result.isConfirmed) {
                  $.ajax({
                  url: "{{ url('changePlaylistStatus') }}",
                  data:{"id":id},
                  type: "POST",
                  dataType: "json",
                  success: function(data) {
                    if (data.active) {
                        Swal.fire({
                            icon: 'success',
                            title: "Done!",
                            text: "Playlist Activated",
                            showConfirmButton: false,
                            timer: 2000
                        });
                        xtreamTableData.ajax.reload();
                        m3uTableData.ajax.reload();
                        multiDnsTableData.ajax.reload();

                    } else {
                        Swal.fire({
                            icon: 'success',
                            title: "Done!",
                            text: "Playlist Deactivate",
                            showConfirmButton: false,
                            timer: 2000
                        });
                        xtreamTableData.ajax.reload();
                        m3uTableData.ajax.reload();
                        multiDnsTableData.ajax.reload();
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
</script>
@endsection


