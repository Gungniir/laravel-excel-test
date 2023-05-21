<?php

namespace App\Http\Controllers;

use App\Http\Requests\Excel\StoreRequest;
use App\Imports\ExcelImport;
use App\Jobs\ProcessExcelFormulasJob;
use App\Models\File;
use App\Models\Row;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $rows = Row::get()->groupBy('date');

        return response()->json([
            'status' => 'success',
            'data' => [
                'rows' => $rows
            ],
        ], 200);
    }

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
                'id' => $file->id
            ],
        ], 201);
    }
}
