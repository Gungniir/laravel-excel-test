<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;

class ExcelService
{
    const CACHE_KEY = 'excel_progress_';

    public function saveStatusForFile(int $fileId, int $rowsProcessed): void
    {
        Redis::set($this->getCacheKey($fileId), $rowsProcessed);
    }

    public function deleteStatusForFile(int $fileId): void
    {
        Redis::del($this->getCacheKey($fileId));
    }

    private function getCacheKey(int $fileId): string
    {
        return self::CACHE_KEY . $fileId;
    }
}
