<?php

namespace Zenserp\Transfer;

class ZenserpPagination implements IZenserpTransfer
{
    public function __construct(public ?array $record = null, private int $perPage = 10) {}

    public function parse(): array
    {
        $pages = (int) ($this->record['pages'] ?? 0);
        $total = (int) ($this->record['total'] ?? 0);

        if ($total <= 0 && $pages > 0) {
            $total = $pages;
        }

        return [
            'pages' => $pages,
            'total' => $total * $this->perPage,
        ];
    }
}
