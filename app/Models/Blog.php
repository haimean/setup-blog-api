<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    public function products()
    {
        return $this->belongsToMany(Product::class, 'map_blog_product', 'blog_id', 'product_id');
    }
}
