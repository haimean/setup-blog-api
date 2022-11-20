<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Blog>
 */
class BlogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'tile' => $this->faker->name(),
            'image_uuid' => $this->faker->image(storage_path('images'), 300, 300),
            'description' => $this->faker->text(20),
            'url' => $this->faker->url(),
        ];
    }
}
