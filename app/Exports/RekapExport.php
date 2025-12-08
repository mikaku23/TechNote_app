<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class RekapExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new RekapPerbaikanSheet(),
            new RekapPenginstalanSheet(),
        ];
    }
}
