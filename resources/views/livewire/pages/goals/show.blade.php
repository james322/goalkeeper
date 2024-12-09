<?php

use App\Models\Goal;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component {
    public Goal $goal;
}; ?>

<div>
    {{ $goal->intent }}
</div>
