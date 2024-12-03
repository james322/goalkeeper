<?php

namespace App\Models;

use App\Builders\GoalBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    /** @use HasFactory<\Database\Factories\GoalFactory> */
    use HasFactory;

    public function newEloquentBuilder($query): GoalBuilder
    {
        return new GoalBuilder($query);
    }
}
