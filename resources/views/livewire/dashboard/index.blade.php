@php use App\Support\Crm; @endphp
<div class="stack">
    <div class="kpi-grid">
        <div class="kpi">
            <div class="kpi__label">{{ Crm::label('appointments') }} сегодня</div>
            <div class="kpi__value">{{ $appointmentsToday }}</div>
            <div class="kpi__delta">{{ now()->translatedFormat('d MMMM') }}</div>
        </div>
        <div class="kpi">
            <div class="kpi__label">Выручка за 7 дней</div>
            <div class="kpi__value">{{ Crm::money($revenueWeek) }}</div>
            <div class="kpi__delta">доходные операции</div>
        </div>
        <div class="kpi">
            <div class="kpi__label">Новых {{ mb_strtolower(Crm::label('clients')) }} · 30 дней</div>
            <div class="kpi__value">{{ $newClients }}</div>
            <div class="kpi__delta">{{ round($newClients / 30, 1) }} в день</div>
        </div>
        <div class="kpi">
            <div class="kpi__label">Завершено за неделю</div>
            <div class="kpi__value">{{ $completedWeek }}</div>
            <div class="kpi__delta">{{ mb_strtolower(Crm::label('appointments')) }}</div>
        </div>
    </div>

    <div class="card-grid card-grid--2">
        <div class="card">
            <div class="card__head">
                <h2 class="card__title">{{ Crm::label('appointments') }} по дням</h2>
                <span class="card__sub">7 дней</span>
            </div>
            <div class="card__body">
                <div class="chart">
                    @foreach ($chart as $bar)
                        <div class="chart__col">
                            <div class="chart__track">
                                <div class="chart__bar" style="height: {{ max(3, round($bar['count'] / $chartMax * 100)) }}%"></div>
                            </div>
                            <div class="chart__val">{{ $bar['count'] }}</div>
                            <div class="chart__label">{{ $bar['label'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card__head"><h2 class="card__title">{{ Crm::label('staff') }}</h2></div>
            <div class="card__body">
                @forelse ($staffStats as $row)
                    <div class="list-row">
                        <span class="avatar">{{ $row['staff']->initials() }}</span>
                        <div class="list-row__main">
                            <div class="list-row__title">{{ $row['staff']->name }}</div>
                            <div class="list-row__sub">{{ $row['count'] }} {{ mb_strtolower(Crm::label('appointments')) }}</div>
                        </div>
                        <div class="list-row__value">{{ Crm::money($row['earned']) }}</div>
                    </div>
                @empty
                    <div class="empty">
                        <div class="empty__title">Нет {{ mb_strtolower(Crm::label('staff')) }}</div>
                        <p>Добавьте их в Настройках.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card__head">
            <h2 class="card__title">Сегодня</h2>
            <span class="card__sub">{{ $todayAppointments->count() }} {{ mb_strtolower(Crm::label('appointments')) }}</span>
        </div>
        <div class="card__body">
            @forelse ($todayAppointments as $appointment)
                <div class="list-row">
                    <span class="list-row__time">{{ $appointment->starts_at->format('H:i') }}</span>
                    <div class="list-row__main">
                        <div class="list-row__title">{{ $appointment->client?->name ?? '—' }}</div>
                        <div class="list-row__sub">{{ $appointment->service?->name }} · {{ $appointment->staff?->name }}</div>
                    </div>
                    <span class="badge badge--{{ $appointment->status->modifier() }}">{{ $appointment->status->label() }}</span>
                </div>
            @empty
                <div class="empty">
                    <div class="empty__title">На сегодня {{ mb_strtolower(Crm::label('appointments')) }} нет</div>
                    <p>Создайте запись в разделе «{{ Crm::label('schedule') }}».</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
