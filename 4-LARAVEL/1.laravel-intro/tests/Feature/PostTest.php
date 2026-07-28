<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

use Tests\TestCase;
use App\Models\Post;
use App\Models\User;

class PostTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_tidak_bisa_akses_halaman_posts(): void
    {
        $response = $this->get('/posts');

        $response->assertRedirect('/login');
    }

    public function test_user_login_bisa_liat_daftar_post(): void
    {
        $user = User::factory()->create();
        Post::factory(3)->create();

        $response = $this->actingAs($user)->get('/posts');

        $response->assertStatus(200);
    }

    public function test_user_bisa_bikin_post_baru(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/posts', [
            'title' => 'Judul Test',
            'body' => 'Isi body buat testing.',
        ]);

        $response->assertRedirect('/posts');
        $this->assertDatabaseHas('posts', [
            'title' => 'Judul Test',
            'user_id' => $user->id,
        ]);
    }

    public function test_user_gak_bisa_edit_post_orang_lain(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($otherUser)->put("/posts/{$post->id}", [
            'title' => 'Judul Diubah Paksa',
            'body' => 'Coba edit punya orang.',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseHas('posts', ['title' => $post->title]);
    }

    public function test_admin_bisa_edit_post_siapa_aja(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $post = Post::factory()->create();

        $response = $this->actingAs($admin)->put("/posts/{$post->id}", [
            'title' => 'Diedit Admin',
            'body' => 'Admin boleh edit post siapa aja.',
        ]);

        $response->assertRedirect('/posts');
        $this->assertDatabaseHas('posts', ['title' => 'Diedit Admin']);
    }
}
