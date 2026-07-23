<?php

namespace Zenserp\Transfer;

class ZenserpResult implements IZenserpTransfer
{
    public function __construct(public array $record) {}

    public function parse(): array
    {
        return [
            'position' => $this->record['position'] ?? null,
            'title' => $this->record['title'] ?? null,
            'url' => $this->record['url'] ?? null,
            'destination' => $this->record['destination'] ?? null,
            'description' => $this->record['description'] ?? null,
        ];
    }
}
