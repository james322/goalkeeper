<?php

namespace App\Builders;

use Illuminate\Database\Eloquent\Builder;

class GoalBuilder extends Builder
{
    public function orderByComplete(): Builder
    {
        return $this->orderBy('is_complete')->orderByDesc('updated_at');
    }

    public function completed(): Builder
    {
        return $this->where('is_complete', true);
    }

    public function incomplete(): Builder
    {
        return $this->where('is_complete', false);
    }
}
