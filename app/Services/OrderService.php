<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\Promo;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public const ORDER_UNFULFILLABLE_QUANTITY = 560;

    public static function create(): self
    {
        return resolve(self::class);
    }

    public function createOrder(User $user = null, Promo $promo = null): Order
    {
        $order = new Order();
        if($user) {
            $order->user_id = $user->getKey();
        }
        $order->total_amount = 0;
        if($promo) {
            $order->promo_id = $promo->getKey();
        }
        $order->save();

        return $order;
    }

    public function addOrderProduct(Order $order, Product $product, int $quantity): OrderProduct
    {
        DB::beginTransaction();

        try {
            if ($product->items_left !== null) {
                if ($product->items_left < $quantity) {
                    throw new \Exception('Unfulfillable quantity', self::ORDER_UNFULFILLABLE_QUANTITY);
                }

                $product->items_left = $product->items_left - $quantity;
                $product->save();
            }

            $orderProduct = new OrderProduct();
            $orderProduct->order_id = $order->getKey();
            $orderProduct->product_id = $product->getKey();
            $orderProduct->quantity = $quantity;
            $orderProduct->total_amount = $product->price * $quantity;
            $orderProduct->save();

            $this->updateOrderTotals($order);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        DB::commit();

        return $orderProduct;
    }

    public function updateOrderTotals(Order $order): void
    {
        $orderSubtotal = $order->orderProducts()->get()->reduce(function ($sum, $orderProduct) {
        	//var_dump($orderProduct);
            return $sum + $orderProduct->total_amount;
        }, 0);

        $orderTotal = 0;

        if ($order->promo) {
            switch ($order->promo->type) {
                case ('amount_off'):
                    $orderTotal = $orderSubtotal - $order->promo->value;
                    break;
                case ('percent_off'):
                    $orderTotal = $orderSubtotal -
                        ceil($orderSubtotal * $order->promo->value / 100);
                    break;
                default:
                    throw new Exception('Unsupported promo type', 404);
            }
        }

        if($orderTotal < 0) {
            $orderTotal = 0;
        }

        $order->total_amount = $orderTotal;
        $order->subtotal_amount = $orderSubtotal;
        $order->save();
    }
}
