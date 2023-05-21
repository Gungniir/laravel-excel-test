<?php

namespace App\Imports;

use App\Events\RowSaved;
use App\Models\File;
use App\Models\Row;
use App\Services\ExcelService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\RemembersRowNumber;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ExcelImport implements ToModel, WithEvents, WithBatchInserts, WithChunkReading, ShouldQueue, WithHeadingRow, WithUpserts
{
    use Importable;
    use RemembersRowNumber;

    protected int $fileId;
    protected array $models;
    protected ExcelService $excelService;

    public function __construct(int $fileId)
    {
        $this->fileId = $fileId;

        $this->excelService = app()->make(ExcelService::class);
    }

    public function model(array $row): Row
    {
        $row['date'] = Date::excelToDateTimeObject($row['date']);

        $row = new Row([
            'id' => $row['id'],
            'file_id' => $this->fileId,
            'name' => $row['name'],
            'date' => $row['date'],
        ]);

        $this->models[] = $row;

        return $row;
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

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function () {
                foreach ($this->models as $model) {
                    RowSaved::dispatch($model);
                }

                $this->excelService->updateFileStatus($this->fileId, $this->rowNumber);
            },
            AfterImport::class => function () {
                $this->excelService->deleteStatusForFile($this->fileId);
            },
        ];
    }
}
