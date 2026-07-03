<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone_country_code',
        'phone_number',
        'profile_photo_path',
        'password',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function targets(): HasMany
    {
        return $this->hasMany(Target::class);
    }

    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class);
    }

    public function getProfilePhotoUrlAttribute(): ?string
    {
        if (!$this->profile_photo_path) {
            return null;
        }

        $url = route('profile.photo', $this);

        if ($this->updated_at) {
            $url .= '?v=' . $this->updated_at->timestamp;
        }

        return $url;
    }

    public function getProfileInitialsAttribute(): string
    {
        $name = trim((string) $this->name);

        if ($name === '') {
            return 'CF';
        }

        $parts = preg_split('/\s+/', $name) ?: [];
        $initials = '';

        foreach (array_slice($parts, 0, 2) as $part) {
            $initials .= strtoupper(substr($part, 0, 1));
        }

        return $initials ?: 'CF';
    }

    public function getPhoneDisplayAttribute(): string
    {
        if (!$this->phone_country_code || !$this->phone_number) {
            return 'Tidak Ada';
        }

        return trim($this->phone_country_code . ' ' . $this->phone_number);
    }
}