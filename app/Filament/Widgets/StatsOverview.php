<?php

namespace App\Filament\Widgets;

use App\Models\Event;
use App\Models\Peserta;
use App\Models\Soal;
use App\Models\JobTitle;
use App\Models\EventPeserta;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalEvent = Event::count();
        $eventAktif = Event::where('status', 'aktif')->count();
        $totalJobTitle = JobTitle::count();
        $totalPeserta = Peserta::count();
        $totalSoal = Soal::count();
        $pesertaMengerjakan = EventPeserta::where('status_pengerjaan', 'selesai')->count();

        return [
            Stat::make('Total Event', $totalEvent)
                ->description('Jumlah seluruh event')
                ->descriptionIcon('heroicon-o-calendar-days')
                ->color('primary'),

            Stat::make('Event Aktif', $eventAktif)
                ->description('Event berstatus aktif')
                ->descriptionIcon('heroicon-o-play-circle')
                ->color('success'),

            Stat::make('Total Job Title', $totalJobTitle)
                ->description('Jumlah job title terdaftar')
                ->descriptionIcon('heroicon-o-briefcase')
                ->color('warning'),

            Stat::make('Total Peserta', $totalPeserta)
                ->description('Jumlah peserta terdaftar')
                ->descriptionIcon('heroicon-o-user-group')
                ->color('info'),

            Stat::make('Total Bank Soal', $totalSoal)
                ->description('Jumlah soal tersedia')
                ->descriptionIcon('heroicon-o-clipboard-document-list')
                ->color('danger'),

            Stat::make('Peserta Selesai Tes', $pesertaMengerjakan)
                ->description('Peserta yang sudah menyelesaikan tes')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),
        ];
    }
}
