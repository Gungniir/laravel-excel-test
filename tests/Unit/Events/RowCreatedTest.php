<?php

namespace Tests\Unit\Events;

use App\Events\RowSaved;
use App\Models\Row;
use Tests\TestCase;

class RowCreatedTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_event_saves_model(): void
    {
        $row = Row::factory()->create();
        $event = new RowSaved($row);

        $this->assertTrue($event->row->is($row));
    }
}
