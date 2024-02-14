<?php

namespace Database\Seeders;

use App\Models\MailSetting;
use Illuminate\Database\Seeder;

class EmailSettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $mailSetting = MailSetting::find(1);
        if ($mailSetting) {
            $mailSetting->update([
                'mail_driver' => 'smtp',
                'mail_host' => 'smtp.mailnest.io',
                'mail_port' => '2525',
                'mail_username' => 'czlrc9ldsfx9@mailnest.io',
                'mail_password' => 'gjyib99ekjpz8ta5',
                'mail_encryption' => 'tls',
                'mail_from_address' => 'info@mailnest.io',
                'mail_from_name' => 'Purple Iptv'
            ]);
        } else {
            MailSetting::create([
                'mail_driver' => 'smtp',
                'mail_host' => 'smtp.mailnest.io',
                'mail_port' => '2525',
                'mail_username' => 'czlrc9ldsfx9@mailnest.io',
                'mail_password' => 'gjyib99ekjpz8ta5',
                'mail_encryption' => 'tls',
                'mail_from_address' => 'info@mailnest.io',
                'mail_from_name' => 'Purple Iptv'
            ]);
        }
    }
}
