<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Report extends Model
{
    use HasFactory;
    
    protected $table = 'hub_reports';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'hub_id',
        'user_id',
        'inbound',
        'outbound',
        'delivered',
        'backlogs',
        'failed',
        'misroutes',
        'date',
        'sdod',
        'failed_rate',
        'success_rate',
    ];

    /**
     * Get the user that created this report.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the hub this report belongs to.
     */
    public function hub()
    {
        return $this->belongsTo(Hub::class);
    }

    /**
     * Get the attendance records for this report.
     */
    public function attendance()
    {
        return $this->hasMany(Attendance::class, 'report_id');
    }

    /**
     * Get the trip for this report.
     */
    public function trip()
    {
        return $this->hasOne(Trip::class, 'report_id');
    }

    /**
     * Get the successful deliveries for this report.
     */
    public function successfulDeliveries()
    {
        return $this->hasOne(SuccessfulDeliveries::class, 'report_id');
    }

    /**
     * Get the failed deliveries for this report.
     */
    public function failedDeliveries()
    {
        return $this->hasOne(FailedDeliveries::class, 'report_id');
    }
}
