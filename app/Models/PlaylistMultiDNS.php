<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlaylistMultiDNS extends Model
{
    use SoftDeletes;

    protected $table = 'playlists_multi_dns';
    protected $primaryKey = 'id';

    public static function get_random_string($field_code='unique_id')
    {
          $random_unique  =  sprintf('%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));

          $playlist = PlaylistMultiDNS::where('unique_id', '=', $random_unique)->first();
          if ($playlist != null) {
              $this->get_random_string();
          }
          return $random_unique;
    }

    public function user()
    {
        return $this->hasOne('App\Models\User', 'user_id', 'user_id');
    }
}
