<x-mail::message>
# Your week on {{ config('app.name') }}

Hi {{ $user->name }},

Here's a quick snapshot of your watching activity.

**This week:** {{ $digest['stats']['completed'] ?? 0 }} completed · {{ $digest['stats']['hours'] ?? 0 }} hours tracked

@if (! empty($digest['continue']))
## Continue watching

@foreach ($digest['continue'] as $item)
- **{{ $item['title'] ?? 'Untitled' }}**@if(isset($item['progress'])) — {{ $item['progress'] }}%@endif
@endforeach
@endif

<x-mail::button :url="url('/')">
Open Flexter
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
