<?php

namespace App\Http\Requests\Excel;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'excel' => 'required|file|mimes:xlsx'
        ];
    }
}
