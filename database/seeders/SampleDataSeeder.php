<?php

namespace Database\Seeders;

use App\Enums\AppointmentStatus;
use App\Enums\TransactionType;
use App\Models\Appointment;
use App\Models\Client;
use App\Models\Service;
use App\Models\Staff;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        // --- Идемпотентность: чистим свои данные (не clients, не users) ---
        // Удаляем child→parent, без FK-нарушений; кросс-СУБД (SQLite/Postgres/MySQL).
        Transaction::query()->delete();
        Appointment::query()->delete();
        Service::query()->delete();
        Staff::query()->delete();

        // --- 1. Мастера ---
        $staffData = [
            [
                'name' => 'Дмитрий Волков',
                'role_title' => 'Главный мастер',
                'specialization' => 'Классика · фейд · борода',
                'color' => '#8b5cf6',
                'phone' => '+7 (916) 234-11-00',
                'is_active' => true,
                'sort' => 1,
            ],
            [
                'name' => 'Артём Носов',
                'role_title' => 'Барбер · фейд',
                'specialization' => 'Фейд · скин · текстура',
                'color' => '#22d3ee',
                'phone' => '+7 (903) 567-88-22',
                'is_active' => true,
                'sort' => 2,
            ],
            [
                'name' => 'Кирилл Попов',
                'role_title' => 'Барбер',
                'specialization' => 'Классика · камуфляж',
                'color' => '#c084fc',
                'phone' => '+7 (926) 100-33-45',
                'is_active' => true,
                'sort' => 3,
            ],
            [
                'name' => 'Максим Ершов',
                'role_title' => 'Барбер · борода',
                'specialization' => 'Бритьё · борода · опасная бритва',
                'color' => '#f0abfc',
                'phone' => '+7 (985) 412-77-09',
                'is_active' => true,
                'sort' => 4,
            ],
            [
                'name' => 'Иван Соколов',
                'role_title' => 'Стажёр',
                'specialization' => 'Детские стрижки · классика',
                'color' => '#38bdf8',
                'phone' => '+7 (911) 900-55-61',
                'is_active' => true,
                'sort' => 5,
            ],
        ];

        $staffRecords = [];
        foreach ($staffData as $data) {
            $staffRecords[] = Staff::create($data);
        }

        // --- 2. Услуги ---
        $servicesData = [
            ['name' => 'Стрижка классическая',        'category' => 'Стрижки',  'duration_min' => 45, 'price' => 1800.00, 'is_active' => true, 'sort' => 1],
            ['name' => 'Стрижка + борода',            'category' => 'Комплекс', 'duration_min' => 75, 'price' => 2800.00, 'is_active' => true, 'sort' => 2],
            ['name' => 'Бритьё опасной бритвой',      'category' => 'Борода',   'duration_min' => 30, 'price' => 1400.00, 'is_active' => true, 'sort' => 3],
            ['name' => 'Оформление бороды',            'category' => 'Борода',   'duration_min' => 30, 'price' => 900.00,  'is_active' => true, 'sort' => 4],
            ['name' => 'Детская стрижка',              'category' => 'Стрижки',  'duration_min' => 45, 'price' => 1200.00, 'is_active' => true, 'sort' => 5],
            ['name' => 'Камуфляж седины',              'category' => 'Окраска',  'duration_min' => 60, 'price' => 2200.00, 'is_active' => true, 'sort' => 6],
        ];

        $serviceRecords = [];
        foreach ($servicesData as $data) {
            $serviceRecords[] = Service::create($data);
        }

        // --- 3. Клиенты: берём существующих или создаём 10 ---
        $clients = Client::inRandomOrder()->get();

        if ($clients->isEmpty()) {
            $clientsData = [
                ['name' => 'Алексей Морозов',   'phone' => '+7 (916) 100-11-01', 'source' => 'instagram'],
                ['name' => 'Николай Захаров',   'phone' => '+7 (926) 200-22-02', 'source' => 'vk'],
                ['name' => 'Сергей Козлов',     'phone' => '+7 (903) 300-33-03', 'source' => 'рекомендация'],
                ['name' => 'Игорь Петров',      'phone' => '+7 (985) 400-44-04', 'source' => 'instagram'],
                ['name' => 'Андрей Сидоров',    'phone' => '+7 (911) 500-55-05', 'source' => 'google'],
                ['name' => 'Павел Фёдоров',     'phone' => '+7 (916) 600-66-06', 'source' => 'vk'],
                ['name' => 'Михаил Лебедев',    'phone' => '+7 (926) 700-77-07', 'source' => 'рекомендация'],
                ['name' => 'Владимир Новиков',  'phone' => '+7 (903) 800-88-08', 'source' => 'instagram'],
                ['name' => 'Роман Орлов',       'phone' => '+7 (985) 900-99-09', 'source' => 'google'],
                ['name' => 'Евгений Степанов',  'phone' => '+7 (911) 010-10-10', 'source' => 'рекомендация'],
            ];
            foreach ($clientsData as $cd) {
                Client::create($cd);
            }
            $clients = Client::inRandomOrder()->get();
        }

        $clientIds = $clients->pluck('id')->toArray();
        $staffIds = array_map(fn ($s) => $s->id, $staffRecords);
        $serviceCount = count($serviceRecords);
        $today = Carbon::today();

        // --- 4. Записи: -14 дней … +5 дней ---
        $appointments = [];

        for ($dayOffset = -14; $dayOffset <= 5; $dayOffset++) {
            $day = $today->copy()->addDays($dayOffset);

            // 6–10 записей в день
            $countPerDay = rand(6, 10);

            // Слоты: с 9:00 до 21:00 с шагом ~30 мин
            $slots = [];
            $cursor = $day->copy()->setTime(9, 0);
            while ($cursor->hour < 21) {
                $slots[] = $cursor->copy();
                $cursor->addMinutes(30);
            }
            shuffle($slots);
            $usedSlots = array_slice($slots, 0, $countPerDay);

            foreach ($usedSlots as $slot) {
                /** @var Service $service */
                $service = $serviceRecords[array_rand($serviceRecords)];
                $staffId = $staffIds[array_rand($staffIds)];
                $clientId = $clientIds[array_rand($clientIds)];

                $startsAt = $slot;
                $endsAt = $slot->copy()->addMinutes($service->duration_min);

                // Определяем статус
                if ($dayOffset < 0) {
                    // Прошлые — в основном completed
                    $roll = rand(1, 10);
                    if ($roll <= 7) {
                        $status = AppointmentStatus::Completed;
                    } elseif ($roll <= 9) {
                        $status = AppointmentStatus::NoShow;
                    } else {
                        $status = AppointmentStatus::Cancelled;
                    }
                } elseif ($dayOffset === 0) {
                    // Сегодня — часть уже завершена (до текущего часа), часть planned
                    $status = $startsAt->lt(Carbon::now())
                        ? AppointmentStatus::Completed
                        : AppointmentStatus::Planned;
                } else {
                    // Будущие — planned
                    $status = AppointmentStatus::Planned;
                }

                $appointments[] = [
                    'service' => $service,
                    'staffId' => $staffId,
                    'clientId' => $clientId,
                    'startsAt' => $startsAt,
                    'endsAt' => $endsAt,
                    'status' => $status,
                    'price' => $service->price,
                ];
            }
        }

        // --- 5. Вставляем записи и транзакции доходов ---
        $paymentMethods = ['наличные', 'карта', 'перевод'];

        foreach ($appointments as $apt) {
            $appointment = Appointment::create([
                'client_id' => $apt['clientId'],
                'staff_id' => $apt['staffId'],
                'service_id' => $apt['service']->id,
                'starts_at' => $apt['startsAt'],
                'ends_at' => $apt['endsAt'],
                'status' => $apt['status']->value,
                'price' => $apt['price'],
            ]);

            // Для completed — создаём транзакцию дохода
            if ($apt['status'] === AppointmentStatus::Completed) {
                Transaction::create([
                    'appointment_id' => $appointment->id,
                    'client_id' => $appointment->client_id,
                    'staff_id' => $appointment->staff_id,
                    'label' => $apt['service']->name,
                    'amount' => $appointment->price,
                    'type' => TransactionType::Income->value,
                    'method' => $paymentMethods[array_rand($paymentMethods)],
                    'occurred_at' => $appointment->starts_at,
                ]);
            }
        }

        // --- 6. Расходные операции (~10 штук) ---
        $expenseData = [
            ['label' => 'Аренда помещения',        'amount' => 85000.00],
            ['label' => 'Расходные материалы',      'amount' => 12400.00],
            ['label' => 'Реклама в Instagram',      'amount' => 8500.00],
            ['label' => 'Коммунальные услуги',      'amount' => 6300.00],
            ['label' => 'Закупка инструментов',     'amount' => 14200.00],
            ['label' => 'Реклама ВКонтакте',        'amount' => 5000.00],
            ['label' => 'Канцелярия и хоз. нужды',  'amount' => 2100.00],
            ['label' => 'Обслуживание оборудования', 'amount' => 7800.00],
            ['label' => 'Интернет и телефония',     'amount' => 3200.00],
            ['label' => 'Лицензионное ПО',          'amount' => 4500.00],
        ];

        foreach ($expenseData as $idx => $expense) {
            // Рассыпаем по последним ~14 дням
            $offsetDays = rand(0, 13);
            $occurredAt = $today->copy()->subDays($offsetDays)->setTime(rand(10, 18), rand(0, 59));

            Transaction::create([
                'appointment_id' => null,
                'client_id' => null,
                'staff_id' => null,
                'label' => $expense['label'],
                'amount' => $expense['amount'],
                'type' => TransactionType::Expense->value,
                'method' => 'перевод',
                'occurred_at' => $occurredAt,
            ]);
        }
    }
}
