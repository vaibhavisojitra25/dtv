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
use App\Models\VerifyUser;
use App\Models\PasswordReset;
use Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\ForgotPassword;
use App\Mail\VerifyEmail;
use App\Models\SiteSetting;
use Carbon\Carbon;
use DB;
use Session;

class AuthenticationController extends Controller
{

    public function showLogin()
    {
        $pageConfigs = ['blankPage' => true];
        $siteSetting = SiteSetting::where('id', 1)->first();
        return view('/content/authentication/auth-login', ['pageConfigs' => $pageConfigs, 'siteSetting' => $siteSetting]);
    }

    public function doLogin(Requests\LoginRequest $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
            // 'g-recaptcha-response' => 'required|captcha',
        ]);
        if ($validator->fails()) {
            flash()->error($validator->errors()->first());
            return redirect()->back();
        } else {
            if (User::login($request)) {

                $user = Auth::user();
                if ($user->status == 1) {
                    if ($user->is_verified == 1) {
                        $current_date = Carbon::now();
                        $todayDate = Carbon::parse($current_date)->format('Y-m-d h:i:s');
                        $user->last_login = $todayDate;
                        if(empty($user->trial_expire)){
                            $trialExpire = Carbon::parse($current_date)->addHours(24)->format('Y-m-d h:i:s');
                            $user->trial_expire = $trialExpire;
                        }
                        $user->save();
                        if($user->user_type != 1){
                            if(empty($user->customer_id)){
                                $createCustomer = UserSubscription::createCustomer($user);
                                if($createCustomer['status'] == 'success'){
                                    $user = User::where('user_id', $user->user_id)->first();
                                    $user->customer_id = $createCustomer['data']['id'];
                                    $user->save();
                                    UserSubscription::where('user_id', $user->user_id)->update(['customer_id'=>$createCustomer['data']['id']]);
                                }else{
                                    $getCustomer = UserSubscription::getCustomerByEmail($user->email);
                                    if($getCustomer['status'] == 'success'){
                                        $user = User::where('user_id', $user->user_id)->first();
                                        $user->customer_id = $getCustomer['data']['id'];
                                        $user->save();
                                        UserSubscription::where('user_id', $user->user_id)->update(['customer_id'=>$getCustomer['data']['id']]);
                                    }
                                }
                            }else{
                                $getCustomer = UserSubscription::getCustomerByEmail($user->email);
                                if($getCustomer['status'] == 'success'){
                                    $user = User::where('user_id', $user->user_id)->first();
                                    $user->customer_id = $getCustomer['data']['id'];
                                    $user->save();
                                    UserSubscription::where('user_id', $user->user_id)->update(['customer_id'=>$getCustomer['data']['id']]);
                                }else{
                                    $createCustomer = UserSubscription::createCustomer($user);
                                    if($createCustomer['status'] == 'success'){
                                        $user = User::where('user_id', $user->user_id)->first();
                                        $user->customer_id = $createCustomer['data']['id'];
                                        $user->save();
                                        UserSubscription::where('user_id', $user->user_id)->update(['customer_id'=>$createCustomer['data']['id']]);
                                    }
                                }
                            }
                        }
                        // flash()->success('Welcome to Purple IPTV');
                        return redirect()->to('/dashboard');
                    } else {
                        Auth::logout();
                        flash()->error('You need to confirm your account. We have sent you an activation code, please check your email.');
                        return redirect()->back();
                    }
                } else {
                    Auth::logout();
                    flash()->error('Account Inactive');
                    return redirect()->back();
                }
            } else {
                flash()->error('Invalid Login Credentials');
                return redirect()->back();
            }
        }
    }

    public function showSignup()
    {
        $pageConfigs = ['blankPage' => true];
        $siteSetting = SiteSetting::where('id', 1)->first();
        return view('/content/authentication/auth-register', ['pageConfigs' => $pageConfigs, 'siteSetting' => $siteSetting]);
    }

    public function doSignup(Requests\LoginRequest $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => ['required', Rule::unique('users')->whereNull('deleted_at')],
            'password' => 'required',
            'g-recaptcha-response' => 'required|captcha',
        ]);
        if ($validator->fails()) {
            flash()->error($validator->errors()->first());
            return redirect()->back();
        } else {

            $user = new User;
            $user_id = $user->get_random_string();
            $user->user_id = $user_id;
			$user->first_name = $request->get('first_name');
            $user->last_name = $request->get('last_name');
			$user->email = $request->get('email');
			$user->password = bcrypt($request->get('password'));
            $user->user_type = 2;
			$user->save();

            if ($user) {
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

                $verifyUser = VerifyUser::create([
                    'user_id' => $user->user_id,
                    'token' => sha1(time())
                ]);
                $user['verifyUser'] = $verifyUser;
                try{
                    \Mail::to($user->email)->send(new VerifyEmail($user,1));

                    if (\Mail::failures()) {
                        flash()->error('Unable to Send Verification Mail.');
                        return redirect()->back();
                    }
                }catch (\Exception $e) {
                    flash()->error('ccessfully registered But Unable to Send Verify Mail');
                    return redirect()->to('/login');
                }
				flash()->success('Successfully registered. You need to confirm your account.We have sent you an activation email. please check your email.');
                return redirect()->to('/login');
            } else {
                flash()->error('Error while registered');
                return redirect()->back();
            }
        }
    }

    public function verifyEmail(Request $request,$token)
    {
        $verifyUser = VerifyUser::where('token', $token)->first();
        if (!empty($verifyUser)) {

            $user = User::where('user_id',$verifyUser->user_id)->first();
            if($user->is_verified == 0) {
                $user->is_verified = 1;
                $user->save();

				flash()->success('Your Email Activate Successfully. Please Login');
                return redirect()->to('/login');
            } else {
                flash()->error('Your Email Already Activate. Please Login');
			    return redirect()->to('/login');
            }
        } else {
            flash()->error('You have no permission to access this link');
			return redirect()->to('/login');
        }
    }

    public function doLogout()
    {
        Auth::logout();
        Session::flush();
        return redirect()->to('/login');
    }

    public function showForgotPassword()
    {
        $pageConfigs = ['blankPage' => true];
        $siteSetting = SiteSetting::where('id', 1)->first();
        return view('/content/authentication/forgot-password', ['pageConfigs' => $pageConfigs, 'siteSetting' => $siteSetting]);
    }

    public function sendResetLink(Request $request)
    {

        $user = User::where('email',$request->get('email'))->first();
        if($user){
            PasswordReset::where('email', $request->get('email'))->delete();
            $passwordReset = new PasswordReset;
            $passwordReset->email = $request->get('email');
            $passwordReset->token = sha1(time());
            $passwordReset->created_at = Carbon::now();
            $passwordReset->save();
            try{
               \Mail::to($request->get('email'))->send(new ForgotPassword($user, $passwordReset));

                if (\Mail::failures()) {
                    flash()->error('Unable to Send Verification Mail.');
                    return redirect()->back();
                }
            }catch (\Exception $e) {
                flash()->error('Unable to Send Verification Mail');
                 return redirect()->to('/login');
            }
            flash()->success('Password Reset link has been sent to  User Email id');
                return redirect()->to('/login');
        }else{
            flash()->error('No User Is asoociated with this account');
                return redirect()->back();
        }

    }

    public function showResetPassword(Request $request,$token)
    {
        $checkToken = PasswordReset::where('token', $token)->first();
        if (!empty($checkToken)) {
                $pageConfigs = ['blankPage' => true];
                return view('/content/authentication/reset-password', ['pageConfigs' => $pageConfigs]);
        } else {
            flash()->error('Invalid Link. Please try again');
			return redirect()->to('/login');
        }
    }

    // public function showResetPassword(Request $request,$token)
    // {
    //     $checkToken = PasswordReset::where('token', $token)->first();

    //     if (!empty($checkToken)) {
    //         $user = User::where('email',$checkToken->email)->first();
    //         if($user) {
    //             $pageConfigs = ['blankPage' => true];
    //             return view('/content/authentication/reset-password', ['pageConfigs' => $pageConfigs,'user' => $user]);
    //         } else {
    //             flash()->error('Invalid Link. Please try again');
	// 		    return redirect()->to('/login');
    //         }
    //     } else {
    //         flash()->error('You Already Reset Password Using This Link. Please try again');
	// 		return redirect()->to('/login');
    //     }
    // }

    public function resetPassword(Request $request)
    {
        $token =  $request->input('token');
        $email =  $request->email;
		$new_password = $request->input('new_password');

		$result = User::where('email', $email)->update(['password' => bcrypt($new_password)]);
		if ($result) {
            $user = User::where('email', $email)->first();
            PasswordReset::where('email', $user->email)->delete();
			flash()->success('Password Reset Successfully. Please Login');
            return redirect()->to('/login');
		} else {
			flash()->error('Error While Reset Password');
            return redirect()->to('/login');
		}
    }

    public function showProfile()
    {
        $breadcrumbs = [['link' => "/", 'name' => "Home"], ['name' => "My Profile"]];
        return view('/content/user/account-settings', ['breadcrumbs' => $breadcrumbs]);
    }

    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'profile_picture' => 'mimes:jpg,jpeg,png',
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => ['required', Rule::unique('users')->ignore(Auth::user()->id)->whereNull('deleted_at')],
        ]);
        if ($validator->fails()) {
            flash()->error($validator->errors()->first());
            return redirect()->back();
        } else {
            $user = User::findOrFail(Auth::user()->id);
            // if (!empty($request->current_password) && !empty($request->new_password)) {
            //     if (Hash::check($request->current_password, $user->password)) {
            //         $user->password = bcrypt($request->new_password);
            //     } else {
            //         flash()->error('Enter valid password.');
            //         return redirect()->back();
            //     }
            // }
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->phone_no = $request->phone_no;
            $user->email = $request->email;
            if ($request->hasFile('profile_picture')) {
                $profilePicture = $request->file('profile_picture');
                $name = Str::uuid() . ".png";
                $profilePicture->move(public_path('uploads/profile_pictures'), $name);
                $user->profile_picture = $name;
            }
            $user->save();
            flash()->success('Profile updated');
            return redirect()->route('my-profile');
        }
    }

    // Account Settings security
    public function account_change_password()
    {
        $breadcrumbs = [['link' => "/", 'name' => "Home"],['name' => "Change Password"]];
        return view('/content/user/account-settings-security', ['breadcrumbs' => $breadcrumbs]);
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => [
                'required','different:current_password'
            ]
        ]);
        if ($validator->fails()) {
            flash()->error($validator->errors()->first());
            return redirect()->back();
        } else {
            $user = User::findOrFail(Auth::user()->id);

            if (!empty($request->current_password) && !empty($request->new_password)) {
                if (Hash::check($request->current_password, $user->password)) {
                    $user->password = bcrypt($request->new_password);
                } else {
                    flash()->error('Enter valid current password.');
                    return redirect()->back();
                }
            }
            $user->save();
            flash()->success('Password Changed Successfully');
            return redirect()->route('my-profile');
        }
    }

    // Account Settings billing
    public function account_settings_billing()
    {
        $userCustomerData = UserSubscription::where('user_id',Auth::user()->user_id)->latest()->first();

        $userUpcomingSubscription = $userCurrnetPlan  = [];
        if($userCustomerData){
            $response3 = UserSubscription::getSubscriptionByCustomerID($userCustomerData->customer_id,$status="");
            foreach($response3['data'] as $value){
                if($value['status'] == 'live' || $value['status'] == 'trial'){
                    $userCurrnetPlan = $value;
                }
            }


            $userUpcomingSubscription = UserSubscription::getScheduledSubscriptionByID($userCustomerData->subscription_id);
            // echo "<pre>";print_r($userUpcomingSubscription);die;
        }
        // echo "<pre>";print_r($userUpcomingSubscription);die;
        $totalDevice = Device::where('user_id',Auth::user()->user_id)->count();
        $remain_device = 1;
        $trial_remain_device = 0;
        if($userCurrnetPlan){
            $userSubscription = UserSubscription::where('user_id',Auth::user()->user_id)->where('plan_id',$userCurrnetPlan['plan']['id'])->first();
            $userCurrnetPlan['subscription_id'] = $userSubscription->subscription_id;
            $userCurrnetPlan['device_limit'] = $userSubscription->device_limit;
            $userCurrnetPlan['trial_device_limit'] = $userSubscription->trial_device_limit;
            $remain_device = $userCurrnetPlan['remaining_device_limit'] = $userSubscription->remaining_device_limit;
            $trial_remain_device = $userCurrnetPlan['trial_remain_device_limit'] = $userSubscription->trial_remaining_device_limit;
        }else{
            if($totalDevice > 0){
                $remain_device = 0;
                $trial_remain_device = 0;
            }
        }

        $breadcrumbs = [['link' => "/", 'name' => "Home"], ['link' => "javascript:void(0)", 'name' => "Account Settings"], ['name' => "Billing & Plans"]];
        return view('/content/user/account-settings-billing', ['breadcrumbs' => $breadcrumbs,'userSubscription' => $userCurrnetPlan,'userUpcomingSubscription' => $userUpcomingSubscription,'remain_device' => $remain_device, 'trial_remain_device' => $trial_remain_device]);
    }

}
