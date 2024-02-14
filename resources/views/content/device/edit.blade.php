@extends('layouts/contentLayoutMaster')

@section('title', 'Edit Device')

@section('vendor-style')
  {{-- vendor css files --}}
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
 <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap5.min.css')) }}">    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">

@endsection
@section('page-style')
  {{-- Page css files --}}
  <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-validation.css')) }}">
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
    <div class="col-md-12 col-12 device_add">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">Edit Device</h4>
          <a href="{{ url()->previous() }}" title="Go Back" class="btn btn-primary me-1 waves-effect waves-float waves-light">Back</a>
        </div>
        <div class="card-body">
       
        <form class="form form-vertical" method="post" id="updateDeviceForm">
        {{csrf_field()}}
            <div class="row">
                <div class="col-12">
                    <div class="mb-1">
                        <label class="form-label" for="activation_code">Activation Code</label>
                        <p>@if($device->device_code){{$device->device_code->code}}@endif</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="mb-1">
                            <label class="form-label" for="device_title">Device Title<span class="text-danger">*</span></label>
                            <input type="text" id="device_title" class="form-control" placeholder="Device Title" name="device_title" value="{{$device->device_title}}">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="row">
                            <div class="col-10">
                                <label class="form-label" for="playlist_id">Playlist<span class="text-danger">*</span></label>
                                <select id="playlist_id" class="playlist_id form-select" name="playlist_id[]" multiple="multiple">
                                    <option disabled>--Select--</option>
                                    @if($playlist)
                                    @foreach($playlist as $value)
                                    <?php if(isset($value['id'])){
                                        $id = $value['id'];
                                        $column = 'id';
                                    }else{
                                        $id = $value['unique_id'];
                                        $column = 'unique_id';

                                    }?>
                                    <option value="{{$id}}" @if(in_array($id,$device_playlist)) {{'selected'}} @endif>{{$value['playlist_name']}}</option>
                                    @endforeach
                                    @endif
                                    <input type="hidden" id="hidden_playlist_id" name="hidden_playlist_id" value="{{implode(',',$device_playlist)}}">

                                </select>
                            </div>
                            <div class="col-2">
                                <div class="mt-0">
                                    <label class="form-label"></label>
                                    <a data-bs-toggle="modal" data-bs-target="#addUpdatePlaylistMdl" data-playlist_limit="{{$is_limit}}" class="btn btn-primary waves-effect waves-float waves-light addPlaylist" title="Add New Playlist">
                                        <i data-feather='plus'></i>
                                    </a>                                          
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mb-1">
                            <label class="form-label" for="mac_id">Mac ID</label>
                            <input type="text" id="mac_id" class="form-control"
                                placeholder="Mac ID" name="mac_id" value="{{$device->mac_id}}">
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="mb-1">
                            <label class="form-label" for="mac_key">Mac Key</label>
                            <input type="text" id="mac_key" class="form-control"
                                placeholder="Mac Key" name="mac_key" value="{{$device->mac_key}}">
                        </div>
                    </div>
                    @if(Auth::user()->user_type == 1)
                    <div class="col-6">
                        <div class="mb-1">
                            <label class="form-label" for="status">Code Status</label>
                            <select id="status" class="form-control" name="status">
                                <option value="1" @if($device->device_code && $device->device_code->status == 1){{'selected'}}@endif>Active</option>
                                <option value="0" @if($device->device_code && $device->device_code->status == 0){{'selected'}}@endif>Expired</option>
                            </select>
                        </div>
                    </div>
                    @endif
                    <div class="col-12">
                        <div class="mb-1">
                            <label class="form-label" for="note">Note</label>
                            <textarea id="note" class="form-control"  name="note">{{$device->note}}</textarea>
                        </div>
                    </div>
                </div>


                <!-- <div class="col-12">
                    <div class="mb-1">
                        <label class="form-label" for="duration">Duration</label>
                        <select id="duration" class="form-control" name="duration">
                            <option value="">--Select--</option>
                            <option value="6">6 Hours</option>
                            <option value="12">12 Hours</option>
                            <option value="24">24 Hours</option>
                        </select>
                        <p><small class="text-muted">Choose when the temporary code should expire</small></p>
                    </div>
                </div> -->
                <!-- <div class="col-12">
                    <div class="mb-1">
                        <label class="form-label" for="expire_date">Expiration Date</label>
                        <p>{{$device->expire_date}}</p>
                    </div>
                </div> -->
                <div class="col-12">
                    <input type="hidden" id="device_id" class="form-control" name="device_id" value="{{$device->id}}">
                    <input type="hidden" id="device_code" class="form-control" name="device_code" value="@if($device->device_code){{$device->device_code->code}}@endif">
                    <button class="btn btn-primary me-1 waves-effect waves-float waves-light">Submit</button>
                    <button type="reset" class="btn btn-outline-secondary waves-effect ResetForm">Reset</button>
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
          <h4 class="card-title">Manage Playlist</h4>
          <input type="hidden" id="playlist_device_id" class="form-control" name="playlist_device_id" value="{{$device->id}}">
        </div>
        <div class="card-datatable">
          <ul class="nav nav-tabs nav-fill" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active"
                    id="xtream" data-bs-toggle="tab" href="#xtream-section"
                    role="tab" aria-controls="xtream-fill" aria-selected="true">Xtream</a>
            </li>
            <li class="nav-item">
                <a class="nav-link"
                    id="m3u" data-bs-toggle="tab" href="#m3u-section"
                    role="tab" aria-controls="m3u-fill" aria-selected="false">M3u</a>
            </li>
            @if(Auth::user()->user_type == 1 || Auth::user()->is_multi_dns == 1)
            <li class="nav-item">
                <a class="nav-link"
                    id="multi-dns" data-bs-toggle="tab" href="#multi-dns-section"
                    role="tab" aria-controls="multi-dns-fill" aria-selected="false">Indivisual List</a>
            </li>
            @endif
          </ul>
          <div class="tab-content pt-1">
            <div class="tab-pane active" id="xtream-section" role="tabpanel" aria-labelledby="xtream">
              <div class="me-3" style="display: flow-root;">
                <div class="float-end">
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
                    <!-- <th>Status</th> -->
                    <th>Action</th>
                  </tr>
                </thead>
              </table>
            </div>
            <div class="tab-pane" id="m3u-section" role="tabpanel" aria-labelledby="m3u">
              <div class="me-3" style="display: flow-root;">
                <div class="float-end">
                    <!-- <a href="{{route('playlist.create')}}?type=2" class="btn btn-primary waves-effect waves-float waves-light">
                        Add New Playlist
                    </a> -->
                    <a data-bs-toggle="modal" data-bs-target="#addUpdatePlaylistMdl" data-playlist_limit="{{$is_limit}}" data-type="2" class="btn btn-primary waves-effect waves-float waves-light addPlaylist">
                        Add New Playlist
                    </a> 
                </div>
              </div>

              <table class="datatables-m3u table" style="width:100%">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Url</th>
                    <th>Epg Url</th>
                    <!-- <th>Status</th> -->
                    <th>Action</th>
                  </tr>
                </thead>
              </table>
            </div>
            <div class="tab-pane" id="multi-dns-section" role="tabpanel" aria-labelledby="multi-dns">
              <div class="me-3" style="display: flow-root;">
                <div class="float-end">
                    <a href="{{route('playlist.create')}}?type=3" class="btn btn-primary waves-effect waves-float waves-light">
                        Indivisual Playlist
                    </a>
                </div>
              </div>

              <table class="datatables-multi-dns table" style="width:100%">
                <thead>
                  <tr>
                    <th>Name</th>
                    <!-- <th>Status</th> -->
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
@include('content/_partials/_modals/modal-addupdate-playlist')

@endsection

@section('vendor-script')
{{-- vendor files --}}
<script src="{{ asset(mix('vendors/js/tables/datatable/jquery.dataTables.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.bootstrap5.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.responsive.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/responsive.bootstrap5.js')) }}"></script>
<script src="{{asset('vendors/js/forms/validation/jquery.validate.min.js')}}"></script>
<script src="{{ asset(mix('vendors/js/forms/validation/additional-methods.min.js')) }}"></script>
<script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
<script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
@endsection

@section('page-script')
  {{-- Page js files --}}
  <script src="{{ asset(mix('js/scripts/pages/modal-addupdate-playlist.js')) }}"></script>

  <script>
 jQuery(document).ready(function () {
    $(".playlist_id").wrap('<div class="position-relative"></div>');
    $(".playlist_id").select2({
        dropdownAutoWidth: true,
        maximumSelectionLength: 10,
        width: '100%',
        dropdownParent: $(".playlist_id").parent()
    });
});
$(function () {
  ('use strict');
        $(document).on('click',".ResetForm",function(){
            $(".playlist_id").select2("destroy").val('').select2();
            var value = $("#hidden_playlist_id").val();
            console.log(value.split(','));
            $(".playlist_id").val(value.split(',')).trigger('change.select2');;

            validator.resetForm();
            $('#updateDeviceForm').find(".error").removeClass("error");
        });
         var validator = $('#updateDeviceForm').validate({
            rules: {
                'device_title': {
                    required: true
                },
                'mac_id':{
                    nowhitespace: true,
                    alphanumeric: true
                },
                'mac_key':{
                    nowhitespace: true,
                    alphanumeric: true
                },
                'playlist_id[]': {
                    required: true
                }
            },
            messages: {
                'device_title': {
                    required: '*Please Enter Device Title'
                },
                'mac_id': {
                        nowhitespace: 'Please Remove Space'
                    },
                    'mac_key': {
                        nowhitespace: 'Please Remove Space'
                    },
                'playlist_id': {
                    required: '*Please Select Playlist'
                },
            }
        });
        $(document).on('submit', '#updateDeviceForm', function (e) {
            e.preventDefault();
            var formdata = new FormData($("#updateDeviceForm")[0]);
            $.ajax({
                url: '{{ route("deviceUpdate") }}',
                type: 'POST',
                data: formdata,
                dataType: "json",
                contentType: false,
                cache: false,
                processData: false,
                success: function (data) {
                    $('.loader').hide();
                    if (data.success == 1) {
                        toastr.success('Device updated');
                        window.location.href = "{{route('device/list')}}";
                    } else {
                        toastr.error(data.message);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert(errorThrown);
                }
            });
            
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
            data.playlist_device_id=$("#playlist_device_id").val();

            },
        },
        columns: [
            // {data: 'code', name: 'code', orderable: false, searchable: false, sClass: "align-middle table-image"},
            {data: 'playlist_name', name: 'playlist_name', sClass: "align-middle"},
            {data: 'dns', name: 'dns', sClass: "align-middle"},
            {data: 'username', name: 'username', sClass: "align-middle"},
            {data: 'password', name: 'password', sClass: "align-middle"},
            // {data: 'status', name: 'status', sClass: "align-middle"},
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


  var m3uTableData = $('.datatables-m3u').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        ajax: {
            url: "{{route('playlist/list')}}",
            data: function ( data ) {
            data.type=2;
            data.playlist_device_id=$("#playlist_device_id").val();

            },
        },
        columns: [
            // {data: 'code', name: 'code', orderable: false, searchable: false, sClass: "align-middle table-image"},
            {data: 'playlist_name', name: 'playlist_name', sClass: "align-middle"},
            {data: 'm3u_url', name: 'm3u_url', sClass: "align-middle"},
            {data: 'epg', name: 'epg', sClass: "align-middle"},
            // {data: 'status', name: 'status', sClass: "align-middle"},
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


  var multiDnsTableData = $('.datatables-multi-dns').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        ajax: {
            url: "{{route('playlist/list')}}",
            data: function ( data ) {
            data.type=3;
            data.playlist_device_id=$("#playlist_device_id").val();

            },
        },
        columns: [
            {data: 'playlist_name', name: 'playlist_name', sClass: "align-middle"},
            // {data: 'status', name: 'status', sClass: "align-middle"},
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
    $(document).on('change', '#dns_id', function(){
          if($(this).val()){
            $("#dns").val("");
            $("#dns").attr('disabled','disabled');
          }else{
            $("#dns").removeAttr('disabled');
          }
      });
      $(document).on('keyup', '#dns', function(){
          if($(this).val()){
            $("#dns_id").val("");
            $("#dns_id").attr('disabled','disabled');
          }else{
            $("#dns_id").removeAttr('disabled');
          }
      });
        $(document).on('submit', '#addUpdatePlaylistForm', function (e) {
            e.preventDefault();
            var formdata = new FormData($("#addUpdatePlaylistForm")[0]);
            var playlist_device_id = $("#playlist_device_id").val();
            formdata.append('playlist_device_id',playlist_device_id);
            if($("#hide_playlist_id").val()){
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
                        $('.playlist_id').append($("<option></option>").attr("value", data.playlist.id).text(data.playlist.playlist_name)); 
                        var selectedItems = $(".playlist_id").select2("val");
                        if((selectedItems).length > 0){
                            selectedItems.push(data.playlist.id); 
                            $(".playlist_id").val(selectedItems).trigger('change');;
                        }else{
                            var selectedItems = [];
                            selectedItems.push(data.playlist.id); 
                            console.log(selectedItems);
                            $(".playlist_id").val(selectedItems).trigger('change');;
                        }
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

        $(document).on('click', '.removeDeviceFromPlaylist', function (e) {
            $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
            });
        
            var device_id= $(this).attr('data-device_id');
            var playlist_id= $(this).attr('data-playlist_id');
        

            var closeInSeconds = 2;
            Swal.fire({
                title: 'Are you sure?',
                text: "you want to unassign from device?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, change it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                    url: "{{ url('removeDevicePlaylist') }}",
                    data:{"device_id":device_id,"playlist_id":playlist_id},
                    type: "POST",
                    dataType: "json",
                    cache: false,
                    success: function(data) {
                        if(data.success == 1){
                            Swal.fire({
                                icon: 'success',
                                title: "Done!",
                                text: "Playlist Unassigned",
                                showConfirmButton: false,
                                timer: 2000
                            });
                                xtreamTableData.ajax.reload();
                                m3uTableData.ajax.reload();
                                multiDnsTableData.ajax.reload();

                                var $select = $('.playlist_id');
                                var idToRemove = playlist_id;

                                var values = $select.val();
                                if (values) {
                                    var i = values.indexOf(idToRemove);
                                    if (i >= 0) {
                                        values.splice(i, 1);
                                        $select.val(values).trigger('change');
                                    }
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
            
        });
    });
</script>
@endsection


