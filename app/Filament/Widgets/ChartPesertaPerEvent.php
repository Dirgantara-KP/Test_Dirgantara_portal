<?php

namespace App\Filament\Widgets;

use App\Models\Event;
use App\Models\EventPeserta;
use Filament\Widgets\ChartWidget;

class ChartPesertaPerEvent extends ChartWidget
{
    protected static ?string $heading = 'Jumlah Peserta per Event';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $events = Event::withCount('pesertas')->orderBy('tanggal_event')->get();

        return [
            'datasets' => [
                [
                    'label' => 'Peserta',
                    'data' => $events->pluck('pesertas_count')->toArray(),
                    'backgroundColor' => '#f59e0b',
                    'borderColor' => '#d97706',
                ],
            ],
            'labels' => $events->pluck('nama_event')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
