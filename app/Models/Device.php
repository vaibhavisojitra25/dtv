<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Device extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'devices';
    protected $primaryKey = 'id';

    public function device_code()
    {
        return $this->hasOne('App\Models\DeviceCode', 'device_id', 'id');
    }

    public function user()
    {
        return $this->hasOne('App\Models\User', 'user_id', 'user_id');
    }
    
}
