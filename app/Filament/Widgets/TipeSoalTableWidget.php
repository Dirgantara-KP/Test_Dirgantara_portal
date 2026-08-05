<?php

namespace App\Filament\Widgets;

use App\Models\TipeSoal;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TipeSoalTableWidget extends BaseWidget
{
    protected static ?string $heading = 'Tipe Soal';

    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        return request()?->routeIs('filament.admin.resources.soals.index') ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(TipeSoal::query())
            ->columns([
                TextColumn::make('kode')->searchable()->label('Kode'),
                TextColumn::make('nama')->searchable()->label('Nama Tipe Soal'),
            ])
            ->headerActions([
                Tables\Actions\Action::make('tambahTipeSoal')
                    ->label('Tambah Tipe Soal')
                    ->icon('heroicon-o-plus')
                    ->form([
                        TextInput::make('kode')
                            ->label('Kode')
                            ->required()
                            ->helperText('contoh: pg / esai')
                            ->unique('tipe_soals', 'kode'),
                        TextInput::make('nama')
                            ->label('Nama Tipe Soal')
                            ->required()
                            ->helperText('contoh: Pilihan Ganda / Esai')
                            ->unique('tipe_soals', 'nama'),
                    ])
                    ->action(function (array $data) {
                        TipeSoal::create($data);
                        Notification::make()->title('Tipe Soal berhasil ditambahkan')->success()->send();
                    }),
            ])
            ->actions([
                EditAction::make()
                    ->form([
                        TextInput::make('kode')
                            ->label('Kode')
                            ->required()
                            ->unique('tipe_soals', 'kode', ignoreRecord: true),
                        TextInput::make('nama')
                            ->label('Nama Tipe Soal')
                            ->required()
                            ->unique('tipe_soals', 'nama', ignoreRecord: true),
                    ]),
                DeleteAction::make(),
            ]);
    }
}
