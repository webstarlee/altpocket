@component('mail::message')
{{-- Greeting --}}
@if (! empty($greeting))
# {{ $greeting }}
@else
@if ($level == 'error')
# Whoops!
@else
# Hello!
@endif
@endif

{{-- Intro Lines --}}
@foreach ($introLines as $line)
<p class="lead">{{ $line }}</p>


@endforeach

{{-- Action Button --}}
@isset($actionText)
<?php
    switch ($level) {
        case 'success':
            $color = 'green';
            break;
        case 'error':
            $color = 'red';
            break;
        default:
            $color = 'blue';
    }
?>
@component('mail::button', ['url' => $actionUrl, 'color' => $color])
{{ $actionText }}
@endcomponent
@endisset

{{-- Outro Lines --}}
@foreach ($outroLines as $line)
<p class="lead">{{ $line }}</p>

@endforeach


<!-- Subcopy -->
@isset($actionText)
@component('mail::subcopy')
<p style="font-size:small;text-align:center;">If you’re having trouble clicking the "{{ $actionText }}" button, copy and paste the URL below
into your web browser: <a href="{{$actionUrl}}" style="color: #545659!important">{{$actionUrl}}</a></p>
@endcomponent
@endisset
@endcomponent
