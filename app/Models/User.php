<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Input;

class User extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'rut', 'dv','name', 'surname', 'second_surname','email', 'password', 'status', 'company_id', 'profile_id' 
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token'
    ];

    public function company() {
        return $this->belongsTo('App\Models\Company', 'company_id', 'id');
    }

    public function profile() {
        return $this->belongsTo('App\Models\Profile', 'profile_id', 'id');
    }
}