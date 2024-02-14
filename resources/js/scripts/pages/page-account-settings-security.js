$(function () {
  ('use strict');
  // variables
  var form = $('#change-password-form'),
    select2 = $('.select2'),
    accountNumberMask = $('.account-number-mask');

  // jQuery Validation for all forms
  // --------------------------------------------------------------------
  if (form.length) {
    form.each(function () {
      var $this = $(this);
console.log($this);
      $this.validate({
        rules: {
          'current_password': {
            required: true,
            nowhitespace: true
          },
          'new_password': {
            required: true,
            minlength: 6,
            nowhitespace: true
          },
          'confirm_new_password': {
            required: true,
            minlength: 6,
            equalTo: '#account-new-password',
            nowhitespace: true
          },
        },
        messages: {
          'current_password': {
            required: '*Enter current password',
            nowhitespace: 'Please Remove Space'
          },
          'new_password': {
            required: '*Enter new password',
            minlength: 'Enter at least 6 characters',
            nowhitespace: 'Please Remove Space'
          },
          'confirm_new_password': {
            required: '*Please Retype new password',
            minlength: 'Enter at least 6 characters',
            equalTo: 'The password and its confirm are not the same',
            nowhitespace: 'Please Remove Space'
          }
        }
      });
      // $this.on('submit', function (e) {
      //   e.preventDefault();
      // });
    });
  }

  //phone
  if (accountNumberMask.length) {
    accountNumberMask.each(function () {
      new Cleave($(this), {
        phone: true,
        phoneRegionCode: 'US'
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
