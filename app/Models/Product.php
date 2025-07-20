<?php

namespace App\Models;

use App\Enums\MediaTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }
    public function media()
    {
        return $this->morphMany(Media::class, 'mediable');
    }
    public function image()
    {
        return $this->morphOne(Media::class, 'mediable')->where('type', MediaTypeEnum::FEATURED);
    }
}
