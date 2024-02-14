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
  
    var pageAddDeviceForm = $('#add-device-form');
  
    // jQuery Validation
    // --------------------------------------------------------------------
    if (pageAddDeviceForm.length) {
      pageAddDeviceForm.validate({
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
          'mac_id': {
            required: true,
          },
          'mac_key': {
            required: true,
          },
          'plan_id': {
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
          'mac_id': {
            required: '*Please Enter Mac ID'
          },
          'mac_key': {
            required: '*Please Enter Mac Key'
          },
          'plan_id': {
            required: '*Please Select Plan'
          },
          'hiddenRecaptcha': {
            required: '*Please validate your reCAPTCHA.'
          }
        },
      });
    }
  });
  