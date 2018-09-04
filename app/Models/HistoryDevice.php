<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class HistoryDevice extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id','device_id','system_settings_backed_up', 'last_backup_status', 'backup_start_time', 'backup_end_time', 'bytes_transferred', 'first_backup_status', 'first_backup_size', 'time_taken', 'created_at', 'updated_at' 
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        
    ];
}