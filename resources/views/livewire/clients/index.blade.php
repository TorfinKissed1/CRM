@php use App\Support\Crm; @endphp
<div>
    <div class="toolbar">
        <div class="search">
            <span class="search__icon">@include('partials.icons', ['name' => 'search'])</span>
            <input type="text" class="search__input" placeholder="Поиск по имени, телефону, городу…" wire:model.live.debounce.350ms="search">
        </div>

        <select class="field__select" style="width:auto" wire:model.live="staffFilter">
            <option value="">Все {{ mb_strtolower(Crm::label('staff')) }}</option>
            @foreach ($staffList as $s)
                <option value="{{ $s->id }}">{{ $s->name }}</option>
            @endforeach
        </select>

        <div class="toolbar__spacer"></div>

        <a href="{{ route('clients.import') }}" wire:navigate class="btn btn--ghost btn--sm">
            @include('partials.icons', ['name' => 'upload']) Импорт
        </a>
        <button class="btn btn--ghost btn--sm" wire:click="export">
            @include('partials.icons', ['name' => 'download']) Экспорт
        </button>
        <button class="btn btn--primary btn--sm" wire:click="create">
            @include('partials.icons', ['name' => 'plus']) Добавить
        </button>
    </div>

    <div class="card card--flush">
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ Crm::label('client') }}</th>
                        <th>Телефон</th>
                        <th>Мессенджеры</th>
                        <th>Город</th>
                        <th>{{ Crm::label('staff_singular') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($clients as $client)
                        <tr wire:key="client-{{ $client->id }}">
                            <td><span class="table__name">{{ $client->name }}</span></td>
                            <td>
                                @if ($client->phone)
                                    <a href="tel:{{ $client->phone }}">{{ $client->phone }}</a>
                                @else
                                    <span class="table__muted">—</span>
                                @endif
                            </td>
                            <td>@include('partials.messengers', ['client' => $client])</td>
                            <td>{{ $client->city ?: '—' }}</td>
                            <td>{{ $client->preferredStaff?->name ?: '—' }}</td>
                            <td>
                                <div class="table__actions">
                                    <button class="btn btn--ghost btn--sm" wire:click="edit({{ $client->id }})">Правка</button>
                                    <button class="btn btn--ghost btn--sm" wire:click="delete({{ $client->id }})"
                                            wire:confirm="Удалить «{{ $client->name }}»?">Удалить</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty">
                                    <div class="empty__title">{{ mb_strtolower(Crm::label('clients')) }} пока нет</div>
                                    <p>Добавьте вручную или импортируйте из xlsx/csv.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($clients->hasPages())
        <div class="pager">
            <button class="btn btn--ghost btn--sm" wire:click="previousPage" @disabled($clients->onFirstPage())>Назад</button>
            <span class="pager__info">{{ $clients->currentPage() }} / {{ $clients->lastPage() }} · {{ $clients->total() }}</span>
            <button class="btn btn--ghost btn--sm" wire:click="nextPage" @disabled(! $clients->hasMorePages())>Вперёд</button>
        </div>
    @endif

    @if ($showForm)
        <div class="modal">
            <div class="modal__overlay" wire:click="$set('showForm', false)"></div>
            <div class="modal__dialog">
                <div class="modal__head">
                    <h2 class="modal__title">{{ $editingId ? 'Правка' : 'Новый' }} {{ mb_strtolower(Crm::label('client')) }}</h2>
                    <button class="modal__close" wire:click="$set('showForm', false)">&times;</button>
                </div>
                <form wire:submit="save">
                    <div class="modal__body">
                        <div class="form">
                            <label class="field">
                                <span class="field__label">Имя *</span>
                                <input type="text" class="field__input" wire:model="form.name">
                                @error('form.name') <span class="field__error">{{ $message }}</span> @enderror
                            </label>
                            <div class="field-row">
                                <label class="field">
                                    <span class="field__label">Телефон</span>
                                    <input type="text" class="field__input" wire:model="form.phone">
                                </label>
                                <label class="field">
                                    <span class="field__label">Город</span>
                                    <input type="text" class="field__input" wire:model="form.city">
                                </label>
                            </div>
                            <div class="field-row">
                                <label class="field">
                                    <span class="field__label">VK</span>
                                    <input type="text" class="field__input" wire:model="form.vk">
                                </label>
                                <label class="field">
                                    <span class="field__label">Telegram</span>
                                    <input type="text" class="field__input" wire:model="form.telegram">
                                </label>
                            </div>
                            <div class="field-row">
                                <label class="field">
                                    <span class="field__label">Instagram</span>
                                    <input type="text" class="field__input" wire:model="form.instagram">
                                </label>
                                <label class="field">
                                    <span class="field__label">WhatsApp</span>
                                    <input type="text" class="field__input" wire:model="form.whatsapp">
                                </label>
                            </div>
                            <label class="field">
                                <span class="field__label">{{ Crm::label('staff_singular') }}</span>
                                <select class="field__select" wire:model="form.preferred_staff_id">
                                    <option value="">— не выбран —</option>
                                    @foreach ($staffList as $s)
                                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <label class="field">
                                <span class="field__label">Заметки</span>
                                <textarea class="field__textarea" wire:model="form.notes"></textarea>
                            </label>
                        </div>
                    </div>
                    <div class="modal__foot">
                        <button type="button" class="btn btn--ghost" wire:click="$set('showForm', false)">Отмена</button>
                        <button type="submit" class="btn btn--primary">Сохранить</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
