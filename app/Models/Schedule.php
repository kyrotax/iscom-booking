<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'mentoring_session_id',
        'day_name',
        'schedule_date',
        'time_slot',
        'start_time',
        'end_time',
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

    public function mentoringSession(): BelongsTo
    {
        return $this->belongsTo(MentoringSession::class, 'mentoring_session_id');
    }

    public function conflictsWith(Schedule $other): bool
    {
        $date1 = $this->schedule_date ? $this->schedule_date->format('Y-m-d') : null;
        $date2 = $other->schedule_date ? $other->schedule_date->format('Y-m-d') : null;

        if (!$date1 || !$date2 || $date1 !== $date2) {
            return false;
        }

        if ($this->start_time && $this->end_time && $other->start_time && $other->end_time) {
            return max($this->start_time, $other->start_time) < min($this->end_time, $other->end_time);
        }

        return trim($this->time_slot) === trim($other->time_slot);
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
