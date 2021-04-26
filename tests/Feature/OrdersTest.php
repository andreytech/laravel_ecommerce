<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\Promo;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrdersTest extends TestCase
{
    use RefreshDatabase;

	/**
	 * A basic feature test example.
	 *
	 * @param Promo $promo
	 * @param int $itemsStockQuantity
	 * @return void
	 */
    public function _createOrder(Promo $promo = null, int $itemsStockQuantity = 100)
    {
        $user = User::factory()->create();
        $products = Product::factory(3)->create(['items_left' => $itemsStockQuantity]);

        $this->assertCount(0, OrderProduct::all());

        $orderService = OrderService::create();

        $order = $orderService->createOrder($user, $promo);

        $orderSubtotalAmount = $products->reduce(
            function ($sum, Product $product) use ($orderService, $order, $itemsStockQuantity) {
                $quantity = rand(1, 99);

                $orderProduct = $orderService->addOrderProduct($order, $product, $quantity);

                // Total amount per Product
                $totalAmount = $quantity * $product->price;

                $this->assertDatabaseHas((new OrderProduct())->getTable(), [
                    'id' => $orderProduct->id,
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'total_amount' => $totalAmount,
                ]);

                $this->assertDatabaseHas((new Product())->getTable(), [
                    'id' => $product->id,
                    'items_left' => $itemsStockQuantity - $quantity,
                ]);

                return $sum + $totalAmount;
            }
        );

		$orderTotalAmount = $orderSubtotalAmount;

        // Correcting Order Total Amount with Promo
        switch ($promo->type) {
            case('amount_off'):
                $orderTotalAmount = $orderTotalAmount - $promo->value;
                break;
            case('percent_off'):
                $orderTotalAmount = $orderTotalAmount - intval(ceil($orderTotalAmount * $promo->value / 100));
                break;
        }
        if($orderTotalAmount < 0) {
            $orderTotalAmount = 0;
        }

        $this->assertDatabaseHas((new Order())->getTable(), [
            'id' => $order->id,
            'user_id' => $user->id,
            'total_amount' => $orderTotalAmount,
            'subtotal_amount' => $orderSubtotalAmount,
            'promo_id' => $promo->getKey(),
        ]);

        $orderProducts = OrderProduct::all();

        $this->assertCount(3, $orderProducts);
    }

    public function testPercentOffPromo()
    {
    	$promo = Promo::factory()->create([
            'type' => 'percent_off',
            'value' => rand(1, 100),
        ]);

        $this->_createOrder($promo);
    }

    public function testAmountOffPromo()
    {
        $promo = Promo::factory()->create([
            'type' => 'amount_off',
            'value' => rand(101, 100000),
        ]);

        $this->_createOrder($promo);
    }

    public function testUnfulfillableQuantityException()
    {
		$promo = Promo::factory()->create([
			'type' => 'amount_off',
			'value' => rand(101, 100000),
		]);

        $errorCode = null;
		try {
            $this->_createOrder($promo, 1);
        }catch (\Exception $e) {
            $errorCode = $e->getCode();
        }
        $this->assertSame(OrderService::ORDER_UNFULFILLABLE_QUANTITY, $errorCode);

    }

}
