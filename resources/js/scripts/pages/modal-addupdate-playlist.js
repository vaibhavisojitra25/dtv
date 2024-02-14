
jQuery(document).ready(function () {
    $('#addUpdatePlaylistMdl').on('hidden.bs.modal', function(e) {
        $("#dns_id").removeAttr('disabled');
        $("#dns").removeAttr('disabled');
        $("#hide_playlist_id").val("");
        $("#addUpdatePlaylistForm")[0].reset();
        validator.resetForm();
        $("#addUpdatePlaylistForm").find('.error').removeClass("error");
    });
    $(document).on('click',".addPlaylist",function(){
      var playlist_limit = $(this).attr('data-playlist_limit');
        if(playlist_limit == 1){
            $(".limitReached").hide();
            $(".playlistForm").show();
        }else{
            $(".limitReached").show();
            $(".playlistForm").hide();
        }
      var type = $(this).attr('data-type');
      $('#type').val(type);
        if(type == 1){
            $(".xtraem_div").show();
            $(".m3u_div").hide();
        }else if(type == 2){
            $(".m3u_div").show();
            $(".xtraem_div").hide();
        }else{
            $('#type').val(1);
            $(".xtraem_div").show();
            $(".m3u_div").hide();
        }
    });
    $(document).on('click',".updatePlaylist",function(){
        $("#mdlTitle").text('Edit PlayList');
        $('#hide_playlist_id').val($(this).attr('data-id'));
        var type = $(this).attr('data-type');
        $('#type').val(type);
        if(type == 1){
            $(".xtraem_div").show();
            $(".m3u_div").hide();
        }else if(type == 2){
            $(".m3u_div").show();
            $(".xtraem_div").hide();
        }else{
            $('#type').val(1);
            $(".xtraem_div").show();
            $(".m3u_div").hide();
        }
        if($(this).attr('data-dns_id')){
            $("#dns").attr('disabled',true);
            $('#dns_id').val($(this).attr('data-dns_id'));
        }else{
            $("#dns_id").attr('disabled',true);
            $('#dns').val($(this).attr('data-dns'));
        }
        $('#playlist_name').val($(this).attr('data-playlist_name'));
        $('#username').val($(this).attr('data-username'));
        $('#password').val($(this).attr('data-password'));
        $('#m3u_url').val($(this).attr('data-m3u_url'));
        $('#epg').val($(this).attr('data-epg'));
        $('#user_agent').val($(this).attr('data-user_agent'));

      });
    // $(document).on('click',".ResetForm",function(){
    //     var value = $("#type").val();
    //     if(value == 1){
    //         $(".xtraem_div").show();
    //         $(".m3u_div").hide();
    //     }else if(value == 2){
    //         $(".m3u_div").show();
    //         $(".xtraem_div").hide();
    //     }else{
    //         $(".xtraem_div").show();
    //         $(".m3u_div").hide();
    //     }
    //   validator.resetForm();
    //   $('#addUpdatePlaylistForm').find(".error").removeClass("error");
    // });

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


});
