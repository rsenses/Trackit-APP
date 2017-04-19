<?php

namespace App\Entities;

class Verification extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'verification';
    protected $primaryKey = 'verification_id';

    protected $fillable = [
        'registration_id',
        'user_id',
    ];

    public function registration()
    {
        return $this->belongsTo('App\Entities\Registration');
    }

    public function user()
    {
        return $this->belongsTo('App\Entities\User');
    }
}
