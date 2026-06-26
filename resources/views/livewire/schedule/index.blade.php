@php use App\Support\Crm; @endphp
<div class="schedule">
    <div class="schedule__bar">
        <button class="btn btn--ghost btn--sm" wire:click="prevDay">←</button>
        <button class="btn btn--ghost btn--sm" wire:click="goToday">Сегодня</button>
        <button class="btn btn--ghost btn--sm" wire:click="nextDay">→</button>
        <span class="schedule__day">{{ $day->isoFormat('dddd, D MMMM') }}</span>
        <div class="toolbar__spacer"></div>
        <button class="btn btn--primary btn--sm" wire:click="create">@include('partials.icons', ['name' => 'plus']) {{ Crm::label('appointment') }}</button>
    </div>

    @if ($staff->isEmpty())
        <div class="card"><div class="card__body"><div class="empty">
            <div class="empty__title">Сначала добавьте {{ mb_strtolower(Crm::label('staff')) }}</div>
            <p>Это делается в Настройках → {{ Crm::label('staff') }}.</p>
        </div></div></div>
    @else
        <div class="schedule__board">
            @foreach ($staff as $s)
                <div class="schedule__col" wire:key="col-{{ $s->id }}">
                    <div class="schedule__col-head">
                        <span class="avatar">{{ $s->initials() }}</span>
                        <span>{{ $s->name }}</span>
                        <div class="toolbar__spacer"></div>
                        <button class="btn btn--ghost btn--sm btn--icon" wire:click="create({{ $s->id }})" title="Добавить">@include('partials.icons', ['name' => 'plus'])</button>
                    </div>
                    <div class="schedule__col-body">
                        @forelse ($appointmentsByStaff[$s->id] ?? [] as $a)
                            <div class="appt appt--{{ $a->status->value }}" wire:key="appt-{{ $a->id }}" wire:click="edit({{ $a->id }})">
                                <div class="appt__time">{{ $a->starts_at->format('H:i') }}–{{ $a->ends_at->format('H:i') }}</div>
                                <div class="appt__client">{{ $a->client?->name ?? 'Без клиента' }}</div>
                                <div class="appt__svc">{{ $a->service?->name }} · {{ Crm::money($a->price) }}</div>
                                <div style="margin-top:6px;display:flex;gap:6px" wire:click.stop>
                                    <span class="badge badge--{{ $a->status->modifier() }}">{{ $a->status->label() }}</span>
                                    @if ($a->status->value === 'planned')
                                        <button class="btn btn--ghost btn--sm" wire:click="complete({{ $a->id }})">Завершить</button>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="schedule__empty">нет записей</div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @if ($showForm)
        <div class="modal">
            <div class="modal__overlay" wire:click="$set('showForm', false)"></div>
            <div class="modal__dialog">
                <div class="modal__head">
                    <h2 class="modal__title">{{ $editingId ? 'Правка' : 'Новая' }} {{ mb_strtolower(Crm::label('appointment')) }}</h2>
                    <button class="modal__close" wire:click="$set('showForm', false)">&times;</button>
                </div>
                <form wire:submit="save">
                    <div class="modal__body">
                        <div class="form">
                            <label class="field"><span class="field__label">{{ Crm::label('client') }}</span>
                                <select class="field__select" wire:model="form.client_id">
                                    <option value="">— без клиента —</option>
                                    @foreach ($clients as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
                                </select>
                            </label>
                            <div class="field-row">
                                <label class="field"><span class="field__label">{{ Crm::label('staff_singular') }} *</span>
                                    <select class="field__select" wire:model="form.staff_id">
                                        <option value="">—</option>
                                        @foreach ($staff as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach
                                    </select>
                                    @error('form.staff_id')<span class="field__error">{{ $message }}</span>@enderror
                                </label>
                                <label class="field"><span class="field__label">Время *</span>
                                    <input type="time" class="field__input" wire:model="form.time">
                                    @error('form.time')<span class="field__error">{{ $message }}</span>@enderror
                                </label>
                            </div>
                            <label class="field"><span class="field__label">{{ Crm::label('service') }} *</span>
                                <select class="field__select" wire:model="form.service_id">
                                    <option value="">—</option>
                                    @foreach ($services as $sv)<option value="{{ $sv->id }}">{{ $sv->name }} · {{ $sv->duration_min }} мин · {{ Crm::money($sv->price) }}</option>@endforeach
                                </select>
                                @error('form.service_id')<span class="field__error">{{ $message }}</span>@enderror
                            </label>
                            <label class="field"><span class="field__label">Статус</span>
                                <select class="field__select" wire:model="form.status">
                                    @foreach ($statusOptions as $opt)<option value="{{ $opt['value'] }}">{{ $opt['label'] }}</option>@endforeach
                                </select>
                            </label>
                            <label class="field"><span class="field__label">Заметка</span><textarea class="field__textarea" wire:model="form.notes"></textarea></label>
                        </div>
                    </div>
                    <div class="modal__foot">
                        @if ($editingId)<button type="button" class="btn btn--danger" wire:click="delete({{ $editingId }})" wire:confirm="Удалить запись?">Удалить</button>@endif
                        <div class="toolbar__spacer"></div>
                        <button type="button" class="btn btn--ghost" wire:click="$set('showForm', false)">Отмена</button>
                        <button type="submit" class="btn btn--primary">Сохранить</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
