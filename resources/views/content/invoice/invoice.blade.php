@extends('layouts/contentLayoutMaster')

@section('title', 'Invoice Preview')

@section('vendor-style')

@endsection
@section('page-style')

    <style>
        .card-body {
            -ms-flex: 1 1 auto;
            flex: 1 1 auto;
            min-height: 1px;
            padding: 1.25rem !important;
        }

        .text-bodycolor {
            color: #212529 !important;
        }

        .border-bottom-primary2 {
            border-bottom: 2px solid #eef7ff !important
        }

        html body .p-t-20 {
            padding-top: 20px !important
        }

        html body .p-b-20 {
            padding-bottom: 20px !important
        }

        html body .p-10 {
            padding: 10px !important
        }

        th {
            background-color: #fff !important;
        }

        html body .font-10 {
            font-size: 10px !important
        }

        html body .font-12 {
            font-size: 12px !important
        }

        html body .font-14 {
            font-size: 14px !important
        }

        html body .p-t-15 {
            padding-top: 15px !important
        }

        html body .m-b-5 {
            margin-bottom: 5px !important
        }

        html body .p-b-15 {
            padding-bottom: 15px !important
        }

        .text-md-right {
            text-align: right !important;
        }

        body {
            margin: 0;
            line-height: 1.5;
            color: #191b1c;
            text-align: left;
            background-color: #f3f7fa !important;
            font-size: 15px
        }

        .container-fluid {
            width: 100%;
            padding-right: 15px;
            padding-left: 15px;
            margin-right: auto;
            margin-left: auto
        }

        .container {
            max-width: 1254px !important;
            padding-right: 15px;
            padding-left: 15px;
            margin-right: auto;
            margin-left: auto
        }

        html body .m-t-15 {
            margin-top: 15px !important
        }

        html body .m-b-15 {
            margin-bottom: 15px !important
        }

        html body .p-b-10 {
            padding-bottom: 10px !important
        }

        . border-bottom {
            border-bottom: 2px solid #eef7ff !important;
        }

        .d-inline-block {
            display: inline-block !important;
        }

        html body .font-12 {
            font-size: 12px !important;
        }

        html body .m-l-5 {
            margin-left: 5px !important;
        }

        .badge {
            padding: 4px !important;
            font-weight: 500 !important;
        }

        .badge-success {
            color: #fff;
            background-color: #28a745;
        }

        .badge {
            display: inline-block;
            padding: 0.25em 0.4em;
            font-size: 75%;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.25rem;
            transition: color .15s ease-in-out, background-color .15s ease-in-out, border-color .15s ease-in-out, box-shadow .15s ease-in-out;
        }

        .float-right {
            float: right !important;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        .h1,
        .h2,
        .h3,
        .h4,
        .h5,
        h1,
        .h1 {
            font-size: 26px !important
        }

        h2,
        .h2 {
            font-size: 22px !important
        }

        h3,
        .h3 {
            font-size: 18px !important
        }

        h4,
        .h4 {
            font-size: 15px !important
        }

        a {
            color: #007bff;
            cursor: pointer
        }

        a:hover {
            text-decoration: none !important
        }

        p {
            font-size: 15px
        }

        li {
            margin-bottom: 5px
        }

        small,
        .small {
            font-size: 12px !important
        }

        .bg-primary2 {
            background-color: #eef7ff !important
        }

        html body .m-b-0 {
            margin-bottom: 0 !important
        }

        /*  end */


        .border-top-primary2 {
            border-top: 2px solid #eef7ff !important
        }

        .card .card-header {
            background: #fff;
            border-bottom: 2px solid #eef7ff;
            border-top-right-radius: 5px !important;
            border-top-left-radius: 5px !important;
            display: flex;
            justify-content: space-between;
        }

        .card {
            border-radius: 5px !important;
            box-shadow: 0 2px 6px #ced4da;
            border: 0 solid #fff0;
            margin-bottom: 30px;
            z-index: inherit
        }

        .card img {
            border: 2px solid #d4e2ff
        }

        .card .card-footer {
            border-top: 2px solid #eef7ff;
            border-bottom-left-radius: 5px !important;
            border-bottom-right-radius: 5px !important
        }

        .table thead th,
        .table {
            font-weight: 500;
            font-size: 14px;
            color: #212529;
            border-bottom: 2px solid #eef7ff !important;
            padding-top: 15px;
            padding-bottom: 15px;
            border-top: none
        }

        .table tbody td {
            font-size: 15px;
            border-top: 2px solid #eef7ff !important;
            padding-top: 15px;
            padding-bottom: 15px
        }

        .table thead tr,
        .table tbody tr {
            border-left: 2px solid #fff0
        }

        .table-hover tbody tr:hover,
        .table-hover tbody tr.active {
            background-color: #eef7ff !important;
            border-left: 2px solid #007bff !important
        }

        .table-hover tbody tr:hover i.fa-info-circle {
            color: #007bff !important
        }

        .text-right {
            text-align: right;
        }
    </style>
@endsection

@section('content')
    <section class="invoice-preview-wrapper">
        <div class="row invoice-preview">
            <div class="container">
                <div class="row">
                    <div class="col-md-2"></div>
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header p-t-15 p-b-15">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h3 class="text-bodycolor m-b-0 p-t-5 p-b-5">Invoice</h3>
                                    </div>
                                </div>

                            </div>
                            <div id="printableAreaid" class="card-body printableArea">
                                <div class="border-bottom-primary2 p-b-20">
                                    <div class="row">
                                        <div class="col-md-6 text-left">
                                            <div>
                                                <h3 class="text-bodycolor ">Purple IPTV</h3>
                                                <p class="text-secondary m-b-5 font-14"></p>
                                                <p class="text-secondary font-14 m-b-10"><span
                                                        class="p-0 text-bodycolor font-demi">Phone :
                                                        {{ $admin->phone_no }}</span> </p>
                                            </div>
                                        </div>
                                        <div class="col-md-6 text-left text-md-right">
                                            <h3>
                                                @if ($response['status'] == 1)
                                                    <span class='badge bg-success m-l-5 font-12'>Active</span>
                                                @elseif($response['status'] == 2)
                                                    <span class='badge bg-info m-l-5 font-12'>Inactive</span>
                                                @else
                                                    <span class='badge bg-danger m-l-5 font-12'>Expired</span>
                                                @endif
                                            </h3>
                                            <h2>INVOICE</h2>
                                            <p class="text-secondary font-14 m-b-10"><span
                                                    class="p-0 text-bodycolor font-demi"><b>Product Name :
                                                </span>{{ $products['data']['product_name'] }}</p>
                                            <p class="text-secondary font-14 m-b-10"><span
                                                    class="p-0 text-bodycolor font-demi"><b> Device Code :
                                                </span>@if($response['device'] && $response['device']['device_code']){{ $response['device']['device_code']['code'] }}@endif</p>
                                            <p class="text-secondary font-14 m-b-10"><span
                                                    class="p-0 text-bodycolor font-demi">Activation Date : </span>
                                                {{ \Carbon\Carbon::parse($response['activation_date'])->format('M d, Y') }}
                                            </p>
                                            <p class="text-secondary font-14 m-b-10"><span
                                                    class="p-0 text-bodycolor font-demi">Expiry Date : </span>
                                                {{ \Carbon\Carbon::parse($response['expiry_date'])->format('M d, Y') }}
                                            </p>
                                            <p class="text-secondary font-14 m-b-10"><span
                                                    class="p-0 text-bodycolor font-demi">Next Billing Date : </span>
                                                {{ \Carbon\Carbon::parse($response['next_billing_date'])->format('M d, Y') }}
                                            </p>
                                            <p class="text-secondary font-14 m-b-10"><span
                                                    class="p-0 text-bodycolor font-demi">Last Billing Date : </span>
                                                {{ \Carbon\Carbon::parse($response['last_billing_date'])->format('M d, Y') }}
                                            </p>
                                            <p class="text-secondary font-14 m-b-10"><span
                                                    class="p-0 text-bodycolor font-demi">Gateway Name : </span>
                                                @if (env('PUBBLY_GATEWAY_TYPE') == 'test')
                                                    {{ 'Test' }}@else{{ 'Stripe' }}
                                                @endif Gateway
                                            </p>
                                        </div>

                                    </div>
                                </div>
                                <div class="border-bottom-primary2 p-b-20 p-t-20">
                                    <div class="row">
                                        <div class="col-md-7 text-left">
                                            <h4>Bill to :</h4>
                                            <h3>@if($user){{ $user['first_name'] }} {{ $user['last_name'] }}@endif</h3>
                                            <p class="text-secondary m-b-5 font-14">@if($user){{ $user['email_id'] }}@endif</p>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="card text-left shadow-none border-primary1 border">
                                                <div class="card-header bg-primary2">
                                                    <h4 class="m-b-0" style="white-space: nowrap;">Order Date :
                                                        {{ \Carbon\Carbon::parse($response['starts_at'])->format('M d, Y') }}
                                                    </h4>
                                                </div>
                                                <div class="card-body">
                                                    <p class="text-secondary m-b-0">Invoice Amount : <span
                                                            class="text-bodycolor font-demi font-20"> {{env('CURRENCY')}}{{ $plan_amount }}</span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="border-bottom-primary2 m-b-20">
                                    <div class="table-responsive">
                                        <table class="table text-nowrap m-b-0">
                                            <thead>
                                                <tr>
                                                    <th>DESCRIPTION</th>
                                                    <th class="text-right">QUANTITY</th>
                                                    <th class="text-right">UNIT COST</th>
                                                    <th class="text-right">TOTAL</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>{{ $plan_name }}<br><span class="font-12 text-secondary">(Plan
                                                            Name)</span>
                                                    </td>
                                                    <td class="text-right">1</td>
                                                    <td class="text-right"> {{env('CURRENCY')}}{{ $plan_amount }}</td>
                                                    <td class="text-right Montserrat_medium"> {{env('CURRENCY')}}{{ $plan_amount }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xl-7 col-lg-7 col-md-12 col-12"></div>
                                    <div class="col-xl-5 col-lg-5 col-md-12 col-12">
                                        <div class="">
                                            <div class="col-md-12 col-12 border-bottom-primary2">
                                                <p class="text-bodycolor font-14 m-b-10 font-demi d-inline-block">Sub Total
                                                    :</p>
                                                <p class="text-secondary font-14 m-b-10 float-right d-inline-block"
                                                    style="margin-right: 30px;"> {{env('CURRENCY')}}{{ $plan_amount }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="">
                                            <div class="col-md-12 col-12">
                                                <p class="text-bodycolor font-14 m-b-10 font-demi d-inline-block">Total
                                                    Amount :</p>
                                                <p class="text-secondary font-14 m-b-10 float-right d-inline-block"
                                                    style="margin-right: 30px;">  {{env('CURRENCY')}}{{ $plan_amount }}
                                                </p>
                                            </div>
                                            <div class="border border-primary1 bg-primary2 p-10 rounded">
                                                <div class="col-md-12 col-12 p-l-5 p-r-5">
                                                    <p class="text-bodycolor font-18 m-b-0 font-demi d-inline-block">Payment
                                                        Made :</p>
                                                    <p
                                                        class="text-bodycolor font-18 m-b-0 font-demi float-right d-inline-block">
                                                        {{env('CURRENCY')}}{{ $plan_amount }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <h3 class="m-t-15 m-b-15 border-bottom p-b-10">Plan Description</h3>
                                <p>
                                    {!! $plan_description !!}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

@section('vendor-script')
<script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
@endsection

@section('page-script')
    <script>
        function printPageArea(areaID) {
            var printContent = document.getElementById("printableAreaid");
            var WinPrint = window.open('', '', 'width=900,height=650');
            WinPrint.document.write(printContent.innerHTML);
            WinPrint.document.close();
            WinPrint.focus();
            WinPrint.print();
            WinPrint.close();
        }
    </script>

@endsection
