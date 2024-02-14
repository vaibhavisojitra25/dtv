
jQuery(document).ready(function () {
    $('#addPlaylistMdl').on('hidden.bs.modal', function(e) {
        $("#addPlaylistForm")[0].reset();
        var validator = $("#addPlaylistForm").validate();
        validator.resetForm();
        $("#addPlaylistForm").find('.error').removeClass("error");
    });
    $(document).on('click',".addPlaylist",function(){
      var type = $(this).attr('data-type');
      $('#type').val(type);
        if(type == 1){
            $(".xtraem_div").show();
            $(".m3u_div").hide();
        }else if(type == 2){
            $(".m3u_div").show();
            $(".xtraem_div").hide();
        }
    });
    $(document).on('click',".ResetForm",function(){
      $(".xtraem_div").show();
      $(".m3u_div").hide();
      $("#device_id").select2("destroy");
      $("#device_id").select2();
      validator.resetForm();
      $('#addPlaylistForm').find(".error").removeClass("error");
    });
    var message = '';

    var validator = $('#addPlaylistForm').validate({
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
        }
    });
 
});
