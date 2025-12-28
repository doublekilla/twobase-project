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
        'user_id',
        'order_number',
        'subtotal',
        'shipping_cost',
        'total',
        'status',
        'payment_status',
        'payment_method',
        'payment_proof',
        'snap_token',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_address',
        'shipping_phone',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    /**
     * Boot method to generate order number
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . strtoupper(uniqid());
            }
        });
    }

    /**
     * Get the user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get order items
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'warning',
            'processing' => 'info',
            'shipped' => 'primary',
            'completed' => 'success',
            'cancelled' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Get payment status badge class
     */
    public function getPaymentBadgeAttribute(): string
    {
        return match ($this->payment_status) {
            'unpaid' => 'danger',
            'paid' => 'success',
            'refunded' => 'warning',
            default => 'secondary',
        };
    }

    /**
     * Get human-readable status label
     */
    public function getStatusLabel(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu',
            'processing' => 'Diproses',
            'shipped' => 'Dikirim',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => ucfirst($this->status),
        };
    }

    /**
     * Get human-readable payment status label
     */
    public function getPaymentStatusLabel(): string
    {
        return match ($this->payment_status) {
            'unpaid' => 'Belum Bayar',
            'paid' => 'Sudah Bayar',
            'refunded' => 'Dikembalikan',
            default => ucfirst($this->payment_status),
        };
    }
}
