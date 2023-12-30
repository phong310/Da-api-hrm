<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;

class BaseExportTemplate implements FromArray
{
    protected $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function array(): array
    {
        return [
            ['name'],
            [$this->name ?: 'Sample name'],
        ];
    }
}
