<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $employees = User::factory(3)->create()->each(function ($employee) {
            $employee->assignRole('employee');
        });
        $customers = User::factory(10)->create();

        $products = Product::all();

        Order::factory(30)->make()->each(function ($order) use ($employees, $customers, $products) {
            $order->user_id = $customers->random()->id;
            $order->assigned_to = $employees->random()->id;
            $order->save();

            $total = 0;
            $availableProducts = $products->filter(fn($p) => $p->stock_quantity > 0)->shuffle();

            foreach ($availableProducts->take(rand(1, 5)) as $product) {
                if ($product->stock_quantity < 1) continue;

                $quantity = rand(1, $product->stock_quantity);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price_at_purchase' => $product->price,
                ]);

                $product->decrement('stock_quantity', $quantity);
                $total += $product->price * $quantity;
            }

            $order->update(['total_amount' => $total]);
        });
    }
}
