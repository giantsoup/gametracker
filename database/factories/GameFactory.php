<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Game>
 */
class GameFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Generate a duration that's a multiple of 15 minutes
        $durationInMinutes = $this->faker->randomElement([15, 30, 45, 60, 90, 120, 180, 240]);

        return [
            'name' => $this->faker->words(2, true),
            'duration' => $durationInMinutes,
            'event_id' => Event::factory(),
        ];
    }

    /**
     * Indicate that the game has a short duration.
     */
    public function shortDuration(): self
    {
        return $this->state(fn (array $attributes) => [
            'duration' => $this->faker->randomElement([15, 30]),
        ]);
    }

    /**
     * Indicate that the game has a long duration.
     */
    public function longDuration(): self
    {
        return $this->state(fn (array $attributes) => [
            'duration' => $this->faker->randomElement([120, 180, 240, 300]),
        ]);
    }
}
