<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\FastSellingItem;
use App\Models\MedPackage;
use App\Models\OpenedMed;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Throwable;

use function Pest\Laravel\get;

class SalesService
{
    //TODO 
    //! DON'T TAKE TOTAL_RETAIL_PRICE FOR GRANTED
    public function Checkout(array $payload, User $user)
    {
        DB::beginTransaction();

        try {

            if ($payload['payment_method'] === 'cash') {
                $main_vault = $user->pharmacy->vaults()->where('name', '=', 'main')->firstOrFail();
                $main_vault->balance += $payload['total_retail_price'];
                $main_vault->save();
            } elseif ($payload['payment_method'] === 'charity') {
                $charity_vault = $user->pharmacy->vaults()->where('name', '=', 'charity')->firstOrFail();
                if ($charity_vault->balance < $payload['total_retail_price']) {
                    throw new UnprocessableEntityHttpException('There is not enough money in the charity box.');
                }
                $charity_vault->balance -= $payload['total_retail_price'];
                $charity_vault->save();
            }
            //! HANDLE DEPT

            $payment = Payment::create([
                'pharmacy_id' => $user->pharmacy_id,
                'payment_method' => $payload['payment_method'],
                'paid_price' => $payload['total_retail_price']
            ]);

            $cart = Cart::create([
                'total_retail_price' => $payload['total_retail_price'],
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
                    'purchase_price' => $item['purchase_price'],
                    'retail_price' => $item['retail_price'],
                    'partial_sale' => $item['partial_sale']
                ]);

                if ($newItem['partial_sale']) {
                    $opened_package = $this->OpenMed($newItem, $product);
                    //! ITEM QUANTITY TO AVOID COMPLETED MED CASE WHERE newItem->quantity = 1
                    $opened_package->blister_packs -= $item['quantity'];
                    $cart->total_purchase_price += ($product->purchase_price / $product->medication->entities) * $newItem->quantity;
                    $opened_package->save();
                } else {
                    $product->quantity -= $newItem->quantity;
                    $product->save();
                    $cart->total_purchase_price += $product->purchase_price * $newItem->quantity;
                }
            }

            $cart->save();
            DB::commit();

            return $cart;
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }


    public function OpenMed(CartItem $newItem, MedPackage $med_package)
    {
        $required = $newItem['quantity'];

        $opened_package = OpenedMed::where('med_package_id', $newItem['product_id'])->first();

        if (!$opened_package) {
            if ($med_package->quantity <= 0) {
                throw new UnprocessableEntityHttpException("No packages available to open for product {$newItem['product_id']}.");
            }

            $opened_package = OpenedMed::create([
                'med_package_id' => $newItem['product_id'],
                'blister_packs' => $med_package->medication->entities
            ]);

            $med_package->quantity -= 1;
        }

        while ($opened_package->blister_packs < $required) {
            if ($med_package->quantity <= 0) {
                dd($med_package->quantity);
                throw new UnprocessableEntityHttpException("Not enough blister packs for product {$newItem['product_id']}.");
            }

            //! COMPLETED MEDPACKAGE SALE
            $newItem->partial_sale = false;
            $newItem->quantity = 1;
            $newItem->save();

            $opened_package->blister_packs += $med_package->medication->entities;
            $med_package->quantity -= 1;
        }

        $opened_package->save();
        $med_package->save();

        return $opened_package;
    }
}
