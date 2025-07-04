<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeatureImage extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function feature()
    {
        return $this->belongsTo(Feature::class, 'feature_id', 'id');
    }
}
