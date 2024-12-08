<?php

namespace App\Schedule;

use App\Mail\WeeklyMotivation;
use App\Models\User;
use App\Models\WeeklyPrompt;
use Illuminate\Support\Facades\Mail;
use OpenAI\Laravel\Facades\OpenAI;

class SendMotivation
{
    public function __invoke()
    {
        User::with(['goals' => function ($query) {

            $query->incomplete()->whereDate('updated_at', '<=', now()->subDays(2))->inRandomOrder()->take(3);

        }])->whereHas('goals', function ($query) {

            $query->incomplete()->whereDate('updated_at', '<=', now()->subDays(2));

        })->lazy()->each(function ($user) {
            try {
                $prompt = WeeklyPrompt::where('personality', $user->preferred_personality)->inRandomOrder()->first();

                $response = OpenAI::chat()->create([
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        [
                            'role' => 'assistant',
                            'content' => $prompt->formattedAssistant(),
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt->formattedUser(),
                        ],
                    ],
                    'max_tokens' => 600,

                ]);

                Mail::to($user)->send(new WeeklyMotivation($response->choices[0]->message->content, $user));

            } catch (\Throwable $th) {
                report($th);
            }
        });
    }
}
