<?php

use App\Models\User;
use function Pest\Laravel\actingAs;

it('allows only administrator users to access dashboard page', function () {
    $user = User::factory()->create(['is_admin' => false]);
    $admin = User::factory()->create(['is_admin' => true]);

    actingAs($user)
        ->get('/dashboard')
        ->assertStatus(403);

    actingAs($admin)
        ->get('/dashboard')
        ->assertStatus(200);
});
