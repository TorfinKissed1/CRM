<?php

namespace App\Actions;

use App\Models\Transaction;
use Illuminate\Support\Carbon;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportTransactions
{
    public function handle(?Carbon $start = null): BinaryFileResponse
    {
        $base = tempnam(sys_get_temp_dir(), 'finance_');
        $path = $base.'.xlsx';
        @unlink($base); // tempnam создаёт пустышку без .xlsx — убираем, чтобы не текла

        $writer = new Writer;
        $writer->openToFile($path);
        $writer->addRow(Row::fromValues(['Дата', 'Время', 'Операция', 'Клиент', 'Мастер', 'Тип', 'Сумма']));

        Transaction::query()
            ->when($start, fn ($q) => $q->where('occurred_at', '>=', $start))
            ->with(['client', 'staff'])
            ->orderByDesc('occurred_at')
            ->chunk(300, function ($chunk) use ($writer) {
                foreach ($chunk as $t) {
                    $writer->addRow(Row::fromValues([
                        $t->occurred_at->format('d.m.Y'),
                        $t->occurred_at->format('H:i'),
                        (string) $t->label,
                        (string) $t->client?->name,
                        (string) $t->staff?->name,
                        $t->type->label(),
                        (float) $t->amount,
                    ]));
                }
            });

        $writer->close();

        return response()
            ->download($path, 'finance_'.date('Y-m-d').'.xlsx')
            ->deleteFileAfterSend();
    }
}
