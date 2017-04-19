<?php

namespace App\Entities;

class Country extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'country';
    protected $primaryKey = 'country_id';
    public $timestamps = false;

    public function state()
    {
        return $this->hasOne('App\Entities\State');
    }
}
