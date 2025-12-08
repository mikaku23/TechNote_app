<?php

namespace App\Exports;

use App\Models\Rekap;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class RekapPenginstalanSheet implements FromArray, WithHeadings, ShouldAutoSize, WithTitle
{
    public function title(): string
    {
        return 'Penginstalan';
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Software',
            'Versi',
            'Kategori',
            'User',
            'NIM User',
            'Estimasi',
            'Status',
        ];
    }

    public function array(): array
    {
        $data = [];
        $rekap = Rekap::with(['penginstalan.software', 'penginstalan.user'])
            ->whereNotNull('penginstalan_id')
            ->get();

        foreach ($rekap as $i => $r) {

            $tanggal = optional($r->penginstalan)->tgl_instalasi
                ? \Carbon\Carbon::parse($r->penginstalan->tgl_instalasi)->format('d-m-Y')
                : '-';

            $data[] = [
                $i + 1,
                $tanggal,
                optional($r->penginstalan->software)->nama,
                optional($r->penginstalan->software)->versi,
                optional($r->penginstalan->software)->kategori,
                optional($r->penginstalan->user)->nama,
                optional($r->penginstalan->user)->nim,
                optional($r->penginstalan)->estimasi,
                optional($r->penginstalan)->status,
            ];
        }

        return $data;
    }
}
