<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_code',
        'user_id',
        'schedule_id',
        'full_name',
        'nim',
        'major',
        'semester',
        'whatsapp',
        'email',
        'status',
        'admin_notes',
    ];

    protected static function booted()
    {
        static::creating(function ($booking) {
            if (empty($booking->booking_code)) {
                $year = date('Y');
                do {
                    $randomNum = rand(1000, 9999);
                    $code = "#ISCOM-{$year}-{$randomNum}";
                } while (static::where('booking_code', $code)->exists());

                $booking->booking_code = $code;
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }
}
