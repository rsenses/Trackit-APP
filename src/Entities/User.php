<?php

namespace App\Entities;

class User extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'user';
    protected $primaryKey = 'user_id';
    protected $fillable = [
        'uuid',
        'email',
        'first_name',
        'last_name',
        'password',
        'phone',
        'is_active'
    ];

    public function products()
    {
        return $this->belongsToMany('App\Entities\Product', 'product_user', 'user_id', 'product_id')
            ->withPivot('product_user_id', 'room_id', 'date_start', 'date_end');
    }

    public function companies()
    {
        return $this->belongsToMany('App\Entities\Company', 'company_user', 'user_id', 'company_id')
            ->withPivot('role');
    }

    public function logs()
    {
        return $this->hasMany('App\Entities\Log');
    }

    public function verifications()
    {
        return $this->hasMany('App\Entities\Verification');
    }

    public function fullName()
    {
        return $this->first_name.' '.$this->last_name;
    }

    public function setPassword($password)
    {
        $this->update([
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ]);
    }
}
