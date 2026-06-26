<?php

namespace App\Livewire\Settings;

use App\Enums\Role;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Staff;
use App\Models\User;
use App\Support\Crm;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Index extends Component
{
    public string $tab = 'profile';

    // Профиль бизнеса / тема
    public string $businessName = '';

    public string $themePrimary = '';

    public string $currencySymbol = '';

    // Инлайн-формы сущностей
    public array $staffForm = [];

    public ?int $staffEditing = null;

    public array $serviceForm = [];

    public ?int $serviceEditing = null;

    public array $userForm = [];

    public ?int $userEditing = null;

    public function mount(): void
    {
        $this->businessName = Crm::businessName();
        $this->themePrimary = Crm::primaryColor();
        $this->currencySymbol = Crm::currencySymbol();
        $this->resetStaffForm();
        $this->resetServiceForm();
        $this->resetUserForm();
    }

    // --- Профиль ---
    public function saveProfile(): void
    {
        $this->validate([
            'businessName' => 'required|string|max:120',
            'themePrimary' => 'required|string|max:9',
            'currencySymbol' => 'required|string|max:8',
        ]);

        Setting::put('business_name', $this->businessName);
        Setting::put('theme_primary', $this->themePrimary);
        Setting::put('currency_symbol', $this->currencySymbol);

        $this->dispatch('toast', message: 'Профиль сохранён.');
    }

    // --- Мастера ---
    public function saveStaff(): void
    {
        $data = $this->validate([
            'staffForm.name' => 'required|string|max:255',
            'staffForm.role_title' => 'nullable|string|max:255',
            'staffForm.specialization' => 'nullable|string|max:255',
            'staffForm.color' => 'nullable|string|max:9',
            'staffForm.is_active' => 'boolean',
        ])['staffForm'];

        Staff::updateOrCreate(['id' => $this->staffEditing], $data);
        $this->resetStaffForm();
        $this->dispatch('toast', message: 'Сохранено.');
    }

    public function editStaff(int $id): void
    {
        $this->staffEditing = $id;
        $this->staffForm = Staff::findOrFail($id)->only(['name', 'role_title', 'specialization', 'color', 'is_active']);
    }

    public function deleteStaff(int $id): void
    {
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
        $data = $this->validate([
            'serviceForm.name' => 'required|string|max:255',
            'serviceForm.category' => 'nullable|string|max:255',
            'serviceForm.duration_min' => 'required|integer|min:5|max:1440',
            'serviceForm.price' => 'required|numeric|min:0',
            'serviceForm.is_active' => 'boolean',
        ])['serviceForm'];

        Service::updateOrCreate(['id' => $this->serviceEditing], $data);
        $this->resetServiceForm();
        $this->dispatch('toast', message: 'Сохранено.');
    }

    public function editService(int $id): void
    {
        $this->serviceEditing = $id;
        $this->serviceForm = Service::findOrFail($id)->only(['name', 'category', 'duration_min', 'price', 'is_active']);
    }

    public function deleteService(int $id): void
    {
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
        $rules = [
            'userForm.name' => 'required|string|max:255',
            'userForm.email' => 'required|email|max:255',
            'userForm.role' => 'required|in:owner,manager',
            'userForm.is_active' => 'boolean',
            'userForm.password' => ($this->userEditing ? 'nullable' : 'required').'|string|min:6',
        ];
        $data = $this->validate($rules)['userForm'];

        $payload = [
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'is_active' => $data['is_active'],
        ];
        if (! empty($data['password'])) {
            $payload['password'] = $data['password']; // хэшируется кастом 'hashed'
        }

        User::updateOrCreate(['id' => $this->userEditing], $payload);
        $this->resetUserForm();
        $this->dispatch('toast', message: 'Сохранено.');
    }

    public function editUser(int $id): void
    {
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
        if (User::where('role', Role::Owner)->count() <= 1 && User::find($id)?->isOwner()) {
            $this->dispatch('toast', message: 'Нельзя удалить единственного владельца.');

            return;
        }
        User::findOrFail($id)->delete();
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
