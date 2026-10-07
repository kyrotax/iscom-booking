<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'day_name',
        'schedule_date',
        'time_slot',
        'mentor_names',
        'location',
        'topic',
        'quota',
        'booked_count',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'schedule_date' => 'date',
            'quota' => 'integer',
            'booked_count' => 'integer',
        ];
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function acceptedBookings(): HasMany
    {
        return $this->hasMany(Booking::class)->where('status', 'accepted');
    }

    public function getRemainingSlotsAttribute(): int
    {
        $acceptedCount = $this->bookings()->where('status', 'accepted')->count();
        $remaining = $this->quota - $acceptedCount;
        return max(0, $remaining);
    }

    public function recalculateStatus(): void
    {
        $acceptedCount = $this->bookings()->where('status', 'accepted')->count();
        $this->booked_count = $acceptedCount;
        $remaining = $this->quota - $acceptedCount;

        if ($remaining <= 0) {
            $this->status = 'penuh';
        } elseif ($remaining <= 3) {
            $this->status = 'hampir_penuh';
        } else {
            $this->status = 'tersedia';
        }

        $this->save();
    }
}
