<?php

namespace App\Entities;

class CustomerMeta extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'customer_meta';
    protected $primaryKey = 'customer_meta_id';

    public function customer()
    {
        return $this->belongsTo('App\Entities\Customer');
    }
}
