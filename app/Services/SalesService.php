<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\FastSellingItem;
use App\Models\MedPackage;
use App\Models\Payment;
use App\Models\User;
use App\Models\Vault;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Throwable;

use function Pest\Laravel\get;

class SalesService
{

    public function Checkout(array $payload, User $user)
    {
        DB::beginTransaction();

        try {

            if ($payload['payment_method'] === 'cash') {
                $main_vault = $user->pharmacy->vaults()->where('name', '=', 'main')->firstOrFail();
                $main_vault->balance += $payload['total_price'];
                $main_vault->save();
            } elseif ($payload['payment_method'] === 'charity') {
                $charity_vault = $user->pharmacy->vaults()->where('name', '=', 'charity')->firstOrFail();
                if ($charity_vault->balance < $payload['total_price']) {
                    throw new UnprocessableEntityHttpException('There is not enough money in the charity box.');
                }
                $charity_vault->balance -= $payload['total_price'];
                $charity_vault->save();
            }
            //! HANDLE DEPT

            $payment = Payment::create([
                'pharmacy_id' => $user->pharmacy_id,
                'payment_method' => $payload['payment_method'],
                'paid_price' => $payload['total_price']
            ]);

            $cart = Cart::create([
                'total_price' => $payload['total_price'],
                'user_id' => $user->id,
                'pharmacy_id' => $user->pharmacy_id,
                'payment_id' => $payment->id
            ]);


            $grouped = collect($payload['items'])->groupBy('type');

            $medPackages = MedPackage::whereIn('id', $grouped->get('med_package', collect())->pluck('product_id'))->get()->keyBy('id');
            $fastSellingItems = FastSellingItem::whereIn('id', $grouped->get('fast_selling_item', collect())->pluck('product_id'))->get()->keyBy('id');


            foreach ($payload['items'] as $item) {

                $product = match ($item['type']) {
                    'med_package' => $medPackages->get($item['product_id']),
                    'fast_selling_item' => $fastSellingItems->get($item['product_id']),
                    default => null,
                };

                if (!$product) {
                    throw new NotFoundHttpException();
                }

                if ($item['quantity'] > $product->quantity) {
                    throw new UnprocessableEntityHttpException("Insufficient stock for product {$item['product_id']}."); //! UPDATE LATER
                }

                $newItem = CartItem::create([
                    'cart_id' => $cart->id,
                    'type' => $item['type'],
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'partial_sale' => $item['partial_sale']
                ]);


                $product->quantity -= $newItem->quantity;
                $product->save();
                $cart->base_price += $product->base_price * $newItem->quantity;
            }

            $cart->save();
            DB::commit();

            return $cart;
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
