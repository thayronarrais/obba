<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Salary extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'date',
        'employee_id',
        'gross_salary_month',
        'food_allowance_month',
        'additional_subsidies',
        'social_security',
        'mandatory_ensurance',
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
            'gross_salary_month' => 'decimal:2',
            'food_allowance_month' => 'decimal:2',
            'additional_subsidies' => 'decimal:2',
            'social_security' => 'decimal:2',
            'mandatory_ensurance' => 'decimal:2',
        ];
    }

    /**
     * Company that this salary belongs to
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Employee that this salary belongs to
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    /**
     * User who created this salary record
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
     * Scope to filter by employee
     */
    public function scopeForEmployee($query, int $employeeId)
    {
        return $query->where('employee_id', $employeeId);
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
     * Get the formatted date
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->date->format('d/m/Y');
    }

    /**
     * Get the formatted gross salary
     */
    public function getFormattedGrossSalaryAttribute(): string
    {
        return number_format($this->gross_salary_month, 2, ',', '.') . ' €';
    }

    /**
     * Get the formatted food allowance
     */
    public function getFormattedFoodAllowanceAttribute(): string
    {
        return number_format($this->food_allowance_month, 2, ',', '.') . ' €';
    }

    /**
     * Get the formatted additional subsidies
     */
    public function getFormattedAdditionalSubsidiesAttribute(): string
    {
        return number_format($this->additional_subsidies, 2, ',', '.') . ' €';
    }

    /**
     * Get the formatted social security
     */
    public function getFormattedSocialSecurityAttribute(): string
    {
        return number_format($this->social_security, 2, ',', '.') . ' €';
    }

    /**
     * Get the formatted mandatory insurance
     */
    public function getFormattedMandatoryEnsuranceAttribute(): string
    {
        return number_format($this->mandatory_ensurance, 2, ',', '.') . ' €';
    }

    /**
     * Calculate the total benefits
     */
    public function getTotalBenefitsAttribute(): float
    {
        return $this->food_allowance_month + $this->additional_subsidies;
    }

    /**
     * Get the formatted total benefits
     */
    public function getFormattedTotalBenefitsAttribute(): string
    {
        return number_format($this->total_benefits, 2, ',', '.') . ' €';
    }

    /**
     * Calculate the total costs for the company
     */
    public function getTotalCostAttribute(): float
    {
        return $this->gross_salary_month + $this->total_benefits + $this->social_security + $this->mandatory_ensurance;
    }

    /**
     * Get the formatted total cost
     */
    public function getFormattedTotalCostAttribute(): string
    {
        return number_format($this->total_cost, 2, ',', '.') . ' €';
    }

    /**
     * Calculate the net salary (simplified - doesn't include all deductions)
     */
    public function getEstimatedNetSalaryAttribute(): float
    {
        // This is a simplified calculation - real calculation would need IRS and SS employee deductions
        $employeeSS = $this->gross_salary_month * 0.11; // 11% employee contribution
        return $this->gross_salary_month - $employeeSS + $this->food_allowance_month;
    }

    /**
     * Get the formatted estimated net salary
     */
    public function getFormattedEstimatedNetSalaryAttribute(): string
    {
        return number_format($this->estimated_net_salary, 2, ',', '.') . ' €';
    }

    /**
     * Get the last salary for an employee
     */
    public static function getLastSalaryForEmployee(int $employeeId)
    {
        return self::forEmployee($employeeId)
            ->orderBy('date', 'desc')
            ->first();
    }

    /**
     * Get total salary costs for a period
     */
    public static function getTotalCostForPeriod($startDate, $endDate, $companyId = null, $employeeId = null)
    {
        $query = self::betweenDates($startDate, $endDate);

        if ($companyId) {
            $query->forCompany($companyId);
        }

        if ($employeeId) {
            $query->forEmployee($employeeId);
        }

        $salaries = $query->get();

        return $salaries->sum(function ($salary) {
            return $salary->total_cost;
        });
    }

    /**
     * Get salary summary for a period
     */
    public static function getSalaryStatsForPeriod($startDate, $endDate, $companyId = null)
    {
        $query = self::betweenDates($startDate, $endDate);

        if ($companyId) {
            $query->forCompany($companyId);
        }

        $salaries = $query->get();

        return [
            'total_employees' => $salaries->unique('employee_id')->count(),
            'total_gross_salary' => $salaries->sum('gross_salary_month'),
            'total_benefits' => $salaries->sum(fn($s) => $s->total_benefits),
            'total_social_security' => $salaries->sum('social_security'),
            'total_insurance' => $salaries->sum('mandatory_ensurance'),
            'total_cost' => $salaries->sum(fn($s) => $s->total_cost),
        ];
    }
}