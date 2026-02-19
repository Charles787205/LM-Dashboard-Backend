<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = 'attendance';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'report_id',
        'hub_lead',
        'backroom',
    ];

    /**
     * Get the report this attendance belongs to.
     */
    public function report()
    {
        return $this->belongsTo(Report::class);
    }
}
