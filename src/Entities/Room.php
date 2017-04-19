<?php

namespace App\Entities;

class Room extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'room';
    protected $primaryKey = 'room_id';

    public function product()
    {
        return $this->belongsTo('App\Entities\Product');
    }

    public function registrations()
    {
        return $this->belongsToMany('App\Entities\Registration', 'registration_room', 'room_id', 'registration_id')
            ->withPivot('permission');
    }
}
