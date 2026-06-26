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

        $appointmentsToday = Appointment::whereDate('starts_at', $today)->count();

        $revenueWeek = Transaction::where('type', TransactionType::Income)
            ->whereBetween('occurred_at', [$weekStart, now()])
            ->sum('amount');

        $newClients = Client::where('created_at', '>=', $today->copy()->subDays(30))->count();

        $completedWeek = Appointment::where('status', AppointmentStatus::Completed)
            ->whereBetween('starts_at', [$weekStart, $today->copy()->endOfDay()])
            ->count();

        $chart = collect(range(6, 0))->map(function ($offset) use ($today) {
            $day = $today->copy()->subDays($offset);

            return [
                'label' => $day->translatedFormat('EEE'),
                'count' => Appointment::whereDate('starts_at', $day)->count(),
            ];
        });
        $chartMax = max(1, $chart->max('count'));

        $staffStats = Staff::active()->orderBy('sort')->get()->map(fn (Staff $s) => [
            'staff' => $s,
            'count' => $s->appointments()
                ->whereBetween('starts_at', [$weekStart, $today->copy()->endOfDay()])->count(),
            'earned' => Transaction::where('staff_id', $s->id)
                ->where('type', TransactionType::Income)
                ->whereBetween('occurred_at', [$weekStart, now()])->sum('amount'),
        ]);

        $todayAppointments = Appointment::with(['client', 'staff', 'service'])
            ->whereDate('starts_at', $today)
            ->orderBy('starts_at')
            ->get();

        return view('livewire.dashboard.index', compact(
            'appointmentsToday',
            'revenueWeek',
            'newClients',
            'completedWeek',
            'chart',
            'chartMax',
            'staffStats',
            'todayAppointments',
        ));
    }
}
