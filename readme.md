## Carpool prototype

### 1. Intro

Click [here](http://47.52.30.33/index.html) to view the demo. To download the code, just click
the green button at upper-right corner.

The basic idea is:
Firstly find all possible ways of spliting given orders(<=5) into two subsets.
Secondly for each possible subset of orders, find all possible sequences of
location points(vehicle locations, pickup locations& delivery locations),
and get the best sequence based on certain criteria(shortest time or duration).
Then for each possible partitioning, it has a solution.
At last we comepare all partitionings, and get the best one.

### 2. Assumptions(todo)

    * Orders:
        1. Each order is randomly created(Inside Hong Kong main island).
        Locations in the rest islands are excluded.

### 3. Algotithm Explainations(todo)

* 3.1 To create random orders

    The key is making sure the random locations are on the land rather than ocean.
Mathematically it is equivalent to the question: determine if a point is
inside/outside a polygon.
I use RayCasting method: cast a ray from the point, count the num of
intersections of the ray and polygon's borders.
If num is odd, it's inside; if num is even, it's outside.

    See code implementation in `app/Library/Location.php:createRandomAccessibleLocation`.
[raycating picture]

* 3.2 Assign orders(find the best path)

    1. To split orders
    2. To find all sequences

### 4. API(todo)

### 5. Testing(todo)

    `phpunit tests/CreateOrderTest.php --filter testSingleRequest`
    `phpunit tests/CreateOrderTest.php --filter testLoopRequest`

### 6. Performances(todo)

    Through self-testing, the APIs took around XXms,
    and less than XXms on my local machine, which is nice.

### 7. Tech Specs

    * PHP 5.6
    * Lumen 5.4
    * Mysql
    * Gaode map Api(高德地图)
    * jQuery &Bootstrap
    * Server: Ubuntu 16.x(Ali cloud server)
