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

        <form class="form form-vertical" method="post" id="updatePlaylistForm">
        {{csrf_field()}}
            <div class="row">
                    <div class="col-6">
                        <div class="mb-1">
                            <label class="form-label" for="playlist_name">Playlist Name*</label>
                            <input type="text" id="playlist_name" class="form-control" placeholder="Playlist Name" name="playlist_name" value="{{$playlist[0]->playlist_name}}">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mb-1">
                            <label class="form-label" for="user_agent">User Agent</label>
                            <input type="text" id="user_agent" class="form-control" placeholder="User Agent" name="user_agent" value="{{$playlist[0]->user_agent}}">
                        </div>
                    </div>
                    @foreach($playlist as $value)
                    @if($value->dns_type == 1)
                    <div class="col-12">
                        <div class="row">
                            <h6>Live TV</h6>
                            <hr>
                            <div class="col-3">
                                <div class="mb-1">
                                    <label class="form-label" for="livetv_type">Select Reseller</label>
                                    <select id="livetv_type" class="form-select" name="livetv_type">
                                        <option value="1" @if($value->type == 1){{"selected"}}@endif>Xtream</option>
                                        <option value="2" @if($value->type == 2){{"selected"}}@endif>M3u</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-3 livetv_xtraem_div">
                                <div class="mb-1">
                                    <label class="form-label" for="livetv_dns">DNS*</label>
                                    <input type="text" id="livetv_dns" class="form-control" placeholder="DNS" name="livetv_dns" value="{{$value->dns}}">
                                </div>
                            </div>

                            <div class="col-3 livetv_xtraem_div">
                                <div class="mb-1">
                                    <label class="form-label" for="livetv_username">Username*</label>
                                    <input type="text" id="livetv_username" class="form-control" placeholder="Username" name="livetv_username" value="{{$value->username}}">
                                </div>
                            </div>
                            <div class="col-3 livetv_xtraem_div">
                                <div class="mb-1">
                                    <label class="form-label" for="livetv_password">Password*</label>
                                    <input type="text" id="livetv_password" class="form-control" placeholder="Password" name="livetv_password" value="{{$value->password}}">
                                </div>
                            </div>
                            <div class="col-4 livetv_m3u_div">
                                <div class="mb-1">
                                    <label class="form-label" for="livetv_m3u_url">M3u Url*</label>
                                    <input type="text" id="livetv_m3u_url" class="form-control" placeholder="M3u Url" name="livetv_m3u_url" value="{{$value->m3u_url}}">
                                </div>
                            </div>
                            <div class="col-4 livetv_m3u_div">
                                <div class="mb-1">
                                    <label class="form-label" for="livetv_epg">EPG</label>
                                    <input type="text" id="livetv_epg" class="form-control" placeholder="EPG" name="livetv_epg" value="{{$value->epg}}">
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    @if($value->dns_type == 2)
                    <div class="col-12">
                        <div class="row">
                            <h6>Movie</h6>
                            <hr>
                            <div class="col-3">
                                <div class="mb-1">
                                    <label class="form-label" for="movie_type">Type</label>
                                    <select id="movie_type" class="form-select" name="movie_type">
                                        <option value="1" @if($value->type == 1){{"selected"}}@endif>Xtream</option>
                                        <option value="2" @if($value->type == 2){{"selected"}}@endif>M3u</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-3 movie_xtraem_div">
                                <div class="mb-1">
                                    <label class="form-label" for="movie_dns">DNS*</label>
                                    <input type="text" id="movie_dns" class="form-control" placeholder="DNS" name="movie_dns" value="{{$value->dns}}">
                                </div>
                            </div>

                            <div class="col-3 movie_xtraem_div">
                                <div class="mb-1">
                                    <label class="form-label" for="movie_username">Username*</label>
                                    <input type="text" id="movie_username" class="form-control" placeholder="Username" name="movie_username" value="{{$value->username}}">
                                </div>
                            </div>
                            <div class="col-3 movie_xtraem_div">
                                <div class="mb-1">
                                    <label class="form-label" for="movie_password">Password*</label>
                                    <input type="text" id="movie_password" class="form-control" placeholder="Password" name="movie_password" value="{{$value->password}}">
                                </div>
                            </div>
                            <div class="col-4 movie_m3u_div">
                                <div class="mb-1">
                                    <label class="form-label" for="movie_m3u_url">M3u Url*</label>
                                    <input type="text" id="movie_m3u_url" class="form-control" placeholder="M3u Url" name="movie_m3u_url" value="{{$value->m3u_url}}">
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    @if($value->dns_type == 3)
                    <div class="col-12">
                        <div class="row">
                            <h6>Show</h6>
                            <hr>
                            <div class="col-3">
                                <div class="mb-1">
                                    <label class="form-label" for="show_type">Type</label>
                                    <select id="show_type" class="form-select" name="show_type">
                                        <option value="1" @if($value->type == 1){{"selected"}}@endif>Xtream</option>
                                        <option value="2" @if($value->type == 2){{"selected"}}@endif>M3u</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-3 show_xtraem_div">
                                <div class="mb-1">
                                    <label class="form-label" for="show_dns">DNS*</label>
                                    <input type="text" id="show_dns" class="form-control" placeholder="DNS" name="show_dns" value="{{$value->dns}}">
                                </div>
                            </div>

                            <div class="col-3 show_xtraem_div">
                                <div class="mb-1">
                                    <label class="form-label" for="show_username">Username*</label>
                                    <input type="text" id="show_username" class="form-control" placeholder="Username" name="show_username" value="{{$value->username}}">
                                </div>
                            </div>
                            <div class="col-3 show_xtraem_div">
                                <div class="mb-1">
                                    <label class="form-label" for="show_password">Password*</label>
                                    <input type="text" id="show_password" class="form-control" placeholder="Password" name="show_password" value="{{$value->password}}">
                                </div>
                            </div>
                            <div class="col-4 show_m3u_div">
                                <div class="mb-1">
                                    <label class="form-label" for="show_m3u_url">M3u Url*</label>
                                    <input type="text" id="show_m3u_url" class="form-control" placeholder="M3u Url" name="show_m3u_url" value="{{$value->m3u_url}}">
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    @if($value->dns_type == 4)
                    <div class="col-12">
                        <div class="row">
                            <h6>24x7</h6>
                            <hr>
                            <div class="col-3">
                                <div class="mb-1">
                                    <label class="form-label" for="24_7_type">Type</label>
                                    <select id="24_7_type" class="form-select" name="24_7_type">
                                        <option value="1" @if($value->type == 1){{"selected"}}@endif>Xtream</option>
                                        <option value="2" @if($value->type == 2){{"selected"}}@endif>M3u</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-3 24_7_xtraem_div">
                                <div class="mb-1">
                                    <label class="form-label" for="24_7_dns">DNS*</label>
                                    <input type="text" id="24_7_dns" class="form-control" placeholder="DNS" name="24_7_dns" value="{{$value->dns}}">
                                </div>
                            </div>

                            <div class="col-3 24_7_xtraem_div">
                                <div class="mb-1">
                                    <label class="form-label" for="24_7_username">Username*</label>
                                    <input type="text" id="24_7_username" class="form-control" placeholder="Username" name="24_7_username" value="{{$value->username}}">
                                </div>
                            </div>
                            <div class="col-3 24_7_xtraem_div">
                                <div class="mb-1">
                                    <label class="form-label" for="24_7_password">Password*</label>
                                    <input type="text" id="24_7_password" class="form-control" placeholder="Password" name="24_7_password" value="{{$value->password}}">
                                </div>
                            </div>
                            <div class="col-4 24_7_m3u_div">
                                <div class="mb-1">
                                    <label class="form-label" for="24_7_m3u_url">M3u Url*</label>
                                    <input type="text" id="24_7_m3u_url" class="form-control" placeholder="M3u Url" name="24_7_m3u_url" value="{{$value->m3u_url}}">
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    @endforeach
                <div class="col-12">
                    <input type="hidden" id="hide_playlist_id" class="form-control" name="hide_playlist_id" value="{{$playlist[0]->unique_id}}">
                    <input type="hidden" id="is_multi_dns" class="form-control" name="is_multi_dns" value="1">
                    <button class="btn btn-primary me-1 waves-effect waves-float waves-light playlistBtn">Submit</button>
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
    var livetv_value = $("#livetv_type").val();
    if(livetv_value == 1){
        $(".livetv_xtraem_div").show();
        $(".livetv_m3u_div").hide();
    }else if(livetv_value == 2){
        $(".livetv_m3u_div").show();
        $(".livetv_xtraem_div").hide();
    }
    var movie_value = $("#movie_type").val();
    if(movie_value == 1){
        $(".movie_xtraem_div").show();
        $(".movie_m3u_div").hide();
    }else if(movie_value == 2){
        $(".movie_m3u_div").show();
        $(".movie_xtraem_div").hide();
    }
    var show_value = $("#show_type").val();
    if(show_value == 1){
        $(".show_xtraem_div").show();
        $(".show_m3u_div").hide();
    }else if(show_value == 2){
        $(".show_m3u_div").show();
        $(".show_xtraem_div").hide();
    }
    var value_24_7 = $("#24_7_type").val();
    if(value_24_7 == 1){
        $(".24_7_xtraem_div").show();
        $(".24_7_m3u_div").hide();
    }else if(value_24_7 == 2){
        $(".24_7_m3u_div").show();
        $(".24_7_xtraem_div").hide();
    }
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
        if(livetv_value == 1){
            $(".livetv_xtraem_div").show();
            $(".livetv_m3u_div").hide();
        }else if(livetv_value == 2){
            $(".livetv_m3u_div").show();
            $(".livetv_xtraem_div").hide();
        }
        if(movie_value == 1){
            $(".movie_xtraem_div").show();
            $(".movie_m3u_div").hide();
        }else if(movie_value == 2){
            $(".movie_m3u_div").show();
            $(".movie_xtraem_div").hide();
        }

        if(show_value == 1){
            $(".show_xtraem_div").show();
            $(".show_m3u_div").hide();
        }else if(show_value == 2){
            $(".show_m3u_div").show();
            $(".show_xtraem_div").hide();
        }

        if(value_24_7 == 1){
            $(".24_7_xtraem_div").show();
            $(".24_7_m3u_div").hide();
        }else if(value_24_7 == 2){
            $(".24_7_m3u_div").show();
            $(".24_7_xtraem_div").hide();
        }

        validator.resetForm();
        $('#updatePlaylistForm').find(".error").removeClass("error");
    });
    var message = '';

    var validator = $('#updatePlaylistForm').validate({
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
                    },
                    nowhitespace: true
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
                    remote: 'Username Already Exist'
                },
                'movie_password': {
                    required: '*Please Enter Password'
                },
                'movie_m3u_url': {
                    required: '*Please Enter M3u URL'
                },
                'show_type': {
                    required: '*Please Select Type'
                },
                'show_dns': {
                    required: '*Please Enter DNS',
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
        $(document).on('submit', '#updatePlaylistForm', function (e) {
        e.preventDefault();
        var formdata = new FormData($("#updatePlaylistForm")[0]);
        $(".playlistBtn").attr('disabled',true);
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
                $(".playlistBtn").removeAttr('disabled');
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

    });
    });
</script>
@endsection
