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
use App\Models\SiteSetting;
use Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeEmail;
use Carbon\Carbon;
use DB;
use Session;

class PubblyController extends Controller
{

    public function showActivationDevice()
    {
        $pageConfigs = ['blankPage' => true];
        $planData = UserSubscription::getAllPlan();
        $plan_data = array_filter($planData['data'],function($element) {
            return ($element['plan_active'] == 'true' && $element['meta_data']['is_credit']==0);
        });
        $plan_data = array_values($plan_data);
        $siteSetting = SiteSetting::where('id', 1)->first();
        if($siteSetting->is_activation == 2){
            return redirect()->to('/login');
        }else{
            return view('/content/pubbly/add-device', ['pageConfigs' => $pageConfigs,'plan_data' => $plan_data,'siteSetting' => $siteSetting]);
        }
    }

    public function getCheckoutLinkForActivation(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'mac_id' => 'required',
            'mac_key' => 'required',
            'plan_id' => 'required',
            'email' => 'required',
        ]);
        if ($validator->fails()) {
            $response['success'] = false;
            $response['message'] = $validator->errors()->first();
            return response()->json($response, 200);
        }
        $chkUser = User::where('email',$request->get('email'))->first();
        if(empty($chkUser)){
            $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
            $pass = array();
            $alphaLength = strlen($alphabet) - 1;
            for ($i = 0; $i < 8; $i++) {
                $n = rand(0, $alphaLength);
                $pass[] = $alphabet[$n];
            }
            $password = implode($pass); 
            $name = substr($request->get('email'), 0, strrpos($request->get('email'), '@'));

            $newuser = new User;
            $user_id = $newuser->get_random_string();
            $newuser->user_id = $user_id;
			$newuser->first_name = $name;
            $newuser->last_name = $name;
			$newuser->email = $request->get('email');
			$newuser->password = bcrypt($password);
            $newuser->user_type = 2;
            $newuser->is_verified = 1;
			$newuser->save();
            Auth::loginUsingId($newuser->id);
            $user = Auth::user();
            $user['rand_password'] = $password;
            try{
                \Mail::to($user->email)->send(new WelcomeEmail($user));

                if (\Mail::failures()) {
                    flash()->error('Unable to Send Verification Mail.');
                    $response['success'] = false;
                    return response()->json($response, 200);
                }
            }catch (\Exception $e) {
                flash()->error('Unable to Send Verification Mail.');
                $response['success'] = false;
                return response()->json($response, 200);
            }
        }else{
            Auth::loginUsingId($chkUser->id);
            $user = Auth::user();
            $current_date = Carbon::now();
            $todayDate = Carbon::parse($current_date)->format('Y-m-d h:i:s');
            $user->last_login = $todayDate;
            $user->save();
        }

        if(empty($user->customer_id)){
            $createCustomer = UserSubscription::createCustomer($user);
            if($createCustomer['status'] == 'success'){
                $user = User::where('user_id', $user->user_id)->first();
                $user->customer_id = $createCustomer['data']['id'];
                $user->save();
            }else{
                $getCustomer = UserSubscription::getCustomerByEmail($user->email);
                if($getCustomer['status'] == 'success'){
                    $user = User::where('user_id', $user->user_id)->first();
                    $user->customer_id = $getCustomer['data']['id'];
                    $user->save();
                }
            }
        }else{
            $getCustomer = UserSubscription::getCustomerByEmail($user->email);
            if($getCustomer['status'] == 'success'){
                $user = User::where('user_id', $user->user_id)->first();
                $user->customer_id = $getCustomer['data']['id'];
                $user->save();
            }else{
                $createCustomer = UserSubscription::createCustomer($user);
                if($createCustomer['status'] == 'success'){
                    $user = User::where('user_id', $user->user_id)->first();
                    $user->customer_id = $createCustomer['data']['id'];
                    $user->save();
                }
            }
        }

        $product_id = env('PUBBLY_PRODUCT_ID');
        $plan_id = $request->plan_id;
        
        $curl2 = curl_init();
  
        curl_setopt_array($curl2, array(
            CURLOPT_URL => 'https://payments.pabbly.com/api/v1/checkoutpage/' . $product_id,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
            ),
    
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_USERPWD => env('PUBBLY_API_KEY') . ':' . env('PUBBLY_STRIPE_KEY'),
        ));
        $response = curl_exec($curl2);
    
        curl_close($curl2);
        $response = json_decode($response, true);
  
        if ($response['status'] == 'success') {
            $data = array_filter($response['data'], function ($element) use ($plan_id) {
            return $element['id'] == $plan_id;
            });
        }
        $mac_id = $request->mac_id;
        $mac_key = $request->mac_key;
        Session::put('mac_id', $mac_id);
        Session::put('mac_key', $mac_key);
        Session::put('flag', 3);
        $data = array_values($data);
        return response()->json(['success' => 1, 'data' => $data, 'customer_id' => $user->customer_id]);
    }

    public function addDevice()
    {
        
        $device = new Device;
        $device->user_id = Auth::user()->user_id;
        $device->device_title = $user->name.' Device';
        $device->mac_id = $request->mac_id;
        $device->mac_key = $request->mac_key;
        $device->note = "";
        $device->save();

        $deviceCode = new DeviceCode;
        $code =  $deviceCode->generate_code();
        $deviceCode->device_id = $device->id;
        $deviceCode->code = $code;
        $deviceCode->user_id = Auth::user()->user_id;
        $deviceCode->save();
    

    }
}