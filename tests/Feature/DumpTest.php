<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class DumpTest extends TestCase
{
    protected function createDump(): string
    {
        $response = $this->get('/create-dump')
            ->assertRedirect();

        $location = $response->headers->get('Location');

        return Str::afterLast($location, '/');
    }

    /** @test */
    public function it_can_create_dumps()
    {
        $dumpId = $this->createDump();

        $this->getJson("/api/dumps/{$dumpId}")->assertExactJson([
            'name' => $dumpId,
            'requests' => []
        ]);
    }

    /** @test */
    public function it_can_store_requests()
    {
        $dumpId = $this->createDump();

        $this->post("/dumps/{$dumpId}", ['key' => 'value']);
        $this->post("/dumps/{$dumpId}", ['key' => 'value']);

        $this->getJson("/api/dumps/{$dumpId}")->assertJsonCount(2, 'requests');
    }

    /** @test */
    public function it_only_keeps_maximum_number_of_requests()
    {
        $dumpId = $this->createDump();

        $this->app['config']['httpdump.max_dumps'] = 5;

        foreach (range(1, 10) as $i) {
            $this->post("/dumps/{$dumpId}", ['key' => 'value']);
        }

        $this->getJson("/api/dumps/{$dumpId}")->assertJsonCount(5, 'requests');
    }

    /** @test */
    public function it_can_clear_dumps()
    {
        $dumpId = $this->createDump();

        foreach (range(1, 10) as $i) {
            $this->post("/dumps/{$dumpId}", ['key' => 'value']);
        }

        $this->getJson("/api/requests/clear/{$dumpId}")->assertOk();
        $this->getJson("/api/dumps/{$dumpId}")->assertJsonCount(0, 'requests');
    }
}
