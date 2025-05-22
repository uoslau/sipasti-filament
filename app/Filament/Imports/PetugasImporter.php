<?php

namespace App\Filament\Imports;

use App\Models\Petugas;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class PetugasImporter extends Importer
{
    protected static ?string $model = Petugas::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('mitra_id')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('kegiatan_id')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('bertugas_sebagai')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('wilayah_tugas')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('beban')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('satuan')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('honor')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('no_kontrak')
                ->rules(['max:255']),
            ImportColumn::make('no_bast')
                ->rules(['max:255']),
        ];
    }

    public function resolveRecord(): ?Petugas
    {
        // return Petugas::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new Petugas();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your petugas import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
