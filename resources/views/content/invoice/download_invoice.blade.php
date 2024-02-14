<!DOCTYPE html>
<html lang="en" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
    <meta charset="utf-8">
    <meta name="x-apple-disable-message-reformatting">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="format-detection" content="telephone=no, date=no, address=no, email=no">
     
  <title>Downlod Invoice</title>
  
    <style>
        .hover-underline:hover {
            text-decoration: underline !important;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        @keyframes ping {

            75%,
            100% {
                transform: scale(2);
                opacity: 0;
            }
        }

        @keyframes pulse {
            50% {
                opacity: .5;
            }
        }

        @keyframes bounce {

            0%,
            100% {
                transform: translateY(-25%);
                animation-timing-function: cubic-bezier(0.8, 0, 1, 1);
            }

            50% {
                transform: none;
                animation-timing-function: cubic-bezier(0, 0, 0.2, 1);
            }
        }

        @media (max-width: 600px) {
            .sm-px-24 {
                padding-left: 24px !important;
                padding-right: 24px !important;
            }

            .sm-py-32 {
                padding-top: 32px !important;
                padding-bottom: 32px !important;
            }

            .sm-w-full {
                width: 100% !important;
            }
        }

    </style>
</head>
<body
    style="margin: 0; padding: 0; width: 100%; word-break: break-word; -webkit-font-smoothing: antialiased; --bg-opacity: 1;">
    <h1 style="text-align: center;">Purple IPTV</h1>
    <div style="word-wrap:break-word;background-color:#fff;text-align:left;border-radius:5px;border:1px solid #d4e2ff;max-width:650px;width:100%;margin-left:auto;margin-right:auto">
        <div style="padding:5px 20px 0px 20px;text-align:left;background:#fff;border-bottom:2px solid #eef7ff;border-top-right-radius:5px;border-top-left-radius:5px">
            <h1 style="font-weight:600;line-height:1.5;font-size:20px;font-family: Montserrat, Helvetica, sans-serif;">Invoice</h1>
        </div>
        <div style="padding:1.25rem">
            <!-- <div>
                <h2 style="margin-bottom:.5rem;margin-top:10px;font-weight:600;line-height:1.2;font-size:22px">
                    Hello sameer Mali,</h2>
                <p style="margin-top:0;color:#6c757d;font-size:14px;line-height:1.6">Your payment is successfully completed.</p>
                <p style="margin-top:0px;color:#6c757d;font-size:14px;margin-bottom:20px;line-height:1.6">Successful Transaction Details:</p>
            </div> -->

            <!-- <div style="border-bottom:2px solid #eef7ff;margin-top:15px;margin-bottom:25px"></div> -->
<div style="width:100%">
    <div style="width:50%;float:left">
        
        <h3 style="margin-top:0;margin-bottom:.5rem;font-weight:600;line-height:1.2;font-size:18px;font-family: Montserrat, Helvetica, sans-serif !important;">Purple IPTV</h3>
        <p style="margin-top:0;margin-bottom:0;line-height:1.6;color:#6c757d;font-size:14px;font-family: Montserrat, Helvetica, sans-serif;">Phone : </p>
        <p style="margin-top:0;margin-bottom:0;line-height:1.6;color:#6c757d;font-size:14px;font-family: Montserrat, Helvetica, sans-serif;">{{$admin->phone_no ?? ''}}</p>
        
    </div>
    <div style="text-align:right">
        <h3 style="box-sizing:border-box;margin-top:0;margin-bottom:0;font-weight:600;line-height:2.8;font-size:.75rem;page-break-after:avoid"> 
            @if($response['status'] == 1)
                <span style="font-weight:600;line-height:1;text-align:center;white-space:nowrap;vertical-align:top;border-radius:.25rem;padding:5px;font-size:12px;margin-top:0;color:white;background-color:#28a745;font-size:14px;font-weight:600;font-family: Montserrat, Helvetica, sans-serif;">Active</span>
            @elseif($response['status'] == 2)
                <span style="font-weight:600;line-height:1;text-align:center;white-space:nowrap;vertical-align:top;border-radius:.25rem;padding:5px;font-size:12px;margin-top:0;color:white;background-color:#EA5455;font-size:14px;font-weight:600;font-family: Montserrat, Helvetica, sans-serif;">Upcoming</span>
            @else
                <span style="font-weight:600;line-height:1;text-align:center;white-space:nowrap;vertical-align:top;border-radius:.25rem;padding:5px;font-size:12px;margin-top:0;color:white;background-color:#ea5455;font-size:14px;font-weight:600;font-family: Montserrat, Helvetica, sans-serif;">Expired</span>
            @endif

           
        </h3>
        <h2 style="margin-top:0;margin-bottom:.5rem;font-weight:600;line-height:1.2;font-size:22px;text-transform:uppercase;font-family: Montserrat, Helvetica, sans-serif;">Invoice</h2>   
        <p style="margin-top:0;margin-bottom:0;line-height:1.6;color:#6c757d;font-size:14px">
            <span style="margin-top:0;color:#212529;font-size:14px;font-weight:600;font-family: Montserrat, Helvetica, sans-serif;">Product Name : </span>
            {{ $products['data']['product_name'] ?? ''}}
        </p>        
        <p style="margin-top:0;margin-bottom:0;line-height:1.6;color:#6c757d;font-size:14px">
            <span style="margin-top:0;color:#212529;font-size:14px;font-weight:600;font-family: Montserrat, Helvetica, sans-serif;">Device Code : </span>
            {{$response['device']['device_code']['code']}}</span>
        </p>
        <p style="margin-top:0;margin-bottom:0;line-height:1.6;color:#6c757d;font-size:14px">
            <span style="margin-top:0;color:#212529;font-size:14px;font-weight:600;font-family: Montserrat, Helvetica, sans-serif;">Activation Date :  {{ \Carbon\Carbon::parse($response['activation_date'])->format('M d, Y') }}</span>
            
        </p>  
        <p style="margin-top:0;margin-bottom:0;line-height:1.6;color:#6c757d;font-size:14px">
            <span style="margin-top:0;color:#212529;font-size:14px;font-weight:600;font-family: Montserrat, Helvetica, sans-serif;">Expiry Date :  {{ \Carbon\Carbon::parse($response['expiry_date'])->format('M d, Y') }}</span>
            
        </p>  
        <p style="margin-top:0;margin-bottom:0;line-height:1.6;color:#6c757d;font-size:14px">
            <span style="margin-top:0;color:#212529;font-size:14px;font-weight:600;font-family: Montserrat, Helvetica, sans-serif;">Next Billing Date :  {{ \Carbon\Carbon::parse($response['next_billing_date'])->format('M d, Y') }}</span>
            
        </p> 
        <p style="margin-top:0;margin-bottom:0;line-height:1.6;color:#6c757d;font-size:14px">
            <span style="margin-top:0;color:#212529;font-size:14px;font-weight:600;font-family: Montserrat, Helvetica, sans-serif;">Last Billing Date :  {{ \Carbon\Carbon::parse($response['last_billing_date'])->format('M d, Y') }}</span>
            
        </p>  
        <p style="margin-top:0;margin-bottom:0;line-height:1.6;color:#6c757d;font-size:14px">
            <span style="margin-top:0;color:#212529;font-size:14px;font-weight:600;font-family: Montserrat, Helvetica, sans-serif;">Gateway Name : @if(env('PUBBLY_GATEWAY_TYPE') == 'test'){{"Test"}}@else{{"Stripe"}}@endif Gateway</span>
            
        </p> 
    </div>
</div>

<div style="border-bottom:2px solid #eef7ff;margin-top:15px;margin-bottom:25px"></div>

<div style="width:100%;height:110px">
    <div style="width:50%;float:left">
        <h4 style="margin-top:0;margin-bottom:.5rem;font-weight:600;line-height:1.2;font-size:15px;font-family: Montserrat, Helvetica, sans-serif;">Bill to :</h4>
        <h3 style="margin-top:0;margin-bottom:.5rem;font-weight:600;line-height:1.2;font-size:18px;font-family: Montserrat, Helvetica, sans-serif;">@if($user){{$user['first_name']}} {{$user['last_name']}}@endif</h3>
        <p style="margin-top:0;margin-bottom:0;line-height:1.6;color:#6c757d;font-size:14px;font-family: Montserrat, Helvetica, sans-serif;"><a href="mailto:sachinteq@gmail.com" target="_blank">@if($user){{$user['email_id']}}@endif</a></p>
        <p style="margin-top:0;margin-bottom:0;line-height:1.6;color:#6c757d;font-size:14px"></p>
        
    </div>
    <div style="text-align:right;float:right;word-break:break-word">
        <div style="word-wrap:break-word;background-color:#fff;border-radius:.25rem;text-align:left;border:1px solid #d4e2ff">

            
            <div style="padding:.75rem 1.25rem;margin-bottom:0;background-color:#eef7ff;border-bottom:1px solid #d4e2ff">
                <h4 style="margin-top:0;margin-bottom:0;font-weight:600;line-height:1.2;font-size:15px;font-family: Montserrat, Helvetica, sans-serif;">Order Date : {{\Carbon\Carbon::parse($response['starts_at'])->format('M d, Y')}}</h4>
            </div>
            <div style="padding:1.25rem">
                <p style="margin-top:0;margin-bottom:0;color:#6c757d;font-size:15px;font-family: Montserrat, Helvetica, sans-serif;"> Invoice Amount :
                    <span style="font-family: DejaVu Sans; sans-serif;">$</span><span style="margin-top:0;color:#212529;font-size:18px;font-weight:600;font-family: Montserrat, Helvetica, sans-serif;">{{ $plan_amount}}</span>
                </p>
            </div>
        </div>
    </div>
</div>


<div style="margin-top:20px">
    <div style="box-sizing:border-box;display:block;width:100%;overflow-x:auto">
        <table style="box-sizing:border-box;border-collapse:collapse;width:100%;margin-bottom:0;color:#212529;white-space:nowrap;font-size:14px">
            <thead style="box-sizing:border-box;display:table-header-group">
                <tr style="box-sizing:border-box;page-break-inside:avoid">
                    <th style="padding:.75rem;border-top:2px solid #eef7ff;border-bottom:2px solid #eef7ff;background-color:#fff;font-size:14px;font-weight:600;font-family: Montserrat, Helvetica, sans-serif;">DESCRIPTION</th>
                    <th style="padding:.75rem;border-top:2px solid #eef7ff;border-bottom:2px solid #eef7ff;background-color:#fff;font-size:14px;text-align:right;font-weight:600;font-family: Montserrat, Helvetica, sans-serif;">QUANTITY</th>
                    <th style="padding:.75rem;border-top:2px solid #eef7ff;border-bottom:2px solid #eef7ff;background-color:#fff;font-size:14px;text-align:right;font-weight:600;font-family: Montserrat, Helvetica, sans-serif;">UNIT COST</th>
                    <th style="padding:.75rem;border-top:2px solid #eef7ff;border-bottom:2px solid #eef7ff;background-color:#fff;font-size:14px;text-align:right;font-weight:600;font-family: Montserrat, Helvetica, sans-serif;">TOTAL</th>
                </tr>
            </thead>
            <tbody style="box-sizing:border-box">
                <tr style="box-sizing:border-box;page-break-inside:avoid">
                    <td style="box-sizing:border-box;padding:.75rem;vertical-align:top;border-top:1px solid #eef7ff;background-color:#fff;word-break:break-word;line-height:1.5;font-family: Montserrat, Helvetica, sans-serif;">{{$plan_name}}
                        
                        <br><span style="font-size:12px;color:#6c757d">(Plan Name)</span>
                    </td>
                    <td style="box-sizing:border-box;padding:.75rem;vertical-align:top;border-top:1px solid #eef7ff;background-color:#fff;text-align:right;white-space:pre-line">1</td>
                    <td style="box-sizing:border-box;padding:.75rem;vertical-align:top;border-top:1px solid #eef7ff;text-align:right;background-color:#fff;white-space:pre-line"><span style="font-family: DejaVu Sans; sans-serif;">$</span>{{ $plan_amount}}</td>
                    <td style="box-sizing:border-box;padding:.75rem;vertical-align:top;border-top:1px solid #eef7ff;text-align:right;background-color:#fff;white-space:pre-line"><span style="font-family: DejaVu Sans; sans-serif;">$</span>{{ $plan_amount}}</td>
                </tr>
                
                
            </tbody>
        </table>
    </div>
</div>



<div style="border-bottom:2px solid #eef7ff;margin-top:0px;margin-bottom:0px"></div>


<div style="width:100%;height:140px">

    <div style="min-width:40%;float:right">
        <table style="box-sizing:border-box;border-collapse:collapse;width:100%;margin-bottom:0;color:#212529;white-space:nowrap;font-size:14px">
            <tbody style="box-sizing:border-box">
            
                <tr style="box-sizing:border-box;page-break-inside:avoid">
                    <td style="padding:.75rem;vertical-align:top;background-color:#fff;border-bottom:2px solid #eef7ff;font-family: Arial, Helvetica, sans-serif;">Sub Total :</td>
                    <td style="padding:.75rem;vertical-align:top;background-color:#fff;text-align:right;border-bottom:2px solid #eef7ff;font-family: Arial, Helvetica, sans-serif;"><span style="font-family: DejaVu Sans; sans-serif;">$</span>{{ $plan_amount}}</td>
                </tr>
                    <tr style="box-sizing:border-box;page-break-inside:avoid">
                        <td style="padding:.75rem;vertical-align:top;background-color:#fff;border-top:1px solid #eef7ff;font-family: Arial, Helvetica, sans-serif;">Total Amount:</td>
                        <td style="padding:.75rem;vertical-align:top;background-color:#fff;text-align:right;border-top:1px solid #eef7ff;font-family: Arial, Helvetica, sans-serif;"><span style="font-family: DejaVu Sans; sans-serif;">$</span>{{ $plan_amount}}</td>
                    </tr>
            </tbody>
        </table>
     <div style="padding:.75rem;background-color:#eef7ff;text-align:right;border:1px solid #d4e2ff;border-radius:4px">
            Payment Made :   <span style="font-family: DejaVu Sans; sans-serif;">$</span>{{ $plan_amount}}
     </div>
    </div>
</div>
    <div style="border-bottom:2px solid #eef7ff;margin-top:15px;margin-bottom:20px"></div>
    <div>
        <h3 style="margin-top:0;margin-bottom:.5rem;font-weight:600;line-height:1.2;font-size:18px;font-family: Arial, Helvetica, sans-serif;">Plan Description</h3>
            <div style="font-family: Arial, Helvetica, sans-serif;">
            @if($plan_description){!! $plan_description !!}@endif
            </div>
    </div>
</div>
</div>
</body>

</html>
