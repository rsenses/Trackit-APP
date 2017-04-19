<?php

namespace App\Entities;

class Place extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'place';
    protected $primaryKey = 'place_id';
    protected $dates = [
        'created_at',
        'updated_at'
    ];
    protected $fillable = [
        'name',
        'slug',
        'address',
        'city',
        'zip',
        'company_id',
        'state_id',
        'is_shareable',
    ];

    public function state()
    {
        return $this->belongsTo('App\Entities\State');
    }

    public function product()
    {
        return $this->hasOne('App\Entities\Product');
    }
}
