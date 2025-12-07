@props(['tag' => 'button', 'size' => 'md'])

@php
    $sizeClasses = match($size) {
        'sm' => 'px-4 py-2 text-xs',
        'md' => 'px-6 py-3 text-xs',
        'lg' => 'px-8 py-4 text-sm',
        default => 'px-6 py-3 text-xs',
    };
    
    $baseClasses = 'inline-flex items-center bg-slate-800 border border-transparent rounded-md font-semibold text-white uppercase tracking-widest hover:bg-slate-700 focus:bg-slate-700 active:bg-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150';
    $classes = $baseClasses . ' ' . $sizeClasses;
@endphp

@if ($tag === 'a')
    <a {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['type' => 'submit', 'class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif