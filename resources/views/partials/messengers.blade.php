@php $ms = $client->messengers(); @endphp
@if ($ms)
    <span class="messengers">
        @isset($ms['vk'])<a href="{{ $ms['vk'] }}" target="_blank" rel="noopener" class="messenger messenger--vk" title="VK">VK</a>@endisset
        @isset($ms['telegram'])<a href="{{ $ms['telegram'] }}" target="_blank" rel="noopener" class="messenger messenger--tg" title="Telegram">TG</a>@endisset
        @isset($ms['instagram'])<a href="{{ $ms['instagram'] }}" target="_blank" rel="noopener" class="messenger messenger--ig" title="Instagram">IG</a>@endisset
        @isset($ms['whatsapp'])<a href="{{ $ms['whatsapp'] }}" target="_blank" rel="noopener" class="messenger messenger--wa" title="WhatsApp">WA</a>@endisset
    </span>
@else
    <span class="table__muted">—</span>
@endif
