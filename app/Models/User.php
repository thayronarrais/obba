<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\UserRole;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
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
            'role' => UserRole::class,
        ];
    }

    /**
     * Invoices created by this user
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'created_by');
    }

    /**
     * Kilometers registered by this user
     */
    public function kilometers(): HasMany
    {
        return $this->hasMany(Kilometer::class, 'created_by');
    }

    /**
     * Salaries created by this user
     */
    public function salariesCreated(): HasMany
    {
        return $this->hasMany(Salary::class, 'created_by');
    }

    /**
     * Salaries of this user as employee
     */
    public function salariesAsEmployee(): HasMany
    {
        return $this->hasMany(Salary::class, 'employee_id');
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    /**
     * Check if user is accountant
     */
    public function isAccountant(): bool
    {
        return $this->role === UserRole::ACCOUNTANT;
    }

    /**
     * Check if user is regular user
     */
    public function isUser(): bool
    {
        return $this->role === UserRole::USER;
    }

    /**
     * Check if user can see all data (admin or accountant)
     */
    public function canSeeAllData(): bool
    {
        return $this->isAdmin() || $this->isAccountant();
    }
}
