<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Post extends Model
{
    protected $fillable = ['title', 'body', 'image', 'user_id'];

    protected static function booted(): void
    {
        static::creating(function (Post $post) {
            $post->slug = static::generateUniqueSlug($post->title);
        });
    }

    protected static function generateUniqueSlug(string $title): string
    {
        $slug = Str::slug($title);
        $original = $slug;
        $count = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = $original . '-' . $count;
            $count++;
        }

        return $slug;
    }

    public function imageUrl(): ?string
    {
        if (!$this->image) {
            return null;
        }

        return Str::startsWith($this->image, ['http://', 'https://'])
            ? $this->image
            : asset('storage/' . $this->image);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}