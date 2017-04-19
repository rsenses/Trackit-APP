<?php

namespace App\Entities;

class Log extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'log';
    protected $primaryKey = 'log_id';
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo('App\Entities\User');
    }

    public function product()
    {
        return $this->belongsTo('App\Entities\Product');
    }
}
