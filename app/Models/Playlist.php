<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Playlist extends Model
{
    use SoftDeletes;

    protected $table = 'playlists';
    protected $primaryKey = 'id';

    public function user()
    {
        return $this->hasOne('App\Models\User', 'user_id', 'user_id');
    }

    public function dns_url()
    {
        return $this->hasOne('App\Models\DNS', 'id', 'dns_id');
    }
}
