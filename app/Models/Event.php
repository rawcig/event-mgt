<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;
    
    protected $table = 'events';
    
    protected $fillable = [
        'title',
        'description',
        'date',
        'location',
        'status',
        'organizer_id',
        'max_attendees',
        'cover_image',
        'detail_image',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    public function organizer()
    {
        return $this->belongsTo(Organizer::class);
    }

    public function guests()
    {
        return $this->hasMany(Guest::class);
    }
    
    // Get registered guests count
    public function getRegisteredCountAttribute()
    {
        return $this->guests()->count();
    }
    
    // Get total tickets count
    public function getTicketCountAttribute()
    {
        return $this->guests()->sum('ticket_count');
    }
    
    // Get confirmed guests count
    public function getConfirmedCountAttribute()
    {
        return $this->guests()->where('status', 'confirmed')->count();
    }
    
    // Get checked in count
    public function getCheckedInCountAttribute()
    {
        return $this->guests()->where('checked_in', true)->count();
    }
    
    // Get available seats
    public function getAvailableSeatsAttribute()
    {
        if (is_null($this->max_attendees)) {
            return 'Unlimited';
        }
        
        $registered = $this->guests()->sum('ticket_count');
        $available = $this->max_attendees - $registered;
        
        return max(0, $available);
    }
    
    // Check if event is full
    public function getIsFullAttribute()
    {
        if (is_null($this->max_attendees)) {
            return false;
        }
        
        $registered = $this->guests()->sum('ticket_count');
        return $registered >= $this->max_attendees;
    }
    
    // Check if user is registered
    public function isUserRegistered($userId = null)
    {
        $userId = $userId ?? auth()->id();
        if (!$userId) return false;
        
        return $this->guests()->where('user_id', $userId)->exists();
    }
    
    // Get user registration
    public function getUserRegistration($userId = null)
    {
        $userId = $userId ?? auth()->id();
        if (!$userId) return null;
        
        return $this->guests()->where('user_id', $userId)->first();
    }

    // Admin Statistics Methods
    
    // Get pending guests count
    public function getPendingCountAttribute()
    {
        return $this->guests()->where('status', 'pending')->count();
    }
    
    // Get cancelled/no-show count
    public function getCancelledCountAttribute()
    {
        return $this->guests()->where('status', 'cancelled')->count();
    }
    
    // Get attendance rate
    public function getAttendanceRateAttribute()
    {
        $total = $this->guests()->count();
        if ($total === 0) return 0;
        
        $checkedIn = $this->guests()->where('checked_in', true)->count();
        return round(($checkedIn / $total) * 100, 2);
    }
    
    // Get breakdown by participation type
    public function getParticipationBreakdownAttribute()
    {
        return $this->guests()
            ->select('participation_type')
            ->selectRaw('count(*) as count')
            ->groupBy('participation_type')
            ->get()
            ->keyBy('participation_type');
    }
    
    // Get guest registration trend (by day)
    public function getRegistrationTrendAttribute()
    {
        return $this->guests()
            ->selectRaw("CAST(created_at AS DATE) as date, COUNT(*) as count")
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();
    }
    
    // Get not checked in count
    public function getNotCheckedInCountAttribute()
    {
        return $this->guests()->where('checked_in', false)->count();
    }
    
    // Get occupancy percentage
    public function getOccupancyPercentageAttribute()
    {
        if (is_null($this->max_attendees) || $this->max_attendees === 0) {
            return null;
        }
        
        $registered = $this->guests()->sum('ticket_count');
        return round(($registered / $this->max_attendees) * 100, 2);
    }
}
