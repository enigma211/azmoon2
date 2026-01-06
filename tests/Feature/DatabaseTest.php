<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class DatabaseTest extends TestCase
{
    /**
     * Test database connection without RefreshDatabase trait.
     */
    public function test_database_connection(): void
    {
        $result = DB::select('select 1 as res');
        $this->assertEquals(1, $result[0]->res);
    }
}
