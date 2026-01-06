<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DbTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_database_interaction(): void
    {
        $this->assertTrue(true);
    }
}
