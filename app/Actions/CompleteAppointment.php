<?php

namespace App\Actions;

use App\Enums\AppointmentStatus;
use App\Enums\TransactionType;
use App\Models\Appointment;
use App\Models\Transaction;

class CompleteAppointment
{
    /** Завершить запись и зафиксировать доход (идемпотентно). */
    public function handle(Appointment $appointment): void
    {
        if ($appointment->status !== AppointmentStatus::Completed) {
            $appointment->update(['status' => AppointmentStatus::Completed]);
        }

        if (! Transaction::where('appointment_id', $appointment->id)->exists()) {
            Transaction::create([
                'appointment_id' => $appointment->id,
                'client_id' => $appointment->client_id,
                'staff_id' => $appointment->staff_id,
                'label' => $appointment->service?->name ?? 'Услуга',
                'amount' => $appointment->price,
                'type' => TransactionType::Income,
                'occurred_at' => $appointment->starts_at,
            ]);
        }
    }
}
