<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'activities';

    /**
     * The primary key used by the model.
     *
     * @var string
     */
    protected $primaryKey = 'activity_id';
}
