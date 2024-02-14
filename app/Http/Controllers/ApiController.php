<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use App\Http\Requests;
use App\Models\User;
use App\Models\UserSubscription;
use App\Models\Device;
use App\Models\DeviceCode;
use App\Models\Playlist;
use App\Models\DevicePlaylist;
use App\Models\CreditHistory;
use App\Models\DNS;
use Validator;
use Carbon\Carbon;
use DB;

class ApiController extends Controller
{
   //2024-01-02 03:16:31
    //2024-01-09 03:16:31
    //2023-12-26 03:16:31
    public function scheduleCron()
    {
        $current_date = Carbon::now();
        $todayDate = Carbon::parse($current_date)->format('Y-m-d h:i:s');

        $userSubscription = UserSubscription::where('status',1)->where('billing_cycle','!=','onetime')->whereDate('expiry_date', '<=', $todayDate)->get();
        foreach($userSubscription as $value){
            $sub = UserSubscription::where('id',$value->id)->first();
            if($sub){
                $sub->status = 0;
                $sub->save();
            }

            $device_code = DeviceCode::where('device_id', $value->device_id)->latest()->first();
            if($device_code){
                $device_code->status = 0;
                $device_code->save();
            }
        }

        $userSubscription = UserSubscription::where('status',0)->get();
        foreach($userSubscription as $value){
            $device_code = DeviceCode::where('device_id', $value->device_id)->latest()->first();
            if($device_code){
                $device_code->status = 0;
                $device_code->save();
            }
        }

        $userSubscription = UserSubscription::where('status',1)->where('billing_cycle','!=','onetime')->whereDate('next_billing_date', '<=', $todayDate)->where('next_billing_date', '!=', '0000-00-00 00:00:00')->whereNotNull('next_billing_date')->where(function($query){
            $query->whereNull('subscription_id')->orWhere('subscription_id','');
        })->get();

        foreach($userSubscription as $value1){
            $device_code = DeviceCode::where('device_id', $value1->device_id)->latest()->first();
            if($device_code && $device_code->is_code_auto_renew == 1){
                $user = User::where('user_id',$value1->user_id)->where('user_type',3)->first();
                if($user->credits > $value1->reseller_credit_amount){

                    if($user->is_unlimited_credit == 1){
                        User::where('user_id',$value1->user_id)->where('user_type','!=',1)->where('unlimited_credits', '>', 0)->decrement('unlimited_credits', $value1->reseller_credit_amount);
                    }else{
                        User::where('user_id',$value1->user_id)->where('user_type','!=',1)->where('credits', '>', 0)->decrement('credits', $value1->reseller_credit_amount);
                    }
                    $device_code->status = 1;
                    $device_code->save();
                    $activationDate = Carbon::parse($value1->next_billing_date)->format('Y-m-d h:i:s');

                    $next_billing_date = $expire_date = '';
                    if ($value1['billing_cycle'] == 'onetime') {
                        $expire_date = Carbon::parse($activationDate)->addYears('100')->format('Y-m-d h:i:s');
                        $next_billing_date = Carbon::parse($activationDate)->format('Y-m-d h:i:s');
                    if ($value1->billing_period == 'm') {
                        $next_billing_date = Carbon::parse($activationDate)->addMonths($value1->billing_period_num)->format('Y-m-d h:i:s');
                    }
                    if ($value1->billing_period == 'y') {
                        $next_billing_date  = Carbon::parse($activationDate)->addYears($value1->billing_period_num)->format('Y-m-d h:i:s');
                    }
                    if ($value1->billing_period == 'w') {
                        $next_billing_date = Carbon::parse($activationDate)->addWeeks($value1->billing_period_num)->format('Y-m-d h:i:s');
                    }
                    } else {
                    if ($value1->billing_period == 'm') {
                        if($value1->billing_cycle_num){
                            $expire_date = Carbon::parse($activationDate)->addMonths($value1->billing_cycle_num)->format('Y-m-d h:i:s');
                        }else{
                            $expire_date = Carbon::parse($activationDate)->addMonths($value1->billing_period_num)->format('Y-m-d h:i:s');
                        }
                        $next_billing_date = Carbon::parse($activationDate)->addMonths($value1->billing_period_num)->format('Y-m-d h:i:s');
                    }
                    if ($value1->billing_period == 'y') {
                        if($value1->billing_cycle_num){
                            $expire_date = Carbon::parse($activationDate)->addYears($value1->billing_cycle_num)->format('Y-m-d h:i:s');
                        }else{
                            $expire_date = Carbon::parse($activationDate)->addYears($value1->billing_period_num)->format('Y-m-d h:i:s');
                        }
                        $next_billing_date = Carbon::parse($activationDate)->addYears($value1->billing_period_num)->format('Y-m-d h:i:s');
                    }
                    if ($value1->billing_period == 'w') {
                        if($value1->billing_cycle_num){
                            $expire_date = Carbon::parse($activationDate)->addWeeks($value1->billing_cycle_num)->format('Y-m-d h:i:s');
                        }else{
                            $expire_date = Carbon::parse($activationDate)->addWeeks($value1->billing_period_num)->format('Y-m-d h:i:s');
                        }
                        $next_billing_date = Carbon::parse($activationDate)->addWeeks($value1->billing_period_num)->format('Y-m-d h:i:s');
                    }
                    }
                    $sub1 = UserSubscription::where('id',$value1->id)->first();
                    if($sub1){
                        $sub1->status = 1;
                        $sub1->expiry_date = $expire_date;
                        $sub1->next_billing_date = $next_billing_date;
                        $sub1->last_billing_date = $todayDate;
                        $sub1->save();
                    }
                }else{
                    if($device_code){
                        $device_code->status = 0;
                        $device_code->save();
                    }
                    if($sub1){
                        $sub1 = UserSubscription::where('id',$value1->id)->first();
                        $sub1->expiry_date = $todayDate;
                        $sub1->status = 0;
                        $sub1->save();
                    }
                }
                $credit_history = new CreditHistory;
                $credit_history->credits = $value1->reseller_credit_amount;
                $credit_history->user_id = $value1->user_id;
                $credit_history->device_id = $value1->device_id;
                $credit_history->plan_id = $value1->plan_id;
                $credit_history->is_credited = 0;
                $credit_history->credited_to = $value1->user_id;
                $credit_history->added_by = $value1->user_id;
                $credit_history->save();

            }else{
                if($device_code){
                    $device_code->status = 0;
                    $device_code->save();
                }
                $sub1 = UserSubscription::where('id',$value1->id)->first();
                if($sub1){
                    $sub1->expiry_date = $todayDate;
                    $sub1->status = 0;
                    $sub1->save();
                }
            }
        }

        $userSubscription1 = UserSubscription::where('status',1)->where('billing_cycle','!=','onetime')->whereDate('next_billing_date', '<=', $todayDate)->where('next_billing_date', '!=', '0000-00-00 00:00:00')->whereNotNull('next_billing_date')->where(function($query){
            $query->whereNotNull('subscription_id')->orWhere('subscription_id','!=','');
        })->get();

        foreach($userSubscription1 as $value1){
                $activationDate = Carbon::parse($value1->next_billing_date)->format('Y-m-d h:i:s');
                $next_billing_date = $expire_date = '';
                if ($value1['billing_cycle'] == 'onetime') {
                    $expire_date = Carbon::parse($activationDate)->addYears('100')->format('Y-m-d h:i:s');
                    $next_billing_date = Carbon::parse($activationDate)->format('Y-m-d h:i:s');
                  if ($value1->billing_period == 'm') {
                    $next_billing_date = Carbon::parse($activationDate)->addMonths($value1->billing_period_num)->format('Y-m-d h:i:s');
                  }
                  if ($value1->billing_period == 'y') {
                    $next_billing_date  = Carbon::parse($activationDate)->addYears($value1->billing_period_num)->format('Y-m-d h:i:s');
                  }
                  if ($value1->billing_period == 'w') {
                    $next_billing_date = Carbon::parse($activationDate)->addWeeks($value1->billing_period_num)->format('Y-m-d h:i:s');
                  }
                } else {
                  if ($value1->billing_period == 'm') {
                    if($value1->billing_cycle_num){
                        $expire_date = Carbon::parse($activationDate)->addMonths($value1->billing_cycle_num)->format('Y-m-d h:i:s');
                    }else{
                        $expire_date = Carbon::parse($activationDate)->addMonths($value1->billing_period_num)->format('Y-m-d h:i:s');
                    }
                    $next_billing_date = Carbon::parse($activationDate)->addMonths($value1->billing_period_num)->format('Y-m-d h:i:s');
                  }
                  if ($value1->billing_period == 'y') {
                    if($value1->billing_cycle_num){
                        $expire_date = Carbon::parse($activationDate)->addYears($value1->billing_cycle_num)->format('Y-m-d h:i:s');
                    }else{
                        $expire_date = Carbon::parse($activationDate)->addYears($value1->billing_period_num)->format('Y-m-d h:i:s');
                    }
                    $next_billing_date = Carbon::parse($activationDate)->addYears($value1->billing_period_num)->format('Y-m-d h:i:s');
                  }
                  if ($value1->billing_period == 'w') {
                    if($value1->billing_cycle_num){
                        $expire_date = Carbon::parse($activationDate)->addWeeks($value1->billing_cycle_num)->format('Y-m-d h:i:s');
                    }else{
                        $expire_date = Carbon::parse($activationDate)->addWeeks($value1->billing_period_num)->format('Y-m-d h:i:s');
                    }
                    $next_billing_date = Carbon::parse($activationDate)->addWeeks($value1->billing_period_num)->format('Y-m-d h:i:s');
                  }
                }
                $sub1 = UserSubscription::where('id',$value1->id)->first();
                $sub1->expire_date = $expire_date;
                $sub1->next_billing_date = $next_billing_date;
                $sub1->last_billing_date = $todayDate;
                $sub1->save();

        }

        return response()->json(['success' => 1]);
    }

    public function scheduleSubscription(Request $request)
    {

        $current_date = Carbon::now();
        $todayDate = Carbon::parse($current_date)->format('Y-m-d h:i:s');

        $userSubscription = UserSubscription::where('status',1)->whereDate('expiry_date', '<=', $todayDate)->get();
        foreach($userSubscription as $value){

            $response = UserSubscription::getPurchaseInfoByCustomerID($value->customer_id,'live');
            if($response['status'] == 'error'){
                $sub = UserSubscription::where('id',$value->id)->first();
                $sub->status = 0;
                $sub->save();

                $device_code = DeviceCode::where('device_id', $value->device_id)->latest()->first();
                $device_code->status = 0;
                $device_code->save();
            }

            $response = UserSubscription::getPurchaseInfoByCustomerID($value->customer_id,'cancelled');
            if($response['status'] == 'success'){
                $sub = UserSubscription::where('id',$value->id)->first();
                $sub->status = 3;
                $sub->save();

                $device_code = DeviceCode::where('device_id', $value->device_id)->latest()->first();
                $device_code->status = 0;
                $device_code->save();
            }

        }
        return response()->json(['success' => 1]);
    }

    public function playlistByMacKey(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                'mac_id' => 'required',
                'mac_key' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()->first()], 200);
            }

            $input = $request->all();

            $checkDevice = Device::where('mac_id', $request->mac_id)->where('mac_key', $request->mac_key)->first();
            if (empty($checkDevice)) {
                return response()->json(['error' => 'Device not found'], 200);
            }
            $data = DevicePlaylist::with('playlist')->with('multiplaylist')->where('device_id',$checkDevice->id)->latest()->get();

            $modifiedData = $data->map(function ($result) {
                if($result->playlist && $result->playlist->dns_id){
                    $dns = DNS::where('id',$result->playlist->dns_id)->first();
                    $result->playlist->dns = $dns->dns_url;
                }
                return $result;
            });

            $response['success'] = true;
            $response['data'] = $modifiedData;
            $response['message'] = 'Get Playlist successfully.';
            return response()->json($response, 200);


        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function playlistByDevice(Request $request)
    {

        try {

            $validator = Validator::make($request->all(), [
                'device_code' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()->first()], 200);
            }

            $input = $request->all();

            $checkDeviceCode = DeviceCode::where('code', $request->device_code)->first();
            if (empty($checkDeviceCode)) {
                return response()->json(['error' => 'Code not found'], 200);
            }else if($checkDeviceCode->status == 0){
                return response()->json(['error' => 'Code is Expired'], 200);
            }else if($checkDeviceCode->status == 2){
                return response()->json(['error' => 'Code is Inactive'], 200);
            }

            $data = DevicePlaylist::with('playlist')->where('device_id',$checkDeviceCode->device_id)->latest()->get();
            if(count($data) > 0){
                $modifiedData = $data->map(function ($result) {
                    if($result->playlist && $result->playlist->dns_id){
                        $dns = DNS::where('id',$result->playlist->dns_id)->first();
                        $result->playlist->dns = $dns->dns_url;
                    }
                    return $result;
                });
                $response['success'] = true;
                $response['data'] = $modifiedData;
                $response['message'] = 'Get Playlist successfully.';
            }else{
                $response['success'] = true;
                $response['data'] = [];
                $response['message'] = 'Playlist Data Not Found.';
            }

            return response()->json($response, 200);


        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function verifyDeviceCode(Request $request)
    {

        try {

            $validator = Validator::make($request->all(), [
                // 'user_id' => 'required',
                'device_type' => 'required',
                'platform' => 'required',
                'app_name' => 'required',
                // 'device_code' => 'required',
                'mac_address' => 'required',
                'ip_address' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()->first()], 200);
            }
            $input = $request->all();

            if($request->device_code){
                $checkDeviceCode = DeviceCode::where('code', $request->device_code)->first();
                if (empty($checkDeviceCode)) {
                    return response()->json(['error' => 'Code not found'], 200);
                }else if($checkDeviceCode->status == 0){
                    return response()->json(['error' => 'Code is Expired'], 200);
                }else if($checkDeviceCode->status == 2){
                    return response()->json(['error' => 'Code is Inactive'], 200);
                }
                $device_id = $checkDeviceCode->device_id;

            }else if($request->mac_id && $request->mac_key){

                $checkDevice = Device::where('mac_id', $request->mac_id)->where('mac_key',$request->mac_key)->first();
                if (empty($checkDevice)) {
                    return response()->json(['error' => 'Device not found'], 200);
                }else{
                    $checkDeviceCode = DeviceCode::where('device_id', $checkDevice->id)->latest()->first();
                    if (empty($checkDeviceCode)) {
                        return response()->json(['error' => 'Code not found'], 200);
                    }else if($checkDeviceCode->status == 0){
                        return response()->json(['error' => 'Code is Expired'], 200);
                    }else if($checkDeviceCode->status == 2){
                        return response()->json(['error' => 'Code is Inactive'], 200);
                    }
                }
                $device_id = $checkDeviceCode->device_id ? $checkDeviceCode->device_id : $checkDevice->id;
            }else{
                return response()->json(['error' => 'mac id and key or device code is required'], 200);
            }

            $checkDevice = Device::where('id', $device_id)->whereNotNull('mac_address')->first();
            if (!empty($checkDevice)) {
                if(!empty($checkDevice->mac_address) && $checkDevice->mac_address != $request->mac_address){
                    return response()->json(['error' => 'Code is not Valid for this Device'], 200);
                }else if ($checkDevice->is_active == 0) {
                    return response()->json(['error' => 'Device is Inactive'], 200);
                }
            }

            $device = Device::where('id', $device_id)->first();
            if(!empty($device)){
				$device->device_type = $request->device_type;
				$device->mac_address = $request->mac_address;
				$device->ip_address = $request->ip_address;
				$device->platform = $request->platform;
				$device->app_name = $request->app_name;
				$device->save();

				DeviceCode::where('code', $request->device_code)->where('status',1)->update(['is_verified'=>1]);

                $userSubscription = UserSubscription::where('device_id',$device_id)->first();

				$response['success'] = true;
				$response['expiry_date'] = $userSubscription ? $userSubscription->expiry_date : "";
				$response['message'] = 'Code Verified successfully.';

				return response()->json($response, 200);
			}else{
				return response()->json(['error' => 'Device not found'], 200);
			}

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function createCode(Request $request)
    {

        try {

            $validator = Validator::make($request->all(), [
                'user_id' => 'required',
                'device_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()->first()], 200);
            }

            $input = $request->all();

            $checkDevice = Device::where('id', $request->device_id)->where('user_id', $request->user_id)->first();
            if (empty($checkDevice)) {
                return response()->json(['error' => 'Device not found'], 200);
            }

            $current_date = Carbon::now();
            $todayDate = Carbon::parse($current_date)->format('Y-m-d h:i:s');
            $expire_date = Carbon::parse($current_date)->addMinutes(72*60)->format('Y-m-d h:i:s');

            $checkDeviceCode = DeviceCode::where('device_id',$request->device_id)->where('status',1)->first();
            if (!empty($checkDeviceCode)) {
                return response()->json(['error' => 'Device Code already active. Please Renew Code. '], 200);
            }
            $deviceCode = new DeviceCode;
            $code =  $deviceCode->generate_code();
            $deviceCode->device_id = $request->device_id;
            $deviceCode->code = $code;
            $deviceCode->user_id = $request->user_id;
            // $deviceCode->duration = 72;
            // $deviceCode->expire_date = $expire_date;
            $deviceCode->save();
            $response['success'] = true;
            $response['message'] = 'Code created successfully.';

            return response()->json($response, 200);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function renewCode(Request $request)
    {

        try {

            $validator = Validator::make($request->all(), [
                'user_id' => 'required',
                'device_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()->first()], 200);
            }

            $input = $request->all();

            $checkDevice = Device::where('id', $request->device_id)->where('user_id', $request->user_id)->first();
            if (empty($checkDevice)) {
                return response()->json(['error' => 'Device not found'], 200);
            }

            $current_date = Carbon::now();
            $todayDate = Carbon::parse($current_date)->format('Y-m-d h:i:s');
            $expire_date = Carbon::parse($current_date)->addMinutes(72*60)->format('Y-m-d h:i:s');

            DeviceCode::where('device_id',$request->device_id)->where('status','!=',0)->where('status',1)->update(['status'=>2]);

            $deviceCode = new DeviceCode;
            $code =  $deviceCode->generate_code();
            $deviceCode->device_id = $request->device_id;
            $deviceCode->code = $code;
            $deviceCode->user_id = $request->user_id;
            // $deviceCode->duration = 72;
            // $deviceCode->expire_date = $expire_date;
            $deviceCode->save();

            $response['success'] = true;
            $response['code'] = $code;
            $response['message'] = 'Code renewd successfully.';

            return response()->json($response, 200);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function checkSubscription(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'device_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()->first()], 200);
            }
            $input = $request->all();
            $userCustomerData = UserSubscription::where('device_id', $request->device_id)->first();
            $userCurrnetPlan = [];
            if($userCustomerData){
                $response3 = UserSubscription::getSubscriptionByCustomerID($userCustomerData->customer_id);
                foreach($response3['data'] as $value){
                    if($value['status'] == 'live' || $value['status'] == 'trial'){
                        $userCurrnetPlan = $value;
                    }
                }
            }
            if($userCurrnetPlan){
                $response['success'] = true;
                $response['message'] = 'User has subscribed.';
            }else{
                $response['success'] = true;
                $response['data'] = [];
                $response['message'] = 'User has not subscribed.';
            }
            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function checkSubscriptionWithDeviceID(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'device_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()->first()], 200);
            }
            $input = $request->all();
            $userCustomerData = UserSubscription::where('device_id', $request->device_id)->first();

            if($userCustomerData){
                $response['success'] = true;
                $response['message'] = 'User has subscribed.';
            }else{
                $response['success'] = true;
                $response['data'] = [];
                $response['message'] = 'User has not subscribed.';
            }
            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function activeDeactiveDevice(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'device_id' => 'required',
                'is_active' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()->first()], 200);
            }
            $input = $request->all();
            $device = Device::where('id', $request->device_id)->first();
            $device->is_active = $request->is_active;
            $device->save();
            if($request->is_active == 1){
                $response['success'] = true;
                $response['message'] = 'Activate Device';
            }else{
                $response['success'] = true;
                $response['data'] = [];
                $response['message'] = 'Deactivate Device';
            }
            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function getCurrentPlan(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'device_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()->first()], 200);
            }
            $input = $request->all();
            $userCustomerData = UserSubscription::where('device_id', $request->device_id)->first();
            $userCurrnetPlan = [];
            if($userCustomerData){
                $response3 = UserSubscription::getSubscriptionByCustomerID($userCustomerData->customer_id);
                foreach($response3['data'] as $value){
                    if($value['status'] == 'live' || $value['status'] == 'trial'){
                        $userCurrnetPlan = $value;
                    }
                }
            }
            if($userCurrnetPlan){
                $userSubscription = UserSubscription::where('device_id',$request->device_id)->where('plan_id',$userCurrnetPlan['plan']['id'])->first();
                $userCurrnetPlan['device_limit'] = $userSubscription->device_limit;
                $userCurrnetPlan['trial_device_limit'] = $userSubscription->trial_device_limit;
                $remain_device = $userCurrnetPlan['remaining_device_limit'] = $userSubscription->remaining_device_limit;
                $trial_remain_device = $userCurrnetPlan['trial_remain_device_limit'] = $userSubscription->trial_remaining_device_limit;

                $response['success'] = true;
                $response['data'] = $userCurrnetPlan;
                $response['message'] = 'Get Subscription successfully.';
            }else{
                $response['success'] = true;
                $response['data'] = [];
                $response['message'] = 'Subscription data not found.';
            }

            return response()->json($response, 200);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getCurrentPlanWithDeviceID(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'device_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()->first()], 200);
            }
            $input = $request->all();
            $userSubscription = UserSubscription::where('device_id', $request->device_id)->first();

            if($userSubscription){
                // $userSubscription = UserSubscription::where('device_id',$request->device_id)->where('plan_id',$userCustomerData['plan']['id'])->first();
                $userCurrnetPlan['device_limit'] = $userSubscription->device_limit;
                $userCurrnetPlan['trial_device_limit'] = $userSubscription->trial_device_limit;
                $remain_device = $userCurrnetPlan['remaining_device_limit'] = $userSubscription->remaining_device_limit;
                $trial_remain_device = $userCurrnetPlan['trial_remain_device_limit'] = $userSubscription->trial_remaining_device_limit;

                $response['success'] = true;
                $response['data'] = $userCurrnetPlan;
                $response['message'] = 'Get Subscription successfully.';
            }else{
                $response['success'] = true;
                $response['data'] = [];
                $response['message'] = 'Subscription data not found.';
            }

            return response()->json($response, 200);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    //         $upcomingUsersSubscription = UserSubscription::where('status', 2)->whereDate('activation_date', '<=', $todayDate)->get();

//         foreach($upcomingUsersSubscription as $value){
//             $subscription = UserSubscription::where('user_id', $value->user_id)->where('status', 1)->first();
//             $postdata['product_id'] = $value->product_id;
//             $postdata['plan_id'] = $value->plan_id;
//             $postdata['payment_mode'] = "offline";
//             $postdata['card_id'] = "offline";
//             $postdata['price'] = $value->amount;
//             $postdata['activated_at_val'] = "immediately";

//             $curl = curl_init();

//             curl_setopt_array($curl, array(
//                 CURLOPT_URL => 'https://payments.pabbly.com/api/v1/subscription/'.$value->subscription_id.'/update',
//                 CURLOPT_RETURNTRANSFER => true,
//                 CURLOPT_ENCODING => '',
//                 CURLOPT_MAXREDIRS => 10,
//                 CURLOPT_TIMEOUT => 0,
//                 CURLOPT_FOLLOWLOCATION => true,
//                 CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//                 CURLOPT_CUSTOMREQUEST => 'PUT',
//                 CURLOPT_POSTFIELDS =>json_encode($postdata),
//                 CURLOPT_HTTPHEADER => array(
//                 'Content-Type: application/json'
//                 ),
//                 CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
//                 CURLOPT_USERPWD => env('PUBBLY_API_KEY').':'.env('PUBBLY_STRIPE_KEY'),
//             ));

//             $response = curl_exec($curl);
//             if (curl_errno($curl)) {
//                 $error_msg = curl_error($curl);
//             }
//             curl_close($curl);

//             $response = json_decode($response,true);

//             echo "<pre>";
//             print_r($response);die;
//             if($response['status'] == 'success'){

//                 UserSubscription::where('subscription_id', $value->subscription_id)->where('status',1)->update(['status'=>3]);

//                 $usersSubscription = UserSubscription::where('id', $value->id)->where('user_id', $value->user_id)->where('status',2)->latest()->first();

//                 $usersSubscription->plan_id = $response['data']['plan']['id'];
//                 $usersSubscription->plan_name = $response['data']['plan']['plan_name'];
//                 $usersSubscription->plan_description = $response['data']['plan']['plan_description'];
//                 $usersSubscription->plan_code = $response['data']['plan']['plan_code'];
//                 $usersSubscription->invoice_number =  isset($response['data']['invoice']) ? $response['data']['invoice']['invoice_id'] : "";
//                 $usersSubscription->invoice_id =  isset($response['data']['invoice']) ? $response['data']['invoice']['id'] : "";
//                 $usersSubscription->invoice_link = isset($response['data']['invoice']) ? $response['data']['invoice']['invoice_link'] : "";
//                 $usersSubscription->invoice_status =  isset($response['data']['invoice']) ? $response['data']['invoice']['status'] : "";
//                 $usersSubscription->invoice_due_date =  isset($response['data']['invoice']) ? Carbon::parse($response['data']['invoice']['due_date'])->toDateString() : "";
//                 $usersSubscription->device_limit = $response['data']['plan']['meta_data']['device_limit'];
//                 $usersSubscription->remaining_device_limit = $response['data']['plan']['meta_data']['device_limit'];
//                 $usersSubscription->subscription_id = $response['data']['subscription']['id'];
//                 $usersSubscription->payment_method = $response['data']['subscription']['payment_method'];
//                 $usersSubscription->email = $response['data']['subscription']['email_id'];
//                 $usersSubscription->currency_symbol= $response['data']['subscription']['currency_symbol'];
//                 $usersSubscription->amount= $response['data']['subscription']['amount'];
//                 // $usersSubscription->is_unlimited= $response['data']['subscription']['plan']['meta_data']['is_unlimited'];

//                 $usersSubscription->billing_period= $response['data']['subscription']['plan']['billing_period'];
//                 $usersSubscription->billing_period_num= $response['data']['subscription']['plan']['billing_period_num'];
//                 $usersSubscription->billing_cycle= $response['data']['subscription']['plan']['billing_cycle'];
//                 $usersSubscription->billing_cycle_num= $response['data']['subscription']['plan']['billing_cycle_num'];
//                 $usersSubscription->setup_fee= $response['data']['subscription']['plan']['setup_fee'];
//                 $usersSubscription->payment_terms= $response['data']['subscription']['payment_terms'];
//                 $usersSubscription->status= 1;
//                 $usersSubscription->status_after_expire= $response['data']['subscription']['status'];
//                 $usersSubscription->specific_keep_live= isset($response['data']['subscription']['plan']['specific_keep_live']) ? $response['data']['subscription']['plan']['specific_keep_live'] : "";

//                 $usersSubscription->start_date= $response['data']['subscription']['starts_at'] ? Carbon::parse($response['data']['subscription']['starts_at'])->toDateString() : Null;
//                 $usersSubscription->activation_date= $response['data']['subscription']['activation_date'] ? Carbon::parse($response['data']['subscription']['activation_date'])->toDateString() : Null;
//                 $usersSubscription->expiry_date= $response['data']['subscription']['expiry_date'] ? Carbon::parse($response['data']['subscription']['expiry_date'])->toDateString() : Null;

//                 $usersSubscription->trial_period= $response['data']['subscription']['plan']['trial_period'];
//                 $usersSubscription->trial_amount= isset($response['data']['subscription']['plan']['trial_amount'])
//                 ? $response['data']['subscription']['plan']['trial_amount'] : $value->trial_amount;
//                 $usersSubscription->trial_type= isset($response['data']['subscription']['plan']['trial_type']) ? $response['data']['subscription']['plan']['trial_type'] : "";
//                 $usersSubscription->trial_days= isset($response['data']['subscription']['trial_days']) ? $response['data']['subscription']['trial_days'] : 0;
//                 $usersSubscription->trial_expiry_date= !empty($response['data']['subscription']['trial_expiry_date']) ? Carbon::parse($response['data']['subscription']['trial_expiry_date'])->toDateString() : Null;

//                 $usersSubscription->next_billing_date= $response['data']['subscription']['next_billing_date'] ? Carbon::parse($response['data']['subscription']['next_billing_date'])->toDateString() : Null;
//                 $usersSubscription->last_billing_date= $response['data']['subscription']['last_billing_date'] ? Carbon::parse($response['data']['subscription']['last_billing_date'])->toDateString() : Null;
//                 $usersSubscription->canceled_date= $response['data']['subscription']['canceled_date'] ? Carbon::parse($response['data']['subscription']['canceled_date'])->toDateString() : Null;
//                 $usersSubscription->save();

//             }

//         }
// // die;
//         $activeUsersSubscription = UserSubscription::whereDate('next_billing_date', '<=', $todayDate)->where('status', 1)->get();
//         foreach($activeUsersSubscription as $value){

//             $curl = curl_init();

//                 curl_setopt_array($curl, array(
//                     CURLOPT_URL => 'https://payments.pabbly.com/api/v1/subscription/'.$value->subscription_id,
//                     CURLOPT_RETURNTRANSFER => true,
//                     CURLOPT_ENCODING => '',
//                     CURLOPT_MAXREDIRS => 10,
//                     CURLOPT_TIMEOUT => 0,
//                     CURLOPT_FOLLOWLOCATION => true,
//                     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//                     CURLOPT_CUSTOMREQUEST => 'GET',
//                     CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
//                     CURLOPT_USERPWD => env('PUBBLY_API_KEY').':'.env('PUBBLY_STRIPE_KEY'),
//                 ));

//                 $response = curl_exec($curl);
//                 if (curl_errno($curl)) {
//                     $error_msg = curl_error($curl);
//                 }
//                 curl_close($curl);

//                 $response = json_decode($response,true);
//             // echo "<pre>";
//             // print_r($response);die;
//             $usersSubscription = UserSubscription::where('id', $value->id)->first();

//             if(empty($response['data']['next_billing_date']) || strtotime($value->expiry_date) <= strtotime($response['data']['next_billing_date'])){
//                 $usersSubscription->status = 0;
//             }else{
//                 $usersSubscription->next_billing_date = $response['data']['next_billing_date'];
//                 $usersSubscription->last_billing_date = $response['data']['last_billing_date'];
//             }
//             $usersSubscription->save();
//         }
}
