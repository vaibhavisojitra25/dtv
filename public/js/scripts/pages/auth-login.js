/*=========================================================================================
  File Name: auth-login.js
  Description: Auth login js file.
  ----------------------------------------------------------------------------------------
  Item Name: Vuexy  - Vuejs, HTML & Laravel Admin Dashboard Template
  Author: PIXINVENT
  Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/

$(function () {
  'use strict';

  var pageLoginForm = $('#auth-login-form');

  // jQuery Validation
  // --------------------------------------------------------------------
  if (pageLoginForm.length) {
    pageLoginForm.validate({
      /*
      * ? To enable validation onkeyup
      onkeyup: function (element) {
        $(element).valid();
      },*/
      /*
      * ? To enable validation on focusout
      onfocusout: function (element) {
        $(element).valid();
      }, */
      ignore: ".ignore",
      rules: {
        'email': {
          required: true,
          email: true
        },
        'password': {
          required: true,
        },
        'hiddenRecaptcha': {
          required: function () {
              if (grecaptcha.getResponse() == '') {
                  return true;
              } else {
                  return false;
              }
          }
        } 
      },
      messages: {
        'email': {
          required: "*Please Enter Email",
          email: 'Please Enter Valid Email'
        },
        'password': {
          required: '*Please Enter Password'
        },
        'hiddenRecaptcha': {
          required: '*Please validate your reCAPTCHA.'
        }
      },
    });
  }
});
