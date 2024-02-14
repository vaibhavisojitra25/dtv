<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;
use App\Models\SiteSetting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $setting = Setting::find(1);
        if ($setting) {
            $setting->update( [
                'playlist_limit' => '20',
                'admin_email' => 'admin@PurpleIPTV.com',
            ]);
        } else {
            Setting::create( [
                'playlist_limit' => '20',
                'admin_email' => 'admin@PurpleIPTV.com',
            ]);
        }

        $SiteSetting = SiteSetting::find(1);
        if ($SiteSetting) {
            $SiteSetting->update( [
                'help_url' => 'https://portal.flowlu.com/hc/5',
            ]);
        } else {
            SiteSetting::create( [
                'help_url' => 'https://portal.flowlu.com/hc/5',
            ]);
        }
    }
}