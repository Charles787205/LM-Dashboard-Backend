<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FailedDeliveries extends Model
{
    protected $table = 'failed_deliveries';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'report_id',
        'two_w',
        'three_w',
        'four_w',
        'canceled_bef_delivery',
        'postponed',
        'invalid_address',
        'unreachable',
        'no_cash_available',
        'not_at_home',
    ];

    /**
     * Get the report this failed delivery belongs to.
     */
    public function report()
    {
        return $this->belongsTo(Report::class);
    }
}
