@props(['color' => 'orange'])

@php
    // Color presets
    $colors = [
        'white' => ['bg' => '#FFF7F1', 'hover' => '#FFD7B5', 'text' => 'text-gray-800'],
        'orange' => ['bg' => '#EC8B18', 'hover' => '#F5A647', 'text' => 'text-white'],
        'gray' => ['bg' => '#EEE9E1', 'hover' => '#D9D4CA', 'text' => 'text-gray-800'],
    ];

    $preset = $colors[$color] ?? $colors['orange'];
@endphp

<div>
    <button 
        {{ $attributes->merge([
            'class' => "
                w-full
                rounded-md
                px-3 py-2
                text-sm font-semibold
                {$preset['text']}
                bg-[{$preset['bg']}]
                hover:bg-[{$preset['hover']}]
                focus:ring-2
                focus:ring-[{$preset['bg']}]
            "
        ]) }}
    >
        {{ $slot }}
    </button>
</div>
