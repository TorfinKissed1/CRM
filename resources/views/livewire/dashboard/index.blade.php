@php use App\Support\Crm; @endphp
<div class="stack">

    {{-- ══════════════════ KPI-КАРТЫ ══════════════════ --}}
    <div class="kpi-grid">

        {{-- Записей сегодня --}}
        <div class="kpi">
            <div class="kpi__label">{{ Crm::label('appointments') }} сегодня</div>
            <div class="kpi__value">{{ $appointmentsToday }}</div>
            <div class="kpi__delta{{ $apptDeltaPct !== null ? ($apptDeltaPct >= 0 ? ' kpi__delta--up' : ' kpi__delta--down') : '' }}">
                @if ($apptDeltaPct !== null)
                    {{ $apptDeltaPct >= 0 ? '↑' : '↓' }} {{ abs($apptDeltaPct) }}% к ср. за 6 дн.
                @else
                    {{ now()->isoFormat('D MMMM') }}
                @endif
            </div>
        </div>

        {{-- Выручка за 7 дней --}}
        <div class="kpi">
            <div class="kpi__label">Выручка за 7 дней</div>
            <div class="kpi__value">{{ Crm::money($revenueWeek) }}</div>
            <div class="kpi__delta{{ $revenueDeltaPct !== null ? ($revenueDeltaPct >= 0 ? ' kpi__delta--up' : ' kpi__delta--down') : '' }}">
                @if ($revenueDeltaPct !== null)
                    {{ $revenueDeltaPct >= 0 ? '↑' : '↓' }} {{ abs($revenueDeltaPct) }}% к прошлой неделе
                @else
                    доходные операции
                @endif
            </div>
        </div>

        {{-- Новые клиенты --}}
        <div class="kpi">
            <div class="kpi__label">Новых {{ mb_strtolower(Crm::label('clients')) }} · 30 дней</div>
            <div class="kpi__value">{{ $newClients }}</div>
            <div class="kpi__delta">{{ round($newClients / 30, 1) }} в день</div>
        </div>

        {{-- Завершено за неделю --}}
        <div class="kpi">
            <div class="kpi__label">Завершено за неделю</div>
            <div class="kpi__value">{{ $completedWeek }}</div>
            <div class="kpi__delta{{ $completedDeltaPct !== null ? ($completedDeltaPct >= 0 ? ' kpi__delta--up' : ' kpi__delta--down') : '' }}">
                @if ($completedDeltaPct !== null)
                    {{ $completedDeltaPct >= 0 ? '↑' : '↓' }} {{ abs($completedDeltaPct) }}% к прошлой неделе
                @else
                    {{ mb_strtolower(Crm::label('appointments')) }}
                @endif
            </div>
        </div>

    </div>

    {{-- ══════════════════ ГРАФИКИ: СТОЛБЦЫ + СПАРКЛАЙН ══════════════════ --}}
    <div class="card-grid card-grid--2">

        {{-- Столбчатый: Записи по дням --}}
        <div class="card">
            <div class="card__head">
                <h2 class="card__title">{{ Crm::label('appointments') }} по дням</h2>
                <span class="card__sub">7 дней</span>
            </div>
            <div class="card__body">
                @php
                    $barCount   = $chart->count();        // 7
                    $svgW       = 560;
                    $svgH       = 200;
                    $padT       = 20;   // под подписи значений
                    $padB       = 36;   // под подписи дней
                    $padL       = 8;
                    $padR       = 8;
                    $innerW     = $svgW - $padL - $padR;
                    $innerH     = $svgH - $padT - $padB;
                    $colW       = $innerW / $barCount;
                    $barW       = min(44, $colW * 0.55);
                    $baseY      = $padT + $innerH;   // Y базовой линии
                @endphp
                <svg class="chart-svg" viewBox="0 0 {{ $svgW }} {{ $svgH }}" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    {{-- Базовая линия --}}
                    <line x1="{{ $padL }}" y1="{{ $baseY }}" x2="{{ $svgW - $padR }}" y2="{{ $baseY }}"
                          stroke="var(--color-border)" stroke-width="1"/>

                    @foreach ($chart as $i => $bar)
                        @php
                            $cx    = $padL + $colW * $i + $colW / 2;
                            $barH  = $bar['count'] > 0 ? max(4, round($bar['count'] / $chartMax * $innerH)) : 4;
                            $barX  = $cx - $barW / 2;
                            $barY  = $baseY - $barH;
                            $isZero = $bar['count'] === 0;
                        @endphp
                        {{-- Столбец со скруглёнными верхними углами --}}
                        <rect
                            x="{{ round($barX, 2) }}"
                            y="{{ round($barY, 2) }}"
                            width="{{ round($barW, 2) }}"
                            height="{{ round($barH, 2) }}"
                            rx="5" ry="5"
                            fill="{{ $isZero ? 'var(--color-border)' : 'var(--chart-1)' }}"
                            opacity="{{ $isZero ? '0.5' : '0.85' }}"
                        />
                        {{-- Подпись значения над столбцом --}}
                        @if (!$isZero)
                        <text
                            x="{{ round($cx, 2) }}"
                            y="{{ round($barY - 5, 2) }}"
                            text-anchor="middle"
                            class="chart-svg__val"
                        >{{ $bar['count'] }}</text>
                        @endif
                        {{-- Подпись дня под графиком --}}
                        <text
                            x="{{ round($cx, 2) }}"
                            y="{{ $svgH - 8 }}"
                            text-anchor="middle"
                            class="chart-svg__label"
                        >{{ $bar['label'] }}</text>
                    @endforeach
                </svg>
            </div>
        </div>

        {{-- Линейный/площадной спарклайн выручки --}}
        <div class="card">
            <div class="card__head">
                <h2 class="card__title">Выручка, динамика</h2>
                <span class="card__sub">14 дней</span>
            </div>
            <div class="card__body">
                @php
                    $spW    = 380;
                    $spH    = 140;
                    $spPadT = 16;
                    $spPadB = 28;
                    $spPadL = 4;
                    $spPadR = 4;
                    $spInW  = $spW - $spPadL - $spPadR;
                    $spInH  = $spH - $spPadT - $spPadB;

                    $amounts  = $sparkRevenue->pluck('amount');
                    $spMax    = max(1, $amounts->max());
                    $spCount  = $sparkRevenue->count();   // 14

                    // Вычисляем точки
                    $pts = $sparkRevenue->values()->map(function ($pt, $idx) use (
                        $spCount, $spInW, $spInH, $spMax, $spPadL, $spPadT
                    ) {
                        $x = $spPadL + ($spInW / ($spCount - 1)) * $idx;
                        $y = $spPadT + $spInH - ($pt['amount'] / $spMax) * $spInH;
                        return ['x' => round($x, 2), 'y' => round($y, 2), 'label' => $pt['label'], 'amount' => $pt['amount']];
                    });

                    // Строим path для линии и области
                    $lineD = $pts->map(fn ($p, $i) => ($i === 0 ? 'M' : 'L') . $p['x'] . ',' . $p['y'])->implode(' ');
                    $areaD = $lineD
                        . ' L' . $pts->last()['x'] . ',' . ($spPadT + $spInH)
                        . ' L' . $pts->first()['x'] . ',' . ($spPadT + $spInH)
                        . ' Z';
                    $gradId = 'spark-grad';
                    $baselineY = $spPadT + $spInH;
                @endphp
                <svg class="chart-svg chart-svg--spark" viewBox="0 0 {{ $spW }} {{ $spH }}" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <defs>
                        <linearGradient id="{{ $gradId }}" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="var(--chart-1)" stop-opacity="0.18"/>
                            <stop offset="100%" stop-color="var(--chart-1)" stop-opacity="0.01"/>
                        </linearGradient>
                    </defs>

                    {{-- Базовая линия --}}
                    <line x1="{{ $spPadL }}" y1="{{ $baselineY }}" x2="{{ $spW - $spPadR }}" y2="{{ $baselineY }}"
                          stroke="var(--color-border)" stroke-width="1"/>

                    {{-- Заливка области --}}
                    <path d="{{ $areaD }}" fill="url(#{{ $gradId }})"/>

                    {{-- Линия --}}
                    <path d="{{ $lineD }}" fill="none" stroke="var(--chart-1)" stroke-width="2" stroke-linejoin="round" stroke-linecap="round"/>

                    {{-- Точки и подписи дат (каждые 2) --}}
                    @foreach ($pts as $idx => $pt)
                        @if ($idx % 2 === 0 || $idx === $spCount - 1)
                            <circle cx="{{ $pt['x'] }}" cy="{{ $pt['y'] }}" r="3"
                                    fill="var(--color-surface)" stroke="var(--chart-1)" stroke-width="1.5"/>
                            <text x="{{ $pt['x'] }}" y="{{ $spH - 6 }}" text-anchor="middle" class="chart-svg__label">{{ $pt['label'] }}</text>
                        @endif
                    @endforeach
                </svg>
            </div>
        </div>

    </div>

    {{-- ══════════════════ ПОНЧИК + ПЕРСОНАЛ ══════════════════ --}}
    <div class="card-grid card-grid--2">

        {{-- Пончик: клиенты по городам --}}
        <div class="card">
            <div class="card__head">
                <h2 class="card__title">Клиенты по городам</h2>
                <span class="card__sub">топ 5</span>
            </div>
            <div class="card__body">
                @php
                    $dW      = 260;
                    $dH      = 200;
                    $cx_d    = $dW / 2;
                    $cy_d    = $dH / 2;
                    $rOuter  = 78;
                    $rInner  = 46;

                    // Строим дольки (cumulative angle)
                    $totalPct  = $donut->sum('pct');
                    $totalPct  = max(1, $totalPct);   // safety
                    $startAngle = -90;   // начинаем сверху

                    $slices = [];
                    foreach ($donut as $seg) {
                        $sweep = 360 * ($seg['pct'] / 100);
                        $slices[] = ['seg' => $seg, 'start' => $startAngle, 'sweep' => $sweep];
                        $startAngle += $sweep;
                    }

                    // Если слайсов нет — показываем заглушку
                    $hasData = count($slices) > 0;

                    // Замыкание (а не function) — безопасно при Livewire-ре-рендере
                    $donutArc = static function (float $cx, float $cy, float $rO, float $rI, float $startDeg, float $sweepDeg): string {
                        $large = $sweepDeg > 180 ? 1 : 0;
                        $s = deg2rad($startDeg);
                        $e = deg2rad($startDeg + $sweepDeg);

                        $x1 = round($cx + $rO * cos($s), 3); $y1 = round($cy + $rO * sin($s), 3);
                        $x2 = round($cx + $rO * cos($e), 3); $y2 = round($cy + $rO * sin($e), 3);
                        $x3 = round($cx + $rI * cos($e), 3); $y3 = round($cy + $rI * sin($e), 3);
                        $x4 = round($cx + $rI * cos($s), 3); $y4 = round($cy + $rI * sin($s), 3);

                        return "M{$x1},{$y1} A{$rO},{$rO} 0 {$large} 1 {$x2},{$y2} L{$x3},{$y3} A{$rI},{$rI} 0 {$large} 0 {$x4},{$y4} Z";
                    };
                @endphp
                <div class="donut-wrap">
                    <svg class="chart-svg chart-svg--donut" viewBox="0 0 {{ $dW }} {{ $dH }}" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        @if ($hasData)
                            @foreach ($slices as $sl)
                                @if ($sl['sweep'] > 0.5)
                                <path d="{{ $donutArc($cx_d, $cy_d, $rOuter, $rInner, $sl['start'], $sl['sweep']) }}"
                                      fill="var({{ $sl['seg']['color'] }})" opacity="0.88"/>
                                @endif
                            @endforeach
                            {{-- Центральный текст --}}
                            <text x="{{ $cx_d }}" y="{{ $cy_d - 6 }}" text-anchor="middle" class="chart-svg__center-num">
                                {{ $donut->sum('count') }}
                            </text>
                            <text x="{{ $cx_d }}" y="{{ $cy_d + 14 }}" text-anchor="middle" class="chart-svg__center-label">
                                клиентов
                            </text>
                        @else
                            <circle cx="{{ $cx_d }}" cy="{{ $cy_d }}" r="{{ $rOuter }}" fill="var(--color-border)" opacity="0.4"/>
                            <circle cx="{{ $cx_d }}" cy="{{ $cy_d }}" r="{{ $rInner }}" fill="var(--color-surface)"/>
                            <text x="{{ $cx_d }}" y="{{ $cy_d + 5 }}" text-anchor="middle" class="chart-svg__center-label">нет данных</text>
                        @endif
                    </svg>

                    <ul class="donut-legend">
                        @foreach ($donut as $seg)
                            <li class="donut-legend__item">
                                <span class="donut-legend__dot" style="background: var({{ $seg['color'] }})"></span>
                                <span class="donut-legend__name">{{ $seg['label'] }}</span>
                                <span class="donut-legend__pct">{{ $seg['pct'] }}%</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        {{-- Персонал --}}
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

    {{-- ══════════════════ СЕГОДНЯШНИЕ ЗАПИСИ ══════════════════ --}}
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
