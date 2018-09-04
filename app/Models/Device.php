<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Device extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id','device_id','device_name', 'customer_id', 'added_on'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        
    ];


    public function history() {
        return $this->hasMany('App\Models\HistoryDevice', 'device_id', 'id');
        //return $this->belongsToMany('\App\Models\Device', 'device_id', 'id');
    }

    
}