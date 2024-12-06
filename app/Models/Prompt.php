<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prompt extends Model
{
    /** @use HasFactory<\Database\Factories\PromptFactory> */
    use HasFactory;

    public function formattedAssistant(): string
    {
        return str_replace('%personality%', $this->personality, $this->assistant);
    }

    public function formattedUser(string $goal): string
    {
        return str_replace('%goal%', $goal, $this->user);
    }
}
