<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class EventFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Event::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Generate a random date between -1 month and +1 month
        $startDate = strtotime('-1 month');
        $endDate = strtotime('+1 month');
        $randomTimestamp = mt_rand($startDate, $endDate);
        $startsAt = new \DateTime;
        $startsAt->setTimestamp($randomTimestamp);
        $endsAt = Carbon::instance($startsAt)->addHours(
            rand(1, 72)
        );

        return [
            'name' => 'Event '.uniqid(),
            'active' => (rand(1, 100) <= 70),
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'started_at' => null,
            'ended_at' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }

    /**
     * Indicate that the event is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'active' => true,
        ]);
    }

    /**
     * Indicate that the event is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'active' => false,
        ]);
    }

    /**
     * Indicate that the event is upcoming.
     */
    public function upcoming(): static
    {
        return $this->state(fn (array $attributes) => [
            'starts_at' => Carbon::now()->addDays(rand(1, 30)),
            'ends_at' => Carbon::now()->addDays(rand(31, 60)),
            'started_at' => null,
            'ended_at' => null,
        ]);
    }

    /**
     * Indicate that the event is ongoing.
     */
    public function ongoing(): static
    {
        return $this->state(fn (array $attributes) => [
            'starts_at' => Carbon::now()->subHours(rand(1, 24)),
            'ends_at' => Carbon::now()->addHours(rand(1, 24)),
            'started_at' => Carbon::now()->subHours(rand(1, 24)),
            'ended_at' => null,
            'active' => true,
        ]);
    }

    /**
     * Indicate that the event is past.
     */
    public function past(): static
    {
        $endsAt = Carbon::now()->subDays(rand(1, 30));
        $startsAt = Carbon::instance($endsAt)->subHours(rand(1, 72));

        return $this->state(fn (array $attributes) => [
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'started_at' => $startsAt,
            'ended_at' => $endsAt,
            'active' => false,
        ]);
    }

    /**
     * Indicate that the event has started.
     */
    public function started(): static
    {
        return $this->state(fn (array $attributes) => [
            'started_at' => Carbon::parse($attributes['starts_at']),
            'active' => true,
        ]);
    }

    /**
     * Indicate that the event has ended.
     */
    public function ended(): static
    {
        return $this->state(fn (array $attributes) => [
            'started_at' => Carbon::parse($attributes['starts_at']),
            'ended_at' => Carbon::parse($attributes['ends_at']),
            'active' => false,
        ]);
    }
}
