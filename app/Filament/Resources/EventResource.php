<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages;
use App\Filament\Resources\EventResource\RelationManagers\PesertasRelationManager;
use App\Models\Event;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'Referensi';

    protected static ?string $navigationLabel = 'Jadwal Event';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('kode_event')
                ->label('ID Event')
                ->required()
                ->unique(ignoreRecord: true)
                ->placeholder('TA2026001'),
            Forms\Components\TextInput::make('nama_event')
                ->required()
                ->maxLength(255),
            Forms\Components\DatePicker::make('tanggal_event')
                ->label('Tanggal')
                ->required(),
            Forms\Components\TimePicker::make('jam_mulai')
                ->required(),
            Forms\Components\TimePicker::make('jam_selesai')
                ->required(),
            Forms\Components\Radio::make('status')
                ->options(['draft' => 'Draft', 'aktif' => 'Aktif'])
                ->inline()
                ->default('draft')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode_event')->label('ID Event')->searchable(),
                Tables\Columns\TextColumn::make('nama_event')->searchable(),
                Tables\Columns\TextColumn::make('tanggal_event')->date('d M Y'),
                Tables\Columns\TextColumn::make('jam_mulai')->time('H:i'),
                Tables\Columns\TextColumn::make('jam_selesai')->time('H:i'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors(['gray' => 'draft', 'success' => 'aktif']),
                Tables\Columns\TextColumn::make('pesertas_count')->counts('pesertas')->label('Jml Peserta'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            PesertasRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}
