<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function images()
    {
        return $this->hasMany(CategoryImage::class, 'catgeory_id', 'id');
    }

    public function product()
    {
        return $this->hasMany(Room::class, 'id', 'category');
    }
}
