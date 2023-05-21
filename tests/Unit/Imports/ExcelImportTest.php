<?php

namespace Tests\Unit\Imports;

use App\Imports\ExcelImport;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Tests\TestCase;

class ExcelImportTest extends TestCase
{
    private int $fileId = 1;
    private array $excelRow  = [
        'id' => 1,
        'name' => 'Denim',
        'date' => 44118.0,
    ];
    private ExcelImport $import;

    protected function setUp(): void
    {
        parent::setUp();

        $this->import = new ExcelImport($this->fileId);
    }

    public function test_correct_created_row(): void
    {
        $row = $this->import->model($this->excelRow);

        $date = Date::excelToDateTimeObject($this->excelRow['date']);
        $this->assertSame($this->excelRow['id'], $row->id);
        $this->assertSame($this->excelRow['name'], $row->name);
        $this->assertSame($date->format('Y-m-d'), $row->date->format('Y-m-d'));
    }
}
