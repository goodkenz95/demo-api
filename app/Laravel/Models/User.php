<?php

namespace App\Laravel\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

use Str;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable,SoftDeletes;

    protected $table = "users";

    protected $connection = "reader_db";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'password',
    ];
    

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];

    protected $appends = [];
    
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            'iss' => env("JWT_ISSUER"),
        ];
    }

    public function getNameAttribute(){
        return Str::title("{$this->firstname} {$this->lastname} ".(strlen($this->suffix) > 0 ? Str::title($this->suffix): NULL));
    }
}
