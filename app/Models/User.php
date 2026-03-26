<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    protected $table = 'user';
    public $timestamps = false;
    protected $fillable = [
        'group_id',
        'admin',
        'name',
        'cedula',
        'email',
        'pass'
    ];

    protected $hidden = [
        'pass',
    ];
    // JWT

    public function getAuthPassword()
    {
        return $this->pass;
    }

    // --- Métodos Requeridos por JWT ---
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
