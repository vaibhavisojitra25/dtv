<?php

namespace App\Http\Controllers;

use App\Models\CaptchaSetting;
use App\Models\MailSetting;
use App\Models\Setting;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;

class SystemSettingController extends Controller
{
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function systemSetting(Request $request)
    {
        $siteSetting = SiteSetting::where("id", '1')->first();
        $mailSetting = MailSetting::where("id", '1')->first();
        $captchaSetting = CaptchaSetting::where("id", '1')->first();
        $breadcrumbs = [['link' => "/", 'name' => "Home"], ['name' => "System Setting"], ['name' => "Edit"]];
        if(Auth::user()->user_type == 1){
        return view('/content/system-setting/system-setting', ['breadcrumbs' => $breadcrumbs, 'siteSetting' => $siteSetting, 'mailSetting' => $mailSetting, 'captchaSetting' => $captchaSetting, 'tabName' =>(!empty($request->session()->get('tabName')) ? $request->session()->get('tabName') : '')]);
        }else{
            return redirect()->to('/dashboard');
        }
    }

    public function updateSiteSetting(Request $request)
    {
        if(Auth::user()->user_type != 1){
            return redirect()->to('/dashboard');
        }
        // // $validator = Validator::make($request->all(), [
        // //     'title_text' => 'required',
        // //     'footer_text' => 'required',
        // // ]);
        // if ($validator->fails()) {
        //     flash()->error($validator->errors()->first());
        //     return redirect()->back();
        // }
        $site_setting = SiteSetting::first();
        if(empty($site_setting)){
            // $validator = Validator::make($request->all(), [
            //     'logo_dark' => 'required',
            //     'logo_light' => 'required',
            //     'favicon' => 'required',
            //     'title_text' => 'required',
            //     'footer_text' => 'required',
            // ]);
            // if ($validator->fails()) {
            //     flash()->error($validator->errors()->first());
            //     return redirect()->back();
            // }
            $site_setting = New SiteSetting;
        }
        if ($request->file('logo_dark')) {
            $file      = $request->file('logo_dark');
            $imageFileName = rand(111, 999) . '.' . $file->getClientOriginalExtension();
            $destinationPath = 'images/logo' . '/';
            File::makeDirectory($destinationPath, $mode = 0777, true, true);
            $file->move($destinationPath, $imageFileName);
            $site_setting->logo_dark = $imageFileName;
        }
        if ($request->file('logo_light')) {
            $file      = $request->file('logo_light');
            $imageFileName = rand(111, 999) . '.' . $file->getClientOriginalExtension();
            $destinationPath = 'images/logo' . '/';
            File::makeDirectory($destinationPath, $mode = 0777, true, true);
            $file->move($destinationPath, $imageFileName);
            $site_setting->logo_light = $imageFileName;
        }
        if ($request->file('favicon')) {
            $file      = $request->file('favicon');
            $imageFileName = rand(111, 999) . '.' . $file->getClientOriginalExtension();
            $destinationPath = 'images/logo' . '/';
            File::makeDirectory($destinationPath, $mode = 0777, true, true);
            $file->move($destinationPath, $imageFileName);
            $site_setting->favicon = $imageFileName;
        }
        $site_setting->title_text = $request->title_text;
        $site_setting->footer_text = $request->footer_text;
        $site_setting->help_url = $request->help_url;
        if($request->is_signup){
            $site_setting->is_signup =$request->is_signup;
        }
        if($request->is_activation){
            $site_setting->is_activation =$request->is_activation;
        }
        $site_setting->save();
        flash()->success('Site setting saved successfully');
        return redirect()->route('systemSetting');
    }

    public function updateEmailSetting(Request $request)
    {
        if(Auth::user()->user_type != 1){
            return redirect()->to('/dashboard');
        }
        $validator = Validator::make($request->all(), [
            'mail_driver' => 'required',
            'mail_host' => 'required',
            'mail_port' => 'required',
            'mail_username' => 'required',
            'mail_password' => 'required',
            'mail_encryption' => 'required',
            'mail_from_address' => 'required',
            'mail_from_name' => 'required',
        ]);
        if ($validator->fails()) {
            flash()->error($validator->errors()->first());
            return redirect()->back()->with('tabName','email-setting');
        }

        $mail_setting = MailSetting::where("id", '1')->first();
        if(empty($mail_setting)){
            $mail_setting = new MailSetting;
        }
            $mail_setting->mail_driver = $request->mail_driver;
            $mail_setting->mail_host = $request->mail_host;
            $mail_setting->mail_port = $request->mail_port;
            $mail_setting->mail_username = $request->mail_username;
            $mail_setting->mail_password = $request->mail_password;
            $mail_setting->mail_encryption = $request->mail_encryption;
            $mail_setting->mail_from_address = $request->mail_from_address;
            $mail_setting->mail_from_name = $request->mail_from_name;
            $mail_setting->save();
            flash()->success('Email setting saved successfully');
            return redirect()->back()->with('tabName','email-setting');

    }

    public function updateCaptchaSetting(Request $request)
    {
        if(Auth::user()->user_type != 1){
            return redirect()->to('/dashboard');
        }
        $validator = Validator::make($request->all(), [
            'captcha_Key' => 'required',
            'captcha_secret' => 'required',
        ]);
        if ($validator->fails()) {
            flash()->error($validator->errors()->first());
            return redirect()->back()->with('tabName','captcha-setting');
        }
        $captcha_setting = CaptchaSetting::where("id", '1')->first();
        if(empty($captcha_setting)){
            $captcha_setting = new CaptchaSetting;
        }
            $captcha_setting->captcha_Key = $request->captcha_Key;
            $captcha_setting->captcha_secret = $request->captcha_secret;
            $captcha_setting->save();
            flash()->success('Captcha setting saved successfully');
            return redirect()->back()->with('tabName','captcha-setting');

    }

}
