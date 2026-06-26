<?php

namespace App\Livewire\Dashboard;

use App\Enums\AppointmentStatus;
use App\Enums\TransactionType;
use App\Models\Appointment;
use App\Models\Client;
use App\Models\Staff;
use App\Models\Transaction;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Index extends Component
{
    public function render()
    {
        $today = Carbon::today();
        $weekStart = $today->copy()->subDays(6)->startOfDay();
        $prevWeekStart = $today->copy()->subDays(13)->startOfDay();
        $prevWeekEnd = $today->copy()->subDays(7)->endOfDay();

        // ── KPI ────────────────────────────────────────────────────────────
        $appointmentsToday = Appointment::whereDate('starts_at', $today)->count();

        // среднее за прошлые 6 дней (вчера..минус6), для дельты «записей сегодня»
        $prevCount = Appointment::whereBetween('starts_at', [
            $today->copy()->subDays(6)->startOfDay(),
            $today->copy()->subDays(1)->endOfDay(),
        ])->count();
        $avgPrev6 = $prevCount / 6;

        $apptDeltaPct = $avgPrev6 > 0
            ? round(($appointmentsToday - $avgPrev6) / $avgPrev6 * 100, 1)
            : null;

        // выручка текущей и предыдущей 7-дневки
        $revenueWeek = (float) Transaction::where('type', TransactionType::Income)
            ->whereBetween('occurred_at', [$weekStart, now()])
            ->sum('amount');

        $revenuePrevWeek = (float) Transaction::where('type', TransactionType::Income)
            ->whereBetween('occurred_at', [$prevWeekStart, $prevWeekEnd])
            ->sum('amount');

        $revenueDeltaPct = $revenuePrevWeek > 0
            ? round(($revenueWeek - $revenuePrevWeek) / $revenuePrevWeek * 100, 1)
            : null;

        $newClients = Client::where('created_at', '>=', $today->copy()->subDays(30))->count();

        // завершённых на этой неделе vs предыдущей
        $completedWeek = Appointment::where('status', AppointmentStatus::Completed)
            ->whereBetween('starts_at', [$weekStart, $today->copy()->endOfDay()])
            ->count();

        $completedPrevWeek = Appointment::where('status', AppointmentStatus::Completed)
            ->whereBetween('starts_at', [$prevWeekStart, $prevWeekEnd])
            ->count();

        $completedDeltaPct = $completedPrevWeek > 0
            ? round(($completedWeek - $completedPrevWeek) / $completedPrevWeek * 100, 1)
            : null;

        // ── График «Записи по дням» (7 столбцов) ─────────────────────────
        $chart = collect(range(6, 0))->map(function (int $offset) use ($today): array {
            $day = $today->copy()->subDays($offset);

            return [
                'label' => $day->isoFormat('dd'),   // пн, вт, ...
                'count' => Appointment::whereDate('starts_at', $day)->count(),
            ];
        });
        $chartMax = max(1, $chart->max('count'));

        // ── Спарклайн выручки (14 дней) ──────────────────────────────────
        $sparkRevenue = collect(range(13, 0))->map(function (int $offset) use ($today): array {
            $day = $today->copy()->subDays($offset);
            $amt = (float) Transaction::where('type', TransactionType::Income)
                ->whereDate('occurred_at', $day)
                ->sum('amount');

            return [
                'label' => $day->isoFormat('D.MM'),
                'amount' => $amt,
            ];
        });

        // ── Пончик «Клиенты по городам» ──────────────────────────────────
        $cityRaw = Client::selectRaw("COALESCE(NULLIF(TRIM(city),''), 'Не указан') as city_name, COUNT(*) as total")
            ->groupBy('city_name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $cityTotal = max(1, $cityRaw->sum('total'));
        $chartColors = ['--chart-1', '--chart-2', '--chart-3', '--chart-4', '--chart-5'];

        $donut = $cityRaw->values()->map(function ($row, int $i) use ($cityTotal, $chartColors): array {
            return [
                'label' => $row->city_name,
                'count' => (int) $row->total,
                'pct' => round($row->total / $cityTotal * 100),
                'color' => $chartColors[$i] ?? '--chart-5',
            ];
        });

        // ── Персонал: загрузка + выручка за неделю ────────────────────────
        $staffStats = Staff::active()->orderBy('sort')->get()->map(fn (Staff $s) => [
            'staff' => $s,
            'count' => $s->appointments()
                ->whereBetween('starts_at', [$weekStart, $today->copy()->endOfDay()])->count(),
            'earned' => (float) Transaction::where('staff_id', $s->id)
                ->where('type', TransactionType::Income)
                ->whereBetween('occurred_at', [$weekStart, now()])->sum('amount'),
        ]);

        // ── Топ услуги (по числу завершённых записей за 30 дней) ─────────
        $monthStart = $today->copy()->subDays(29)->startOfDay();
        $topServicesRaw = Appointment::with('service')
            ->where('status', AppointmentStatus::Completed)
            ->whereBetween('starts_at', [$monthStart, now()])
            ->whereNotNull('service_id')
            ->selectRaw('service_id, COUNT(*) as cnt, SUM(price) as revenue')
            ->groupBy('service_id')
            ->orderByDesc('cnt')
            ->limit(5)
            ->get();

        $topServicesMax = max(1, $topServicesRaw->max('cnt'));

        $topServices = $topServicesRaw->map(fn ($row) => [
            'name' => $row->service?->name ?? '—',
            'cnt' => (int) $row->cnt,
            'revenue' => (float) $row->revenue,
            'barPct' => round($row->cnt / $topServicesMax * 100),
        ]);

        // ── Ближайшие записи (сегодня + завтра, статус planned) ──────────
        $upcomingAppointments = Appointment::with(['client', 'staff', 'service'])
            ->where('status', AppointmentStatus::Planned)
            ->whereBetween('starts_at', [now(), $today->copy()->addDay()->endOfDay()])
            ->orderBy('starts_at')
            ->limit(8)
            ->get();

        // ── Сегодняшние записи ────────────────────────────────────────────
        $todayAppointments = Appointment::with(['client', 'staff', 'service'])
            ->whereDate('starts_at', $today)
            ->orderBy('starts_at')
            ->get();

        return view('livewire.dashboard.index', compact(
            'appointmentsToday',
            'apptDeltaPct',
            'revenueWeek',
            'revenueDeltaPct',
            'newClients',
            'completedWeek',
            'completedDeltaPct',
            'chart',
            'chartMax',
            'sparkRevenue',
            'donut',
            'staffStats',
            'topServices',
            'upcomingAppointments',
            'todayAppointments',
        ));
    }
}
