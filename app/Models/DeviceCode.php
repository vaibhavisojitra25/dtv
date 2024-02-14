<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DeviceCode extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'device_code';
    protected $primaryKey = 'id';

    public static function generate_code($field_code='code')
    {
        $random_unique  =  rand(1111111111,9999999999);

        $device = DeviceCode::where('code', '=', $random_unique)->first();
        if ($device != null) {
            $this->generate_code();
        }
        return $random_unique;
    }

}
