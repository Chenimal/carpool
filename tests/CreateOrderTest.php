<?php

class CreateOrderTest extends TestCase
{
    private $loop = 10000;

    public function testSingleRequest()
    {
        $response = $this->call('GET', 'orders/create-random');

        $this->assertEquals(
            200, $response->status()
        );
    }

    public function testLoopRequest()
    {
        $pickup_time   = [];
        $delivery_time = [
            'max' => null,
            'min' => null,
        ];
        $gap = [
            'max' => null,
            'min' => null,
        ];
        $service_types = [];

        $i = 0;
        while ($i < $this->loop) {
            $response = $this->call('GET', 'orders/create')
                ->getData();

            if (!isset($service_types[$response->service_type])) {
                $service_types[$response->service_type] = 0;
            }
            $service_types[$response->service_type]++;

            if (!isset($pickup_time[$response->pickup_time])) {
                $pickup_time[$response->pickup_time] = 0;
            }
            $pickup_time[$response->pickup_time]++;

            $delivery_time['max'] = $delivery_time['max'] ? max($delivery_time['max'], $response->delivery_time) : $response->delivery_time;
            $delivery_time['min'] = $delivery_time['min'] ? min($delivery_time['min'], $response->delivery_time) : $response->delivery_time;
            $gap['max']           = $gap['max'] ? max($gap['max'], strtotime($response->delivery_time) - strtotime($response->pickup_time)) : strtotime($response->delivery_time) - strtotime($response->pickup_time);
            $gap['min']           = $gap['min'] ? min($gap['min'], strtotime($response->delivery_time) - strtotime($response->pickup_time)) : strtotime($response->delivery_time) - strtotime($response->pickup_time);
            $i++;
        }
        echo "\nservice_types: \n";
        foreach ($service_types as $key => $v) {
            echo "{$key}: {$v}\n";
        }
        echo "\npickup_time: \n";
        foreach ($pickup_time as $key => $v) {
            echo "{$key}: {$v}\n";
        }
        echo "\ndelivery time:\nmin: {$delivery_time['min']}\nmax: {$delivery_time['max']}\n";
        echo "\nduration(delivery_time - pickup_time):\nmin: " . ($gap['min'] / 3600) . " hrs\nmax: " . ($gap['max'] / 3600) . " hrs\n";
    }
}
