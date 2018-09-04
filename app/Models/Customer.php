<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Customer extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id','user_id','user_name', 'email_id', 'profile', 'added_on', 'storage', 'plan', 'company_id', 'date_subscribed','date_actived' 
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    
    public function company() {
        return $this->belongsTo('App\Models\Company', 'company_id', 'id');
    }

    
    public function devices() {
         return $this->hasMany('App\Models\Device', 'customer_id', 'id');        
    }

    public static function history($date_from, $date_to, $company_id) {
        $report = Customer::select( 'history_customers.customer_id as customer_id ',
                'customers.email_id as email_id',    
                'history_customers.created_at as his_cus_created_at',
                'devices.id as device_id',
                'history_devices.last_backup_status as last_backup_status',
                'history_devices.backup_end_time as backup_end_time',
                'history_devices.created_at as his_dev_created_at')
            ->join('history_customers', function ($join) {
                $join->on('history_customers.customer_id', '=', 'customers.id');
            })
            ->leftjoin('devices', function ($join) {
                $join->on('devices.customer_id', '=', 'customers.id');
            })
            ->leftjoin('history_devices', function ($join) use ($date_from, $date_to){
                $join->on('history_devices.device_id', '=', 'devices.id')
                ;                                    
            })                                
            ->whereRaw("DATE(history_customers.created_at)>='".$date_from."'" )
            ->whereRaw("DATE(history_customers.created_at)<='".$date_to."'" )
            ->whereRaw("(DATE(history_devices.created_at)= DATE(history_customers.created_at) OR  history_devices.created_at IS NULL)"   )
            ->orderBy('history_customers.customer_id')
            ->orderBy('history_devices.created_at', 'desc')
            ;
        if ($company_id) {
           $report = $report->where('customers.company_id','=', '$company_id'); 
        }

        return $report->get();
    }


    /*public function squads() {
        return $this->belongsToMany('\App\Models\Squad', 'squads_students', 'student_id', 'squad_id')
                        ->withPivot('student_id','squad_id');
    }*/


}