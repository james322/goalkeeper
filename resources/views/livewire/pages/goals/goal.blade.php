<?php

use App\Models\Goal;
use Livewire\Volt\Component;

new class extends Component
{
    public Goal $goal;
}; ?>

<div>
    <div class="flex items-center justify-between border-b border-dashed border-gray-700 py-6">
        <h2 @class(['opacity-30' => $goal->is_complete, 'text-2xl text-gray-800 dark:text-gray-200'])>
            {{ $goal->intent }}
        </h2>

        <div class="flex shrink-0 items-center space-x-4">
            <button
                wire:confirm="Are you sure you want to delete {{ substr($goal->intent, 0, 20) }}...? "
                wire:click="$parent.deleteGoal({{ $goal->id }})"
            >
                <x-trash-icon />
            </button>

            @if ($goal->is_complete)
                <button wire:click="$parent.uncompleteGoal({{ $goal->id }})">
                    <x-checkmark-icon :complete="$goal->is_complete" />
                </button>
            @else
                <button wire:click="$parent.completeGoal({{ $goal->id }})">
                    <x-checkmark-icon :complete="$goal->is_complete" />
                </button>
            @endif
        </div>
    </div>
</div>
