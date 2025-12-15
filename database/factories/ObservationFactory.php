<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Observation>
 */
class ObservationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $observationType = fake()->randomElement(['met', 'not-met']);
        $discipline = fake()->randomElement(['Mechanical', 'Electrical', 'Civil', 'Operations']);
        $location = fake()->randomElement(['Building A', 'Building B', 'Building C', 'Warehouse']);

        return [
            'title' => $discipline.' - '.$location.' - '.now()->format('M d, Y'),
            'form_data' => [
                'discipline' => $discipline,
                'company' => fake()->company(),
                'location' => $location,
                'observationType' => $observationType,
                'intentionality' => fake()->randomElement(['intentional', 'convenience']),
                'gap' => fake()->sentence(),
                'whyDetails' => fake()->paragraph(),
                'consequence' => $observationType === 'not-met' ? fake()->sentence() : null,
                'impactfulAction' => fake()->sentence(),
                'peerToPeer' => fake()->sentence(),
            ],
            'response' => fake()->optional(0.7)->paragraph(),
            'status' => fake()->randomElement(['draft', 'finalized']),
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
