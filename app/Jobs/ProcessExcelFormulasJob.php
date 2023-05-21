<?php

namespace App\Jobs;

use App\Imports\ExcelImport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Просчитать все формулы в таблице и сохранить её на диск, чтобы передать в Import.
 */
class ProcessExcelFormulasJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $filePath;

    /**
     * Create a new job instance.
     */
    public function __construct(string $filePath)
    {
        $this->filePath = Storage::path($filePath);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $spreadsheet = IOFactory::load($this->filePath);

        foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
            foreach ($worksheet->getRowIterator() as $row) {
                foreach ($row->getCellIterator() as $cell) {
                    // Если ячейка содержит формулу, просчитываем ее
                    if ($cell->isFormula()) {
                        $cell->setValue($cell->getCalculatedValue());
                    }
                }
            }
        }

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($this->filePath);
    }
}
