<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class Post extends Model
{
    use HasFactory;
    use Sluggable;

    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }



    protected $fillable = ['title', 'post_body', 'is_featured', 'post_image'];

    public function users()
    {
        return $this->belongsTo(User::class);
    }

    public function cats()
    {
        return $this->belongsToMany(Cat::class, 'post_cat')->withTimestamps();
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }
}
