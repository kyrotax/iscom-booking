<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'nim',
        'major',
        'semester',
        'whatsapp',
        'role',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'semester' => 'integer',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function latestBooking(): HasOne
    {
        return $this->hasOne(Booking::class)->latestOfMany();
    }

    public function activeBookings(): HasMany
    {
        return $this->hasMany(Booking::class)->whereIn('status', ['pending', 'accepted']);
    }

    public function hasBookingForSession(int $sessionId): bool
    {
        return $this->activeBookings()
            ->whereHas('schedule', function ($query) use ($sessionId) {
                $query->where('mentoring_session_id', $sessionId);
            })
            ->exists();
    }

    public function findConflictingSchedule(Schedule $schedule): ?Schedule
    {
        $activeBookings = $this->activeBookings()
            ->with('schedule.mentoringSession')
            ->get();

        foreach ($activeBookings as $booking) {
            $existing = $booking->schedule;
            if (!$existing || $existing->id === $schedule->id) {
                continue;
            }
            if ($schedule->conflictsWith($existing)) {
                return $existing;
            }
        }

        return null;
    }
}
