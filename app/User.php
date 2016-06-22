<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Model implements JWTSubject, AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The primary key used by the model.
     *
     * @var string
     */
    protected $primaryKey = 'user_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['provider_id', 'name', 'email', 'phone', 'password', 'provider', 'avatar', 'confirm_code', 'active', 'role'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['provider_id', 'password', 'confirm_code', 'remember_token','summary', 'designation']; 

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }


    public function profile()
    {
        return $this->hasOne('App\Profile');
    }

    public function skills()
    {
        return $this->belongsToMany('App\Skill');
    }

    public function locations()
    {
        return $this->hasMany('App\Location');
    }

    public function reviews()
    {
        return $this->hasMany('App\Comment');
    }

    public function history()
    {
        return $this->hasMany('App\History');
    }

    public function scopeAdmins($query)
    {
        return $query->where('role', 'Admin'); 
    }

    public function scopeMembers($query)
    {
        return $query->where('role', 'Member'); 
    }

    public function scopeMerchants($query, $provider)
    {
        return $query->where('role', 'Merchant')->where('provider', $provider->name); 
    }

    public function scopeProviders($query)
    {
        return $query->where('role', 'Provider'); 
    }

    public function scopeActive($query)
    {
        return $query->where('active', 1); 
    }
}
