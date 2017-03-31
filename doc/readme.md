# Carpool prototype

### 1.0 Intro(todo)

    To demonstrate, visit [ali-cloud/index.html].

    The basic idea is that for given orders(<=5), find all possible ways of spliting
    them into two subsets(two vehicles). Then for each possible subset(orders), find
    all possible sequences of location points(vehicle locations, pickup locations& delivery locations),
    and get the best sequence based on certain criteria(shortest time or duration).
    Then for each possible partitioning, we have a solution. At last we comepare all
    partitionings, find the best one.

### 2.0 Assumptions(todo)

    * Orders:
        1. Each order is randomly created(Inside Hong Kong main island).
        Locations in the rest islands are excluded.

### 3.0 Algotithm Illustrations(todo)

    * To create orders
    * To split orders
    * To find best sequences

### 4.0 API(todo)

### 5.0 Testing(todo)

    `phpunit tests/CreateOrderTest.php --filter testSingleRequest`
    `phpunit tests/CreateOrderTest.php --filter testLoopRequest`

### 6.0 Tech Specs

    * PHP 5.6
    * Lumen 5.4
    * Mysql
    * Gaode map Api(高德地图)
    * jQuery &Bootstrap
    * Server: Ubuntu 16.x(Ali cloud server)
