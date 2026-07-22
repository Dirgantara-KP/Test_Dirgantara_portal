<?php

namespace App\Filament\Widgets;

use App\Models\EventPeserta;
use Filament\Widgets\ChartWidget;

class ChartStatusPengerjaan extends ChartWidget
{
    protected static ?string $heading = 'Status Pengerjaan Tes';
    protected static ?int $sort = 4;

    protected function getData(): array
    {
        $selesai = EventPeserta::where('status_pengerjaan', 'selesai')->count();
        $total = EventPeserta::count();

        if ($total <= 0) {
            return [
                'datasets' => [[
                    'label' => 'Status',
                    'data' => [0, 0],
                    'backgroundColor' => ['#10b981', '#f59e0b'],
                    'borderColor' => ['#059669', '#d97706'],
                ]],
                'labels' => ['Selesai', 'Belum Selesai'],
            ];
        }

        return [
            'datasets' => [[
                'label' => 'Status',
                'data' => [$selesai, $total - $selesai],
                'backgroundColor' => ['#10b981', '#f59e0b'],
                'borderColor' => ['#059669', '#d97706'],
            ]],
            'labels' => ['Selesai', 'Belum Selesai'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
