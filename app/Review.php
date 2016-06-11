<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    /**
     * The primary key used by the model.
     *
     * @var string
     */
    protected $primaryKey = 'review_id';

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'worker_id', 'rating', 'review'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function worker()
    {
        return $this->belongsTo('App\User');
    }
}
