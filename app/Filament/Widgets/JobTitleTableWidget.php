<?php

namespace App\Filament\Widgets;

use App\Models\JobTitle;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class JobTitleTableWidget extends BaseWidget
{
    protected static ?string $heading = 'Job Title';

    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        return request()?->routeIs('filament.admin.pages.data-peserta') ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(JobTitle::query())
            ->columns([
                TextColumn::make('nama_jobtitle')->searchable()->label('Nama Job Title'),
            ])
            ->headerActions([
                Tables\Actions\Action::make('tambahJobTitle')
                    ->label('Tambah Job Title')
                    ->icon('heroicon-o-plus')
                    ->form([
                        TextInput::make('nama_jobtitle')
                            ->label('Nama Job Title')
                            ->required()
                            ->unique('job_titles', 'nama_jobtitle'),
                    ])
                    ->action(function (array $data) {
                        JobTitle::create($data);
                        Notification::make()->title('Job Title berhasil ditambahkan')->success()->send();
                    }),
            ])
            ->actions([
                EditAction::make()
                    ->form([
                        TextInput::make('nama_jobtitle')
                            ->label('Nama Job Title')
                            ->required()
                            ->unique('job_titles', 'nama_jobtitle', ignoreRecord: true),
                    ]),
                DeleteAction::make(),
            ]);
    }
}
