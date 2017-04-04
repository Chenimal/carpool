<?php

class GetVehiclesTest extends TestCase
{
    private $loop = 100;
    private $url  = 'vehicles/random';

    public function testSingleRequest()
    {
        $response = $this->call('GET', $this->url);

        $this->assertEquals(
            200, $response->status()
        );
    }

    public function testMultiRequests()
    {
        $start_timestamp = microtime(true);
        for ($i = 0; $i < $this->loop; $i++) {
            $response = $this->call('GET', $this->url);
            $i++;
        }
        $end_timestamp = microtime(true);

        echo "\nAverage time for each request: " . ($end_timestamp - $start_timestamp) / $this->loop . "\n";
    }
}
