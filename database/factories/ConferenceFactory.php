<?php

namespace Database\Factories;

use App\Enums\Region;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Conference;
use App\Models\Venue;

class ConferenceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Conference::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $startDate = now()->addMonths($this->faker->numberBetween(1, 12));
        $endDate = $startDate->copy()->addDays($this->faker->numberBetween(1, 5));

        return [
            'name' => $this->faker->name(),
            'description' => $this->faker->text(),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => $this->faker->randomElement(['draft', 'published', 'archived']),
            'region' => $this->faker->randomElement(Region::class),
            'venue_id' => null,
        ];
    }
}
