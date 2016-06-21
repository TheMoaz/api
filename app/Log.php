<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'logs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['log_user', 'log_method', 'log_path', 'log_ip', 'log_status'];

    public function scopeMerchants($query, $id)
    {
        return $query->where('log_user', $id);
    }

    public function scopeMembers($query, $id)
    {
        return $query->where('log_user', $id);
    }

    public function scopeUsers($query, $id)
    {
        return $query->where('log_user', $id);
    }

    public function scopeOkays($query)
    {
        return $query->where('log_status', 200);
    }

    public function scopeErrors($query)
    {
        return $query->where('log_status', '!=', 200);
    }

    public function scopeServerError($query)
    {
        return $query->whereBetween('log_status', [500,599]);
    }

    public function scopeClientError($query)
    {
        return $query->whereBetween('log_status', [401,499]);
    }

    public function scopeAdditions($query)
    {
        return $query->where('log_path', 'like', '%add');
    }

    public function scopeEdits($query)
    {
        return $query->where('log_path', 'like', '%edit');
    }

    public function scopeLogins($query)
    {
        return $query->where('log_path', 'login');
    }
}
