@php use App\Support\Crm; @endphp
<div>
    <div class="tabs">
        <button class="tabs__tab {{ $tab === 'profile' ? 'tabs__tab--active' : '' }}" wire:click="$set('tab','profile')">Профиль бизнеса</button>
        <button class="tabs__tab {{ $tab === 'staff' ? 'tabs__tab--active' : '' }}" wire:click="$set('tab','staff')">{{ Crm::label('staff') }}</button>
        <button class="tabs__tab {{ $tab === 'services' ? 'tabs__tab--active' : '' }}" wire:click="$set('tab','services')">{{ Crm::label('services') }}</button>
        <button class="tabs__tab {{ $tab === 'users' ? 'tabs__tab--active' : '' }}" wire:click="$set('tab','users')">Пользователи</button>
    </div>

    {{-- ПРОФИЛЬ --}}
    @if ($tab === 'profile')
        <div class="card" style="max-width:520px">
            <div class="card__head"><h2 class="card__title">Профиль бизнеса</h2></div>
            <div class="card__body">
                <form class="form" wire:submit="saveProfile">
                    <label class="field">
                        <span class="field__label">Название бизнеса</span>
                        <input type="text" class="field__input" wire:model="businessName">
                        @error('businessName') <span class="field__error">{{ $message }}</span> @enderror
                    </label>
                    <label class="field">
                        <span class="field__label">Символ валюты</span>
                        <input type="text" class="field__input" wire:model="currencySymbol">
                        @error('currencySymbol') <span class="field__error">{{ $message }}</span> @enderror
                    </label>
                    <div><button type="submit" class="btn btn--primary">Сохранить</button></div>
                </form>
            </div>
        </div>
    @endif

    {{-- МАСТЕРА --}}
    @if ($tab === 'staff')
        <div class="card-grid card-grid--2">
            <div class="card card--flush">
                <div class="table-wrap">
                    <table class="table">
                        <thead><tr><th>{{ Crm::label('staff_singular') }}</th><th>Должность</th><th>Статус</th><th></th></tr></thead>
                        <tbody>
                            @forelse ($staff as $s)
                                <tr wire:key="staff-{{ $s->id }}">
                                    <td><span class="avatar" style="margin-right:8px">{{ $s->initials() }}</span><span class="table__name">{{ $s->name }}</span></td>
                                    <td class="table__muted">{{ $s->role_title ?: '—' }}</td>
                                    <td>@if ($s->is_active)<span class="badge badge--success">активен</span>@else<span class="badge badge--muted">скрыт</span>@endif</td>
                                    <td><div class="table__actions">
                                        <button class="btn btn--ghost btn--sm" wire:click="editStaff({{ $s->id }})">Правка</button>
                                        <button class="btn btn--ghost btn--sm" wire:click="deleteStaff({{ $s->id }})" wire:confirm="Удалить?">×</button>
                                    </div></td>
                                </tr>
                            @empty
                                <tr><td colspan="4"><div class="empty"><div class="empty__title">Нет {{ mb_strtolower(Crm::label('staff')) }}</div></div></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card">
                <div class="card__head"><h2 class="card__title">{{ $staffEditing ? 'Правка' : 'Новый' }}</h2></div>
                <div class="card__body">
                    <form class="form" wire:submit="saveStaff">
                        <label class="field"><span class="field__label">Имя *</span><input type="text" class="field__input" wire:model="staffForm.name">@error('staffForm.name')<span class="field__error">{{ $message }}</span>@enderror</label>
                        <label class="field"><span class="field__label">Должность</span><input type="text" class="field__input" wire:model="staffForm.role_title"></label>
                        <label class="field"><span class="field__label">Специализация</span><input type="text" class="field__input" wire:model="staffForm.specialization"></label>
                        <label class="checkbox"><input type="checkbox" class="checkbox__input" wire:model="staffForm.is_active"> <span class="checkbox__label">Активен</span></label>
                        <div style="display:flex;gap:8px">
                            <button type="submit" class="btn btn--primary">Сохранить</button>
                            @if ($staffEditing)<button type="button" class="btn btn--ghost" wire:click="resetStaffForm">Отмена</button>@endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- УСЛУГИ --}}
    @if ($tab === 'services')
        <div class="card-grid card-grid--2">
            <div class="card card--flush">
                <div class="table-wrap">
                    <table class="table">
                        <thead><tr><th>{{ Crm::label('service') }}</th><th>Время</th><th>Цена</th><th></th></tr></thead>
                        <tbody>
                            @forelse ($services as $sv)
                                <tr wire:key="svc-{{ $sv->id }}">
                                    <td><span class="table__name">{{ $sv->name }}</span>@if ($sv->category)<div class="table__muted">{{ $sv->category }}</div>@endif</td>
                                    <td class="table__muted">{{ $sv->duration_min }} мин</td>
                                    <td>{{ Crm::money($sv->price) }}</td>
                                    <td><div class="table__actions">
                                        <button class="btn btn--ghost btn--sm" wire:click="editService({{ $sv->id }})">Правка</button>
                                        <button class="btn btn--ghost btn--sm" wire:click="deleteService({{ $sv->id }})" wire:confirm="Удалить?">×</button>
                                    </div></td>
                                </tr>
                            @empty
                                <tr><td colspan="4"><div class="empty"><div class="empty__title">Нет {{ mb_strtolower(Crm::label('services')) }}</div></div></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card">
                <div class="card__head"><h2 class="card__title">{{ $serviceEditing ? 'Правка' : 'Новая' }}</h2></div>
                <div class="card__body">
                    <form class="form" wire:submit="saveService">
                        <label class="field"><span class="field__label">Название *</span><input type="text" class="field__input" wire:model="serviceForm.name">@error('serviceForm.name')<span class="field__error">{{ $message }}</span>@enderror</label>
                        <label class="field"><span class="field__label">Категория</span><input type="text" class="field__input" wire:model="serviceForm.category"></label>
                        <div class="field-row">
                            <label class="field"><span class="field__label">Минут *</span><input type="number" class="field__input" wire:model="serviceForm.duration_min">@error('serviceForm.duration_min')<span class="field__error">{{ $message }}</span>@enderror</label>
                            <label class="field"><span class="field__label">Цена *</span><input type="number" step="0.01" class="field__input" wire:model="serviceForm.price">@error('serviceForm.price')<span class="field__error">{{ $message }}</span>@enderror</label>
                        </div>
                        <label class="checkbox"><input type="checkbox" class="checkbox__input" wire:model="serviceForm.is_active"> <span class="checkbox__label">Активна</span></label>
                        <div style="display:flex;gap:8px">
                            <button type="submit" class="btn btn--primary">Сохранить</button>
                            @if ($serviceEditing)<button type="button" class="btn btn--ghost" wire:click="resetServiceForm">Отмена</button>@endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- ПОЛЬЗОВАТЕЛИ --}}
    @if ($tab === 'users')
        <div class="card-grid card-grid--2">
            <div class="card card--flush">
                <div class="table-wrap">
                    <table class="table">
                        <thead><tr><th>Пользователь</th><th>Роль</th><th>Статус</th><th></th></tr></thead>
                        <tbody>
                            @foreach ($users as $u)
                                <tr wire:key="user-{{ $u->id }}">
                                    <td><span class="table__name">{{ $u->name }}</span><div class="table__muted">{{ $u->email }}</div></td>
                                    <td><span class="badge {{ $u->isOwner() ? 'badge--info' : '' }}">{{ $u->role->label() }}</span></td>
                                    <td>@if ($u->is_active)<span class="badge badge--success">активен</span>@else<span class="badge badge--muted">отключён</span>@endif</td>
                                    <td><div class="table__actions">
                                        <button class="btn btn--ghost btn--sm" wire:click="editUser({{ $u->id }})">Правка</button>
                                        <button class="btn btn--ghost btn--sm" wire:click="deleteUser({{ $u->id }})" wire:confirm="Удалить пользователя?">×</button>
                                    </div></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card">
                <div class="card__head"><h2 class="card__title">{{ $userEditing ? 'Правка' : 'Новый' }}</h2></div>
                <div class="card__body">
                    <form class="form" wire:submit="saveUser">
                        <label class="field"><span class="field__label">Имя *</span><input type="text" class="field__input" wire:model="userForm.name">@error('userForm.name')<span class="field__error">{{ $message }}</span>@enderror</label>
                        <label class="field"><span class="field__label">Email *</span><input type="email" class="field__input" wire:model="userForm.email">@error('userForm.email')<span class="field__error">{{ $message }}</span>@enderror</label>
                        <label class="field"><span class="field__label">Роль</span>
                            <select class="field__select" wire:model="userForm.role">
                                @foreach ($roleOptions as $opt)<option value="{{ $opt['value'] }}">{{ $opt['label'] }}</option>@endforeach
                            </select>
                        </label>
                        <label class="field"><span class="field__label">Пароль {{ $userEditing ? '(оставьте пустым)' : '*' }}</span><input type="password" class="field__input" wire:model="userForm.password">@error('userForm.password')<span class="field__error">{{ $message }}</span>@enderror</label>
                        <label class="checkbox"><input type="checkbox" class="checkbox__input" wire:model="userForm.is_active"> <span class="checkbox__label">Активен</span></label>
                        <div style="display:flex;gap:8px">
                            <button type="submit" class="btn btn--primary">Сохранить</button>
                            @if ($userEditing)<button type="button" class="btn btn--ghost" wire:click="resetUserForm">Отмена</button>@endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
