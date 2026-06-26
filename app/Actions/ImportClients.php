<?php

namespace App\Actions;

use App\Models\Client;
use OpenSpout\Reader\CSV\Options as CsvOptions;
use OpenSpout\Reader\CSV\Reader as CsvReader;
use OpenSpout\Reader\XLSX\Reader as XlsxReader;

class ImportClients
{
    /** Поля назначения для маппинга. */
    public const FIELDS = [
        'name' => 'Имя',
        'phone' => 'Телефон',
        'email' => 'Email',
        'city' => 'Город',
        'vk' => 'VK',
        'telegram' => 'Telegram',
        'instagram' => 'Instagram',
        'whatsapp' => 'WhatsApp',
        'source' => 'Источник',
        'notes' => 'Заметки',
    ];

    private function reader(string $ext, string $path): XlsxReader|CsvReader
    {
        if (in_array(strtolower($ext), ['csv', 'txt'], true)) {
            $options = new CsvOptions;
            $options->FIELD_DELIMITER = $this->detectDelimiter($path);
            $options->SHOULD_PRESERVE_EMPTY_ROWS = false;

            return new CsvReader($options);
        }

        return new XlsxReader;
    }

    /** Нормализация телефона к +7XXXXXXXXXX для консистентного дедупа. */
    private function normalizePhone(string $raw): string
    {
        $digits = preg_replace('/\D+/', '', $raw) ?? '';
        if ($digits === '') {
            return trim($raw);
        }
        if (strlen($digits) === 11 && $digits[0] === '8') {
            $digits = '7'.substr($digits, 1);
        }
        if (strlen($digits) === 10) {
            $digits = '7'.$digits;
        }
        if (strlen($digits) === 11 && $digits[0] === '7') {
            return '+'.$digits;
        }

        return trim($raw);
    }

    private function detectDelimiter(string $path): string
    {
        $line = '';
        if ($handle = @fopen($path, 'r')) {
            $line = (string) fgets($handle);
            fclose($handle);
        }

        return substr_count($line, ';') > substr_count($line, ',') ? ';' : ',';
    }

    /** Заголовки + до 3 строк-образцов для шага маппинга. */
    public function preview(string $path, string $ext): array
    {
        $reader = $this->reader($ext, $path);
        $reader->open($path);

        $headers = [];
        $sample = [];
        $i = 0;

        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $values = array_map(fn ($v) => is_scalar($v) ? (string) $v : '', $row->toArray());
                if ($i === 0) {
                    $headers = $values;
                } else {
                    $sample[] = $values;
                }
                $i++;
                if ($i > 3) {
                    break;
                }
            }
            break;
        }

        $reader->close();

        return [$headers, $sample];
    }

    /** Импорт. $map: field => имя колонки-источника. $dedup: skip|update. */
    public function handle(string $path, string $ext, array $map, string $dedup = 'skip'): array
    {
        $reader = $this->reader($ext, $path);
        $reader->open($path);

        $imported = 0;
        $updated = 0;
        $skipped = 0;
        $headerIndex = [];
        $i = 0;

        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $values = array_map(fn ($v) => is_scalar($v) ? trim((string) $v) : '', $row->toArray());

                if ($i === 0) {
                    // Позиционный маппинг: первое непустое вхождение заголовка побеждает
                    // (array_flip ломается на дублях/пустых заголовках).
                    foreach ($values as $idx => $header) {
                        $header = trim($header);
                        if ($header !== '' && ! array_key_exists($header, $headerIndex)) {
                            $headerIndex[$header] = $idx;
                        }
                    }
                    $i++;

                    continue;
                }

                $data = [];
                foreach ($map as $field => $sourceHeader) {
                    if ($sourceHeader === '' || $sourceHeader === null) {
                        continue;
                    }
                    $idx = $headerIndex[$sourceHeader] ?? null;
                    $data[$field] = $idx !== null ? ($values[$idx] ?? '') : '';
                }

                $i++;

                if (empty($data['name'])) {
                    $skipped++; // строки без имени тоже учитываем в сводке

                    continue;
                }

                if (! empty($data['phone'])) {
                    $data['phone'] = $this->normalizePhone($data['phone']);
                }

                $phone = $data['phone'] ?? null;
                $existing = $phone ? Client::where('phone', $phone)->first() : null;

                if ($existing) {
                    if ($dedup === 'update') {
                        $existing->update(array_filter($data, fn ($v) => $v !== '' && $v !== null));
                        $updated++;
                    } else {
                        $skipped++;
                    }

                    continue;
                }

                Client::create($data);
                $imported++;
            }
            break;
        }

        $reader->close();

        return compact('imported', 'updated', 'skipped');
    }
}
