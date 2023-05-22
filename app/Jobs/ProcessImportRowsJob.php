<?php

namespace App\Jobs;

use App\Models\File;
use App\Models\Row;
use App\Services\RowsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

/**
 * Просчитать все формулы в таблице и сохранить её на диск, чтобы передать в Import.
 */
class ProcessImportRowsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected RowsService $rowsService;
    protected File $file;
    protected int $startRow;
    protected int $batchSize;

    /**
     * Create a new job instance.
     */
    public function __construct(File $file, int $startRow, int $batchSize = 1000)
    {
        $this->file = $file;
        $this->startRow = $startRow;
        $this->batchSize = $batchSize;


        $this->rowsService = app()->make(RowsService::class);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $reader = IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(true);
        $reader->setReadEmptyCells(false);

        $spreadsheet = $reader->load(Storage::path($this->file->path));
        $sheet = $spreadsheet->getActiveSheet();

        foreach ($sheet->getRowIterator($this->startRow, $this->startRow + $this->batchSize) as $row) {
            $model = [];

            foreach ($row->getCellIterator() as $cell) {
                if ($cell->isFormula()) {
                    $cell->setValue($cell->getCalculatedValue());
                }

                if ($cell->getValue() === null) {
                    continue 2;
                }

                $model[] = $cell->getValue();
            }

            Row::updateOrCreate([
                'id' => $model[0],
            ], [
                'file_id' => $this->file->id,
                'name' => $model[1],
                'date' => Date::excelToDateTimeObject($model[2]),
            ]);

            $this->rowsService->saveStatusForFile($this->file->id, $row->getRowIndex());
        }

        // Сохраняем просчитанные значения формул в файл (чтобы следующие job'ы могли их использовать)
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save(Storage::path($this->file->path));
    }
}
