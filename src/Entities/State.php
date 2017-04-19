<?php

namespace App\Entities;

class State extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'state';
    protected $primaryKey = 'state_id';

    public function country()
    {
        return $this->belongsTo('App\Entities\Country');
    }

    public function place()
    {
        return $this->hasOne('App\Entities\Place');
    }
}
