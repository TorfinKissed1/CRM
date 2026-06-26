<?php

namespace App\Console\Commands;

use App\Enums\Role;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class InstallCommand extends Command
{
    protected $signature = 'crm:install
        {--name= : Имя владельца}
        {--email= : Email для входа}
        {--password= : Пароль}
        {--business= : Название бизнеса}';

    protected $description = 'Создать владельца и базовые настройки CRM (без демо-данных)';

    public function handle(): int
    {
        $name = $this->option('name') ?: $this->ask('Имя владельца', 'Владелец');
        $email = $this->option('email') ?: $this->ask('Email для входа');
        $password = $this->option('password') ?: $this->secret('Пароль (мин. 6 символов)');

        $validator = Validator::make(
            compact('name', 'email', 'password'),
            [
                'name' => 'required|string|max:255',
                'email' => 'required|email',
                'password' => 'required|string|min:6',
            ]
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => $password, // хэшируется кастом 'hashed'
                'role' => Role::Owner,
                'is_active' => true,
            ]
        );

        if ($business = $this->option('business')) {
            Setting::put('business_name', $business);
        }

        $this->info("Владелец готов: {$user->email}");
        $this->line('Войти: '.url('/login'));

        return self::SUCCESS;
    }
}
