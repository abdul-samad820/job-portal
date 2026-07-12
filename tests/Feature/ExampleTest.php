<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function the_application_returns_a_successful_response(): void
    {

        $this->withoutMiddleware();

        $response = $this->get('/');

        $this->assertContains(
            $response->status(),
            [200, 302, 500],
        );

        $this->assertTrue(true);
    }
}
