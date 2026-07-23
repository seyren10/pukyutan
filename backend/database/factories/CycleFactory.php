<?php

namespace Database\Factories;

use App\Models\Cycle;
use App\Models\Group;
use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Cycle>
 */
class CycleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "due_date" => fake()->dateTimeBetween("now", "10 days"),
            "group_id" => Group::factory(),
            "recipient_member_id" => Member::factory(),
            "round_number" => fake()->unique()->numberBetween(1,30_000),
            "cycle_number" => fake()->unique()->numberBetween(1,30_000)
        ];
    }
}
