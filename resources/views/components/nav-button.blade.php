@props(['href' => '#'])

<div>
    <a 
        href="{{ $href }}"
        {{ $attributes->merge([
            'class' => '
                w-full
                inline-block
                rounded-md
                px-6 py-2
                text-sm font-semibold
                text-gray-800
                text-center
                bg-[#FFF7F1]
                hover:bg-[#F0E8DF]  
                focus:ring-2
                focus:ring-[#FFF7F1]
            '
        ]) }}
    >
        {{ $slot }}
    </a>
</div>
