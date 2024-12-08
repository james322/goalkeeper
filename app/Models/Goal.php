<?php

namespace App\Models;

use App\Builders\GoalBuilder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Goal extends Model
{
    /** @use HasFactory<\Database\Factories\GoalFactory> */
    use HasFactory;

    public function newEloquentBuilder($query): GoalBuilder
    {
        return new GoalBuilder($query);
    }

    protected function intent(): Attribute
    {
        return Attribute::make(get: fn ($value) => decrypt($value), set: fn ($value) => encrypt($value));
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
