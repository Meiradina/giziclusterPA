<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodImage extends Model
{
    protected $table = 'food_images';

    protected $fillable = [
        'nama_makanan',
        'gambar'
    ];
}