<?php

namespace Database\Seeders;

use Database\Factories\Map_blog_productFactory;
use Illuminate\Database\Seeder;

class Map_blog_productSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    Map_blog_productFactory::times(50)->create();
  }
}
