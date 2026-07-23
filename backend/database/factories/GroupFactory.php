<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Str;

/**
 * @extends Factory<Group>
 */
class GroupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "name" => fake()->name(),
            "contribution_amount" => fake()->randomFloat(2, 10, 5000),
            "invite_code" => Str::random(6),
            "user_id" => User::factory(),
            "start_date" => fake()->dateTimeBetween("now", "10 days")
        ];
    }
}
