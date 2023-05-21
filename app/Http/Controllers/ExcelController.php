<?php

namespace App\Http\Controllers;

use App\Http\Requests\Excel\StoreRequest;
use App\Imports\ExcelImport;
use App\Jobs\ProcessExcelFormulasJob;
use App\Models\File;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController
{
    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $path = $request->file('excel')->store('excels');

        $file = File::create([
            'user_id' => Auth::id(),
            'path' => $path,
        ]);

        ProcessExcelFormulasJob::dispatch($path)->chain([
            function () use ($file) {
                Excel::import(new ExcelImport($file->id), $file->path);
            }
        ]);

        return response()->json([
            'status' => 'success',
            'data' => [
                'path' => $path
            ],
        ], 201);
    }
}
