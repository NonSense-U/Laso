<?php

namespace App\Services;

use App\Helpers\vaultsHelper;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\FastSellingItem;
use App\Models\Insurance;
use App\Models\InsuranceRecords;
use App\Models\MedPackage;
use App\Models\OpenedMed;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Exceptions\MathException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Throwable;

use function Pest\Laravel\get;
use function PHPSTORM_META\map;

class SalesService
{
    //TODO 
    //! DON'T TAKE TOTAL_RETAIL_PRICE FOR GRANTED
    public function Checkout(array $payload, User $user)
    {
        DB::beginTransaction();

        try {
            //* DONE    TODO don't trust total_retail_price
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

            $redisKeys = $medPackages->mapWithKeys(fn($med) => [$med->id => $med->medication_id . '_medication_price'])->all();
            $values = Redis::mget(array_values($redisKeys));
            $prices = collect($redisKeys)->keys()->combine($values)->all();
            $actual_total_retail_price = 0;

            foreach ($payload['items'] as $item) {

                if ($item['type'] == 'med_package') {
                    $product = $medPackages->get($item['product_id']);
                    $price = $prices[$product->id];
                    if ($item['partial_sale']) {
                        $price = ($price / $product->medication->entities) * $item['quantity'];
                    }
                    $actual_total_retail_price += $price * $item['quantity'];
                } else {
                    $product = $fastSellingItems->get($item['product_id']);
                    $actual_total_retail_price += $product->retail_price * $item['quantity'];
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
                    'retail_price' => $price,
                    'partial_sale' => $item['partial_sale']
                ]);

                if ($newItem['partial_sale']) {
                    $opened_package = $this->OpenMed($newItem, $product);
                    //! ITEM QUANTITY TO AVOID COMPLETED MED CASE WHERE newItem->quantity = 1
                    $opened_package->blister_packs -= $item['quantity'];
                    $cart->total_purchase_price += ($product->purchase_price / $product->medication->entities) * $item['quantity'];
                    $opened_package->save();
                } else {
                    $product->quantity -= $newItem->quantity;
                    $product->save();
                    $cart->total_purchase_price += $product->purchase_price * $newItem->quantity;
                }
            }

            if (isset($payload['insurance'])) {
                /** @var App\Models\Patient $patient */
                $patient = Patient::findOrFail($payload['insurance']['patient_id']);
                $insurance = $patient->insurance;
                if (!$insurance) {
                    throw new NotFoundHttpException("patient {$patient->full_name} doesn't have a registered insurance.");
                }
                $discounted_amount = round(($insurance->discount_percentage * $actual_total_retail_price) / 100, 2);
                InsuranceRecords::create([
                    'insurance_id' => $patient->insurance->id,
                    'cart_id' => $cart->id,
                    'discounted_amount' => $discounted_amount
                ]);
                $actual_total_retail_price = $actual_total_retail_price - $discounted_amount;
            }

            $actual_total_retail_price = round($actual_total_retail_price, 2);
            if ($cart->total_retail_price != $actual_total_retail_price) {
                throw new MathException("sum of cart items prices does not amount to total cart price {$actual_total_retail_price}");
            }
            $cart->save();

            //* DONE TODO HANDLE DEBT            
            $main_vault = vaultsHelper::getMain($user);

            if ($payload['payment_method'] === 'cash') {
                $main_vault->balance += $actual_total_retail_price;
            } elseif ($payload['payment_method'] === 'charity') {
                $charity_vault = vaultsHelper::getCharity($user);
                if ($charity_vault->balance < $actual_total_retail_price) {
                    throw new UnprocessableEntityHttpException('There is not enough money in the charity box.');
                }
                $charity_vault->balance -= $actual_total_retail_price;
                $main_vault->balance += $actual_total_retail_price;
                $charity_vault->save();
            } elseif ($payload['payment_method'] === 'debt') {
                PatientService::addDebt($cart->id, $payload['patient_id'], $user->pharmacy->id);
            }

            $main_vault->save();

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

    public function customerReturnMeds(array $payload, User $user)
    {
        /** @var app/models/MedPackage */
        $med_package = MedPackage::where('medication_id', $payload['medication_id'])
            ->where('expiration_date', $payload['expiration_date'])
            ->firstOrFail();

        $med_package->quantity += $payload['return_quantity'];
        $med_package->save();

        $main_vault = vaultsHelper::getMain($user);
        $main_vault->balance -= $payload['return_quantity'] * $med_package->medication->retail_price;
        $main_vault->save();
    }
}
