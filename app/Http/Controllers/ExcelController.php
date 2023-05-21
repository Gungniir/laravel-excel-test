<?php

namespace App\Http\Controllers;

use App\Http\Requests\Excel\StoreRequest;
use Illuminate\Http\JsonResponse;

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

        return response()->json([
            'status' => 'success',
            'data' => [
                'path' => $path
            ],
        ], 201);
    }
}
