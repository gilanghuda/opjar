<?php

use App\Models\Agenda;
use App\Models\Role;
use App\Models\User;
use App\Models\Lecturer;
use App\Policies\AgendaPolicy;
use Illuminate\Support\Facades\Artisan;

beforeEach(function () {
    // ensure migrations run for test environment
});

it('allows admin to update any agenda', function () {
    $role = Role::firstOrCreate(['name' => 'admin']);
    $admin = User::factory()->create();
    $admin->roles()->attach($role);

    $agenda = Agenda::create([
        'date' => now()->toDateString(),
        'created_by' => $admin->id,
    ]);

    $policy = new AgendaPolicy();

    expect($policy->update($admin, $agenda))->toBeTrue();
});

it('allows owning lecturer to update their agenda and denies others', function () {
    $lecturerRole = Role::firstOrCreate(['name' => 'lecturer']);

    $lecturerUser = User::factory()->create();
    $lecturerUser->roles()->attach($lecturerRole);

    $lecturer = Lecturer::create([
        'user_id' => $lecturerUser->id,
        'nidn' => '123',
        'phone' => '081234',
        'bio' => 'Test',
    ]);

    $agenda = Agenda::create([
        'date' => now()->toDateString(),
        'created_by' => $lecturerUser->id,
        'lecturer_id' => $lecturer->id,
    ]);

    $policy = new AgendaPolicy();

    expect($policy->update($lecturerUser, $agenda))->toBeTrue();

    $other = User::factory()->create();
    expect($policy->update($other, $agenda))->toBeFalse();
});
