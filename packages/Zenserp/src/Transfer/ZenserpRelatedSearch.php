<?php

namespace Zenserp\Transfer;

class ZenserpRelatedSearch implements IZenserpTransfer
{
    public function __construct(public array $record) {}

    public function parse(): array
    {
        return [
            'title' => $this->record['title'] ?? null,
            'url' => $this->record['url'] ?? null,
        ];
    }
}
