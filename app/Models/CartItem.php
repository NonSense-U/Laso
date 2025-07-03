<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    /** @use HasFactory<\Database\Factories\CartItemFactory> */
    use HasFactory;

    public function cart(){
        return $this->belongsTo(Cart::class);
    }


    public function getProductAttribute()
    {
        return match ($this->type) {
            'med_package' => MedPackage::find($this->product_id),
            'fast_selling_item' => FastSellingItem::find($this->product_id),
            default => null,
        };
    }
}
