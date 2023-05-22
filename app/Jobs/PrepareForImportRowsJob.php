<?php

namespace App\Jobs;

use App\Models\File;
use App\Services\RowsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class PrepareForImportRowsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const BATCH_SIZE = 1000;

    protected RowsService $rowsService;

    protected File $file;

    /**
     * Create a new job instance.
     */
    public function __construct(File $file)
    {
        $this->file = $file;
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

        $lastRow = $sheet->getHighestRow();
        unset($spreadsheet, $sheet, $reader);

        $firstRow = 2;

        $this->rowsService->saveStatusForFile($this->file->id, 0);

        for ($startRow = $firstRow; $startRow <= $lastRow; $startRow += self::BATCH_SIZE) {
            $last = ProcessImportRowsJob::dispatch($this->file, $startRow, self::BATCH_SIZE);
        }

        if (isset($last)) {
            $service = $this->rowsService;
            $id = $this->file->id;

            $last->chain([
                static function () use ($service, $id) {
                    $service->deleteStatusForFile($id);
                }
            ]);
        } else {
            $this->rowsService->deleteStatusForFile($this->file->id);
        }
    }
}
