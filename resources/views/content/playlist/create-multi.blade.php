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

    <div class="col-md-12 col-12">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">Add Playlist</h4>
          <a href="{{ url()->previous() }}" title="Go Back" class="btn btn-primary me-1 waves-effect waves-float waves-light">Back</a>
        </div>
        <div class="card-body">
        @if($is_limit == 1)
        <form class="form form-vertical" method="post" id="addPlaylistForm">
        {{csrf_field()}}
            <div class="row">
                    <div class="col-6">
                        <div class="mb-1">
                            <label class="form-label" for="playlist_name">Playlist Name*</label>
                            <input type="text" id="playlist_name" class="form-control" placeholder="Playlist Name" name="playlist_name">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mb-1">
                            <label class="form-label" for="user_agent">User Agent</label>
                            <input type="text" id="user_agent" class="form-control" placeholder="User Agent" name="user_agent">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="row">
                            <h6>Live TV</h6>
                            <hr>
                            <div class="col-3">
                                <div class="mb-1">
                                    <label class="form-label" for="livetv_type">Type</label>
                                    <select id="livetv_type" class="form-select" name="livetv_type">
                                        <option value="1">Xtream</option>
                                        <option value="2">M3u</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-3 livetv_xtraem_div">
                                <div class="mb-1">
                                    <label class="form-label" for="livetv_dns">DNS*</label>
                                    <input type="text" id="livetv_dns" class="form-control" placeholder="DNS" name="livetv_dns">
                                </div>
                            </div>

                            <div class="col-3 livetv_xtraem_div">
                                <div class="mb-1">
                                    <label class="form-label" for="livetv_username">Username*</label>
                                    <input type="text" id="livetv_username" class="form-control" placeholder="Username" name="livetv_username">
                                </div>
                            </div>
                            <div class="col-3 livetv_xtraem_div">
                                <div class="mb-1">
                                    <label class="form-label" for="livetv_password">Password*</label>
                                    <input type="text" id="livetv_password" class="form-control" placeholder="Password" name="livetv_password">
                                </div>
                            </div>
                            <div class="col-4 livetv_m3u_div" style="display:none;">
                                <div class="mb-1">
                                    <label class="form-label" for="livetv_m3u_url">M3u Url*</label>
                                    <input type="text" id="livetv_m3u_url" class="form-control" placeholder="M3u Url" name="livetv_m3u_url">
                                </div>
                            </div>
                            <div class="col-4 livetv_m3u_div" style="display:none;">
                                <div class="mb-1">
                                    <label class="form-label" for="livetv_epg">EPG</label>
                                    <input type="text" id="livetv_epg" class="form-control" placeholder="EPG" name="livetv_epg">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="row">
                            <h6>Movie</h6>
                            <hr>
                            <div class="col-3">
                                <div class="mb-1">
                                    <label class="form-label" for="movie_type">Type*</label>
                                    <select id="movie_type" class="form-select" name="movie_type">
                                        <option value="1">Xtream</option>
                                        <option value="2">M3u</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-3 movie_xtraem_div">
                                <div class="mb-1">
                                    <label class="form-label" for="movie_dns">DNS*</label>
                                    <input type="text" id="movie_dns" class="form-control" placeholder="DNS" name="movie_dns">
                                </div>
                            </div>

                            <div class="col-3 movie_xtraem_div">
                                <div class="mb-1">
                                    <label class="form-label" for="movie_username">Username*</label>
                                    <input type="text" id="movie_username" class="form-control" placeholder="Username" name="movie_username">
                                </div>
                            </div>
                            <div class="col-3 movie_xtraem_div">
                                <div class="mb-1">
                                    <label class="form-label" for="movie_password">Password*</label>
                                    <input type="text" id="movie_password" class="form-control" placeholder="Password" name="movie_password">
                                </div>
                            </div>
                            <div class="col-4 movie_m3u_div" style="display:none;">
                                <div class="mb-1">
                                    <label class="form-label" for="movie_m3u_url">M3u Url*</label>
                                    <input type="text" id="movie_m3u_url" class="form-control" placeholder="M3u Url" name="movie_m3u_url">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="row">
                            <h6>Show</h6>
                            <hr>
                            <div class="col-3">
                                <div class="mb-1">
                                    <label class="form-label" for="show_type">Type</label>
                                    <select id="show_type" class="form-select" name="show_type">
                                        <option value="1">Xtream</option>
                                        <option value="2">M3u</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-3 show_xtraem_div">
                                <div class="mb-1">
                                    <label class="form-label" for="show_dns">DNS*</label>
                                    <input type="text" id="show_dns" class="form-control" placeholder="DNS" name="show_dns">
                                </div>
                            </div>

                            <div class="col-3 show_xtraem_div">
                                <div class="mb-1">
                                    <label class="form-label" for="show_username">Username*</label>
                                    <input type="text" id="show_username" class="form-control" placeholder="Username" name="show_username">
                                </div>
                            </div>
                            <div class="col-3 show_xtraem_div">
                                <div class="mb-1">
                                    <label class="form-label" for="show_password">Password*</label>
                                    <input type="text" id="show_password" class="form-control" placeholder="Password" name="show_password">
                                </div>
                            </div>
                            <div class="col-4 show_m3u_div" style="display:none;">
                                <div class="mb-1">
                                    <label class="form-label" for="show_m3u_url">M3u Url*</label>
                                    <input type="text" id="show_m3u_url" class="form-control" placeholder="M3u Url" name="show_m3u_url">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="row">
                            <h6>24x7</h6>
                            <hr>
                            <div class="col-3">
                                <div class="mb-1">
                                    <label class="form-label" for="24_7_type">Type*</label>
                                    <select id="24_7_type" class="form-select" name="24_7_type">
                                        <option value="1">Xtream</option>
                                        <option value="2">M3u</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-3 24_7_xtraem_div">
                                <div class="mb-1">
                                    <label class="form-label" for="24_7_dns">DNS*</label>
                                    <input type="text" id="24_7_dns" class="form-control" placeholder="DNS" name="24_7_dns">
                                </div>
                            </div>

                            <div class="col-3 24_7_xtraem_div">
                                <div class="mb-1">
                                    <label class="form-label" for="24_7_username">Username*</label>
                                    <input type="text" id="24_7_username" class="form-control" placeholder="Username" name="24_7_username">
                                </div>
                            </div>
                            <div class="col-3 24_7_xtraem_div">
                                <div class="mb-1">
                                    <label class="form-label" for="24_7_password">Password*</label>
                                    <input type="text" id="24_7_password" class="form-control" placeholder="Password" name="24_7_password">
                                </div>
                            </div>
                            <div class="col-4 24_7_m3u_div" style="display:none;">
                                <div class="mb-1">
                                    <label class="form-label" for="24_7_m3u_url">M3u Url*</label>
                                    <input type="text" id="24_7_m3u_url" class="form-control" placeholder="M3u Url" name="24_7_m3u_url">
                                </div>
                            </div>
                        </div>
                    </div>

                <div class="col-12">
                    <input type="hidden" id="hide_playlist_id" class="form-control" name="hide_playlist_id" value="">
                    <input type="hidden" id="is_multi_dns" class="form-control" name="is_multi_dns" value="1">
                    <button class="btn btn-primary me-1 waves-effect waves-float waves-light playlistBtn">Submit</button>
                    <button type="reset" class="btn btn-outline-secondary waves-effect ResetForm">Reset</button>
                </div>
            </div>
          </form>
          @else
          <div class="row">
                <div class="col-12 text-center">
                    <h2> Your playlist limit is zero </h2>
                    <p> The limit is added by the admin.</p>
                </div>
            </div>
          @endif
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
  $(document).on('change',"#livetv_type",function(){
    var value = $(this).val();
    if(value == 1){
        $(".livetv_xtraem_div").show();
        $(".livetv_m3u_div").hide();
    }else if(value == 2){
        $(".livetv_m3u_div").show();
        $(".livetv_xtraem_div").hide();
    }
  });
  $(document).on('change',"#movie_type",function(){
    var value = $(this).val();
    if(value == 1){
        $(".movie_xtraem_div").show();
        $(".movie_m3u_div").hide();
    }else if(value == 2){
        $(".movie_m3u_div").show();
        $(".movie_xtraem_div").hide();
    }
  });
  $(document).on('change',"#show_type",function(){
    var value = $(this).val();
    if(value == 1){
        $(".show_xtraem_div").show();
        $(".show_m3u_div").hide();
    }else if(value == 2){
        $(".show_m3u_div").show();
        $(".show_xtraem_div").hide();
    }
  });
  $(document).on('change',"#24_7_type",function(){
    var value = $(this).val();
    if(value == 1){
        $(".24_7_xtraem_div").show();
        $(".24_7_m3u_div").hide();
    }else if(value == 2){
        $(".24_7_m3u_div").show();
        $(".24_7_xtraem_div").hide();
    }
  });
  $(document).on('click',".ResetForm",function(){
        $(".livetv_xtraem_div").show();
        $(".livetv_m3u_div").hide();
        $(".movie_xtraem_div").show();
        $(".movie_m3u_div").hide();
        $(".show_xtraem_div").show();
        $(".show_m3u_div").hide();
        $(".24_7_xtraem_div").show();
        $(".24_7_m3u_div").hide();
        validator.resetForm();
        $('#addPlaylistForm').find(".error").removeClass("error");
    });
    var message = '';

    var validator = $('#addPlaylistForm').validate({
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
                            flag: function () { return 2; },
                        }
                    }
                },
                'livetv_type': {
                    required: true
                },
                'livetv_dns': {
                    required: function() {
                        if ($('#livetv_type').val() == 1) {
                            return true;
                        }
                        else {
                            return false;
                        }
                    },
                    url: true,
                    nowhitespace: true
                },
                'livetv_username': {
                    required: function() {
                        if ($('#livetv_type').val() == 1) {
                            return true;
                        }
                        else {
                            return false;
                        }
                    },
                    nowhitespace: true
                },
                'livetv_password': {
                    required: function() {
                        if ($('#livetv_type').val() == 1) {
                            return true;
                        }
                        else {
                            return false;
                        }
                    },
                    nowhitespace: true
                },
                'livetv_m3u_url': {
                    required: function() {
                        if ($('#livetv_type').val() == 2) {
                            return true;
                        }
                        else {
                            return false;
                        }
                    },
                    url: true,
                    nowhitespace: true
                },
                'livetv_epg': {
                    url: true,
                    nowhitespace: true
                },
                'movie_type': {
                    required: true
                },
                'movie_dns': {
                    required: function() {
                        if ($('#movie_type').val() == 1) {
                            return true;
                        }
                        else {
                            return false;
                        }
                    },
                    url: true,
                    nowhitespace: true
                },
                'movie_username': {
                    required: function() {
                        if ($('#movie_type').val() == 1) {
                            return true;
                        }
                        else {
                            return false;
                        }
                    },
                    nowhitespace: true
                },
                'movie_password': {
                    required: function() {
                        if ($('#movie_type').val() == 1) {
                            return true;
                        }
                        else {
                            return false;
                        }
                    }
                },
                'movie_m3u_url': {
                    required: function() {
                        if ($('#movie_type').val() == 2) {
                            return true;
                        }
                        else {
                            return false;
                        }
                    },
                    url: true,
                    nowhitespace: true
                },
                'show_type': {
                    required: true
                },
                'show_dns': {
                    required: function() {
                        if ($('#show_type').val() == 1) {
                            return true;
                        }
                        else {
                            return false;
                        }
                    },
                    url: true,
                    nowhitespace: true
                },
                'show_username': {
                    required: function() {
                        if ($('#show_type').val() == 1) {
                            return true;
                        }
                        else {
                            return false;
                        }
                    },
                    nowhitespace: true
                },
                'show_password': {
                    required: function() {
                        if ($('#show_type').val() == 1) {
                            return true;
                        }
                        else {
                            return false;
                        }
                    },
                    nowhitespace: true
                },
                'show_m3u_url': {
                    required: function() {
                        if ($('#show_type').val() == 2) {
                            return true;
                        }
                        else {
                            return false;
                        }
                    },
                    url: true,
                    nowhitespace: true
                },
                '24_7_type': {
                    required: true
                },
                '24_7_dns': {
                    required: function() {
                        if ($('#24_7_type').val() == 1) {
                            return true;
                        }
                        else {
                            return false;
                        }
                    },
                    url: true,
                    nowhitespace: true
                },
                '24_7_username': {
                    required: function() {
                        if ($('#24_7_type').val() == 1) {
                            return true;
                        }
                        else {
                            return false;
                        }
                    },
                    nowhitespace: true
                },
                '24_7_password': {
                    required: function() {
                        if ($('#24_7_type').val() == 1) {
                            return true;
                        }
                        else {
                            return false;
                        }
                    },
                    nowhitespace: true
                },
                '24_7_m3u_url': {
                    required: function() {
                        if ($('#24_7_type').val() == 2) {
                            return true;
                        }
                        else {
                            return false;
                        }
                    },
                    url: true,
                    nowhitespace: true
                }
            },
            messages: {
                'playlist_name': {
                    required: '*Please Enter Playlist name',
                    nowhitespace: 'Please Remove Space',
                    remote: "Playlist Name already Exist"
                },
                'livetv_type': {
                    required: '*Please Select Type'
                },
                'livetv_dns': {
                    required: '*Please Enter DNS',
                    nowhitespace: 'Please Remove Space'
                },
                'livetv_username': {
                    required: '*Please Enter Username',
                    remote: 'Username Already Exist',
                    nowhitespace: 'Please Remove Space'
                },
                'livetv_password': {
                    required: '*Please Enter Password',
                    nowhitespace: 'Please Remove Space'
                },
                'livetv_m3u_url': {
                    required: '*Please Enter M3u URL',
                    nowhitespace: 'Please Remove Space'
                },
                'movie_type': {
                    required: '*Please Select Type'
                },
                'movie_dns': {
                    required: '*Please Enter DNS',
                    nowhitespace: 'Please Remove Space'
                },
                'movie_username': {
                    required: '*Please Enter Username',
                    remote: 'Username Already Exist',
                    nowhitespace: 'Please Remove Space'
                },
                'movie_password': {
                    required: '*Please Enter Password',
                    nowhitespace: 'Please Remove Space'
                },
                'movie_m3u_url': {
                    required: '*Please Enter M3u URL',
                    nowhitespace: 'Please Remove Space'
                },
                'show_type': {
                    required: '*Please Select Type'
                },
                'show_dns': {
                    required: '*Please Enter DNS',
                    nowhitespace: 'Please Remove Space'
                },
                'show_username': {
                    required: '*Please Enter Username',
                    remote: 'Username Already Exist',
                    nowhitespace: 'Please Remove Space'
                },
                'show_password': {
                    required: '*Please Enter Password',
                    nowhitespace: 'Please Remove Space'
                },
                'show_m3u_url': {
                    required: '*Please Enter M3u URL',
                    nowhitespace: 'Please Remove Space'
                },
                '24_7_type': {
                    required: '*Please Select Type'
                },
                '24_7_dns': {
                    required: '*Please Enter DNS',
                    nowhitespace: 'Please Remove Space'
                },
                '24_7_username': {
                    required: '*Please Enter Username',
                    remote: 'Username Already Exist',
                    nowhitespace: 'Please Remove Space'
                },
                '24_7_password': {
                    required: '*Please Enter Password',
                    nowhitespace: 'Please Remove Space'
                },
                '24_7_m3u_url': {
                    required: '*Please Enter M3u URL',
                    nowhitespace: 'Please Remove Space'
                }
            }
        });
        $(document).on('submit', '#addPlaylistForm', function (e) {
            e.preventDefault();
            var formdata = new FormData($("#addPlaylistForm")[0]);
            $(".playlistBtn").attr('disabled',true);
            $.ajax({
                url: '{{ route("playlist.store") }}',
                type: 'POST',
                data: formdata,
                dataType: "json",
                contentType: false,
                cache: false,
                processData: false,
                success: function (data) {
                    $('.loader').hide();
                    $(".playlistBtn").removeAttr('disabled');
                    if (data.success == 1) {
                        toastr.success('Playlist created');
                        window.location.href = "{{route('playlist/list')}}";
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
</script>
@endsection
