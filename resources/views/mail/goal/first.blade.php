<x-mail::message>
Hi {{$user->name}}, congratulations on making your first goal with GoalKeeper! Here is some motivation for completing your goal.

{{$motivation}}

<x-mail::button :url="route('goals.index')">
Your goals
</x-mail::button>

@include('mail.goal.footer')
</x-mail::message>
