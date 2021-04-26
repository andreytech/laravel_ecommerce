<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $users = User::factory(2)->create();
        $products = Product::factory(30)->create();

        $orderService = OrderService::create();
        $order = $orderService->createOrder($users->random());
        $products->random(3)->each(function () use ($order,$orderService,$products) {
            $orderService->addOrderProduct($order, $products->random()->id, rand(1, 99));
        });

    }
}
