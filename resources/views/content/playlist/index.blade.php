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
@endsection

@section('content')
<section id="ajax-datatable">
@if(!empty($device))
<div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header border-bottom">
          <h4 class="card-title">Assign Playlist To Device</h4>
        </div>

        <div class="card-body">
            <form class="form form-vertical" method="post" id="assignPlaylist">
            {{csrf_field()}}
                <div class="row">
                    <div class="col-3">
                        <div class="mb-1">
                            <label class="form-label" for="mac_id">Mac ID</label>
                            <input type="text" id="mac_id" class="form-control" name="mac_id" value="{{$device->mac_id}}" readonly>
                            <input type="hidden" id="device_id" class="form-control" name="device_id" value="{{$device->id}}" readonly>

                        </div>
                    </div>
                    <div class="col-3">
                        <div class="mb-1">
                            <label class="form-label" for="mac_key">Mac Key</label>
                            <input type="text" id="mac_key" class="form-control" name="mac_key" value="{{$device->mac_key}}" readonly>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="mb-1">
                            <label class="form-label" for="assign_playlist_id">Playlist</label>
                            <select id="assign_playlist_id" class="assign_playlist_id form-select" name="assign_playlist_id[]" multiple="multiple">
                                <option value="" disabled>--Select--</option>
                                @if($playlist)
                                @foreach($playlist as $value)
                                <?php if(isset($value['id'])){
                                        $id = $value['id'];
                                        $column = 'id';
                                    }else{
                                        $id = $value['unique_id'];
                                        $column = 'unique_id';

                                    }?>
                                <option value="@if(isset($value['id'])){{$value['id']}}@else{{$value['unique_id']}}@endif" @if(in_array($id,$device_playlist)) {{'selected'}} @endif>{{$value['playlist_name']}}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-3">
                        <button class="btn btn-primary me-1 waves-effect waves-float waves-light mt-2">Add Playlist to Device</button>
                    </div>
                </div>
            </form>

            </div>
        </div>
    </div>
  </div>
@endif
<input type="hidden" id="user_type" value="{{Auth::user()->user_type}}">

  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header border-bottom">
          <h4 class="card-title">Playlist List</h4>
        </div>
        @if (Auth::user()->user_type == 1 || Auth::user()->user_type == 3)
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

            </div>
          </div>
          @endif
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
            @if (Auth::user()->user_type == 1)
            <li class="nav-item">
                <a class="nav-link"
                    id="multi-dns" data-bs-toggle="tab" href="#multi-dns-section"
                    role="tab" aria-controls="multi-dns-fill" aria-selected="false">Indivisual List</a>
            </li>
            @endif
            @if(Auth::user()->is_multi_dns == 1)
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
                    @if (Auth::user()->user_type != 1)

                    <a data-bs-toggle="modal" data-bs-target="#addUpdatePlaylistMdl" data-playlist_limit="{{$is_limit}}" data-type="1" class="btn btn-primary waves-effect waves-float waves-light addPlaylist">
                        Add New Playlist
                    </a>
                    @endif
                    <button class="btn btn-danger delete_all" data-type="1">Delete All Selected</button>

                </div>
              </div>
              <div class="p-1" style="width: 100%;">
                <div class="table-responsive">
                  <table class="datatables-xtream table">
                    <thead>
                      <tr>
                      <th width="50px"><input type="checkbox" id="select-xtrem-all-checkbox"></th>
                        <th>Name</th>
                        <th>URL</th>
                        <th>Username</th>
                        <th>Password</th>
                        <th>Status</th>
                        @if (Auth::user()->user_type == 1 || Auth::user()->user_type == 3)
                        <th>Added by</th>
                        @endif
                        <th>Action</th>
                      </tr>
                    </thead>
                  </table>
                </div>
              </div>
            </div>
            <div class="tab-pane" id="m3u-section" role="tabpanel" aria-labelledby="m3u">
              <div class="me-3" style="display: flow-root;">
                <div class="float-end">
                    <!-- <a href="{{route('playlist.create')}}?type=2" class="btn btn-primary waves-effect waves-float waves-light">
                        Add New Playlist
                    </a> -->
                    @if (Auth::user()->user_type != 1)
                    <a data-bs-toggle="modal" data-bs-target="#addUpdatePlaylistMdl" data-playlist_limit="{{$is_limit}}" data-type="2" class="btn btn-primary waves-effect waves-float waves-light addPlaylist">
                        Add New Playlist
                    </a>
                    @endif
                    <button class="btn btn-danger delete_all" data-type="2">Delete All Selected</button>

                </div>
              </div>
              <div class="p-1" style="width: 100%;">
                <div class="table-responsive">
                  <table class="datatables-m3u table">
                    <thead>
                      <tr>
                      <th width="50px"><input type="checkbox" id="select-m3u-all-checkbox"></th>
                        <th>Name</th>
                        <th>Url</th>
                        <th>Epg Url</th>
                        <th>Status</th>
                        @if (Auth::user()->user_type == 1 || Auth::user()->user_type == 3)
                        <th>Added by</th>
                        @endif
                        <th>Action</th>
                      </tr>
                    </thead>
                  </table>
                </div>
              </div>
            </div>
            <div class="tab-pane" id="multi-dns-section" role="tabpanel" aria-labelledby="multi-dns">
              <div class="me-3" style="display: flow-root;">
                <div class="float-end">
                @if (Auth::user()->user_type != 1)
                    <a href="{{route('playlist.create')}}?type=3" class="btn btn-primary waves-effect waves-float waves-light">
                        Indivisual Playlist
                    </a>
                    @endif
                    <button class="btn btn-danger delete_all" data-type="3">Delete All Selected</button>

                </div>
              </div>
              <div class="p-1" style="width: 100%;">
                <div class="table-responsive">
                  <table class="datatables-multi-dns table">
                    <thead>
                      <tr>
                      <th width="50px"><input type="checkbox" id="select-multi-all-checkbox"></th>
                        <th>Name</th>
                        <th>Status</th>
                        @if (Auth::user()->user_type == 1 || Auth::user()->user_type == 3)
                        <th>Added by</th>
                        @endif
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
        $(".user-select").select2().on('select2:open', function(e){
          $('.select2-search__field').attr('placeholder', 'Search User');
      });;

        if($(".assign_playlist_id").length > 0){
          $(".assign_playlist_id").wrap('<div class="position-relative"></div>');
          $(".assign_playlist_id").select2({
              dropdownAutoWidth: true,
              maximumSelectionLength: 10,
              width: '100%',
              dropdownParent: $(".assign_playlist_id").parent()
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
    if($('#user_type').val() == 1 || $('#user_type').val() == 3){

    var xtreamTableData = $('.datatables-xtream').DataTable({
        stateSave: true,
        processing: true,
        serverSide: true,
        autoWidth: false,
        paging: true,
        deferRender: true,
        dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        ajax: {
            url: "{{route('playlist/list')}}",
            data: function ( data ) {
              data.type=1;
              data.added_by = $("#added_by").val();
              data.status = $("#status").val();
            },
        },
        columns: [
            // {data: 'code', name: 'code', orderable: false, searchable: false, sClass: "align-middle table-image"},
            { data: "checkbox", orderable:false, searchable:false},
            {data: 'playlist_name', name: 'playlist_name', sClass: "align-middle"},
            {data: 'dns', name: 'dns', sClass: "align-middle"},
            {data: 'username', name: 'username', sClass: "align-middle"},
            {data: 'password', name: 'password', sClass: "align-middle"},
            {data: 'status', name: 'status', sClass: "align-middle"},
            {data: 'added_by', name: 'user_id', sClass: "align-middle"},
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
        stateSave: true,
        processing: true,
        serverSide: true,
        autoWidth: false,
        paging: true,
        deferRender: true,
        dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        ajax: {
            url: "{{route('playlist/list')}}",
            data: function ( data ) {
              data.type=2;
              data.added_by = $("#added_by").val();
              data.status = $("#status").val();
            },
        },
        columns: [
            // {data: 'code', name: 'code', orderable: false, searchable: false, sClass: "align-middle table-image"},
            { data: "checkbox", orderable:false, searchable:false},
            {data: 'playlist_name', name: 'playlist_name', sClass: "align-middle"},
            {data: 'm3u_url', name: 'm3u_url', sClass: "align-middle"},
            {data: 'epg', name: 'epg', sClass: "align-middle"},
            {data: 'status', name: 'status', sClass: "align-middle"},
            {data: 'added_by', name: 'added_by', sClass: "align-middle"},
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
        stateSave: true,
        processing: true,
        serverSide: true,
        autoWidth: false,
        paging: true,
        deferRender: true,
        dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        ajax: {
            url: "{{route('playlist/list')}}",
            data: function ( data ) {
              data.type=3;
              data.added_by = $("#added_by").val();
              data.status = $("#status").val();
            },
        },
        columns: [
          { data: "checkbox", orderable:false, searchable:false},
            {data: 'playlist_name', name: 'playlist_name', sClass: "align-middle"},
            {data: 'status', name: 'status', sClass: "align-middle"},
            {data: 'added_by', name: 'user_id', sClass: "align-middle"},
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
  }else{
    var xtreamTableData = $('.datatables-xtream').DataTable({
        stateSave: true,
        processing: true,
        serverSide: true,
        autoWidth: false,
        paging: true,
        deferRender: true,
        dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        ajax: {
            url: "{{route('playlist/list')}}",
            data: function ( data ) {
              data.type=1;
            },
        },
        columns: [
            // {data: 'code', name: 'code', orderable: false, searchable: false, sClass: "align-middle table-image"},
            { data: "checkbox", orderable:false, searchable:false},
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


  var m3uTableData = $('.datatables-m3u').DataTable({
        stateSave: true,
        processing: true,
        serverSide: true,
        autoWidth: false,
        paging: true,
        deferRender: true,
        dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        ajax: {
            url: "{{route('playlist/list')}}",
            data: function ( data ) {
              data.type=2;
            },
        },
        columns: [
            // {data: 'code', name: 'code', orderable: false, searchable: false, sClass: "align-middle table-image"},
            { data: "checkbox", orderable:false, searchable:false},
            {data: 'playlist_name', name: 'playlist_name', sClass: "align-middle"},
            {data: 'm3u_url', name: 'm3u_url', sClass: "align-middle"},
            {data: 'epg', name: 'epg', sClass: "align-middle"},
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


  var multiDnsTableData = $('.datatables-multi-dns').DataTable({
        stateSave: true,
        processing: true,
        serverSide: true,
        autoWidth: false,
        paging: true,
        deferRender: true,
        dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        ajax: {
            url: "{{route('playlist/list')}}",
            data: function ( data ) {
              data.type=3;
            },
        },
        columns: [
          { data: "checkbox", orderable:false, searchable:false},
            {data: 'playlist_name', name: 'playlist_name', sClass: "align-middle"},
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
  }

    jQuery(document).ready(function () {

      $('#select-xtrem-all-checkbox').on('click', function(){
        var rows = xtreamTableData.rows({ 'search': 'applied' }).nodes();
        $('.playlist_checkbox', rows).prop('checked', this.checked);
      });
        $('.table tbody').on('change', '.playlist_checkbox', function(){
          // If checkbox is not checked
          if(!this.checked){
            var el = $('#select-xtrem-all-checkbox').get(0);
            // If "Select all" control is checked and has 'indeterminate' property
            if(el && el.checked && ('indeterminate' in el)){
                // Set visual state of "Select all" control
                // as 'indeterminate'
                el.indeterminate = true;
            }
          }
      });

      $('#select-m3u-all-checkbox').on('click', function(){
        var rows = m3uTableData.rows({ 'search': 'applied' }).nodes();
        $('.playlist_checkbox', rows).prop('checked', this.checked);
      });
        $('.table tbody').on('change', '.playlist_checkbox', function(){
          // If checkbox is not checked
          if(!this.checked){
            var el = $('#select-m3u-all-checkbox').get(0);
            // If "Select all" control is checked and has 'indeterminate' property
            if(el && el.checked && ('indeterminate' in el)){
                // Set visual state of "Select all" control
                // as 'indeterminate'
                el.indeterminate = true;
            }
          }
      });
      $('#select-multi-all-checkbox').on('click', function(){
        var rows = multiDnsTableData.rows({ 'search': 'applied' }).nodes();
        $('.playlist_checkbox', rows).prop('checked', this.checked);
      });
        $('.table tbody').on('change', '.playlist_checkbox', function(){
          // If checkbox is not checked
          if(!this.checked){
            var el = $('#select-multi-all-checkbox').get(0);
            // If "Select all" control is checked and has 'indeterminate' property
            if(el && el.checked && ('indeterminate' in el)){
                // Set visual state of "Select all" control
                // as 'indeterminate'
                el.indeterminate = true;
            }
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


      var message = '';

      var validator = $('#addUpdatePlaylistForm').validate({
          rules: {
              'playlist_name': {
                  required: true,
                  nowhitespace: true,
                  remote: {
                      headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      },
                      url: '{{ url("checkExistPlaylist") }}',
                      type: "post",
                      data: {
                          playlist_name: function () { return $("#playlist_name").val(); },
                          playlist_id: function () { return $("#hide_playlist_id").val(); },
                          flag: function () { return 1; },
                      }
                  }
              },
              'type': {
                  required: true
              },
              'dns_id': {
                  required: function() {
                      if ($('#type').val() == 1) {
                        if ($('#dns').val() == '') {
                          return true;
                        }else{
                          return false;
                        }
                      }
                      else {
                          return false;
                      }
                  },
              },
              'dns': {
                  required: function() {
                      if ($('#type').val() == 1) {
                        if ($('#dns_id').val() == '') {
                          return true;
                        }else{
                          return false;
                        }
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
              'epg': {
                  url: true,
                  nowhitespace: true
              },
          },
          messages: {
              'playlist_name': {
                  required: '*Please Enter Playlist name',
                  nowhitespace: 'Please Remove Space',
                  remote: "Playlist Name already Exist"
              },
              'type': {
                  required: '*Please Select Type'
              },
              'dns_id': {
                  required: '*Please Select DNS',
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
          }
      });

      $(document).on('submit', '#addUpdatePlaylistForm', function (e) {
        e.preventDefault();
        var formdata = new FormData($("#addUpdatePlaylistForm")[0]);
        if($("#hide_playlist_id").val()){
          var url = '{{ route("playlistUpdate") }}';
        }else{
          var url = '{{ route("playlist.store") }}';
        }
        $(".playlistBtn").attr('disabled',true);
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
                $(".playlistBtn").removeAttr('disabled');
                if (data.success == 1) {
                    toastr.success(data.message);
                    $(".addPlaylist").attr('data-playlist_limit',data.limit);
                    xtreamTableData.ajax.reload();
                    m3uTableData.ajax.reload();
                    multiDnsTableData.ajax.reload();

                    $('.assign_playlist_id').append($("<option></option>").attr("value", data.playlist.id).text(data.playlist.playlist_name));
                    var selectedItems = $(".assign_playlist_id").select2("val");
                    if((selectedItems).length > 0){
                        selectedItems.push(data.playlist.id);
                        $(".assign_playlist_id").val(selectedItems).trigger('change');;
                    }else{
                        var selectedItems = [];
                        selectedItems.push(data.playlist.id);
                        $(".assign_playlist_id").val(selectedItems).trigger('change');;
                    }

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
        var type = $(this).attr('data-type');
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
                  data:{"id":id,type:type},
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

      $(document).on('submit', '#assignPlaylist', function (e) {
          $.ajaxSetup({
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
          });
          e.preventDefault();
            var formdata = new FormData($("#assignPlaylist")[0]);
          $.ajax({
            url: "{{ url('assignPlaylist') }}",
            data: formdata,
            type: "POST",
            dataType: "json",
            contentType: false,
            cache: false,
            processData: false,
            success: function(data) {
              if (data.success == 1) {
                  Swal.fire({
                      icon: 'success',
                      title: "Done!",
                      text: "Playlist Assign Successfully",
                      showConfirmButton: false,
                      timer: 2000
                  });
                  window.location.href = "{{url('playlist/list')}}";
              } else {
                  Swal.fire({
                      icon: 'error',
                      title: "Error!",
                      text: "Error While Assign Playlist",
                      showConfirmButton: false,
                      timer: 2000
                  });
              }
            }
        });
      });
    });
    jQuery(document).ready(function () {

      $(document).on('click', '.delete_all', function(){
        var id = [];
        var closeInSeconds = 2;
        $('.playlist_checkbox:checked').each(function(){
              id.push($(this).val());
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
                      url:"{{ url('playlist/deleteAll') }}",
                      method:"POST",
                      data:{ids:id,type:type},
                      dataType: "json",
                      success:function(data)
                      {
                        if (data.success == 1) {
                            Swal.fire({
                                icon: 'success',
                                title: "Done!",
                                text: "Playlist Deleted",
                                showConfirmButton: false,
                                timer: 2000
                            });
                            $(".addPlaylist").attr('data-playlist_limit',data.limit);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: "Error!",
                                text: "Error while Playlist Delete",
                                showConfirmButton: false,
                                timer: 2000
                            });
                        }

                        xtreamTableData.ajax.reload();
                        m3uTableData.ajax.reload();
                        multiDnsTableData.ajax.reload();
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

      $(document).on('change','#added_by',function() {
          xtreamTableData.ajax.reload();
          m3uTableData.ajax.reload();
          multiDnsTableData.ajax.reload();

      });
      $(document).on('change','#status',function() {
          xtreamTableData.ajax.reload();
          m3uTableData.ajax.reload();
          multiDnsTableData.ajax.reload();
      });
      $(document).on('click','#clearFilter',function() {
          $(".user-select").select2("destroy").val('').select2();
          $("#status").val("");
          xtreamTableData.ajax.reload();
          m3uTableData.ajax.reload();
          multiDnsTableData.ajax.reload();
      });
    });
</script>
@endsection
