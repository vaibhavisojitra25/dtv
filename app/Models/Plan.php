<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plan extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'plan_id',
        'plan_name',
        'duration',
        'term',
        'short_description',
        'description',
        'code_limit',
        'is_free',
        'amount',
        'expire_hour'
    ];
    public static function get_random_string($field_code='user_id')
    {
          $random_unique  =  sprintf('%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));

          $plan = Plan::where('plan_id', '=', $random_unique)->first();
          if ($plan != null) {
              $this->get_random_string();
          }
          return $random_unique;
      }
}
