<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'booking_date',
        'start_time',
        'end_time',
        'customer_name',
        'customer_email',
        'booking_type',
        'slot',
    ];

    // convenience: scope to same date
    public function scopeOnDate($query, $date)
    {
        return $query->where('booking_date', $date);
    }
}
