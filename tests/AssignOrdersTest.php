<?php

class AssignOrdersTest extends TestCase
{
    private $loop      = 10;
    private $url       = 'orders/assign';
    private $time_cost = 0;

    public function testBorder()
    {
        // missing order_ids
        $response = $this->call('GET', $this->url);
        if ($response->status() != 500) {
            echo "\n[error]missing order_ids";
            $this->assertTrue(false);
        }
        // too many order_ids
        $response = $this->call('GET', $this->url . "?order_ids[]=" . implode('&order_ids[]=', [1, 2, 3, 4, 5, 6]));
        if ($response->status() != 500) {
            echo "\n[error]too many order_ids";
            $this->assertTrue(false);
        }
        // missing vehicles
        $response = $this->call('GET', $this->url . '?order_ids[]=12000');
        if ($response->status() != 500) {
            echo "\n[error]missing 2 vehicles";
            $this->assertTrue(false);
        }
        // missing vehicles
        $response = $this->call('GET', $this->url . '?order_ids[]=12000&vehicles[0][0]=114&vehicles[0][1]=22');
        if ($response->status() != 500) {
            echo "\n[error]missing 1 vehicles";
            $this->assertTrue(false);
        }
        // too many vehicles
        $response = $this->call('GET', $this->url . '?order_ids[]=12000&vehicles[0][0]=114&vehicles[0][1]=22&vehicles[1][0]=114&vehicles[1][1]=22&vehicles[2][0]=114&vehicles[2][1]=22');
        if ($response->status() != 500) {
            echo "\n[error]too many vehicles";
            $this->assertTrue(false);
        }
    }

    public function testSingleRequest()
    {
        $order_ids     = [];
        $num_of_orders = mt_rand(1, 5);
        for ($i = 0; $i < $num_of_orders; $i++) {
            $order = $this->call('GET', 'orders/create-random')
                ->getData();
            $order_ids[] = 'order_ids[]=' . $order->id;
        }
        $order_ids_str = implode('&', $order_ids);

        $vehicles = $this->call('GET', 'vehicles/random')
            ->getData();
        $vehicles     = [$vehicles->a, $vehicles->b];
        $vehicles_str = '';
        for ($i = 0; $i < count($vehicles); $i++) {
            foreach ($vehicles[$i] as $k => $c) {
                $vehicles_str .= '&vehicles[' . $i . '][' . $k . ']=' . $c;
            }
        }
        $start_timestamp = microtime(true);
        $result          = $this->call('GET', 'orders/assign?' . $order_ids_str . $vehicles_str)
            ->getData();
        $end_timestamp = microtime(true);
        $this->time_cost += $end_timestamp - $start_timestamp;
        echo json_encode($result) . ": {$this->time_cost}\n";
    }

    public function testMultiRequests()
    {
        for ($i = 0; $i < $this->loop; $i++) {
            $this->testSingleRequest();
            $i++;
        }
        echo "\nAverage time for each request: " . ($this->time_cost / $this->loop) . "\n";
    }
}
