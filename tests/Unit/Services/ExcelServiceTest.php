<?php

namespace Tests\Unit\Services;

use App\Services\RowsService;
use Tests\TestCase;

class ExcelServiceTest extends TestCase
{
    private RowsService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(RowsService::class);
    }

    public function test_saving_status(): void
    {
        $fileId = 1;
        $rowsProcessed = 10;
        $this->service->saveStatusForFile($fileId, $rowsProcessed);
        $this->assertSame($rowsProcessed, $this->service->getStatusForFile($fileId));
    }

    public function test_deleting_status(): void
    {
        $fileId = 1;
        $rowsProcessed = 10;
        $this->service->saveStatusForFile($fileId, $rowsProcessed);
        $this->service->deleteStatusForFile($fileId);
        $this->assertNull($this->service->getStatusForFile($fileId));
    }
}
