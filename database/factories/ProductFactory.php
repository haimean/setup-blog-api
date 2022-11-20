<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
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
            'category_id' => Category::inRandomOrder()->first(),
            'image_uuid' => $this->faker->image(storage_path('images'), 300, 300),
            'url' => $this->faker->url(),
        ];
    }
}
