<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $appends = ["full_path"];
    public $timestamps = false;
    public function mediable()
    {
        return $this->morphTo();
    }

    public function getFullPathAttribute()
    {
        return $this->path ? asset('storage/' . $this->path) : null;
    }
}
