function handleConfirmation(url, token,flag=0) {
    if(flag == 1){
        Swal.fire({
            title: 'You can not inactivate, delete or change the type of this reseller',
            text: 'because there is a subreseller inside this reseller. First you have to change the user type of all the subreseller inside it, only then you can deactivate, delete or change its type.',
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
                return false;
            }
        });
    }else if(flag == 2){
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
                return false;
            }
        });
    }else{

        Swal.fire({
            title: 'Are you sure?',
            text: 'you want to delete?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!',
            customClass: {
                confirmButton: 'btn btn-primary',
                cancelButton: 'btn btn-outline-danger ms-1'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: token
                    },
                    url: url,
                    success: function (data) {
                        if(data.success == 1){

                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: 'Your record has been deleted.',
                                showConfirmButton: false,
                                timer: 2000
                            });
                        }else{
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Error While delete data.',
                                // customClass: {
                                //   confirmButton: 'btn btn-danger'
                                // }
                                showConfirmButton: false,
                                timer: 2000
                            });
                        }
                        if(data.flag == 1){
                            xtreamTableData.ajax.reload();
                            m3uTableData.ajax.reload();
                            multiDnsTableData.ajax.reload();
                            $(".addPlaylist").attr('data-playlist_limit',data.limit);
                        }else{
                            tableData.ajax.reload();
                        }

                        // setTimeout(function () {
                        //     window.location.reload();
                        // }, 500);
                    },
                    error: function (data) {
                        toastr.error('Something went wrong');
                    }
                });
            }
        });
    }

}
