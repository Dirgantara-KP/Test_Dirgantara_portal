<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class DataPeserta extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static string $view = 'filament.pages.data-peserta';

    protected static ?string $navigationLabel = 'Job Title';

    protected static ?string $title = 'Job Title';

    protected static ?string $navigationGroup = 'Referensi';

    protected static ?int $navigationSort = 2;

    protected static ?string $slug = 'job-title';

    protected static bool $shouldRegisterNavigation = true;

    public static function getNavigationLabel(): string
    {
        return 'Job Title';
    }
}
