<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kilometer extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'licenseplate',
        'date',
        'origin',
        'destination',
        'kilometers',
        'reason',
        'company_id',
        'created_by',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date' => 'date',
            'kilometers' => 'integer',
        ];
    }

    /**
     * Company that this kilometer record belongs to
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * User who created this kilometer record
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope to filter by company
     */
    public function scopeForCompany($query, int $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope to filter by creator (for user role restrictions)
     */
    public function scopeByCreator($query, int $userId)
    {
        return $query->where('created_by', $userId);
    }

    /**
     * Scope to filter by date range
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    /**
     * Scope to filter by year
     */
    public function scopeInYear($query, int $year)
    {
        return $query->whereYear('date', $year);
    }

    /**
     * Scope to filter by month
     */
    public function scopeInMonth($query, int $month)
    {
        return $query->whereMonth('date', $month);
    }

    /**
     * Scope to filter by license plate
     */
    public function scopeByLicensePlate($query, string $licenseplate)
    {
        return $query->where('licenseplate', $licenseplate);
    }

    /**
     * Scope to filter by driver name
     */
    public function scopeByDriver($query, string $name)
    {
        return $query->where('name', 'like', "%{$name}%");
    }

    /**
     * Get the formatted date
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->date->format('d/m/Y');
    }

    /**
     * Get the formatted license plate
     */
    public function getFormattedLicensePlateAttribute(): string
    {
        return strtoupper($this->licenseplate);
    }

    /**
     * Get the formatted kilometers
     */
    public function getFormattedKilometersAttribute(): string
    {
        return number_format($this->kilometers, 0, ',', '.') . ' km';
    }

    /**
     * Get the trip route
     */
    public function getRouteAttribute(): string
    {
        return $this->origin . ' → ' . $this->destination;
    }

    /**
     * Calculate the estimated cost (0.36€ per km for personal vehicles)
     */
    public function getEstimatedCostAttribute(): float
    {
        return $this->kilometers * 0.36;
    }

    /**
     * Get the formatted estimated cost
     */
    public function getFormattedEstimatedCostAttribute(): string
    {
        return number_format($this->estimated_cost, 2, ',', '.') . ' €';
    }

    /**
     * Validate Portuguese license plate format
     */
    public static function validateLicensePlate(string $licenseplate): bool
    {
        // Portuguese format: XX-XX-XX (2 letters/numbers, dash, 2 letters/numbers, dash, 2 letters/numbers)
        return preg_match('/^[A-Z0-9]{2}-[A-Z0-9]{2}-[A-Z0-9]{2}$/', strtoupper($licenseplate));
    }

    /**
     * Get total kilometers for a period
     */
    public static function getTotalKilometersForPeriod($startDate, $endDate, $companyId = null, $userId = null)
    {
        $query = self::betweenDates($startDate, $endDate);

        if ($companyId) {
            $query->forCompany($companyId);
        }

        if ($userId) {
            $query->byCreator($userId);
        }

        return $query->sum('kilometers');
    }

    /**
     * Get total estimated cost for a period
     */
    public static function getTotalCostForPeriod($startDate, $endDate, $companyId = null, $userId = null)
    {
        $totalKm = self::getTotalKilometersForPeriod($startDate, $endDate, $companyId, $userId);
        return $totalKm * 0.36;
    }
}