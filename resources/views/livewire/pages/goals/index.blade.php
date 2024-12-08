<?php

use App\Jobs\FirstGoalMotivation;
use App\Models\Goal;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Layout('layouts.app')] class extends Component
{
    use WithPagination;

    #[Validate(['required', 'min:1', 'max:500'])]
    public $newGoal = '';

    #[Url(except: '')]
    public $filter = '';

    #[Computed]
    public function goals()
    {
        return Auth::user()
            ->goals()
            ->when($this->filter == 'completed', function ($query) {
                $query->completed();
            })

            ->when($this->filter == 'incomplete', function ($query) {
                $query->incomplete();
            })
            ->orderByComplete()
            ->paginate(50);
    }

    public function createGoal()
    {
        $this->validate();
        $goal = Auth::user()
            ->goals()
            ->create(['intent' => $this->pull('newGoal')]);
        dispatch(new FirstGoalMotivation(Auth::user(), $goal));
        $this->js("localStorage.removeItem('newGoal')");
        unset($this->goals);
    }

    public function deleteGoal($id)
    {
        $goal = Goal::findOrFail($id);

        $this->authorize('delete', $goal);

        $goal->delete();

        unset($this->goals);
    }

    public function completeGoal($id)
    {
        $goal = Goal::findOrFail($id);

        $this->authorize('update', $goal);

        $goal->is_complete = true;
        $goal->save();

        unset($this->goals);
    }

    public function uncompleteGoal($id)
    {
        $goal = Goal::findOrFail($id);

        $this->authorize('update', $goal);

        $goal->is_complete = false;
        $goal->save();

        unset($this->goals);
    }

    public function showAll()
    {
        $this->filter = '';
        $this->resetPage();
        unset($this->goals);
    }

    public function showCompleted()
    {
        $this->filter = 'completed';
        $this->resetPage();
        unset($this->goals);
    }

    public function showIncomplete()
    {
        $this->filter = 'incomplete';
        $this->resetPage();
        unset($this->goals);
    }
}; ?>

@section('title')
    Goals
@endsection

<div class="mx-auto max-w-xl py-6">
    <div class="flex flex-col">
        <div x-data="goalText">
            <textarea
                @input="handleInput"
                @keydown.ctrl.enter="save"
                @focus="showKeyComboText = true"
                @blur="showKeyComboText = false"
                wire:model="newGoal"
                class="block h-full w-full resize-none overflow-hidden border-0 border-b bg-transparent pb-0 text-gray-800 focus:ring-0 dark:text-gray-200"
                placeholder="What would you like to accomplish?"
                name="new-goal"
            ></textarea>

            <div class="flex items-center justify-between pt-4">
                <div class="text-gray-800 dark:text-gray-200">
                    <span class="hidden" x-show="$wire.newGoal.length">
                        <span x-text="$wire.newGoal.length"></span>
                        /500
                    </span>
                </div>
                <div class="space-x-2">
                    <span
                        x-show="$wire.newGoal.length > 0 && showKeyComboText"
                        class="text-xs text-gray-800/40 dark:text-gray-200/30"
                    >
                        ctrl+enter
                    </span>
                    <x-secondary-button wire:click="createGoal" disabled x-bind:disabled="$wire.newGoal.length <= 0">
                        Save
                    </x-secondary-button>
                </div>
            </div>
        </div>
    </div>

    <div class="pt-6">
        <div class="space-x-4 text-gray-800 dark:text-gray-200">
            <button @class(['opacity-30' => $filter == 'completed' || $filter == 'incomplete']) wire:click="showAll">
                All
            </button>
            <button wire:click="showCompleted" @class(['opacity-30' => $filter != 'completed'])>Completed</button>
            <button wire:click="showIncomplete" @class(['opacity-30' => $filter != 'incomplete'])>Incomplete</button>
        </div>

        @if ($this->goals->hasPages())
            <div class="pt-6">
                {{ $this->goals->links() }}
            </div>
        @endif

        <ul>
            @foreach ($this->goals as $goal)
                <li wire:key="{{ $goal->id . $goal->is_complete }}">
                    <livewire:pages.goals.goal :key="$goal->id . $goal->is_complete" :$goal />
                </li>
            @endforeach
        </ul>

        @if ($this->goals->hasPages())
            <div class="pt-6">
                {{ $this->goals->links() }}
            </div>
        @endif
    </div>

    @script
        <script>
            Alpine.data('goalText', () => {
                return {
                    showKeyComboText: false,
                    limitCharacters() {
                        if ($wire.newGoal.length >= 500) {
                            $wire.newGoal = $wire.newGoal.substring(0, 500);
                        }
                        localStorage.setItem('newGoal', $wire.newGoal);
                    },
                    autoResize(e) {
                        const textarea = e.target;
                        textarea.style.height = 'auto'; // Reset height to auto to calculate the correct scrollHeight
                        textarea.style.height = `${textarea.scrollHeight}px`; // Set height to the scrollHeight
                    },
                    handleInput(e) {
                        this.limitCharacters();
                        this.autoResize(e);
                    },
                    save() {
                        if ($wire.newGoal.length > 500 || $wire.newGoal.length < 1) {
                            return;
                        }
                        $wire.createGoal();
                    },
                };
            });
            Livewire.hook('component.init', ({ component, cleanup }) => {
                if (component.name === 'pages.goals.goal') {
                    if (localStorage.getItem('newGoal')) {
                        $wire.newGoal = localStorage.getItem('newGoal');
                    }
                }
            });
        </script>
    @endscript
</div>
