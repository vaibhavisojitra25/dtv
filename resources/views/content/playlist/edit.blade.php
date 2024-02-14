@extends('layouts/contentLayoutMaster')

@section('title', 'Playlist')

@section('vendor-style')
  {{-- vendor css files --}}
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap5.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
@endsection
@section('page-style')
  {{-- Page css files --}}
  <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-validation.css')) }}">

@endsection

@section('content')
<section id="ajax-datatable">
  <div class="row">
    <div class="col-md-12 col-12">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">Edit Playlist</h4>
          <a href="{{ url()->previous() }}" title="Go Back" class="btn btn-primary me-1 waves-effect waves-float waves-light">Back</a>
        </div>
        <div class="card-body">
       
        <form class="form form-vertical" method="post" id="updatePlaylistForm">
        {{csrf_field()}}
            <div class="row">
                <div class="col-6">
                    <div class="col-12">
                        <div class="mb-1">
                            <label class="form-label" for="playlist_name">Playlist Name*</label>
                            <input type="text" id="playlist_name" class="form-control" placeholder="Playlist Name" name="playlist_name" value="{{$playlist->playlist_name}}">

                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-1">
                            <label class="form-label" for="type">Type</label>
                            <select id="type" class="form-select" name="type">
                                <option value="1" @if($playlist->type == 1){{"selected"}}@endif>Xtream</option>
                                <option value="2" @if($playlist->type == 2){{"selected"}}@endif>M3u</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 xtraem_div">
                        <div class="mb-1">
                            <label class="form-label" for="dns">DNS*</label>
                            <input type="text" id="dns" class="form-control" placeholder="DNS" name="dns" value="{{$playlist->dns}}">
                        </div>
                    </div>
                    
                    <div class="col-12 xtraem_div">
                        <div class="mb-1">
                            <label class="form-label" for="username">Username*</label>
                            <input type="text" id="username" class="form-control" placeholder="Username" name="username" value="{{$playlist->username}}">
                        </div>
                    </div>
                    <div class="col-12 xtraem_div">
                        <div class="mb-1">
                            <label class="form-label" for="password">Password*</label>
                            <input type="text" id="password" class="form-control" placeholder="Password" name="password" value="{{$playlist->password}}">
                        </div>
                    </div>
                    
                    <div class="col-12 m3u_div">
                        <div class="mb-1">
                            <label class="form-label" for="m3u_url">M3u Url*</label>
                            <input type="text" id="m3u_url" class="form-control" placeholder="M3u Url" name="m3u_url" value="{{$playlist->m3u_url}}">
                        </div>
                    </div>
                </div>
                <div class="col-6">

                    <div class="col-12">
                        <div class="mb-1">
                            <label class="form-label" for="epg">EPG</label>
                            <input type="text" id="epg" class="form-control" placeholder="EPG" name="epg" value="{{$playlist->epg}}">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-1">
                            <label class="form-label" for="user_agent">User Agent</label>
                            <input type="text" id="user_agent" class="form-control" placeholder="User Agent" name="user_agent" value="{{$playlist->user_agent}}">
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <input type="hidden" id="playlist_id" class="form-control" name="playlist_id" value="{{$playlist->id}}">
                    <button class="btn btn-primary me-1 waves-effect waves-float waves-light">Submit</button>
                    <button type="reset" class="btn btn-outline-secondary waves-effect ResetForm">Reset</button>
                </div>
            </div>
          </form>
        </div>
      </div>
    </div>
   
  </div>
</section>

@endsection

@section('vendor-script')
{{-- vendor files --}}
<script src="{{asset('vendors/js/forms/validation/jquery.validate.min.js')}}"></script>
<script src="{{ asset(mix('vendors/js/forms/validation/additional-methods.min.js')) }}"></script>
<script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
<script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>

@endsection

@section('page-script')
  {{-- Page js files --}}
  <script src="{{ asset(mix('js/scripts/forms/form-select2.js')) }}"></script>
  <script>

$(function () {
  ('use strict');
  var value = $("#type").val();
    if(value == 1){
        $(".xtraem_div").show();
        $(".m3u_div").hide();
    }else if(value == 2){
        $(".m3u_div").show();
        $(".xtraem_div").hide();
    }
  $(document).on('change',"#type",function(){
    var value = $(this).val();
    if(value == 1){
        $(".xtraem_div").show();
        $(".m3u_div").hide();
    }else if(value == 2){
        $(".m3u_div").show();
        $(".xtraem_div").hide();
    }
  });
  $(document).on('click',".ResetForm",function(){
    if(value == 1){
        $(".xtraem_div").show();
        $(".m3u_div").hide();
    }else if(value == 2){
        $(".m3u_div").show();
        $(".xtraem_div").hide();
    }
    $("#device_id").select2("destroy");
    $("#device_id").select2();
    validator.resetForm();
    $('#addPlaylistForm').find(".error").removeClass("error");
});

    var validator =  $('#updatePlaylistForm').validate({
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
    $(document).on('submit', '#updatePlaylistForm', function (e) {
        e.preventDefault();
        var type = $("#type").val();
        var flag = 0;
        if(type == 1){
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ url('checkExistUserName') }}",
                type: "POST",
                dataType:'json',
                data: {
                    username: function () { return $("#username").val(); },
                    playlist_id: function () { return $("#playlist_id").val(); },
                },
                complete: function(data){
                    if( data.responseText == 'true' ) {
                        Swal.fire({
                            title: 'Are you sure?',
                            text: "Username already exist. you want to add with same username?",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes',
                            cancelButtonText: 'No'
                        }).then((result) => {
                            if (result.isConfirmed == true) {
                                 addPlaylist();                   
                            }else{
                                return false;
                            }
                        })
                    }else{
                        addPlaylist();
                        return true;
                    }
                }
            });
        }else{
            addPlaylist();
        }
        
    });
    function addPlaylist(){
        var formdata = new FormData($("#updatePlaylistForm")[0]);
        $.ajax({
            url: '{{ route("playlistUpdate") }}',
            type: 'POST',
            data: formdata,
            dataType: "json",
            contentType: false,
            cache: false,
            processData: false,
            success: function (data) {
                $('.loader').hide();
                if (data.success == 1) {
                    toastr.success('Playlist updated');
                    window.location.href = "{{route('playlist/list')}}";
                } else {
                    toastr.error(data.message);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert(errorThrown);
            }
        });
    }
});
</script>
@endsection


