<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InvoiceCategory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'name',
        'group',
        'tax_deductible',
        'active',
        'order',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tax_deductible' => 'boolean',
            'active' => 'boolean',
            'order' => 'integer',
        ];
    }

    /**
     * Invoices belonging to this category
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'category_id');
    }

    /**
     * Scope to get only active categories
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope to order by order field
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Scope to get tax deductible categories
     */
    public function scopeTaxDeductible($query)
    {
        return $query->where('tax_deductible', true);
    }

    /**
     * Get categories grouped by group
     */
    public static function getGrouped()
    {
        return self::active()
            ->ordered()
            ->get()
            ->groupBy('group');
    }

    /**
     * Get categories for select options
     */
    public static function getSelectOptions()
    {
        return self::active()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();
    }

    /**
     * Get the deductible status label
     */
    public function getTaxDeductibleLabelAttribute(): string
    {
        return $this->tax_deductible ? 'Sim' : 'Não';
    }

    /**
     * Get the active status label
     */
    public function getActiveLabelAttribute(): string
    {
        return $this->active ? 'Ativo' : 'Inativo';
    }
}