<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TipeSoalResource\Pages;
use App\Models\TipeSoal;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TipeSoalResource extends Resource
{
    protected static ?string $model = TipeSoal::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'Mengelola Soal';

    protected static ?string $navigationLabel = 'Tipe Soal';

    protected static ?int $navigationSort = 1;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('kode')->required()->helperText('contoh: pg / esai'),
            Forms\Components\TextInput::make('nama')->required()->helperText('contoh: Pilihan Ganda / Esai'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('kode'),
            Tables\Columns\TextColumn::make('nama'),
        ])->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTipeSoals::route('/'),
            'create' => Pages\CreateTipeSoal::route('/create'),
            'edit' => Pages\EditTipeSoal::route('/{record}/edit'),
        ];
    }
}
