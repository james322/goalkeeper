<?php

use App\Jobs\FirstGoalMotivation;
use App\Mail\FirstGoal;
use App\Models\Goal;
use App\Models\Prompt;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Chat\CreateResponse;

it('only processes for users first goal', function () {
    Prompt::factory()->create();
    $user = User::factory()->create();
    $goal1 = Goal::factory()->incomplete()->for($user)->create();
    $goal2 = Goal::factory()->incomplete()->for($user)->create();

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

    dispatch(new FirstGoalMotivation($user, $goal1));
    dispatch(new FirstGoalMotivation($user, $goal2));

    Mail::assertSent(FirstGoal::class, 1);
});
