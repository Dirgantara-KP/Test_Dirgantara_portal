<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KataPengantarResource\Pages;
use App\Models\KataPengantar;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class KataPengantarResource extends Resource
{
    protected static ?string $model = KataPengantar::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'Mengelola Soal';

    protected static ?string $navigationLabel = 'Kata Pengantar Soal';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('tipe_soal_id')
                ->label('Kriteria Soal')
                ->relationship('tipeSoal', 'nama')
                ->required(),
            Forms\Components\RichEditor::make('deskripsi')
                ->label('Deskripsi (bisa berisi gambar)')
                ->fileAttachmentsDirectory('kata-pengantar')
                ->required(),
            Forms\Components\Radio::make('status')
                ->options(['draft' => 'Draft', 'publish' => 'Publish'])
                ->inline()->default('draft')->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('tipeSoal.nama')->label('Kriteria Soal'),
            Tables\Columns\TextColumn::make('deskripsi')->limit(60)->html(),
            Tables\Columns\BadgeColumn::make('status')->colors(['gray' => 'draft', 'success' => 'publish']),
        ])->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKataPengantars::route('/'),
            'create' => Pages\CreateKataPengantar::route('/create'),
            'edit' => Pages\EditKataPengantar::route('/{record}/edit'),
        ];
    }
}
