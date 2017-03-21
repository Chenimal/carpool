<?php

class CreateOrderTest extends TestCase
{
    private $loop = 10000;

    public function testSingleRequest()
    {
        $response = $this->call('GET', 'orders/create');

        $this->assertEquals(
            200, $response->status()
        );
    }

    public function testLoopRequest()
    {
        $pickup_time = [
            'max' => null,
            'min' => null,
        ];
        $delivery_time = [
            'max' => null,
            'min' => null,
        ];
        $gap = [
            'max' => null,
            'min' => null,
        ];

        $i = 0;
        while ($i < $this->loop) {
            $response = $this->call('GET', 'orders/create')
                ->getData();
            $pickup_time['max']   = $pickup_time['max'] ? max($pickup_time['max'], $response->pickup_time) : $response->pickup_time;
            $pickup_time['min']   = $pickup_time['min'] ? min($pickup_time['min'], $response->pickup_time) : $response->pickup_time;
            $delivery_time['max'] = $delivery_time['max'] ? max($delivery_time['max'], $response->delivery_time) : $response->delivery_time;
            $delivery_time['min'] = $delivery_time['min'] ? min($delivery_time['min'], $response->delivery_time) : $response->delivery_time;
            $gap['max']           = $gap['max'] ? max($gap['max'], strtotime($response->delivery_time) - strtotime($response->pickup_time)) : strtotime($response->delivery_time) - strtotime($response->pickup_time);
            $gap['min']           = $gap['min'] ? min($gap['min'], strtotime($response->delivery_time) - strtotime($response->pickup_time)) : strtotime($response->delivery_time) - strtotime($response->pickup_time);
            $i++;
        }
        echo "\npickup time\nmin: {$pickup_time['min']}\nmax: {$pickup_time['max']}\n";
        echo "\ndelivery time\nmin: {$delivery_time['min']}\nmax: {$delivery_time['max']}\n";
        echo "\ndelivery_time - pickup_time\nmin: " . ($gap['min'] / 3600) . " hrs\nmax: " . ($gap['max'] / 3600) . " hrs\n";
    }
}
