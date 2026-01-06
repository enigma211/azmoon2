<?php

namespace Tests\Feature;

use Tests\TestCase;

class SimpleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_simple_assertion(): void
    {
        $this->assertTrue(true);
    }

    /**
     * Test application bootstrapping.
     */
    public function test_app_is_booted(): void
    {
        $this->assertNotNull($this->app);
    }
}
