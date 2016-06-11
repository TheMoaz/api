<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class User extends Model implements AuthenticatableContract, AuthorizableContract
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
    protected $fillable = ['provider_id', 'name', 'email', 'phone', 'password', 'provider', 'avatar', 'confirm_code', 'active'];

    protected $hidden = ['provider_id', 'provider', 'role','password', 'active', 'confirm_code', 'remember_token','summary']; 

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

    public function scopeActive($query)
    {
        return $query->where('active', 1); 
    }
}
