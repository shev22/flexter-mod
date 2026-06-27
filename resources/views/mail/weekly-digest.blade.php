<x-mail::message>
# Your week on {{ config('app.name') }}

Hi {{ $user->name }},

Here's a quick look at your watching activity.

**In progress:** {{ $digest['stats']['in_progress'] ?? 0 }} title(s)  
**Completed:** {{ $digest['stats']['completed'] ?? 0 }} title(s)  
**Estimated hours:** {{ $digest['stats']['hours'] ?? 0 }}h

@if (! empty($digest['continue']))
## Continue watching

@foreach ($digest['continue'] as $item)
- {{ $item['title'] ?? 'Untitled' }}@if (($item['progress'] ?? 0) > 0) ({{ $item['progress'] }}%)@endif
@endforeach
@endif

<x-mail::button :url="url('/')">
Open Flexter
</x-mail::button>

You can turn off these emails in your account settings.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
