<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'type',
        'quantity',
        'stock_before',
        'stock_after',
        'purchase_price',
        'notes',
        'user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'purchase_price' => 'decimal:2',
        ];
    }

    /**
     * Get the product that owns this log.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Get the user that created this log.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope a query to only include stock in records.
     */
    public function scopeStockIn(\Illuminate\Database\Eloquent\Builder $query)
    {
        return $query->where('type', 'IN');
    }

    /**
     * Scope a query to only include stock out records.
     */
    public function scopeStockOut(\Illuminate\Database\Eloquent\Builder $query)
    {
        return $query->where('type', 'OUT');
    }

    /**
     * Scope a query to only include void records.
     */
    public function scopeVoid(\Illuminate\Database\Eloquent\Builder $query)
    {
        return $query->where('type', 'VOID');
    }

    /**
     * Scope a query to only include adjustment records.
     */
    public function scopeAdjustment(\Illuminate\Database\Eloquent\Builder $query)
    {
        return $query->where('type', 'ADJUSTMENT');
    }

    /**
     * Scope a query for specific product.
     */
    public function scopeForProduct(\Illuminate\Database\Eloquent\Builder $query, int $productId)
    {
        return $query->where('product_id', $productId);
    }

    /**
     * Scope a query for today only.
     */
    public function scopeToday(\Illuminate\Database\Eloquent\Builder $query)
    {
        return $query->whereDate('created_at', today());
    }
}
