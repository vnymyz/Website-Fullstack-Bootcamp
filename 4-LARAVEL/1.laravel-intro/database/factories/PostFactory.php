<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    protected array $images = [
        'https://images.unsplash.com/photo-1499750310107-5fef28a66643?w=640&q=80',
        'https://images.unsplash.com/photo-1517842645767-c639042777db?w=640&q=80',
        'https://images.unsplash.com/photo-1499951360447-b19be8fe80f5?w=640&q=80',
        'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=640&q=80',
        'https://images.unsplash.com/photo-1519389950473-47ba0277781c?w=640&q=80',
        'https://images.unsplash.com/photo-1488590528505-98d2b5aba04b?w=640&q=80',
        'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=640&q=80',
        'https://images.unsplash.com/photo-1461749280684-dccba630e2f6?w=640&q=80',
    ];

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'body' => fake()->paragraphs(3, true),
            'image' => fake()->randomElement($this->images),
            'user_id' => User::factory(),
        ];
    }
}
