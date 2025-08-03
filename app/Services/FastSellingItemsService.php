<?php

namespace App\Services;

use App\Models\FastSellingItem;
use App\Models\User;

class FastSellingItemsService {

    public function getItems(User $user)
    {
        $items = $user->pharmacy->fast_selling_items;
        return $items;
    }

    public function addItem(array $payload, User $user)
    {
       $payload['pharmacy_id'] = $user->pharmacy_id;
       $payload['purchase_price'] = $payload['purchase_price'] / $payload['quantity'];
       $item = FastSellingItem::create($payload);
        return $item;
    }

    public function updateItem(array $payload, FastSellingItem $item)
    {
        $addedQuantity = $payload['quantity'] ?? 0;

        $item->quantity += $addedQuantity;

        if (isset($payload['retail_price'])) {
            $item->retail_price = $payload['retail_price'];
        }

        if (isset($payload['purchase_price']) && $item->quantity > 0) {
            $item->purchase_price = $payload['purchase_price'] / $item->quantity;
        }

        $item->save();

        return $item;
    }
}
