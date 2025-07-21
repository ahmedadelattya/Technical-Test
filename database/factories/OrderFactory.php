<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Status;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'assigned_to' => User::factory(),
            'status_id' => $this->faker->randomElement([1, 2, 3]),
            'total_amount' => 0,
            'shipping_address' => $this->faker->address(),
            'phone_number' => $this->faker->phoneNumber(),
        ];
    }
}
