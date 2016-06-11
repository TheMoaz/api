<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    /**
     * The primary key used by the model.
     *
     * @var string
     */
    protected $primaryKey = 'report_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'worker_id', 'reason', 'comment'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function worker()
    {
        return $this->belongsTo('App\Worker');
    }
}
