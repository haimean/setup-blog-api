<?php

namespace Database\Factories;

use App\Models\Blog;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Map_blog_product>
 */
class Map_blog_productFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition()
  {
    return [
      'blog_id' => Blog::inRandomOrder()->first(),
      'product_id' => Product::inRandomOrder()->first(),
    ];
  }
}
