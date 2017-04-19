<?php

namespace App\Entities;

class Company extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'company';
    protected $primaryKey = 'company_id';

    public function users()
    {
        return $this->belongsToMany('App\Entities\User', 'company_user', 'company_id', 'user_id')
            ->withPivot('role');
    }

    public function product()
    {
        return $this->belongsTo('App\Entities\Product');
    }
}
