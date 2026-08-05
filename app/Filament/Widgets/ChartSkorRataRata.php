<?php

namespace App\Filament\Widgets;

use App\Models\EventPeserta;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ChartSkorRataRata extends ChartWidget
{
    protected static ?string $heading = 'Rata-rata Skor per Job Title';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $data = EventPeserta::select('job_titles.nama_jobtitle', DB::raw('AVG(COALESCE(event_peserta.skor_pg,0) + COALESCE(event_peserta.skor_esai_manual,0)) as rata_rata'))
            ->join('pesertas', 'event_peserta.peserta_id', '=', 'pesertas.id')
            ->join('job_titles', 'pesertas.job_title_id', '=', 'job_titles.id')
            ->where('event_peserta.status_pengerjaan', 'selesai')
            ->groupBy('job_titles.nama_jobtitle')
            ->get();

        return [
            'datasets' => [[
                'label' => 'Rata-rata Skor',
                'data' => $data->pluck('rata_rata')->map(fn ($v) => round((float) $v, 1))->toArray(),
                'backgroundColor' => '#10b981',
                'borderColor' => '#059669',
            ]],
            'labels' => $data->pluck('nama_jobtitle')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
