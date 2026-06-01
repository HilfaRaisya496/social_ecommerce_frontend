@props(['variant' => 'default'])

@php
    $variants = [
        'pending' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
        'processed' => 'bg-blue-100 text-blue-700 border-blue-200',
        'shipped' => 'bg-purple-100 text-purple-700 border-purple-200',
        'completed' => 'bg-green-100 text-green-700 border-green-200',
        'cancelled' => 'bg-red-100 text-red-700 border-red-200',
        'active' => 'bg-green-100 text-green-700 border-green-200',
        'inactive' => 'bg-gray-100 text-gray-700 border-gray-200',
        'default' => 'bg-gray-100 text-gray-700 border-gray-200',
    ];
    $class = $variants[$variant] ?? $variants['default'];
@endphp

<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium border {{ $class }}">
    {{ $slot }}
</span>