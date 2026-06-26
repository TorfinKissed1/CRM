@php use App\Support\Crm; @endphp
<div class="stack">
    <div class="toolbar">
        <a href="{{ route('clients') }}" wire:navigate class="btn btn--ghost btn--sm">← Назад к {{ mb_strtolower(Crm::label('clients')) }}</a>
    </div>

    @if ($result)
        <div class="flash">
            Импорт завершён: добавлено {{ $result['imported'] }}, обновлено {{ $result['updated'] }}, пропущено {{ $result['skipped'] }}.
        </div>
        <div class="toolbar">
            <a href="{{ route('clients') }}" wire:navigate class="btn btn--primary">Открыть {{ mb_strtolower(Crm::label('clients')) }}</a>
            <button class="btn btn--ghost" wire:click="$set('result', null)">Импортировать ещё</button>
        </div>
    @else
        <div class="card">
            <div class="card__head"><h2 class="card__title">Импорт из xlsx / csv</h2></div>
            <div class="card__body">
                <div class="upload">
                    <input type="file" wire:model="file" accept=".xlsx,.csv,.txt">
                    <div wire:loading wire:target="file" class="muted">Читаю файл…</div>
                    @error('file') <span class="field__error">{{ $message }}</span> @enderror
                    <p class="field__hint">Поддерживаются .xlsx и .csv (разделитель определяется автоматически). Первая строка — заголовки.</p>
                </div>
            </div>
        </div>

        @if ($headers)
            <div class="card">
                <div class="card__head"><h2 class="card__title">Сопоставление колонок</h2></div>
                <div class="card__body">
                    <div class="map">
                        @foreach ($fields as $field => $label)
                            <div class="map__row">
                                <span class="map__field">{{ $label }}</span>
                                <select class="field__select" wire:model="map.{{ $field }}">
                                    <option value="">— не импортировать —</option>
                                    @foreach ($headers as $header)
                                        <option value="{{ $header }}">{{ $header }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endforeach
                    </div>

                    <div class="map__dedup">
                        <span class="field__label">Если телефон уже есть:</span>
                        <label class="checkbox"><input type="radio" value="skip" wire:model="dedup" class="checkbox__input"> <span class="checkbox__label">Пропустить</span></label>
                        <label class="checkbox"><input type="radio" value="update" wire:model="dedup" class="checkbox__input"> <span class="checkbox__label">Обновить</span></label>
                    </div>
                </div>
                <div class="modal__foot">
                    <button class="btn btn--primary" wire:click="import" wire:loading.attr="disabled" wire:target="import">
                        <span wire:loading.remove wire:target="import">Импортировать</span>
                        <span wire:loading wire:target="import">Импортирую…</span>
                    </button>
                </div>
            </div>

            @if ($sample)
                <div class="card card--flush">
                    <div class="card__head"><h2 class="card__title">Предпросмотр</h2></div>
                    <div class="table-wrap">
                        <table class="table">
                            <thead>
                                <tr>@foreach ($headers as $h)<th>{{ $h }}</th>@endforeach</tr>
                            </thead>
                            <tbody>
                                @foreach ($sample as $row)
                                    <tr>@foreach ($headers as $i => $h)<td>{{ $row[$i] ?? '' }}</td>@endforeach</tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        @endif
    @endif
</div>
