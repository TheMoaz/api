<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    //protected $table = 'locations';

    /**
     * The primary key used by the model.
     *
     * @var string
     */
    protected $primaryKey = 'location_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'type', 'latitude', 'longitude', 'address', 'city', 'country'];

    public function scopePresent($query)
    {
        return $query->where('type', 'Present');
    }

    public function scopePreferred($query)
    {
        return $query->where('type', 'Preferred');
    }
}
