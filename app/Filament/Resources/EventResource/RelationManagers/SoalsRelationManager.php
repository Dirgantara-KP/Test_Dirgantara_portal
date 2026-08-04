<?php

namespace App\Filament\Resources\EventResource\RelationManagers;

use App\Models\Soal;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class SoalsRelationManager extends RelationManager
{
    protected static string $relationship = 'soals';

    protected static ?string $title = 'Bank Soal untuk Event (berdasarkan Job Title yang dipilih)';

    public function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('pertanyaan')
            ->columns([
                Tables\Columns\TextColumn::make('jobTitle.nama_jobtitle')->label('Job Title'),
                Tables\Columns\TextColumn::make('kode_soal')->label('Kode Soal')->searchable(),
Tables\Columns\TextColumn::make('pertanyaan')->limit(60),
                Tables\Columns\TextColumn::make('tipeSoal.nama')->label('Tipe')->badge(),
                Tables\Columns\TextColumn::make('kategori')->label('Kategori'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tipe_soal_id')
                    ->relationship('tipeSoal', 'nama')
                    ->label('Tipe Soal'),
                Tables\Filters\SelectFilter::make('kategori')
                    ->options(function () {
                        return Soal::whereNotNull('kategori')
                            ->pluck('kategori', 'kategori')
                            ->unique()
                            ->toArray();
                    })
                    ->label('Kategori'),
            ])
            ->headerActions([])
            ->actions([
                Tables\Actions\Action::make('lihatDetail')
                    ->label('Lihat Detail')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Detail Soal')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->infolist(function (Soal $record) {
                        $opsiList = $record->jawabanSoals->map(function ($opsi) {
                            $label = ($opsi->nilai ? '✅ (Benar) ' : '   ') . $opsi->jawaban;
                            return $label;
                        })->implode("\n");

                        return [
                            \Filament\Infolists\Components\TextEntry::make('pertanyaan')
                                ->label('Pertanyaan')
                                ->default($record->pertanyaan),
                            \Filament\Infolists\Components\TextEntry::make('jobTitle')
                                ->label('Job Title')
                                ->default($record->jobTitle?->nama_jobtitle),
                            \Filament\Infolists\Components\TextEntry::make('tipeSoal')
                                ->label('Tipe Soal')
                                ->default($record->tipeSoal?->nama),
                            \Filament\Infolists\Components\TextEntry::make('opsi')
                                ->label('Opsi Jawaban')
                                ->default($opsiList ?: '-'),
                        ];
                    }),
            ]);
    }

    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $eventId = $this->owner->id;

        // Ambil Job Title IDs dari pivot event_soal (bukan dari peserta)
        $jobTitleIds = DB::table('event_soal')
            ->where('event_id', $eventId)
            ->pluck('job_title_id')
            ->unique()
            ->toArray();

        return Soal::whereIn('job_title_id', $jobTitleIds)
            ->with(['jobTitle', 'tipeSoal']);
    }
}
