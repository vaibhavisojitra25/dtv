@extends('layouts/contentLayoutMaster')

@section('title', 'Plan')

@section('vendor-style')
    <!-- vendor css files -->
    <link rel='stylesheet' href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
    <link rel='stylesheet' href="{{ asset(mix('vendors/css/animate/animate.min.css')) }}">
    <link rel='stylesheet' href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">
@endsection
@section('page-style')
    <!-- Page css files -->
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-sweet-alerts.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-validation.css')) }}">
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <!-- profile -->
            <div class="card">

                <div class="card-body py-2 my-25">
                    <!-- form -->
                    <form class="form form-vertical" method="post" id="updatePlanForm">
                        {{ csrf_field() }}
                        <!-- header section -->

                        <div class="row mt-2 pt-50">
                            <div class="row mb-1">
                                <div class="col-sm-4">
                                    <label class="form-label" for="plan_name">Plan Name<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="plan_name" name="plan_name"
                                        placeholder="Enter Plan name" value="{{$plan['plan_name']}}" data-msg="Please enter plan name" />
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-check mt-2 mb-1">
                                        <input type="hidden" id="freeCheckValue" value="@if(isset($plan['meta_data']['is_free']) && $plan['meta_data']['is_free'] == 1){{1}}@else{{0}}@endif">
                                        <input class="form-check-input" name="is_free" type="checkbox" id="freeCheck"
                                        onchange="freeValueChanged()" @if(isset($plan['meta_data']['is_free']) && $plan['meta_data']['is_free'] == 1){{'checked'}}@endif>
                                        <label class="form-check-label" for="freeCheck">
                                            Is Free
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-check mt-2 mb-1">
                                        <input type="hidden" id="resellerCreditCheckValue" value="@if(isset($plan['meta_data']['is_reseller_credit']) && $plan['meta_data']['is_reseller_credit'] == 1){{1}}@else{{0}}@endif">
                                        <input class="form-check-input" name="is_reseller_credit" type="checkbox" id="resellerCreditCheck"
                                        onchange="resellerCreditValueChanged()" @if(isset($plan['meta_data']['is_reseller_credit']) && $plan['meta_data']['is_reseller_credit'] == 1){{'checked'}}@endif>
                                        <label class="form-check-label" for="resellerCreditCheck">
                                            Is For Reseller
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <label class="form-label amount_label" for="amount">@if(isset($plan['meta_data']['is_reseller_credit']) && $plan['meta_data']['is_reseller_credit'] == 1){{'Credit Amount Deduct for Reseller'}}@else{{'Amount'}}@endif<span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">{{env('CURRENCY')}}</span>
                                        <input type="text" class="form-control" name="amount" id="amount" value="@if(isset($plan['meta_data']['is_reseller_credit']) && $plan['meta_data']['is_reseller_credit'] == 1){{$plan['meta_data']['reseller_credit']}}@else{{$plan['price']}}@endif" placeholder="100" @if(isset($plan['meta_data']['is_free']) && $plan['meta_data']['is_free'] == 1){{'readonly'}}@endif />
                                    </div>

                                </div>

                            </div>

                            <div class="row mb-1">
                                <div class="col-sm-4">
                                    <label class="form-label" for="billing_cycle">Billing Cycle<span class="text-danger">*</span></label>
                                    <select class="form-select" name="billing_cycle" aria-label="Default select example" id="billing_cycle">
                                        <option value="onetime" @if($plan['billing_cycle'] == 'onetime'){{'selected'}}@endif>One Time</option>
                                        <option value="specific" @if($plan['billing_cycle'] != 'onetime'){{'selected'}}@endif>Recurring</option>
                                    </select>
                                  </div>
                                <div class="col-sm-2">
                                    <div class="form-check mt-2 mb-1">
                                        <input type="hidden" id="creditCheckValue" value="@if($plan['meta_data']['is_credit'] == 1){{1}}@else{{0}}@endif">
                                        <input class="form-check-input" name="is_credit" type="checkbox" id="creditCheck"
                                        onchange="creditValueChanged()" @if($plan['meta_data']['is_credit'] == 1){{'checked'}}@endif>
                                        <label class="form-check-label" for="creditCheck">
                                            Is Credit
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-check mt-2 mb-1" id="UnlimitedCreditField" style="@if($plan['meta_data']['is_credit'] == 0){{'display:none'}}@endif" >
                                        <input type="hidden" id="unlimitedCreditCheckValue" value="0">
                                        <input class="form-check-input" name="is_credit_unlimited" type="checkbox" value="1" id="unlimitedCreditCheck"
                                        onchange="unlimitedCreditValueChanged()" @if(isset($plan['meta_data']['is_credit_unlimited']) && $plan['meta_data']['is_credit_unlimited'] == 1){{'checked'}}@endif>
                                        <label class="form-check-label" for="unlimitedCreditCheck">
                                            Is Unlimited Credit
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div id="CreditField" style="@if($plan['meta_data']['is_credit'] == 0 || (isset($plan['meta_data']['is_credit_unlimited']) && $plan['meta_data']['is_credit_unlimited'] == 1)){{'display:none'}}@endif" >
                                        <label class="form-label" for="credit_amount">Credit Amount<span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="credit_amount" name="credit_amount" value="@if($plan['meta_data']['is_credit'] == 1 && (isset($plan['meta_data']['is_credit_unlimited']) && $plan['meta_data']['is_credit_unlimited'] == 0)){{$plan['meta_data']['credit_amount']}}@endif"
                                            placeholder="Credit Amount" />
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-1 forspecific" style="@if($plan['billing_cycle'] == 'onetime'){{'display:none'}}@endif">
                                <div class="col-sm-3">
                                    <label class="form-label" for="billing_period_num">Bill Every<span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="billing_period_num" name="billing_period_num" value="{{$plan['billing_period_num']}}" min="1"/>
                                  </div>
                                <div class="col-sm-3">
                                    <label class="form-label" for="billing_period"></label>
                                    <select class="form-select" name="billing_period" aria-label="Default select example">
                                        <option value="y" @if($plan['billing_period'] == 'y'){{'selected'}}@endif>Year</option>
                                        <option value="m" @if($plan['billing_period'] == 'm'){{'selected'}}@endif>Month</option>
                                        <option value="w" @if($plan['billing_period'] == 'w'){{'selected'}}@endif>Week</option>
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label" for="billing_cycle_num">No. of Billing Cycles</label>
                                    <input type="text" class="form-control" id="billing_cycle_num" name="billing_cycle_num" value="{{$plan['billing_cycle_num']}}" placeholder="Forever" />
                                    <span class="text-muted text-small"> Leave it blank to use the forever billing cycle type. </span>
                                  </div>
                            </div>

                            <div class="col-sm-12">
                            <label class="form-label" for="description">Short description</label>
                            <input type="hidden" id="descriptionValue" value="{{$plan['plan_description']}}">
                                <textarea type="text" id="summernote" name="description" placeholder="Description"
                                rows="3">{{$plan['plan_description']}}</textarea>
                            </div>

                            <div class="col-12">
                                <input type="hidden" name="plan_id" value="{{$plan['id']}}">
                                <button class="btn btn-primary mt-1 me-1">Save changes</button>
                                <button type="reset" class="btn btn-outline-secondary mt-1 ResetForm">Discard</button>
                            </div>
                        </div>
                </div>

                </form>
                <!--/ form -->
            </div>

        </div>
    </div>
@endsection

@section('vendor-script')
    <!-- vendor files -->
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/forms/validation/jquery.validate.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/forms/validation/additional-methods.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/forms/cleave/cleave.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/forms/cleave/addons/cleave-phone.us.js')) }}"></script>
@endsection
@section('page-script')
    <!-- Page js files -->
    <script src="{{ asset(mix('js/scripts/pages/page-account-settings-account.js')) }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <script type="text/javascript">
           function creditValueChanged()
        {
            if($('#creditCheck').is(":checked")){
                $("#CreditField").show();
                $("#UnlimitedCreditField").show();
                $('#creditCheckValue').val(1);
                $('#unlimitedCreditCheck').prop('checked',false);
            }else{
                $("#CreditField").hide();
                $("#UnlimitedCreditField").hide();
                $('#creditCheckValue').val(0);
                $('#unlimitedCreditCheck').prop('checked',false);
            }
        }

        function unlimitedCreditValueChanged()
        {
            if($('#unlimitedCreditCheck').is(":checked")){
                $("#CreditField").hide();
            }else{
                $("#CreditField").show();
            }
        }

        function freeValueChanged()
        {
            if($('#freeCheck').is(":checked"))  {
                $("#amount").val(0);
                $("#amount").attr('readonly','readonly');
            }else{
                $("#amount").val();
                $("#amount").removeAttr('readonly');
            }
        }

        function resellerCreditValueChanged()
        {
            if($('#resellerCreditCheck').is(":checked"))  {
                $(".amount_label").text('Credit Amount Deduct for Reseller');
            }else{
                $(".amount_label").text('Amount');
            }

        }

        $(document).ready(function() {

            $(document).on('click',".ResetForm",function(){
                if($('#creditCheckValue').val() == 1){
                    $("#CreditField").show();
                    $("#UnlimitedCreditField").show();
                } else{
                    $("#CreditField").hide();
                    $("#UnlimitedCreditField").hide();
                }

                if($('#freeCheckValue').val() == 1)  {
                    $("#amount").val(0);
                    $("#amount").attr('readonly','readonly');
                }else{
                    $("#amount").val();
                    $("#amount").removeAttr('readonly');
                }
                if($('#resellerCreditCheckValue').val() == 1)  {
                    $(".amount_label").text('Credit Amount Deduct for Reseller');
                }else{
                    $(".amount_label").text('Amount');
                }
                var description = $("#descriptionValue").val();
                $('#summernote').summernote('code', description);
                validator.resetForm();
                $('#updatePlanForm').find(".error").removeClass("error");
            });

            $('#summernote').summernote({
                height: 200,
            });
            $(document).on('change','#billing_cycle',function(){
                {
                if($(this).val() == 'specific')
                    $(".forspecific").show();
                else
                    $(".forspecific").hide();
                }
            });

            var validator =  $('#updatePlanForm').validate({
                rules: {
                    'plan_name': {
                        required: true
                    },
                    'amount': {
                        required: true
                    },
                    'credit_amount': {
                        required: function() {
                            if($('#creditCheck').is(":checked")){
                                return true;
                            }
                            else {
                                return false;
                            }
                        },
                    },
                    // 'billing_cycle_num': {
                    //     required: function() {
                    //         if($('#billing_cycle').val() == 'specific'){
                    //             return true;
                    //         }
                    //         else {
                    //             return false;
                    //         }
                    //     },
                    // },
                },
                messages: {
                    'plan_name': {
                        required: '*Please Enter Plan Name'
                    },
                    'amount': {
                        required: '*Please Enter Amount'
                    },
                    'credit_amount': {
                        required: '*Please Enter Credit Amount'
                    },
                    'billing_cycle_num': {
                        required: '*Please Enter Billing Cycle Number'
                    },
                }
            });
            $(document).on('submit', '#updatePlanForm', function (e) {
                e.preventDefault();
                var formdata = new FormData($("#updatePlanForm")[0]);
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('planUpdate') }}",
                    type: "POST",
                    data: formdata,
                    dataType: "json",
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(data) {
                        toastr.success('Plan updates');
                        window.location.href = "{{route('subscription/plan')}}";
                    },
                    error: function(data) {
                    }
                });
            });
        });
    </script>

@endsection
