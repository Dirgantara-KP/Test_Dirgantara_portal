<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PesertaResource\Pages;
use App\Models\Peserta;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PesertaResource extends Resource
{
    protected static ?string $model = Peserta::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Referensi';

    protected static ?string $navigationLabel = 'Peserta';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nik')->label('NIK')->required()->unique(ignoreRecord: true),
            Forms\Components\TextInput::make('nama')->required(),
            Forms\Components\TextInput::make('organisasi'),
            Forms\Components\TextInput::make('email')->email()->required()->unique(ignoreRecord: true),
            Forms\Components\Select::make('job_title_id')
                ->label('Job Title')
                ->relationship('jobTitle', 'nama_jobtitle')
                ->searchable()
                ->preload(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nik')->searchable(),
                Tables\Columns\TextColumn::make('nama')->searchable(),
                Tables\Columns\TextColumn::make('organisasi'),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\TextColumn::make('jobTitle.nama_jobtitle')->label('Job Title'),
                Tables\Columns\IconColumn::make('user_id')->label('Akun Login')->boolean()
                    ->getStateUsing(fn ($record) => filled($record->user_id)),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                // Buat akun login dummy untuk peserta ini (dipakai sebelum SSO aktif)
                Tables\Actions\Action::make('buatAkun')
                    ->label('Buat Akun Login')
                    ->icon('heroicon-o-key')
                    ->visible(fn (Peserta $record) => blank($record->user_id))
                    ->action(function (Peserta $record) {
                        $user = User::create([
                            'name' => $record->nama,
                            'email' => $record->email,
                            'password' => Hash::make('password123'), // password default demo
                            'role' => 'peserta',
                        ]);
                        $record->update(['user_id' => $user->id]);
                    })
                    ->requiresConfirmation(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPesertas::route('/'),
            'create' => Pages\CreatePeserta::route('/create'),
            'edit' => Pages\EditPeserta::route('/{record}/edit'),
        ];
    }
}
