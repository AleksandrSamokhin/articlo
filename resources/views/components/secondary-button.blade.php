@props(['tag' => 'button', 'size' => 'md'])

@php
    $sizeClasses = match($size) {
        'sm' => 'px-4 py-2 text-xs',
        'md' => 'px-6 py-3 text-xs',
        'lg' => 'px-8 py-4 text-sm',
        default => 'px-6 py-3 text-xs',
    };

    $baseClasses = 'inline-flex items-center bg-white border border-slate-300 rounded-md font-semibold text-slate-700 uppercase tracking-widest shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150';
    $classes = $baseClasses . ' ' . $sizeClasses;
@endphp

@if ($tag === 'a')
    <a {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
<button {{ $attributes->merge(['type' => 'button', 'class' => $classes]) }}>
    {{ $slot }}
</button>
@endif