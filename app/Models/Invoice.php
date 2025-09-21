<?php

namespace App\Models;

use App\Enums\InvoiceCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\InvoiceType;

class Invoice extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type',
        'category_id',
        'atcud',
        'nif',
        'date',
        'total_iva',
        'total',
        'files',
        'metadata',
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
            'type' => InvoiceType::class,
            'category' => InvoiceCategory::class,
            'date' => 'date',
            'total_iva' => 'decimal:2',
            'total' => 'decimal:2',
            'metadata' => 'array',
        ];
    }

    /**
     * Company that this invoice belongs to
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Category that this invoice belongs to
     */
    // public function category(): BelongsTo
    // {
    //     return $this->belongsTo(InvoiceCategory::class, 'category_id');
    // }

    /**
     * User who created this invoice
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope to filter by type
     */
    public function scopeOfType($query, InvoiceType $type)
    {
        return $query->where('type', $type->value);
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
     * Get the type label
     */
    public function getTypeLabelAttribute(): string
    {
        return $this->type->label();
    }

    /**
     * Get the formatted total
     */
    public function getFormattedTotalAttribute(): string
    {
        return number_format($this->total, 2, ',', '.') . ' €';
    }

    /**
     * Get the formatted total IVA
     */
    public function getFormattedTotalIvaAttribute(): string
    {
        return number_format($this->total_iva, 2, ',', '.') . ' €';
    }

    /**
     * Get the formatted date
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->date->format('d/m/Y');
    }

    /**
     * Get the formatted NIF
     */
    public function getFormattedNifAttribute(): string
    {
        return number_format($this->nif, 0, '', ' ');
    }

    /**
     * Check if invoice is an expense
     */
    public function isExpense(): bool
    {
        return $this->type === InvoiceType::EXPENSE;
    }

    /**
     * Check if invoice is a sale
     */
    public function isSale(): bool
    {
        return $this->type === InvoiceType::SALE;
    }

    /**
     * Get the file path for storage
     */
    public function getStoragePathAttribute(): string
    {
        return "invoices/{$this->date->year}/{$this->date->format('m')}/";
    }

    /**
     * Get the full file path
     */
    public function getFullFilePathAttribute(): string
    {
        return storage_path('app/' . $this->storage_path . $this->files);
    }

    /**
     * Check if file exists
     */
    public function fileExists(): bool
    {
        return file_exists($this->full_file_path);
    }
}