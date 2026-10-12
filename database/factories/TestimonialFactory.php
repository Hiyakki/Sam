<?php

namespace Database\Factories;

use App\Models\Testimonial;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Testimonial>
 */
class TestimonialFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'role_or_info' => 'Pasien SAM',
            'content' => $this->faker->paragraph(),
            'rating' => 5,
            'status' => 'approved',
            'source' => 'public',
            'is_active' => true,
            'consent_confirmed' => true,
        ];
    }
}
