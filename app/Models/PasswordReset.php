<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class PasswordReset extends Authenticatable
{
	protected $table = 'password_resets';
	public $primaryKey = 'email';
	public $timestamps = false;
}
?>