<?php

namespace App\Imports;

use App\Models\File;
use App\Models\Row;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ExcelImport implements ToModel, WithBatchInserts, WithChunkReading, ShouldQueue, WithHeadingRow, WithUpserts
{
    use Importable;

    protected int $fileId;

    public function __construct(int $fileId)
    {
        $this->fileId = $fileId;
    }

    public function model(array $row): Row
    {
        $row['date'] = Date::excelToDateTimeObject($row['date']);

        return new Row([
            'id' => $row['id'],
            'file_id' => $this->fileId,
            'name' => $row['name'],
            'date' => $row['date'],
        ]);
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function uniqueBy(): string
    {
        return 'id';
    }
}
