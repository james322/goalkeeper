<?php

namespace App\Jobs;

use App\Mail\FirstGoal;
use App\Models\Goal;
use App\Models\Prompt;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\Skip;
use Illuminate\Support\Facades\Mail;
use OpenAI\Laravel\Facades\OpenAI;

class FirstGoalMotivation implements ShouldQueue
{
    use Queueable;

    public $user;

    public $goal;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user, Goal $goal)
    {
        $this->user = $user;
        $this->goal = $goal;
    }

    public function middleware(): array
    {
        return [
            Skip::when($this->user->goals()->orderBy('id')->first()->id != $this->goal->id),
        ];
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $prompt = Prompt::where('personality', $this->user->preferred_personality)->inRandomOrder()->first();

        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                [
                    'role' => 'assistant',
                    'content' => $prompt->formattedAssistant(),
                ],
                [
                    'role' => 'user',
                    'content' => $prompt->formattedUser($this->goal->intent),
                ],
            ],
            'max_tokens' => 600,

        ]);

        foreach ($response->choices as $result) {

            Mail::to($this->user)->send(new FirstGoal($result->message->content, $this->user));

        }
    }
}
