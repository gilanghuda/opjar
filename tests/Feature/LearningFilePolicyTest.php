<?php

use App\Models\LearningFile;
use App\Models\Role;
use App\Models\User;
use App\Policies\LearningFilePolicy;

it('allows admin to update any learning file', function () {
    $role = Role::firstOrCreate(['name' => 'admin']);
    $admin = User::factory()->create();
    $admin->roles()->attach($role);

    $file = LearningFile::create([
        'title' => 'Test File',
        'original_name' => 'test.txt',
        'filename' => 'test.txt',
        'path' => 'learning_files/test.txt',
        'mimetype' => 'text/plain',
        'size' => 10,
        'uploaded_by' => $admin->id,
    ]);

    $policy = new LearningFilePolicy();

    expect($policy->update($admin, $file))->toBeTrue();
});

it('allows uploader to update their file and denies others', function () {
    $uploader = User::factory()->create();
    $file = LearningFile::create([
        'title' => 'My File',
        'original_name' => 'my.txt',
        'filename' => 'my.txt',
        'path' => 'learning_files/my.txt',
        'mimetype' => 'text/plain',
        'size' => 20,
        'uploaded_by' => $uploader->id,
    ]);

    $policy = new LearningFilePolicy();

    expect($policy->update($uploader, $file))->toBeTrue();

    $other = User::factory()->create();
    expect($policy->update($other, $file))->toBeFalse();
});
