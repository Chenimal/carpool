<?php

class CreateOrderTest extends TestCase
{
    private $loop = 100;

    public function testSingleRequest()
    {
        $response = $this->call('GET', 'orders/create-random');

        $this->assertEquals(
            200, $response->status()
        );
    }

    public function testMultiRequests()
    {
        $service_types = [];
        $pickup_times  = [];

        $start_timestamp = microtime(true);

        $i = 0;
        while ($i < $this->loop) {
            $response = $this->call('GET', 'orders/create-random')
                ->getData();

            if (!isset($service_types[$response->service_type])) {
                $service_types[$response->service_type] = 0;
            }
            $service_types[$response->service_type]++;

            if (!isset($pickup_times[$response->pickup_time])) {
                $pickup_times[$response->pickup_time] = 0;
            }
            $pickup_times[$response->pickup_time]++;

            $i++;
        }
        $end_timestamp = microtime(true);

        echo "\nservice_types: \n";
        foreach ($service_types as $key => $v) {
            echo "{$key}: {$v}\n";
        }
        echo "\npickup_time: \n";
        foreach ($pickup_times as $key => $v) {
            echo "{$key}: {$v}\n";
        }
        echo "\nAverage time for each request: " . ($end_timestamp - $start_timestamp) / $this->loop . "\n";
    }
}
