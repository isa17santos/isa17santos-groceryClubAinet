<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'category_id', 'price', 'stock', 'description', 'photo',
        'discount_min_qty', 'discount', 'stock_lower_limit', 'stock_upper_limit'
    ];

    // imagem dos produtos
    public function getImageUrlAttribute(): string
    {
        if ($this->photo && Storage::disk('public')->exists('products/' . $this->photo)) {
            return asset('storage/products/' . $this->photo);
        }

        return asset('images/placeholder.jpg');
    }

    // Accessor: verifica se tem desconto
    public function getHasDiscountAttribute(): bool
    {
        return $this->discount !== null && $this->discount_min_qty !== null;
    }

    // Accessor: devolve o preço com desconto (ou preço normal)
    public function getDiscountedPriceAttribute(): float
    {
        return $this->has_discount ? ($this->price - $this->discount) : $this->price;
    }



    // ------------------- CODIGO RELACOES ----------------------
    public function stockAdjustments(): HasMany
    {
        return $this->hasMany(StockAdjustment::class);
    }


    public function supplyOrders(): HasMany
    {
        return $this->hasMany(SupplyOrder::class);
    }


    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }


    public function orders()
    {
        return $this->belongsToMany(Order::class, 'items_orders')
            ->withPivot('quantity', 'unit_price', 'discount', 'subtotal');
    }

    public function isLowStock(): bool
    {
        return $this->stock <= $this->stock_lower_limit;
    }

    public function isHighStock(): bool
    {
        return $this->stock >= $this->stock_upper_limit;
    }

}
