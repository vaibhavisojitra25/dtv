@extends('layouts/contentLayoutMaster')

@section('title', 'Add User')

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
          <h4 class="card-title">Add User</h4>
          <a href="{{ url()->previous() }}" title="Go Back" class="btn btn-primary me-1 waves-effect waves-float waves-light">Back</a>
        </div>
        <div class="card-body">
        <form class="form form-vertical" method="post" id="addUserForm">
        {{csrf_field()}}
            <div class="row">
                <div class="col-6">
                    <div class="col-12">
                        <div class="mb-1">
                            <label class="form-label" for="first_name">First Name<span class="text-danger">*</span></label>
                            <input type="text" id="first_name" class="form-control" placeholder="First Name" name="first_name">
                        </div>
                    </div>
                   
                    <div class="col-12">
                        <div class="mb-1">
                            <label class="form-label" for="last_name">Last Name<span class="text-danger">*</span></label>
                            <input type="text" id="last_name" class="form-control" placeholder="Last Name" name="last_name">
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="mb-1">
                            <label class="form-label" for="email">Email<span class="text-danger">*</span></label>
                            <input type="email" id="email" class="form-control" placeholder="Email" name="email" value=""  autocomplete="false">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-1">
                            <label class="form-label" for="password">Password<span class="text-danger">*</span></label>
                            <div class="input-group form-password-toggle input-group-merge">
                                <input type="password" class="form-control"
                                id="password" name="password"  placeholder="Please password"  onpaste="return false;" autocomplete="false" readonly onfocus="this.removeAttribute('readonly');" style="background-color: #fff;">
                                <div class="input-group-text cursor-pointer">
                                    <i data-feather="eye"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="col-6">


                    <div class="col-12">
                        <div class="mb-1">
                            <label class="form-label" for="phone_no">Mobile Number</label>
                            <input type="text" id="phone_no" class="form-control" maxlength="14" placeholder="(xxx) xxx-xxxx" name="phone_no">
                        </div>
                    </div>
                    @if(Auth::user()->user_type == 1)
                    <div class="row">
                        <div class="col-6">
                            <div class="mb-1">
                                <label class="form-label" for="is_reseller">Is Reseller</label>
                                <br>
                                <span class="btnOn">
                                    <label class="switch">
                                        <input type="checkbox"  name="is_reseller" class="user_type" id="is_reseller"/>
                                        <span class="slider round"></span>
                                    </label>
                                </span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-1">
                                <label class="form-label" for="is_subreseller">Is Sub Reseller</label>
                                <br>
                                <span class="btnOn">
                                    <label class="switch">
                                        <input type="checkbox"  name="is_subreseller" class="user_type" id="is_subreseller"/>
                                        <span class="slider round"></span>
                                    </label>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 reseller_div" style="display:none;">
                        <div class="mb-1">
                            <label class="form-label" for="reseller_id">Select Reseller*</label>
                            <select id="reseller_id" class="form-select" name="reseller_id">
                            <option value="" >--Select--</option>
                                @foreach($reseller as $value)
                                <option value="{{$value->user_id}}" >{{$value->first_name}} {{$value->last_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @endif
                    <div class="col-12">
                        <div class="mb-1">
                            <label class="form-label" for="is_multi_dns">Is Multi DNS</label>
                            <br>
                            <span class="btnOn">
                                <label class="switch">
                                    <input type="checkbox"  name="is_multi_dns"/>
                                    <span class="slider round"></span>
                                </label>
                            </span>
                        </div>
                    </div>

                </div>
                <div class="col-12">
                    <input type="hidden" id="user_id" class="form-control" name="user_id" value="">
                    <button class="btn btn-primary me-1 waves-effect waves-float waves-light saveUser">Submit</button>
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
<script src="{{ asset(mix('vendors/js/forms/validation/jquery.validate.min.js')) }}"></script>
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
  $(document).on('click',".ResetForm",function(){
        $(".reseller_div").hide();
        validator.resetForm();
        $('#addUserForm').find(".error").removeClass("error");
    });
    var message = '';

    $('input.user_type').on('change', function() {
        $('input.user_type').not(this).prop('checked', false);  
    });
    $('#is_reseller').on('change', function() {
        if($(this).is(":checked")) {
            $(".reseller_div").hide();
        }
    });
    $('#is_subreseller').on('change', function() {
        if($(this).is(":checked")) {
            $(".reseller_div").show();
        } else {
            $(".reseller_div").hide();
        }
    });
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
    
    var validator = $('#addUserForm').validate({
            rules: {
                'first_name': {
                    required: true
                },
                'last_name': {
                    required: true
                },
               'email': {
                  required: true,
                  email: true,
                  remote: {
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '{{ url("checkExistEmail") }}',
                    type: "post",
                    data: {
                        email: function () { return $("#email").val(); },
                        user_id: function () { return $("#user_id").val(); },
                    }
                }
                },
                'password': {
                    required: true,
                    nowhitespace: true,
                    minlength:6,
                },
                'reseller_id': {
                    required: {
                        depends: function() {
                            return $('#is_subreseller').is(':checked');
                        }
                    }
                },
                
            },
            messages: {
                'first_name': {
                    required: '*Please Enter First name'
                },
                'last_name': {
                    required: '*Please Enter Last name'
                },
                'email': {
                    required: '*Please Enter Email',
                    remote: "Email already Exist"
                },
               'password': {
                    required: '*Please Enter Password',
                    nowhitespace: 'Please Remove Space',
                    minlength: 'Enter at least 6 characters',
                }, 
                'reseller_id': {
                    required: '*Please Select Reseller',
                },
            },
        });
        $(document).on('submit', '#addUserForm', function (e) {
            e.preventDefault();
            $('.loader').show();
            $(".saveUser").attr('disabled',true);
            var formdata = new FormData($("#addUserForm")[0]);
            $.ajax({
                url: '{{ route("users.store") }}',
                type: 'POST',
                data: formdata,
                dataType: "json",
                contentType: false,
                cache: false,
                processData: false,
                success: function (data) {
                    $('.loader').hide();
                    $(".saveUser").removeAttr('disabled');
                    if (data.success == 1) {
                        toastr.success('User created successfully. User need to confirm their account. We have sent you an activation email.');
                        setTimeout(function () {
                            window.location.href = "{{url('users/list')}}";
                        }, 1000);
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


