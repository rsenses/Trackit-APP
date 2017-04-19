<?php

namespace App\Entities;

class Registration extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'registration';
    protected $primaryKey = 'registration_id';

    public function customer()
    {
        return $this->belongsTo('App\Entities\Customer');
    }

    public function product()
    {
        return $this->belongsTo('App\Entities\Product', 'registration_id');
    }

    public function rooms()
    {
        return $this->belongsToMany('App\Entities\Room', 'registration_room', 'registration_id', 'room_id')
            ->withPivot('permission');
    }

    public function verifications()
    {
        return $this->hasMany('App\Entities\Verification');
    }
}
