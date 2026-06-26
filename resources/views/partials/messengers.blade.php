@php
    use Illuminate\Support\Str;

    $ms = $client->messengers();
    $mods = ['vk' => 'vk', 'telegram' => 'tg', 'instagram' => 'ig', 'whatsapp' => 'wa'];
    $labels = ['vk' => 'VK', 'telegram' => 'TG', 'instagram' => 'IG', 'whatsapp' => 'WA'];
    // Пропускаем только http(s)-ссылки — защита от javascript:/data: схем.
    $safe = fn ($u) => Str::startsWith(strtolower(trim((string) $u)), ['http://', 'https://']) ? $u : null;
    $links = collect($ms)->map($safe)->filter();
@endphp
@if ($links->isNotEmpty())
    <span class="messengers">
        @foreach ($links as $type => $url)
            <a href="{{ $url }}" target="_blank" rel="noopener noreferrer"
               class="messenger messenger--{{ $mods[$type] ?? 'vk' }}" title="{{ $labels[$type] ?? $type }}">{{ $labels[$type] ?? $type }}</a>
        @endforeach
    </span>
@else
    <span class="table__muted">—</span>
@endif
