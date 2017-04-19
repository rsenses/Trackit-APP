<?php

namespace App\Entities;

class Customer extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'customer';
    protected $primaryKey = 'customer_id';
    protected $fillable = [
        'first_name',
        'last_name',
        'email'
    ];

    public function products()
    {
        return $this->belongsToMany('App\Entities\Product', 'registration', 'customer_id', 'product_id')
            ->withPivot('unique_id', 'product_user_id', 'verified_time', 'created_at');
    }

    public function registration()
    {
        return $this->hasOne('App\Entities\Registration');
    }

    public function metas()
    {
        return $this->hasMany('App\Entities\CustomerMeta');
    }

    public function fullName()
    {
        return $this->first_name.' '.$this->last_name;
    }
}
