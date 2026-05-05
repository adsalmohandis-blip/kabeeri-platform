<?php

namespace Tests\Feature;

use Tests\TestCase;

class HealthTest extends TestCase
{
    public function test_application_boots_and_home_route_is_healthy(): void
    {
        $this->assertNotNull($this->app);

        $this->get('/')
            ->assertOk();
    }
}
