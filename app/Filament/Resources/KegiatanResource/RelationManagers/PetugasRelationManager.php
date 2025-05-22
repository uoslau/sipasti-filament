<?php

namespace App\Filament\Resources\KegiatanResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Validation\Rule;
use Filament\Forms\Components\Grid;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ActionGroup;
use App\Filament\Imports\PetugasImporter;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ImportAction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\Modal\Actions\Action;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

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
                            ->unique(
                                table: 'petugas',
                                column: 'mitra_id',
                                modifyRuleUsing: fn(Rule $rule) => $rule->where('kegiatan_id', $this->ownerRecord->id)
                            )
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

    protected function getValidationMessages(): array
    {
        return [
            'mitra_id.unique' => 'Petugas ini sudah terdaftar dalam kegiatan ini.',
        ];
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
                ImportAction::make()->importer(PetugasImporter::class),
                Tables\Actions\CreateAction::make()
                    ->using(function (array $data) {
                        $data = $this->mutateFormDataBeforeCreate($data);
                        return $this->getRelationship()->create($data);
                    }),
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make()
                        ->using(function ($record, array $data) {
                            $data = $this->mutateFormDataBeforeSave($data);
                            $record->update($data);
                            return $record;
                        }),
                    DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
