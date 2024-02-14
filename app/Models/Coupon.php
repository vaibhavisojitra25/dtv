<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use HasFactory, SoftDeletes;
    public static function get_random_string()
    {
          $random_unique  =  sprintf('%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));

          $code = Coupon::where('code', '=', $random_unique)->first();
          if ($code != null) {
              $this->get_random_string();
          }
          return $random_unique;
      }

      public function is_used($id)
    {
        $count=Coupon::where('parent_id', $id)->where('is_used', 1)->count();
        return $count;
    }

}
