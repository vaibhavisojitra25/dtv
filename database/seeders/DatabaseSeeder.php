<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            SettingSeeder::class,
            UsersTableSeeder::class,
            CaptchaTableSeeder::class,
            EmailSettingTableSeeder::class,
        ]);
    }
}
