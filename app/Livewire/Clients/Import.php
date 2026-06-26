<?php

namespace App\Livewire\Clients;

use App\Actions\ImportClients;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app')]
class Import extends Component
{
    use WithFileUploads;

    public $file;

    public array $headers = [];

    public array $sample = [];

    public array $map = [];

    public string $dedup = 'skip';

    public ?array $result = null;

    public array $fields = ImportClients::FIELDS;

    public function updatedFile(): void
    {
        $this->reset(['headers', 'sample', 'map', 'result']);
        $this->validate(['file' => 'required|file|max:10240']);

        [$headers, $sample] = app(ImportClients::class)
            ->preview($this->file->getRealPath(), $this->file->getClientOriginalExtension());

        $this->headers = $headers;
        $this->sample = $sample;
        $this->map = $this->autoMap($headers);
    }

    public function import(): void
    {
        $this->validate(['file' => 'required|file']);

        $this->result = app(ImportClients::class)->handle(
            $this->file->getRealPath(),
            $this->file->getClientOriginalExtension(),
            $this->map,
            $this->dedup,
        );
    }

    private function autoMap(array $headers): array
    {
        $rules = [
            'name' => ['name', 'имя', 'клиент', 'фио', 'название'],
            'phone' => ['phone', 'телефон', 'тел'],
            'email' => ['email', 'почта', 'e-mail'],
            'city' => ['city', 'город'],
            'vk' => ['vk', 'вконтакте'],
            'telegram' => ['telegram', 'телеграм', 'tg'],
            'instagram' => ['instagram', 'инстаграм', 'ig'],
            'whatsapp' => ['whatsapp', 'вотсап', 'wa'],
            'source' => ['source', 'источник', 'segment', 'сегмент'],
            'notes' => ['notes', 'замет', 'comment', 'why', 'коммент'],
        ];

        $map = [];
        foreach (array_keys($this->fields) as $field) {
            $map[$field] = '';
            foreach ($headers as $header) {
                $hl = Str::lower($header);
                foreach ($rules[$field] ?? [] as $kw) {
                    if (str_contains($hl, $kw)) {
                        $map[$field] = $header;
                        break 2;
                    }
                }
            }
        }

        return $map;
    }

    public function render()
    {
        return view('livewire.clients.import');
    }
}
