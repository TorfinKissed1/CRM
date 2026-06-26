<?php

namespace App\Livewire\Schedule;

use App\Actions\CompleteAppointment;
use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Client;
use App\Models\Service;
use App\Models\Staff;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Index extends Component
{
    public string $date;

    public bool $showForm = false;

    public ?int $editingId = null;

    public array $form = [];

    public function mount(): void
    {
        $this->date = Carbon::today()->toDateString();
        $this->resetForm();
    }

    public function prevDay(): void
    {
        $this->date = Carbon::parse($this->date)->subDay()->toDateString();
    }

    public function nextDay(): void
    {
        $this->date = Carbon::parse($this->date)->addDay()->toDateString();
    }

    public function goToday(): void
    {
        $this->date = Carbon::today()->toDateString();
    }

    public function create(?int $staffId = null): void
    {
        $this->resetForm();
        $this->form['staff_id'] = $staffId;
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $a = Appointment::findOrFail($id);
        $this->editingId = $id;
        $this->form = [
            'client_id' => $a->client_id,
            'staff_id' => $a->staff_id,
            'service_id' => $a->service_id,
            'time' => $a->starts_at->format('H:i'),
            'status' => $a->status->value,
            'notes' => $a->notes,
        ];
        $this->showForm = true;
    }

    public function save(): void
    {
        $data = $this->validate([
            'form.client_id' => 'nullable|exists:clients,id',
            'form.staff_id' => ['required', Rule::exists('staff', 'id')->where('is_active', true)],
            'form.service_id' => ['required', Rule::exists('services', 'id')->where('is_active', true)],
            'form.time' => 'required|date_format:H:i',
            'form.status' => 'required|in:planned,completed,no_show,cancelled',
            'form.notes' => 'nullable|string',
        ])['form'];

        $service = Service::find($data['service_id']);
        $starts = Carbon::parse($this->date.' '.$data['time']);

        $payload = [
            'client_id' => $data['client_id'],
            'staff_id' => $data['staff_id'],
            'service_id' => $data['service_id'],
            'starts_at' => $starts,
            'ends_at' => $starts->copy()->addMinutes($service?->duration_min ?? 60),
            'status' => $data['status'],
            'price' => $service?->price ?? 0,
            'notes' => $data['notes'] ?? null,
        ];

        $this->editingId
            ? Appointment::findOrFail($this->editingId)->update($payload)
            : Appointment::create($payload);

        $this->showForm = false;
        $this->dispatch('toast', message: 'Запись сохранена.');
    }

    public function complete(int $id, CompleteAppointment $action): void
    {
        $action->handle(Appointment::findOrFail($id));
        $this->dispatch('toast', message: 'Завершено, операция учтена в финансах.');
    }

    public function delete(int $id): void
    {
        Appointment::findOrFail($id)->delete();
        $this->dispatch('toast', message: 'Удалено.');
    }

    public function resetForm(): void
    {
        $this->editingId = null;
        $this->form = [
            'client_id' => null,
            'staff_id' => null,
            'service_id' => null,
            'time' => '10:00',
            'status' => 'planned',
            'notes' => '',
        ];
    }

    public function render()
    {
        $day = Carbon::parse($this->date);

        $appointments = Appointment::with(['client', 'service'])
            ->whereDate('starts_at', $day)
            ->orderBy('starts_at')
            ->get()
            ->groupBy('staff_id');

        return view('livewire.schedule.index', [
            'day' => $day,
            'staff' => Staff::active()->orderBy('sort')->orderBy('name')->get(),
            'appointmentsByStaff' => $appointments,
            'clients' => Client::orderBy('name')->limit(500)->get(),
            'services' => Service::active()->orderBy('name')->get(),
            'statusOptions' => AppointmentStatus::options(),
        ]);
    }
}
