<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\CompanyLocation;

class Company extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'nif',
        'address',
        'location',
        'iva_monthly_period',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'location' => CompanyLocation::class,
            'iva_monthly_period' => 'boolean',
        ];
    }

    /**
     * Invoices belonging to this company
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Kilometers registered for this company
     */
    public function kilometers(): HasMany
    {
        return $this->hasMany(Kilometer::class);
    }

    /**
     * Salaries registered for this company
     */
    public function salaries(): HasMany
    {
        return $this->hasMany(Salary::class);
    }

    /**
     * Get the formatted NIF
     */
    public function getFormattedNifAttribute(): string
    {
        return number_format($this->nif, 0, '', ' ');
    }

    /**
     * Get the location label
     */
    public function getLocationLabelAttribute(): string
    {
        return $this->location->label();
    }

    /**
     * Get IVA period label
     */
    public function getIvaPeriodLabelAttribute(): string
    {
        return $this->iva_monthly_period ? 'Mensal' : 'Trimestral';
    }
}