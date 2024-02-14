<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DevicePlaylist extends Model
{
    use SoftDeletes;

    protected $table = 'device_playlist';
    protected $primaryKey = 'id';
    protected $fillable = ['code'];

    public function playlist()
    {
        return $this->hasOne('App\Models\Playlist', 'id', 'playlist_id')->where('status',1);
    }

    public function multiplaylist()
    {
        return $this->hasOne('App\Models\PlaylistMultiDNS', 'unique_id', 'playlist_id')->where('status',1);
    }

    public function device()
    {
        return $this->hasOne('App\Models\Device', 'id', 'device_id');
    }
}
