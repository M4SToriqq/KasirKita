<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Transaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'invoice_number',
        'user_id',
        'total_price',
        'total_paid',
        'change',
        'payment_method',
        'status',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'total_price' => 'decimal:2',
            'total_paid' => 'decimal:2',
            'change' => 'decimal:2',
        ];
    }

    /**
     * Get the user (kasir) that created this transaction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the transaction details for this transaction.
     */
    public function details(): HasMany
    {
        return $this->hasMany(TransactionDetail::class, 'transaction_id');
    }

    /**
     * Get the products in this transaction.
     */
    public function products(): HasManyThrough
    {
        return $this->hasManyThrough(Product::class, TransactionDetail::class, 'transaction_id');
    }

    /**
     * Check if transaction is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'COMPLETED';
    }

    /**
     * Check if transaction is voided.
     */
    public function isVoided(): bool
    {
        return $this->status === 'VOID';
    }

    /**
     * Scope a query to only include completed transactions.
     */
    public function scopeCompleted(\Illuminate\Database\Eloquent\Builder $query)
    {
        return $query->where('status', 'COMPLETED');
    }

    /**
     * Scope a query to only include voided transactions.
     */
    public function scopeVoided(\Illuminate\Database\Eloquent\Builder $query)
    {
        return $query->where('status', 'VOID');
    }

    /**
     * Scope a query to only include transactions by payment method.
     */
    public function scopeByPaymentMethod(\Illuminate\Database\Eloquent\Builder $query, string $method)
    {
        return $query->where('payment_method', $method);
    }

    /**
     * Scope a query for today only.
     */
    public function scopeToday(\Illuminate\Database\Eloquent\Builder $query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Scope a query for specific kasir.
     */
    public function scopeByKasir(\Illuminate\Database\Eloquent\Builder $query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Calculate total profit from this transaction.
     */
    public function calculateProfit(): float
    {
        $profit = 0;
        foreach ($this->details as $detail) {
            $product = $detail->product;
            $profit += ($detail->unit_price - $product->purchase_price) * $detail->quantity;
        }
        return $profit;
    }
}
