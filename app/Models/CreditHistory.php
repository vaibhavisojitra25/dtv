<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CreditHistory extends Model
{
    use SoftDeletes;

    protected $table = 'credit_history';
    protected $primaryKey = 'id';

    public function device_code()
    {
        return $this->hasOne('App\Models\DeviceCode', 'device_id', 'device_id')->where('status',1)->latest();
    }

    public function added_by_user()
    {
        return $this->hasOne('App\Models\User', 'user_id', 'added_by')->latest();
    }

    public function credited_to_user()
    {
        return $this->hasOne('App\Models\User', 'user_id', 'credited_to')->latest();
    }
}
