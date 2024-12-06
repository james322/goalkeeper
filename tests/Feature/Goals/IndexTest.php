<?php

use App\Mail\FirstGoal;
use App\Models\Goal;
use App\Models\Prompt;
use App\Models\User;
use Illuminate\Support\Facades\Queue;
use Livewire\Volt\Volt;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Chat\CreateResponse;

it('user can create a goal', function () {
    $user = User::factory()->create();
    Queue::fake();
    $goalText = 'learn laravel';

    Volt::actingAs($user)->test('pages.goals.index')->set('newGoal', $goalText)->call('createGoal')->assertSee($goalText);

    expect($user->goals->count())->toBe(1);
    expect($user->goals()->first()->intent)->toBe($goalText);
});

it('user cannot create blank goal', function () {
    $user = User::factory()->create();

    Volt::actingAs($user)->test('pages.goals.index')->set('newGoal', '')->call('createGoal')->assertHasErrors('newGoal');
});

it('user cannot create a goal over 500 characters', function () {
    $user = User::factory()->create();

    Volt::actingAs($user)->test('pages.goals.index')->set('newGoal', str()->random(501))->call('createGoal')->assertHasErrors('newGoal');
});

it('user can mark goal as complete', function () {
    $user = User::factory()->create();
    $goal = Goal::factory()->incomplete()->for($user)->create();

    expect($goal->is_complete)->toBeFalsy();

    Volt::actingAs($user)->test('pages.goals.index')->call('completeGoal', $goal->id);

    expect($goal->fresh()->is_complete)->toBeTruthy();
});

it('user can mark goal as incomplete', function () {
    $user = User::factory()->create();
    $goal = Goal::factory()->complete()->for($user)->create();

    expect($goal->is_complete)->toBeTruthy();

    Volt::actingAs($user)->test('pages.goals.index')->call('uncompleteGoal', $goal->id);

    expect($goal->fresh()->is_complete)->toBeFalsy();
});

it('user can delete goal', function () {
    $user = User::factory()->create();
    $goal = Goal::factory()->incomplete()->for($user)->create();

    expect($user->goals()->count())->toBe(1);

    Volt::actingAs($user)->test('pages.goals.index')->call('deleteGoal', $goal->id);

    expect($user->goals()->count())->toBe(0);
});

it('user cannot update another users goals', function () {
    $user = User::factory()->create();
    $stranger = User::factory()->create();

    $goal = Goal::factory()->for($stranger)->create();

    Volt::actingAs($user)->test('pages.goals.index')->call('completeGoal', $goal->id)->assertForbidden();

    Volt::actingAs($user)->test('pages.goals.index')->call('uncompleteGoal', $goal->id)->assertForbidden();
});

it('user cannot delete another users goal', function () {
    $user = User::factory()->create();
    $stranger = User::factory()->create();

    $goal = Goal::factory()->for($stranger)->create();

    Volt::actingAs($user)->test('pages.goals.index')->call('deleteGoal', $goal->id)->assertForbidden();
});

it('users cannot see others goals', function () {
    $user = User::factory()->has(Goal::factory()->state(['intent' => 'user 1 goal']))->create();
    User::factory()->has(Goal::factory()->state(['intent' => 'user 2 goal']))->create();

    Volt::actingAs($user)->test('pages.goals.index')->assertSee('user 1 goal')->assertDontSee('user 2 goal');
});

it('users first goal gets ai motivation email', function () {
    $user = User::factory()->create();
    Prompt::factory()->create();
    Mail::fake();

    OpenAI::fake([CreateResponse::fake([
        'choices' => [
            [
                'text' => 'awesome!',
            ],
        ],
    ]), ]);

    $goalText = 'learn laravel';

    Volt::actingAs($user)->test('pages.goals.index')->set('newGoal', $goalText)->call('createGoal')->assertSee($goalText);

    expect($user->goals->count())->toBe(1);

    Mail::assertSent(FirstGoal::class);
});
