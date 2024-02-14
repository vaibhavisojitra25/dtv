@extends('layouts/contentLayoutMaster')

@section('title', 'Create New Plan')

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

                    <form class="form form-vertical" method="post" id="addPlanForm">
                        {{ csrf_field() }}
                        <!-- header section -->
                        <div class="row mt-2 pt-50">
                            <div class="row mb-1">
                                <div class="col-sm-4">
                                    <label class="form-label" for="plan_name">Plan Name<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="plan_name" name="plan_name"
                                        placeholder="Enter Plan name" data-msg="Please enter plan name" />
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-check mt-2 mb-1">
                                        <input type="hidden" id="freeCheckValue" value="0">
                                        <input class="form-check-input" name="is_free" type="checkbox" value="1" id="freeCheck"
                                        onchange="freeValueChanged()">
                                        <label class="form-check-label" for="freeCheck">
                                            Is Free
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-check mt-2 mb-1">
                                        <input type="hidden" id="resellerCreditCheckValue" value="0">
                                        <input class="form-check-input" name="is_reseller_credit" type="checkbox" value="1" id="resellerCreditCheck"
                                        onchange="resellerCreditValueChanged()">
                                        <label class="form-check-label" for="resellerCreditCheck">
                                            Is For Reseller
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <label class="form-label amount_label" for="amount">Amount<span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">{{env('CURRENCY')}}</span>
                                        <input type="text" class="form-control" name="amount" id="amount" placeholder="Enter Amount" />
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-1">
                                <div class="col-sm-4">
                                    <label class="form-label" for="billing_cycle">Billing Cycle<span class="text-danger">*</span></label>
                                    <select class="form-select" name="billing_cycle" aria-label="Default select example" id="billing_cycle">
                                        <option value="onetime">One Time</option>
                                        <option value="specific">Recurring</option>
                                    </select>
                                  </div>

                                <div class="col-sm-2">
                                    <div class="form-check mt-2 mb-1">
                                        <input type="hidden" id="creditCheckValue" value="0">
                                        <input class="form-check-input" name="is_credit" type="checkbox" value="1" id="creditCheck"
                                        onchange="creditValueChanged()">
                                        <label class="form-check-label" for="creditCheck">
                                            Is Credit
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-check mt-2 mb-1" id="UnlimitedCreditField" style="display:none">
                                        <input type="hidden" id="unlimitedCreditCheckValue" value="0">
                                        <input class="form-check-input" name="is_credit_unlimited" type="checkbox" value="1" id="unlimitedCreditCheck"
                                        onchange="unlimitedCreditValueChanged()">
                                        <label class="form-check-label" for="unlimitedCreditCheck">
                                            Is Unlimited Credit
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div id="CreditField" style="display:none">
                                        <label class="form-label" for="credit_amount">Credit Amount<span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="credit_amount" name="credit_amount" placeholder="Credit Amount" />
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-1 forspecific" style="display:none">
                                <div class="col-sm-3">
                                    <label class="form-label" for="billing_period_num">Bill Every</label>
                                    <input type="number" class="form-control" id="billing_period_num" name="billing_period_num" value="1" min="1"/>
                                  </div>
                                <div class="col-sm-3">
                                    <label class="form-label" for="billing_period"></label>
                                    <select class="form-select" name="billing_period" aria-label="Default select example">
                                        <option value="y">Year</option>
                                        <option value="m" selected>Month</option>
                                        <option value="w">Week</option>
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label" for="billing_cycle_num">No. of Billing Cycles</label>
                                    <input type="text" class="form-control" id="billing_cycle_num" name="billing_cycle_num" placeholder="Forever" />
                                    <span class="text-muted text-small"> Leave it blank to use the forever billing cycle type. </span>
                                  </div>
                            </div>

                            <div class="col-sm-12">
                            <label class="form-label" for="description">Short description</label>
                                <textarea type="text" id="summernote" name="description" placeholder="Description"
                                rows="3"></textarea>
                            </div>

                            <div class="col-12">
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
                $('#freeCheckValue').val(1);
                $("#freeField").show();
                $("#amount").val(0);
                $("#amount").attr('readonly','readonly');
            }else{
                $('#freeCheckValue').val(0);
                $("#freeField").hide();
                $("#amount").val();
                $("#amount").removeAttr('readonly');
            }

        }
        function resellerCreditValueChanged()
        {
            if($('#resellerCreditCheck').is(":checked"))  {
                $('#resellerCreditCheckValue').val(1);
                $(".amount_label").text('Credit Amount Deduct for Reseller');
            }else{
                $('#resellerCreditCheckValue').val(0);
                $(".amount_label").text('Amount');
            }

        }
        $(document).ready(function() {
            $(document).on('click',".ResetForm",function(){
                if($('#creditCheckValue').val() == 0){
                    $("#CreditField").show();
                    $("#UnlimitedCreditField").show();
                } else{
                    $("#CreditField").hide();
                    $("#UnlimitedCreditField").hide();
                }

                if($('#freeCheckValue').val() == 0)  {
                    $("#freeField").show();
                    $("#amount").val(0);
                    $("#amount").attr('readonly','readonly');
                }else{
                    $("#freeField").hide();
                    $("#amount").val();
                    $("#amount").removeAttr('readonly');
                }

                if($('#resellerCreditCheckValue').val() == 1)  {
                    $("#resellerCreditField").show();
                    $(".amount_label").text('Credit Amount Deduct for Reseller');
                }else{
                    $("#resellerCreditField").hide();
                    $(".amount_label").text('Amount');
                }

                $('#summernote').summernote('code', '');
                validator.resetForm();
                $('#addPlanForm').find(".error").removeClass("error");
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

            var validator =  $('#addPlanForm').validate({
                rules: {
                    'plan_name': {
                        required: true
                    },
                    'amount': {
                        required: true
                    },
                    'credit_amount': {
                        required: function() {
                            if($('#creditCheckValue').val() == 1){
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
            $(document).on('submit', '#addPlanForm', function (e) {
                e.preventDefault();
                var amount = $("#amount").val();
                if(amount == 0){
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: "{{ route('checkFreePlan') }}",
                        type: "POST",
                        dataType: "json",
                        cache: false,
                        success: function(data) {
                            if(data.success == 1){
                                Swal.fire({
                                    title: 'Free Plan Exist',
                                    html: 'You have already one free plan. Please Enter Amount grater than 0.',
                                    icon: 'warning',
                                    showCancelButton: false,
                                    showConfirmButton: true,
                                    confirmButtonColor: '#3085d6',
                                    cancelButtonColor: '#d33',
                                })
                            }else{
                                createPlan();
                            }
                        },
                        error: function(data) {
                        }
                    });
                }else{
                    createPlan();
                }

            });
            function createPlan(){
                var formdata = new FormData($("#addPlanForm")[0]);
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('plan.store') }}",
                    type: "POST",
                    data: formdata,
                    dataType: "json",
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(data) {
                        if(data.data.status == 'success'){
                            toastr.success('Plan created');
                            window.location.href = "{{route('subscription/plan')}}";
                        }else{
                            toastr.error('Error While Create Plan');
                        }

                    },
                    error: function(data) {
                    }
                });
            }
        });
    </script>


@endsection
