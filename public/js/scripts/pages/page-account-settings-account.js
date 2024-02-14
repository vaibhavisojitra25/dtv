$(function () {
  ('use strict');

  function FormatPhoneNumber(number)
  {
      number.replace(/[^\d]/g, '')
      if (number.length <= 10) {
          number = number.replace(/(\d{3})(\d{3})(\d{4})/, "($1) $2-$3");
      }
      $('#phone_no').val(number);
  }

  $(document).on('keyup','#phone_no', function() {
      var number = $(this).val();
      FormatPhoneNumber(number);
  });

  // variables
  var form = $('.validate-form'),
    accountUploadImg = $('#account-upload-img'),
    accountUploadBtn = $('#account-upload'),
    accountUserImage = $('.uploadedAvatar'),
    accountResetBtn = $('#account-reset'),
    accountNumberMask = $('.account-number-mask'),
    accountZipCode = $('.account-zip-code'),
    select2 = $('.select2');
    // deactivateAcc = document.querySelector('#formAccountDeactivation');
    // deactivateButton = deactivateAcc.querySelector('.deactivate-account');

  // Update user photo on click of button

  if (accountUserImage) {
    var resetImage = accountUserImage.attr('src');
    accountUploadBtn.on('change', function (e) {
      var reader = new FileReader(),
        files = e.target.files;
      reader.onload = function () {
        if (accountUploadImg) {
          accountUploadImg.attr('src', reader.result);
        }
      };
      reader.readAsDataURL(files[0]);
    });

    accountResetBtn.on('click', function () {
      accountUserImage.attr('src', resetImage);
      accountUploadBtn.val('');
    });
    accountUploadImg.on('click', function () {
      accountUploadBtn.trigger('click');
    });
  }

  // jQuery Validation for all forms
  // --------------------------------------------------------------------
  if (form.length) {
    form.each(function () {
      var $this = $(this);

      $this.validate({
        rules: {
          first_name: {
            required: true
          },
          last_name: {
            required: true
          },
          email: {
            required: true
          },
          phone_no: {
            required: true,
          }
        }
      });
      $this.on('submit', function (e) {
        e.preventDefault();
      });
    });
  }

  // disabled submit button on checkbox unselect
  // if (deactivateAcc) {
  //   $(document).on('click', '#accountActivation', function () {
  //     if (accountActivation.checked == true) {
  //       deactivateButton.removeAttribute('disabled');
  //     } else {
  //       deactivateButton.setAttribute('disabled', 'disabled');
  //     }
  //   });
  // }

  // Deactivate account alert
  const accountActivation = document.querySelector('#accountActivation');

  // Alert With Functional Confirm Button
  // if (deactivateButton) {
  //   deactivateButton.onclick = function () {
  //     if (accountActivation.checked == true) {
  //       Swal.fire({
  //         text: 'Are you sure you would like to deactivate your account?',
  //         icon: 'warning',
  //         showCancelButton: true,
  //         confirmButtonText: 'Yes',
  //         customClass: {
  //           confirmButton: 'btn btn-primary',
  //           cancelButton: 'btn btn-outline-danger ms-2'
  //         },
  //         buttonsStyling: false
  //       }).then(function (result) {
  //         if (result.value) {
  //           Swal.fire({
  //             icon: 'success',
  //             title: 'Deleted!',
  //             text: 'Your file has been deleted.',
  //             customClass: {
  //               confirmButton: 'btn btn-success'
  //             }
  //           });
  //         } else if (result.dismiss === Swal.DismissReason.cancel) {
  //           Swal.fire({
  //             title: 'Cancelled',
  //             text: 'Deactivation Cancelled!!',
  //             icon: 'error',
  //             customClass: {
  //               confirmButton: 'btn btn-success'
  //             }
  //           });
  //         }
  //       });
  //     }
  //   };
  // }

  //phone
  if (accountNumberMask.length) {
    accountNumberMask.each(function () {
      new Cleave($(this), {
        phone: true,
        phoneRegionCode: 'IN'
      });
    });
  }

  //zip code
  if (accountZipCode.length) {
    accountZipCode.each(function () {
      new Cleave($(this), {
        delimiter: '',
        numeral: true
      });
    });
  }

  // For all Select2
  if (select2.length) {
    select2.each(function () {
      var $this = $(this);
      $this.wrap('<div class="position-relative"></div>');
      $this.select2({
        dropdownParent: $this.parent()
      });
    });
  }
});
