<?php

namespace App\Exports;

use App\Models\EventPeserta;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SelectedHasilTestExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(protected array $ids) {}

    public function collection()
    {
        return EventPeserta::with(['event', 'peserta.jobTitle'])
            ->whereIn('id', $this->ids)
            ->get();
    }

    public function headings(): array
    {
        return ['Nama Event', 'Tanggal', 'NIK', 'Nama Peserta', 'Email', 'Organisasi', 'Job Title', 'Skor PG', 'Skor Esai', 'Skor Akhir'];
    }

    public function map($row): array
    {
        return [
            $row->event->nama_event,
            $row->event->tanggal_event->format('d-m-Y'),
            $row->peserta->nik,
            $row->peserta->nama,
            $row->peserta->email,
            $row->peserta->organisasi,
            $row->peserta->jobTitle?->nama_jobtitle,
            $row->skor_pg,
            $row->skor_esai_manual ?? 'Belum dinilai',
            ($row->skor_pg ?? 0) + ($row->skor_esai_manual ?? 0),
        ];
    }
}
