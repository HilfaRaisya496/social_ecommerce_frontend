@props(['title', 'value', 'icon', 'trend' => '', 'trendUp' => true, 'iconBgColor', 'iconColor', 'href' => null])

<{{ $href ? 'a href=' . $href : 'div' }} class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 transition-all
    hover:shadow-md block @if($href) hover:-translate-y-1 @endif">
    <div class="flex items-center gap-4">
        <div class="{{ $iconBgColor }} w-14 h-14 rounded-2xl flex items-center justify-center shrink-0">
            <i data-lucide="{{ $icon }}" class="w-7 h-7 {{ $iconColor }}"></i>
        </div>
        <div class="flex-1">
            <div class="flex items-center justify-between">
                <p class="text-[10px] uppercase font-bold text-gray-400 tracking-widest leading-none">{{ $title }}</p>
                @if($trend)
                    <div class="flex items-center gap-1 text-xs {{ $trendUp ? 'text-green-600' : 'text-red-600' }}">
                        <i data-lucide="{{ $trendUp ? 'trending-up' : 'trending-down' }}" class="w-3 h-3"></i>
                        <span class="font-bold">{{ $trend }}</span>
                    </div>
                @endif
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mt-1.5 leading-none">{{ $value }}</h3>
        </div>
    </div>
</{{ $href ? 'a' : 'div' }}>