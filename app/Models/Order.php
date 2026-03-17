<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'status',
        'total_price',
        'amount_paid',
        'delivery_date',
        'payment_status',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'total_price' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'delivery_date' => 'date',
            'metadata' => 'array',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0, $this->total_price - $this->amount_paid);
    }

    public function addPayment(float $amount, string $method = 'other', ?string $notes = null): Payment
    {
        $payment = $this->payments()->create([
            'amount' => $amount,
            'method' => $method,
            'notes' => $notes,
            'paid_at' => now(),
        ]);

        $this->amount_paid = $this->payments()->sum('amount');

        if ($this->amount_paid >= $this->total_price) {
            $this->payment_status = 'paid';
        } else {
            $this->payment_status = 'partial';
        }

        $this->save();

        return $payment;
    }

    public function scopeOpen($query)
    {
        return $query->whereIn('status', ['pending', 'ready']);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                     ->whereYear('created_at', now()->year);
    }
}
