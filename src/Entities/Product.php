<?php

namespace App\Entities;

class Product extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'product';
    protected $primaryKey = 'product_id';
    protected $dates = [
        'date_start',
        'date_end',
        'created_at',
        'updated_at'
    ];
    protected $fillable = [
        'name',
        'slug',
        'image',
        'slider',
        'description',
        'date_start',
        'date_end',
        'capacity',
        'place_id',
        'company_id',
        'url_legal',
    ];

    public function place()
    {
        return $this->belongsTo('App\Entities\Place');
    }

    public function company()
    {
        return $this->hasOne('App\Entities\Company');
    }

    public function users()
    {
        return $this->belongsToMany('App\Entities\User', 'product_user', 'product_id', 'user_id')
            ->withPivot('product_user_id', 'room_id', 'date_start', 'date_end');
    }

    public function registrations()
    {
        return $this->hasMany('App\Entities\Registration', 'product_id');
    }

    public function customers()
    {
        return $this->belongsToMany('App\Entities\Customer', 'registration', 'product_id', 'customer_id')
            ->withPivot('unique_id', 'created_at')
            ->where('registration.is_authorized', 1);
    }

    public function cancelledCustomers()
    {
        return $this->belongsToMany('App\Entities\Customer', 'registration')
            ->withPivot('unique_id', 'product_user_id', 'verified_time', 'created_at')
            ->where('registration.is_authorized', 0);
    }

    public function rooms()
    {
        return $this->hasMany('App\Entities\Room');
    }

    public function logs()
    {
        return $this->hasMany('App\Entities\Log');
    }
}
