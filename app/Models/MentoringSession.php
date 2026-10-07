<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MentoringSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'badge_label',
        'is_active',
    ];

    protected static function booted()
    {
        static::saving(function ($session) {
            if (empty($session->slug) && !empty($session->title)) {
                $session->slug = \Illuminate\Support\Str::slug($session->title);
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where('slug', $value)
            ->orWhere('id', $value)
            ->firstOrFail();
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class)->orderBy('schedule_date', 'asc')->orderBy('time_slot', 'asc');
    }

    public function getTotalSlotsAttribute(): int
    {
        return $this->schedules->sum('quota');
    }

    public function getTotalRemainingSlotsAttribute(): int
    {
        return $this->schedules->sum(fn ($sched) => $sched->remaining_slots);
    }

    public function getMentorsSummaryAttribute(): string
    {
        $mentors = $this->schedules->pluck('mentor_names')->filter()->unique()->values();
        return $mentors->implode(', ');
    }
}
