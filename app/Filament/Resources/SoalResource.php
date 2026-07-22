<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SoalResource\Pages;
use App\Models\Soal;
use App\Models\TipeSoal;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SoalResource extends Resource
{
    protected static ?string $model = Soal::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Mengelola Soal';

    protected static ?string $navigationLabel = 'Bank Soal';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('job_title_id')
                ->label('Job Title')
                ->relationship('jobTitle', 'nama_jobtitle')
                ->searchable()->preload()->required(),

            Forms\Components\TextInput::make('kode_soal')
                ->label('Kode Soal')
                ->placeholder('001A')
                ->required(),

            Forms\Components\Select::make('tipe_soal_id')
                ->label('Tipe Soal')
                ->relationship('tipeSoal', 'nama')
                ->required()
                ->live()
                ->preload(),

            Forms\Components\TextInput::make('kategori')
                ->helperText('Contoh: Risk Management, K3LH (dipakai untuk pengelompokan di report)'),

            Forms\Components\Textarea::make('pertanyaan')
                ->required()
                ->rows(4),

            Forms\Components\FileUpload::make('gambar_pertanyaan')
                ->label('Gambar Pertanyaan (opsional)')
                ->image()
                ->directory('soal/pertanyaan'),

            Forms\Components\Repeater::make('jawabanSoals')
                ->relationship()
                ->label('Opsi Jawaban')
                ->visible(fn (Forms\Get $get) => TipeSoal::find($get('tipe_soal_id'))?->kode === 'pg')
                ->schema([
                    Forms\Components\Textarea::make('jawaban')->rows(2),
                    Forms\Components\FileUpload::make('gambar_jawaban')->image()->directory('soal/jawaban'),
                    Forms\Components\Toggle::make('nilai')->label('Kunci Jawaban Benar'),
                    Forms\Components\Hidden::make('urutan')->default(0),
                ])
                ->columns(2)
                ->minItems(2)
                ->reorderable(false)
                ->addActionLabel('Tambah Opsi Jawaban'),

            Forms\Components\Radio::make('status')
                ->options(['draft' => 'Draft', 'publish' => 'Publish'])
                ->inline()
                ->default('draft')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('jobTitle.nama_jobtitle')->label('Job Title')->searchable(),
                Tables\Columns\TextColumn::make('kode_soal')->searchable(),
                Tables\Columns\TextColumn::make('pertanyaan')->limit(60),
                Tables\Columns\TextColumn::make('tipeSoal.nama')->label('Tipe')->badge(),
                Tables\Columns\BadgeColumn::make('status')->colors(['gray' => 'draft', 'success' => 'publish']),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('job_title_id')
                    ->relationship('jobTitle', 'nama_jobtitle')
                    ->label('Job Title'),
                Tables\Filters\SelectFilter::make('tipe_soal_id')
                    ->relationship('tipeSoal', 'nama')
                    ->label('Tipe Soal'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('importExcel')
                    ->label('Import dari Excel')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->form([
                        Forms\Components\FileUpload::make('file')
                            ->label('File Excel (.xlsx)')
                            ->required()
                            ->disk('local')
                            ->directory('import-soal'),
                    ])
                    ->action(function (array $data) {
                        $path = \Illuminate\Support\Facades\Storage::disk('local')->path($data['file']);
                        if (! file_exists($path)) {
                            \Filament\Notifications\Notification::make()
                                ->title('File gagal diupload, coba upload ulang')
                                ->danger()
                                ->send();
                            return;
                        }
                        \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\SoalImport, $path);
                        \Filament\Notifications\Notification::make()
                            ->title('Soal berhasil diimport')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSoals::route('/'),
            'create' => Pages\CreateSoal::route('/create'),
            'edit' => Pages\EditSoal::route('/{record}/edit'),
        ];
    }
}
