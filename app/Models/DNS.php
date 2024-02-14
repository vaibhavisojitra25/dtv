<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DNS extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'dns';
    protected $primaryKey = 'id';

    public function user()
    {
        return $this->hasOne('App\Models\User', 'user_id', 'user_id');
    }
}
