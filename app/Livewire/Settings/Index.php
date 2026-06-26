<?php

namespace App\Livewire\Settings;

use App\Enums\Role;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Staff;
use App\Models\User;
use App\Support\Crm;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Index extends Component
{
    public string $tab = 'profile';

    // Профиль бизнеса
    public string $businessName = '';

    public string $currencySymbol = '';

    // Инлайн-формы сущностей
    public array $staffForm = [];

    public ?int $staffEditing = null;

    public array $serviceForm = [];

    public ?int $serviceEditing = null;

    public array $userForm = [];

    public ?int $userEditing = null;

    /**
     * Авторизация на уровне компонента: route-middleware `can:owner` защищает только
     * первый GET, а каждый Livewire-метод — отдельный POST-эндпоинт. Поэтому проверяем
     * владельца и в mount(), и в каждом методе записи/чтения.
     */
    protected function guardOwner(): void
    {
        abort_unless(auth()->user()?->isOwner(), 403);
    }

    public function mount(): void
    {
        $this->guardOwner();
        $this->businessName = Crm::businessName();
        $this->currencySymbol = Crm::currencySymbol();
        $this->resetStaffForm();
        $this->resetServiceForm();
        $this->resetUserForm();
    }

    // --- Профиль ---
    public function saveProfile(): void
    {
        $this->guardOwner();

        $this->validate([
            'businessName' => 'required|string|max:120',
            'currencySymbol' => 'required|string|max:8',
        ]);

        Setting::put('business_name', $this->businessName);
        Setting::put('currency_symbol', $this->currencySymbol);

        $this->dispatch('toast', message: 'Профиль сохранён.');
    }

    // --- Мастера ---
    public function saveStaff(): void
    {
        $this->guardOwner();

        $data = $this->validate([
            'staffForm.name' => 'required|string|max:255',
            'staffForm.role_title' => 'nullable|string|max:255',
            'staffForm.specialization' => 'nullable|string|max:255',
            'staffForm.color' => 'nullable|regex:/^#[0-9a-fA-F]{3,8}$/',
            'staffForm.is_active' => 'boolean',
        ])['staffForm'];

        $this->staffEditing
            ? Staff::findOrFail($this->staffEditing)->update($data)
            : Staff::create($data);

        $this->resetStaffForm();
        $this->dispatch('toast', message: 'Сохранено.');
    }

    public function editStaff(int $id): void
    {
        $this->guardOwner();
        $this->staffEditing = $id;
        $this->staffForm = Staff::findOrFail($id)->only(['name', 'role_title', 'specialization', 'color', 'is_active']);
    }

    public function deleteStaff(int $id): void
    {
        $this->guardOwner();
        Staff::findOrFail($id)->delete();
        $this->dispatch('toast', message: 'Удалено.');
    }

    public function resetStaffForm(): void
    {
        $this->staffEditing = null;
        $this->staffForm = ['name' => '', 'role_title' => '', 'specialization' => '', 'color' => '#2f6fed', 'is_active' => true];
    }

    // --- Услуги ---
    public function saveService(): void
    {
        $this->guardOwner();

        $data = $this->validate([
            'serviceForm.name' => 'required|string|max:255',
            'serviceForm.category' => 'nullable|string|max:255',
            'serviceForm.duration_min' => 'required|integer|min:5|max:1440',
            'serviceForm.price' => 'required|numeric|min:0',
            'serviceForm.is_active' => 'boolean',
        ])['serviceForm'];

        $this->serviceEditing
            ? Service::findOrFail($this->serviceEditing)->update($data)
            : Service::create($data);

        $this->resetServiceForm();
        $this->dispatch('toast', message: 'Сохранено.');
    }

    public function editService(int $id): void
    {
        $this->guardOwner();
        $this->serviceEditing = $id;
        $this->serviceForm = Service::findOrFail($id)->only(['name', 'category', 'duration_min', 'price', 'is_active']);
    }

    public function deleteService(int $id): void
    {
        $this->guardOwner();
        Service::findOrFail($id)->delete();
        $this->dispatch('toast', message: 'Удалено.');
    }

    public function resetServiceForm(): void
    {
        $this->serviceEditing = null;
        $this->serviceForm = ['name' => '', 'category' => '', 'duration_min' => 60, 'price' => 0, 'is_active' => true];
    }

    // --- Пользователи ---
    public function saveUser(): void
    {
        $this->guardOwner();

        $data = $this->validate([
            'userForm.name' => 'required|string|max:255',
            'userForm.email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->userEditing)],
            'userForm.role' => 'required|in:owner,manager',
            'userForm.is_active' => 'boolean',
            'userForm.password' => ($this->userEditing ? 'nullable' : 'required').'|string|min:6',
        ])['userForm'];

        // Защита: нельзя снять роль/деактивировать единственного активного владельца.
        if ($this->userEditing) {
            $target = User::find($this->userEditing);
            $isLastOwner = $target?->isOwner()
                && User::where('role', Role::Owner)->where('is_active', true)->count() <= 1;
            if ($isLastOwner && ($data['role'] !== 'owner' || ! $data['is_active'])) {
                $this->addError('userForm.role', 'Это единственный владелец — нельзя снять роль или деактивировать.');

                return;
            }
        }

        $payload = [
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'is_active' => $data['is_active'],
        ];
        if (! empty($data['password'])) {
            $payload['password'] = $data['password']; // хэшируется кастом 'hashed'
        }

        $this->userEditing
            ? User::findOrFail($this->userEditing)->update($payload)
            : User::create($payload);

        $this->resetUserForm();
        $this->dispatch('toast', message: 'Сохранено.');
    }

    public function editUser(int $id): void
    {
        $this->guardOwner();
        $user = User::findOrFail($id);
        $this->userEditing = $id;
        $this->userForm = [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role->value,
            'is_active' => $user->is_active,
            'password' => '',
        ];
    }

    public function deleteUser(int $id): void
    {
        $this->guardOwner();

        $target = User::findOrFail($id);
        if ($target->isOwner() && User::where('role', Role::Owner)->count() <= 1) {
            $this->dispatch('toast', message: 'Нельзя удалить единственного владельца.');

            return;
        }

        $target->delete();
        $this->dispatch('toast', message: 'Удалено.');
    }

    public function resetUserForm(): void
    {
        $this->userEditing = null;
        $this->userForm = ['name' => '', 'email' => '', 'role' => 'manager', 'is_active' => true, 'password' => ''];
    }

    public function render()
    {
        return view('livewire.settings.index', [
            'staff' => Staff::orderBy('sort')->orderBy('name')->get(),
            'services' => Service::orderBy('sort')->orderBy('name')->get(),
            'users' => User::orderBy('name')->get(),
            'roleOptions' => Role::options(),
        ]);
    }
}
