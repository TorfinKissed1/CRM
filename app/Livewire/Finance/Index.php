<?php

namespace App\Livewire\Finance;

use App\Actions\ExportTransactions;
use App\Enums\TransactionType;
use App\Models\Staff;
use App\Models\Transaction;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class Index extends Component
{
    use WithPagination;

    public string $period = '7';

    public bool $showForm = false;

    public array $form = [];

    public function mount(): void
    {
        $this->resetForm();
    }

    public function updatingPeriod(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function save(): void
    {
        $data = $this->validate([
            'form.label' => 'required|string|max:255',
            'form.amount' => 'required|numeric|min:0',
            'form.type' => 'required|in:income,expense',
            'form.staff_id' => 'nullable|exists:staff,id',
            'form.occurred_at' => 'required|date',
        ])['form'];

        Transaction::create($data);
        $this->showForm = false;
        $this->dispatch('toast', message: 'Операция добавлена.');
    }

    public function delete(int $id): void
    {
        Transaction::findOrFail($id)->delete();
        $this->dispatch('toast', message: 'Удалено.');
    }

    public function export(ExportTransactions $exporter)
    {
        return $exporter->handle($this->rangeStart());
    }

    public function resetForm(): void
    {
        $this->form = [
            'label' => '',
            'amount' => 0,
            'type' => 'income',
            'staff_id' => null,
            'occurred_at' => now()->format('Y-m-d\TH:i'),
        ];
    }

    private function rangeStart(): ?Carbon
    {
        return match ($this->period) {
            '7' => Carbon::now()->subDays(7)->startOfDay(),
            '30' => Carbon::now()->subDays(30)->startOfDay(),
            default => null,
        };
    }

    public function render()
    {
        $start = $this->rangeStart();

        $base = Transaction::query()->when($start, fn ($q) => $q->where('occurred_at', '>=', $start));

        $income = (clone $base)->where('type', TransactionType::Income)->sum('amount');
        $expense = (clone $base)->where('type', TransactionType::Expense)->sum('amount');
        $incomeCount = (clone $base)->where('type', TransactionType::Income)->count();

        $transactions = (clone $base)
            ->with(['client', 'staff'])
            ->orderByDesc('occurred_at')
            ->paginate(20);

        return view('livewire.finance.index', [
            'transactions' => $transactions,
            'income' => $income,
            'expense' => $expense,
            'operations' => (clone $base)->count(),
            'avgCheck' => $incomeCount ? $income / $incomeCount : 0,
            'staffList' => Staff::active()->orderBy('name')->get(),
        ]);
    }
}
