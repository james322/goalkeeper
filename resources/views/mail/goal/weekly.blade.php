<x-mail::message>
Hi {{$user->name}}, you have some goals that haven't been completed. Have you made any progress on the following goals?

@foreach($user->goals as $goal)
- {{$goal->intent}}

@endforeach

{{$motivation}}

<x-mail::button :url="route('goals.index')">
Your goals
</x-mail::button>

@include('mail.goal.footer')
</x-mail::message>
