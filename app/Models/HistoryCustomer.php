<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class HistoryCustomer extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'customer_id', 'user_status', 'total_usage', 'allocated_quota', 'created_at', 'updated_at' 
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        
    ];


    public function customer() {
        return $this->belongsTo('App\Models\Customer', 'customer_id', 'id');
    }

}