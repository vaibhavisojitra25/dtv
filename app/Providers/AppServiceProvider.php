<?php

namespace App\Providers;

use App\Models\CaptchaSetting;
use App\Models\MailSetting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // get email view data in provider class
        if (Schema::hasTable('mail_settings')) {
                $configuration = MailSetting::where("id", 1)->first();
                if(!empty($configuration)) {
                    $config = array(
                        'driver'     =>     $configuration->mail_driver,
                        'host'       =>     $configuration->mail_host,
                        'port'       =>     $configuration->mail_port,
                        'username'   =>     $configuration->mail_username,
                        'password'   =>     $configuration->mail_password,
                        'encryption' =>     $configuration->mail_encryption,
                        'from'       =>     array('address' => $configuration->mail_from_address, 'name' => $configuration->mail_from_name),
                    );
                    Config::set('mail', $config);
                }
            }
            if (Schema::hasTable('captcha_settings')) {
                $captcha = CaptchaSetting::where('id', 1)->first();
                if(!is_null($captcha)) {
                    $config1 = array(
                        'secret'     =>     $captcha->captcha_secret,
                        'sitekey'    =>     $captcha->captcha_Key,
                        'options' => [
                            'timeout' => 30,
                        ],
                    );
                    Config::set('captcha', $config1);
                }
            }
    }
}
