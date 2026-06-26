<?php

namespace App\Actions;

use App\Models\Client;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportClients
{
    /** Колонки экспорта (заголовок => поле модели). */
    public const COLUMNS = [
        'Имя' => 'name',
        'Телефон' => 'phone',
        'Email' => 'email',
        'Город' => 'city',
        'VK' => 'vk',
        'Telegram' => 'telegram',
        'Instagram' => 'instagram',
        'WhatsApp' => 'whatsapp',
        'Заметки' => 'notes',
    ];

    public function handle(): BinaryFileResponse
    {
        $base = tempnam(sys_get_temp_dir(), 'clients_');
        $path = $base.'.xlsx';
        @unlink($base); // tempnam создаёт пустышку без .xlsx — убираем, чтобы не текла

        $writer = new Writer;
        $writer->openToFile($path);
        $writer->addRow(Row::fromValues(array_keys(self::COLUMNS)));

        Client::query()->orderBy('name')->chunk(300, function ($chunk) use ($writer) {
            foreach ($chunk as $client) {
                $writer->addRow(Row::fromValues(array_map(
                    fn ($field) => (string) $client->{$field},
                    array_values(self::COLUMNS)
                )));
            }
        });

        $writer->close();

        return response()
            ->download($path, 'clients_'.date('Y-m-d').'.xlsx')
            ->deleteFileAfterSend();
    }
}
