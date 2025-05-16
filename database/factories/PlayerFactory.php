<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Player>
 */
class PlayerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'event_id' => Event::factory(),
            'nickname' => $this->faker->optional()->userName(),
            'joined_at' => $this->faker->optional()->dateTimeBetween('-1 month', 'now'),
            'left_at' => $this->faker->optional()->dateTimeBetween('now', '+1 month'),
        ];
    }

    /**
     * Indicate that the player has joined the event.
     */
    public function joined(): self
    {
        return $this->state(fn (array $attributes) => [
            'joined_at' => now()->subMinutes($this->faker->numberBetween(1, 60)),
        ]);
    }

    /**
     * Indicate that the player has left the event.
     */
    public function left(): self
    {
        return $this->state(fn (array $attributes) => [
            'joined_at' => now()->subHours($this->faker->numberBetween(1, 24)),
            'left_at' => now(),
        ]);
    }
}
