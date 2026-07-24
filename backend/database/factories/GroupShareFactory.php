<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\GroupShare;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GroupShare>
 */
class GroupShareFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
           "requested_at" => now(),
           "group_id" => Group::factory(),
           "user_id" => User::factory()
        ];
    }
}
