<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class ReportGeneral extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'period','customers_subscribed','customers_active','customers_activo_used','customers_activo_unused','customers_unsubscribed','company_id'
    ];

    protected $table = "report_general";

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        
    ];
}