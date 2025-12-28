<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'discount_price',
        'stock',
        'size',
        'color',
        'is_featured',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Boot method for model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name) . '-' . uniqid();
            }
        });
    }

    /**
     * Get the category
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get product images
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    /**
     * Get primary image
     */
    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    /**
     * Get product reviews
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get approved reviews
     */
    public function approvedReviews(): HasMany
    {
        return $this->reviews()->where('is_approved', true);
    }

    /**
     * Get average rating
     */
    public function getAverageRatingAttribute(): float
    {
        return $this->approvedReviews()->avg('rating') ?? 0;
    }

    /**
     * Get current price (discount or regular)
     */
    public function getCurrentPriceAttribute(): float
    {
        return (float) ($this->discount_price ?? $this->price);
    }

    /**
     * Check if product has discount
     */
    public function getHasDiscountAttribute(): bool
    {
        return $this->discount_price !== null && $this->discount_price < $this->price;
    }

    /**
     * Get discount percentage
     */
    public function getDiscountPercentageAttribute(): int
    {
        if (!$this->has_discount) {
            return 0;
        }
        return round((($this->price - $this->discount_price) / $this->price) * 100);
    }

    /**
     * Get size options as array
     */
    public function getSizeOptionsAttribute(): array
    {
        return $this->size ? explode(',', $this->size) : [];
    }

    /**
     * Get color options as array
     */
    public function getColorOptionsAttribute(): array
    {
        return $this->color ? explode(',', $this->color) : [];
    }
}
