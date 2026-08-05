<?php

namespace App\Filament\Widgets;

use App\Models\JobTitle;
use Filament\Widgets\ChartWidget;

class ChartPesertaPerJobTitle extends ChartWidget
{
    protected static ?string $heading = 'Distribusi Peserta per Job Title';

    protected static ?int $sort = 5;

    protected function getData(): array
    {
        $jobTitles = JobTitle::withCount('pesertas')->get();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Peserta',
                    'data' => $jobTitles->pluck('pesertas_count')->toArray(),
                    'backgroundColor' => '#8b5cf6',
                    'borderColor' => '#7c3aed',
                ],
            ],
            'labels' => $jobTitles->pluck('nama_jobtitle')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
