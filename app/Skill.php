<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    /**
     * The primary key used by the model.
     *
     * @var string
     */
    protected $primaryKey = 'skill_id';

    public function user()
    {
        return $this->belongsTo('App\User', 'added_by', 'user_id');
    }

    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }

    public function scopeProposed($query)
    {
        return $query->where('active', 0);
    }

    public function scopeUsed($query)
    {
        return $query->join('skill_user', 'skill_user.skill_id', '=', 'skills.skill_id')
                     ->groupBy('skill_user.skill_id');
    }
}
