<?php

namespace Database\Seeders;

use App\Models\CaptchaSetting;
use Illuminate\Database\Seeder;

class CaptchaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $captchaSetting = CaptchaSetting::find(1);
        if ($captchaSetting) {
            $captchaSetting->update(['captcha_Key' => '6LdWtJkhAAAAAPfe0RJ4gKSEKPJ1KMRBXq4cdRfK',
            'captcha_secret' => '6LdWtJkhAAAAAIqukP-gdaXaB0RCthBNOjza2SVd']);
        } else {
            CaptchaSetting::create(['captcha_Key' => '6LdWtJkhAAAAAPfe0RJ4gKSEKPJ1KMRBXq4cdRfK',
            'captcha_secret' => '6LdWtJkhAAAAAIqukP-gdaXaB0RCthBNOjza2SVd']);
        }
    }
}
