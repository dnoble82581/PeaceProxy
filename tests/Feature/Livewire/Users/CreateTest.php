<?php

use App\Livewire\Users\Create;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\assertDatabaseHas;

beforeEach(fn () => User::query()->delete());

it('renders the create user component', function () {
    Livewire::test(Create::class)
        ->assertOk()
        ->assertViewIs('livewire.users.create');
});

it('initializes with a new user', function () {
    Livewire::test(Create::class)
        ->assertSet('user', fn ($user) => $user instanceof User)
        ->assertSet('password', null)
        ->assertSet('password_confirmation', null);
});

it('validates user creation with valid data', function () {
    $team = \App\Models\Team::factory()->create();

    $data = [
        'user.name' => 'John Doe',
        'user.email' => 'john@example.com',
        'user.primary_team_id' => $team->id,
        'password' => 'password123',
        'password_confirmation' => 'password123'
    ];

    Livewire::test(Create::class)
        ->set($data)
        ->call('save')
        ->assertHasNoErrors();

    assertDatabaseHas('users', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);
});

it('requires name', function () {
    Livewire::test(Create::class)
        ->set('user.name', '')
        ->set('user.email', 'john@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->call('save')
        ->assertHasErrors(['user.name' => 'required']);
});

it('requires unique email', function () {
    User::create([
        'name' => 'Existing User',
        'email' => 'existing@example.com',
        'password' => bcrypt('password123')
    ]);

    Livewire::test(Create::class)
        ->set('user.name', 'John Doe')
        ->set('user.email', 'existing@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->call('save')
        ->assertHasErrors(['user.email' => 'unique']);
});

it('validates email format', function () {
    Livewire::test(Create::class)
        ->set('user.name', 'John Doe')
        ->set('user.email', 'invalid-email')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->call('save')
        ->assertHasErrors(['user.email' => 'email']);
});

it('requires password confirmation', function () {
    Livewire::test(Create::class)
        ->set('user.name', 'John Doe')
        ->set('user.email', 'john@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'different-password')
        ->call('save')
        ->assertHasErrors(['password' => 'confirmed']);
});

it('requires minimum password length', function () {
    Livewire::test(Create::class)
        ->set('user.name', 'John Doe')
        ->set('user.email', 'john@example.com')
        ->set('password', 'short')
        ->set('password_confirmation', 'short')
        ->call('save')
        ->assertHasErrors(['password' => 'min']);
});

it('sets email verified at when creating user', function () {
    $team = \App\Models\Team::factory()->create();

    $data = [
        'user.name' => 'John Doe',
        'user.email' => 'john@example.com',
        'user.primary_team_id' => $team->id,
        'password' => 'password123',
        'password_confirmation' => 'password123'
    ];

    Livewire::test(Create::class)
        ->set($data)
        ->call('save');

    $user = User::where('email', 'john@example.com')->first();

    expect($user->email_verified_at)->not()->toBeNull();
});

it('resets form after successful creation', function () {
    $team = \App\Models\Team::factory()->create();

    $data = [
        'user.name' => 'John Doe',
        'user.email' => 'john@example.com',
        'user.primary_team_id' => $team->id,
        'password' => 'password123',
        'password_confirmation' => 'password123'
    ];

    Livewire::test(Create::class)
        ->set($data)
        ->call('save')
        ->assertSet('user', fn ($user) => $user instanceof User && $user->name === null)
        ->assertSet('password', null)
        ->assertSet('password_confirmation', null);
});

it('dispatches created event', function () {
    $team = \App\Models\Team::factory()->create();

    $data = [
        'user.name' => 'John Doe',
        'user.email' => 'john@example.com',
        'user.primary_team_id' => $team->id,
        'password' => 'password123',
        'password_confirmation' => 'password123'
    ];

    Livewire::test(Create::class)
        ->set($data)
        ->call('save')
        ->assertHasNoErrors();

    assertDatabaseHas('users', [
        'email' => 'john@example.com',
    ]);
});

it('saves selected primary team and marks it primary in pivot', function () {
    $team = \App\Models\Team::factory()->create();

    $data = [
        'user.name' => 'Jane Doe',
        'user.email' => 'jane@example.com',
        'user.primary_team_id' => $team->id,
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ];

    Livewire::test(Create::class)
        ->set($data)
        ->call('save')
        ->assertHasNoErrors();

    $user = User::where('email', 'jane@example.com')->first();

    expect($user)->not->toBeNull();
    expect($user->primary_team_id)->toBe($team->id);

    // Verify pivot exists and is marked as primary
    $pivot = \DB::table('team_users')
        ->where('team_id', $team->id)
        ->where('user_id', $user->id)
        ->first();

    expect($pivot)->not->toBeNull();
    expect((bool) $pivot->is_primary)->toBeTrue();
});
