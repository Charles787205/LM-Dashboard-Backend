<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hub extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hub';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'hub_lead_id',
        'client_id',
    ];

    /**
     * Get the hub lead (user) for this hub.
     */
    public function hubLead()
    {
        return $this->belongsTo(User::class, 'hub_lead_id');
    }

    /**
     * Get the client for this hub.
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the comments for this hub.
     */
    public function comments()
    {
        return $this->hasMany(HubComment::class);
    }
}
