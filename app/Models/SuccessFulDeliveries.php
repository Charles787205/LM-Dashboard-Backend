<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuccessfulDeliveries extends Model
{
    protected $table = 'successful_deliveries';

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
    ];

    /**
     * Get the report this successful delivery belongs to.
     */
    public function report()
    {
        return $this->belongsTo(Report::class);
    }
}
