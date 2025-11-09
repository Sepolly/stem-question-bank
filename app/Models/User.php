<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function events()
    {
        return $this->belongsToMany(Event::class, 'user_events', 'user_id', 'event_id');
    }

    public function inEvent(int $eventId)
    {
        return in_array($eventId, $this->events()->pluck('events.id')->toArray());
    }

    public function isCreatorOfEvent(Event $event)
    {
        return $event->creator()->is($this);
    }

    public function isSuperAdmin()
    {
        return $this->hasRole('super admin');
    }

    public function canManageSubject()
    {
        return $this->hasAnyRole(['admin', 'super admin']);
    }

    public function canAddQuestion()
    {
        return $this->hasAnyRole(['admin', 'super admin', 'contributor']);
    }

    public function canManageQuestion()
    {
        return $this->hasAnyRole(['admin', 'super admin']);
    }
}
