<?php

namespace Tests\Feature;

use App\Jobs\PrepareForImportRowsJob;
use App\Jobs\ProcessImportRowsJob;
use App\Models\Row;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class ExcelControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_store(): void
    {
        Queue::fake();

        $file = UploadedFile::fake()->create('test.xlsx');

        $this->withBasicAuth($this->email, $this->password)
            ->post('/api/excel', ['excel' => $file])
            ->assertStatus(201);

        Queue::assertPushed(PrepareForImportRowsJob::class);
    }

    public function test_index(): void
    {
        Row::factory()->count(10)->create([
            'date' => today(),
        ]);

        Row::factory()->count(10)->create([
            'date' => today()->subDay(),
        ]);

        $response = $this->withBasicAuth($this->email, $this->password)
            ->get('/api/excel');

        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data.rows');
        $response->assertJsonCount(10, 'data.rows.' . today()->format('Y-m-d H:i:s'));
    }
}
