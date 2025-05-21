<?php

namespace App\Filament\Resources\KegiatanResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PetugasRelationManager extends RelationManager
{
    protected static string $relationship = 'petugas';
    // protected static ?string $recordTitleAttribute = 'mitra.nama_mitra';

    // public function getRecordTitle(): string
    // {
    //     return $this->record->mitra->nama_mitra ?? 'Nama Petugas Tidak Ditemukan';
    // }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make('full')
                    ->schema([
                        Forms\Components\Select::make('mitra_id')
                            ->label('Nama Petugas')
                            ->relationship('mitra', 'nama_mitra')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->disabled(fn(string $context) => $context === 'edit')
                    ]),
                Forms\Components\TextInput::make('bertugas_sebagai')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('wilayah_tugas')
                    ->options([
                        '1201' => 'Nias',
                        '1225' => 'Nias Barat',
                    ])
                    ->required()
                    ->searchable(),
                Forms\Components\TextInput::make('beban')
                    ->required()
                    ->numeric()
                    ->maxLength(255),
                Forms\Components\TextInput::make('satuan')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('honor')
                    ->label('Honor (Otomatis)')
                    ->hidden()
                    ->dehydrated(true)
                    ->numeric(),

            ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $kegiatan = $this->ownerRecord;

        $honorPerWilayah = match (strtolower($data['wilayah_tugas'])) {
            '1201' => $kegiatan->honor_nias,
            '1225' => $kegiatan->honor_nias_barat,
            default => 0,
        };

        $data['honor'] = $data['beban'] * $honorPerWilayah;

        $data['kegiatan_id'] = $kegiatan->id;

        // dd($data);

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $kegiatan = $this->ownerRecord;

        $honorPerWilayah = match (strtolower($data['wilayah_tugas'])) {
            '1201' => $kegiatan->honor_nias,
            '1225' => $kegiatan->honor_nias_barat,
            default => 0,
        };

        $data['honor'] = $data['beban'] * $honorPerWilayah;

        return $data;
    }

    public function table(Table $table): Table
    {
        return $table
            // ->recordTitleAttribute('nama_petugas')
            ->columns([
                Tables\Columns\TextColumn::make('mitra.nama_mitra')
                    ->label('Nama Petugas')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bertugas_sebagai')
                    ->label('Bertugas Sebagai'),
                Tables\Columns\TextColumn::make('wilayah_tugas')
                    ->label('Wilayah Tugas')
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            '1201' => 'Nias',
                            '1225' => 'Nias Barat',
                            default => 'Tidak Diketahui',
                        };
                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('beban')
                    ->label('Beban')
                    ->getStateUsing(function ($record) {
                        return $record->beban . ' ' . $record->satuan;
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('honor')
                    ->label('Honor')
                    ->prefix('Rp. ')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->using(function (array $data) {
                        $data = $this->mutateFormDataBeforeCreate($data);
                        return $this->getRelationship()->create($data);
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->using(function ($record, array $data) {
                        $data = $this->mutateFormDataBeforeSave($data);
                        $record->update($data);
                        return $record;
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
