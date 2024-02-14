<?php
namespace Database\Seeders;


use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new User;
        $user_id = $user->get_random_string();
        $user = User::updateOrCreate([
            'email' => 'admin@PurpleIPTV.com',
        ],[
            'user_id' => $user_id,
            'user_type' => '1',
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'admin@PurpleIPTV.com',
            'phone_no' => '9999999999',
            'password' => bcrypt('admin@123'),
            'is_verified' => 1
        ]);

    }
}
