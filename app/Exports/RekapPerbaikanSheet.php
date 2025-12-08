<?php

namespace App\Exports;

use App\Models\Rekap;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class RekapPerbaikanSheet implements FromArray, WithHeadings, ShouldAutoSize, WithTitle
{
    public function title(): string
    {
        return 'Perbaikan';
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Nama Barang',
            'Kategori',
            'User',
            'NIP User',
            'Estimasi',
            'Status',
        ];
    }

    public function array(): array
    {
        $data = [];
        $rekap = ReKap::with(['perbaikan.user'])
            ->whereNotNull('perbaikan_id')
            ->get();

        foreach ($rekap as $i => $r) {

            $tanggal = optional($r->perbaikan)->tgl_perbaikan
                ? \Carbon\Carbon::parse($r->perbaikan->tgl_perbaikan)->format('d-m-Y')
                : '-';

            $data[] = [
                $i + 1,
                $tanggal,
                optional($r->perbaikan)->nama,
                optional($r->perbaikan)->kategori,
                optional($r->perbaikan->user)->nama,
                optional($r->perbaikan->user)->nip,
                optional($r->perbaikan)->estimasi,
                optional($r->perbaikan)->status,
            ];
        }

        return $data;
    }
}
