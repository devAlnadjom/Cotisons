<?php

use App\Models\User;
use App\Models\Group;
use Inertia\Testing\AssertableInertia as AssertableInertia;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('guests are redirected to the login page', function () {
    $response = $this->get('/dashboard');
    $response->assertRedirect('/login');
});

test('authenticated users can visit the dashboard and receive data', function () {
    $user = User::factory()->create();

    // create some groups for this user so controller has data to return
    Group::factory()->create(['created_by' => $user->id]);
    Group::factory()->create(['created_by' => $user->id]);

    $this->actingAs($user);

    $response = $this->get('/dashboard');
    $response->assertStatus(200)
        ->assertInertia(fn (AssertableInertia $page) =>
            $page->component('Dashboard')
                ->has('summary')
                ->has('created')
                ->has('recent_cotisations')
        );
});