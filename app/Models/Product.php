<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'category_id',
        'sku',
        'barcode',
        'name',
        'description',
        'stock_quantity',
        'min_stock',
        'purchase_price',
        'selling_price',
        'is_active',
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
            'selling_price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the category that owns the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Get the stock logs for this product.
     */
    public function stockLogs(): HasMany
    {
        return $this->hasMany(StockLog::class, 'product_id');
    }

    /**
     * Get the transaction details for this product.
     */
    public function transactionDetails(): HasMany
    {
        return $this->hasMany(TransactionDetail::class, 'product_id');
    }

    /**
     * Get current margin (selling price - purchase price).
     */
    public function getMarginAttribute(): float
    {
        return $this->selling_price - $this->purchase_price;
    }

    /**
     * Get total value of current stock.
     */
    public function getStockValueAttribute(): float
    {
        return $this->stock_quantity * $this->purchase_price;
    }

    /**
     * Check if stock is below minimum.
     */
    public function isLowStock(): bool
    {
        return $this->stock_quantity <= $this->min_stock;
    }

    /**
     * Calculate profit for a given quantity.
     */
    public function calculateProfit(int $quantity): float
    {
        return ($this->selling_price - $this->purchase_price) * $quantity;
    }

    /**
     * Scope a query to only include active products.
     */
    public function scopeActive(\Illuminate\Database\Eloquent\Builder $query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include low stock products.
     */
    public function scopeLowStock(\Illuminate\Database\Eloquent\Builder $query)
    {
        return $query->whereColumn('stock_quantity', '<=', 'min_stock')
            ->where('is_active', true);
    }

    /**
     * Scope a query to search by barcode.
     */
    public function scopeByBarcode(\Illuminate\Database\Eloquent\Builder $query, string $barcode)
    {
        return $query->where('barcode', $barcode);
    }

    /**
     * Scope a query to search by SKU.
     */
    public function scopeBySku(\Illuminate\Database\Eloquent\Builder $query, string $sku)
    {
        return $query->where('sku', $sku);
    }
}
