@php use App\Support\Crm; @endphp
<div class="stack">
    <div class="toolbar">
        <div class="segmented">
            <button class="segmented__btn {{ $period === '7' ? 'segmented__btn--active' : '' }}" wire:click="$set('period','7')">7 дней</button>
            <button class="segmented__btn {{ $period === '30' ? 'segmented__btn--active' : '' }}" wire:click="$set('period','30')">30 дней</button>
            <button class="segmented__btn {{ $period === 'all' ? 'segmented__btn--active' : '' }}" wire:click="$set('period','all')">Всё время</button>
        </div>
        <div class="toolbar__spacer"></div>
        <button class="btn btn--ghost btn--sm" wire:click="export">@include('partials.icons', ['name' => 'download']) Экспорт</button>
        <button class="btn btn--primary btn--sm" wire:click="create">@include('partials.icons', ['name' => 'plus']) Операция</button>
    </div>

    <div class="kpi-grid" style="grid-template-columns:repeat(4,1fr)">
        <div class="kpi"><div class="kpi__label">Доход</div><div class="kpi__value">{{ Crm::money($income) }}</div></div>
        <div class="kpi"><div class="kpi__label">Расход</div><div class="kpi__value">{{ Crm::money($expense) }}</div></div>
        <div class="kpi"><div class="kpi__label">Операций</div><div class="kpi__value">{{ $operations }}</div></div>
        <div class="kpi"><div class="kpi__label">Средний чек</div><div class="kpi__value">{{ Crm::money($avgCheck) }}</div></div>
    </div>

    <div class="card card--flush">
        <div class="table-wrap">
            <table class="table">
                <thead><tr><th>Дата</th><th>Операция</th><th>Клиент</th><th>Мастер</th><th>Тип</th><th>Сумма</th><th></th></tr></thead>
                <tbody>
                    @forelse ($transactions as $t)
                        <tr wire:key="tx-{{ $t->id }}">
                            <td class="table__muted">{{ $t->occurred_at->format('d.m H:i') }}</td>
                            <td><span class="table__name">{{ $t->label }}</span></td>
                            <td>{{ $t->client?->name ?: '—' }}</td>
                            <td>{{ $t->staff?->name ?: '—' }}</td>
                            <td><span class="badge {{ $t->type->value === 'income' ? 'badge--success' : 'badge--warning' }}">{{ $t->type->label() }}</span></td>
                            <td>{{ Crm::money($t->amount) }}</td>
                            <td><div class="table__actions"><button class="btn btn--ghost btn--sm" wire:click="delete({{ $t->id }})" wire:confirm="Удалить операцию?">×</button></div></td>
                        </tr>
                    @empty
                        <tr><td colspan="7"><div class="empty"><div class="empty__title">Операций нет</div><p>Завершайте записи в расписании — доход учтётся автоматически, или добавьте операцию вручную.</p></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($transactions->hasPages())
        <div class="pager">
            <button class="btn btn--ghost btn--sm" wire:click="previousPage" @disabled($transactions->onFirstPage())>Назад</button>
            <span class="pager__info">{{ $transactions->currentPage() }} / {{ $transactions->lastPage() }}</span>
            <button class="btn btn--ghost btn--sm" wire:click="nextPage" @disabled(! $transactions->hasMorePages())>Вперёд</button>
        </div>
    @endif

    @if ($showForm)
        <div class="modal">
            <div class="modal__overlay" wire:click="$set('showForm', false)"></div>
            <div class="modal__dialog">
                <div class="modal__head"><h2 class="modal__title">Новая операция</h2><button class="modal__close" wire:click="$set('showForm', false)">&times;</button></div>
                <form wire:submit="save">
                    <div class="modal__body">
                        <div class="form">
                            <label class="field"><span class="field__label">Описание *</span><input type="text" class="field__input" wire:model="form.label">@error('form.label')<span class="field__error">{{ $message }}</span>@enderror</label>
                            <div class="field-row">
                                <label class="field"><span class="field__label">Сумма *</span><input type="number" step="0.01" class="field__input" wire:model="form.amount">@error('form.amount')<span class="field__error">{{ $message }}</span>@enderror</label>
                                <label class="field"><span class="field__label">Тип</span>
                                    <select class="field__select" wire:model="form.type"><option value="income">Доход</option><option value="expense">Расход</option></select>
                                </label>
                            </div>
                            <div class="field-row">
                                <label class="field"><span class="field__label">{{ Crm::label('staff_singular') }}</span>
                                    <select class="field__select" wire:model="form.staff_id"><option value="">—</option>@foreach ($staffList as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach</select>
                                </label>
                                <label class="field"><span class="field__label">Когда *</span><input type="datetime-local" class="field__input" wire:model="form.occurred_at"></label>
                            </div>
                        </div>
                    </div>
                    <div class="modal__foot">
                        <button type="button" class="btn btn--ghost" wire:click="$set('showForm', false)">Отмена</button>
                        <button type="submit" class="btn btn--primary">Добавить</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
