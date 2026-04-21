<?php
// User model - user account, handles login and auth
// extends Authenticatable so Laravel handles login/logout automatically

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

/* CLASS */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // fields allowed for mass assignment
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    // never send these fields in json response - security
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /* PRIVATE METHOD */
    /* casts */
    protected function casts(): array
    {
        return [
            // store as datetime not string
            'email_verified_at' => 'datetime',
            // auto hash password on save - no need to hash manually
            'password' => 'hashed',
        ];
    }

    /* PUBLIC METHOD */
    /* initials */
    public function initials(): string
    {
        // get first letter of each word - used for avatar initials fallback in the view
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }
}
