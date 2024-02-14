<!-- Edit User Modal -->
<div class="modal fade" id="addUpdatePlaylistMdl" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-edit-user">
    <div class="modal-content">
      <div class="modal-header bg-transparent">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body pb-5 px-sm-5 pt-50">
        <div class="text-center mb-2">
          <h1 class="mb-1" id="mdlTitle">Add Playlist</h1>
        </div>
        <div class="playlistForm">
            <form class="form form-vertical" method="post" id="addUpdatePlaylistForm">
            {{csrf_field()}}
                <div class="row">
                    <div class="col-6">
                        <div class="col-12">
                            <div class="mb-1">
                                <label class="form-label" for="playlist_name">Playlist Name<span class="text-danger">*</span></label>
                                <input type="text" id="playlist_name" class="form-control" placeholder="Playlist Name" name="playlist_name">

                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-1">
                                <label class="form-label" for="type">Type</label>
                                <select id="type" class="form-select" name="type">
                                    <option value="1">Xtream</option>
                                    <option value="2">M3u</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 xtraem_div">
                            <div class="mb-1">
                                <label class="form-label" for="dns_id">Select DNS<span class="text-danger">*</span></label>
                                <select id="dns_id" class="dns_id form-select" name="dns_id">
                                    <option value="">--Select--</option>
                                    @if($dns)
                                    @foreach($dns as $value)
                                    <option value="{{$value['id']}}">{{$value['dns_url']}}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <p class="text-center xtraem_div">OR</p>
                        <div class="col-12 xtraem_div">
                            <div class="mb-1">
                                <label class="form-label" for="dns">Enter DNS<span class="text-danger">*</span></label>
                                <input type="text" id="dns" class="form-control" placeholder="DNS" name="dns">
                            </div>
                        </div>
                        <div class="col-12 xtraem_div">
                            <div class="mb-1">
                                <label class="form-label" for="username">Username<span class="text-danger">*</span></label>
                                <input type="text" id="username" class="form-control" placeholder="Username" name="username">
                            </div>
                        </div>
                        <div class="col-12 xtraem_div">
                            <div class="mb-1">
                                <label class="form-label" for="password">Password<span class="text-danger">*</span></label>
                                <input type="text" id="password" class="form-control" placeholder="Password" name="password">
                            </div>
                        </div>
                        <div class="col-12 m3u_div" style="display:none;">
                            <div class="mb-1">
                                <label class="form-label" for="m3u_url">M3u Url<span class="text-danger">*</span></label>
                                <input type="text" id="m3u_url" class="form-control" placeholder="M3u Url" name="m3u_url">
                            </div>
                        </div>
                    </div>
                    <div class="col-6">

                        <div class="col-12">
                            <div class="mb-1">
                                <label class="form-label" for="epg">EPG</label>
                                <input type="text" id="epg" class="form-control" placeholder="EPG" name="epg">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-1">
                                <label class="form-label" for="user_agent">User Agent</label>
                                <input type="text" id="user_agent" class="form-control" placeholder="User Agent" name="user_agent">
                            </div>
                        </div>

                </div>
                <div class="col-12">
                    <input type="hidden" id="hide_playlist_id" class="form-control" name="hide_playlist_id" value="">
                    <button class="btn btn-primary me-1 waves-effect waves-float waves-light playlistBtn">Submit</button>
                    <button type="reset" class="btn btn-outline-secondary waves-effect" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                </div>
            </div>
          </form>
            </div>
          <div class="row limitReached" style="display:none">
                <div class="col-12 text-center">
                    <h2> Your playlist limit is zero </h2>
                    <p> The limit is added by the admin.</p>
                </div>
            </div>
      </div>
    </div>
  </div>
</div>
<!--/ Edit User Modal -->
