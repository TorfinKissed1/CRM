<?php

namespace App\Livewire\Clients;

use App\Actions\ExportClients;
use App\Models\Client;
use App\Models\Staff;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public ?int $staffFilter = null;

    public bool $showForm = false;

    public ?int $editingId = null;

    public array $form = [];

    protected function rules(): array
    {
        return [
            'form.name' => 'required|string|max:255',
            'form.phone' => 'nullable|string|max:50',
            'form.email' => 'nullable|email|max:255',
            'form.city' => 'nullable|string|max:120',
            'form.vk' => 'nullable|string|max:255',
            'form.telegram' => 'nullable|string|max:255',
            'form.instagram' => 'nullable|string|max:255',
            'form.whatsapp' => 'nullable|string|max:255',
            'form.preferred_staff_id' => 'nullable|exists:staff,id',
            'form.notes' => 'nullable|string',
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStaffFilter(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->editingId = null;
        $this->form = $this->blankForm();
        $this->resetValidation();
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $client = Client::findOrFail($id);
        $this->editingId = $id;
        $this->form = $client->only(array_keys($this->blankForm()));
        $this->resetValidation();
        $this->showForm = true;
    }

    public function save(): void
    {
        $data = $this->validate()['form'];

        if ($this->editingId) {
            Client::findOrFail($this->editingId)->update($data);
        } else {
            Client::create($data);
        }

        $this->showForm = false;
        $this->dispatch('toast', message: 'Сохранено.');
    }

    public function delete(int $id): void
    {
        Client::findOrFail($id)->delete();
        $this->dispatch('toast', message: 'Удалено.');
    }

    public function export(ExportClients $exporter)
    {
        return $exporter->handle();
    }

    public function render()
    {
        $clients = Client::query()
            ->with('preferredStaff')
            ->search($this->search)
            ->when($this->staffFilter, fn ($q) => $q->where('preferred_staff_id', $this->staffFilter))
            ->latest()
            ->paginate(15);

        return view('livewire.clients.index', [
            'clients' => $clients,
            'staffList' => Staff::active()->orderBy('sort')->get(),
        ]);
    }

    private function blankForm(): array
    {
        return [
            'name' => '',
            'phone' => '',
            'email' => '',
            'city' => '',
            'vk' => '',
            'telegram' => '',
            'instagram' => '',
            'whatsapp' => '',
            'preferred_staff_id' => null,
            'notes' => '',
        ];
    }
}
