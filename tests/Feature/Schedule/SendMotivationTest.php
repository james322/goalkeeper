<?php

use App\Mail\WeeklyMotivation;
use App\Models\Goal;
use App\Models\User;
use App\Models\WeeklyPrompt;
use App\Schedule\SendMotivation;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Chat\CreateResponse;

it('sends email to users with goal atleast 2 days old', function () {
    User::factory()->has(Goal::factory()->incomplete())->create();
    User::factory()->has(Goal::factory()->incomplete())->create();
    WeeklyPrompt::factory()->create();
    Mail::fake();
    OpenAI::fake([CreateResponse::fake([
        'choices' => [
            [
                'text' => 'awesome!',
            ],
        ],

    ]), CreateResponse::fake([
        'choices' => [
            [
                'text' => 'awesome!',
            ],
        ],

    ]), ]);

    $this->travel(2)->days(function () {
        call_user_func(new SendMotivation);

        Mail::assertSent(WeeklyMotivation::class, 2);
    });

});

it('does not sends email to users with goals less than 2 days old', function () {
    WeeklyPrompt::factory()->create();
    Mail::fake();
    OpenAI::fake([CreateResponse::fake([
        'choices' => [
            [
                'text' => 'awesome!',
            ],
        ],

    ])]);

    $this->travel(1)->days(function () {
        User::factory()->has(Goal::factory()->incomplete())->create();
        call_user_func(new SendMotivation);

        Mail::assertSent(WeeklyMotivation::class, 0);
    });

});
