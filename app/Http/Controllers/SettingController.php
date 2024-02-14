<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class SettingController extends Controller
{

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function setting()
    {
        $setting = Setting::where("id", '1')->first();
        if (Auth::user()->user_type == 1) {
            $breadcrumbs = [['link' => "/", 'name' => "Home"], ['name' => "Setting"], ['name' => "Edit"]];
            return view('/content/setting/setting', ['breadcrumbs' => $breadcrumbs, 'setting' => $setting]);
        } else {
            return redirect()->to('/dashboard');
        }
    }

    public function update_setting(Request $request)
    {
        if(Auth::user()->user_type != 1){
            return redirect()->to('/dashboard');
        }
        $validator = Validator::make($request->all(), [
            'playlist_limit' => 'required_if:flag,1',
            'device_limit' => 'required_if:flag,1',
            'admin_email' => 'required_if:flag,2',
        ]);
        if ($validator->fails()) {
            flash()->error($validator->errors()->first());
            return redirect()->back();
        } else {
            $setting = Setting::first();
            if (empty($setting)) {
                $setting = new Setting;
            }
            if ($request->flag == 1) {
                $setting->playlist_limit = $request->playlist_limit;
                $setting->device_limit = $request->device_limit;
            }
            if ($request->flag == 2) {
                $setting->admin_email = $request->admin_email;
            }

            $setting->save();
            flash()->success('Setting saved successfully');
            return redirect()->route('setting');
        }
    }
}
