## Carpool prototype

### 1. Introduction(todo)

Click [here](http://47.52.30.33/index.html) to view the demo. The source code is available to download [here](github download link).

#### 1.1 Steps to run

To run the program, follow the steps below:

1. First you need to create random orders and vehicles. Simply click 'New orders' and 'Get vehicles'. You will see some new spots showing on the map. Each order has an unique color(Label S(start) and E(end) represent pickup& deliver locations respectively). You will also see two lightgreen spots labeled Va and Vb representing two different vehicles.

2. Then there will be more options show up and the button 'Assign orders' should be available to click. For the first time you can just leave the options as default and click 'Assign orders'.

Animation type: I personally think linear one can better illustrate the order&relationship, but real route is more

#### 1.2 Basic Strategy

The basic idea is:

1. Firstly find all possible ways of spliting given orders(<=5) into two subsets.

2. For each possible subset of orders, find all possible sequences of location points(vehicle locations, pickup locations& delivery locations).

3. Get the best sequence based on certain criteria(shortest time or duration).

3. For each possible partitioning, it has a solution.

At last we comepare all partitionings, and get the best one.

---

### 2. Algotithm Explainations(todo)

* 2.1 Creating random orders

    The key is making sure the random locations are on the land rather than ocean. Mathematically it is equivalent to the question: determine if a point is inside/outside a polygon.

    I use RayCasting method: cast a ray from the point, count the num of intersections of the ray and polygon's borders. If num is odd, it's inside; if num is even, it's outside.

    See code implementation in `app/Library/Location.php:createRandomAccessibleLocation`.
[raycating picture]

* 2.2 Assign orders(find the best path)

    - 2.2.1. Split orders

        The problem of spliting n orders into two subsets is equivalent to putting n balls into two boxes. Thus it has Cn2(markdwon syntax?) possibilities.

        Code implementation: `app/bootstrap/functions.php:math_combination`


    - 2.2.2. To find all sequences

---

### 3. Testing(todo)

    `phpunit tests/CreateOrderTest.php --filter testSingleRequest`
    `phpunit tests/CreateOrderTest.php --filter testLoopRequest`
    Performance
    Through self-testing, the APIs took around XXms,
    and less than XXms on my local machine, which is nice.

---

### 4. Assumptions(todo)

    * Orders:
        1. Each order is randomly created(Inside Hong Kong main island).
        Locations in the rest islands are excluded.

---

### 5. Tech Specs

    * PHP 5.6
    * Lumen 5.4
    * Mysql
    * Gaode map Api(高德地图)
    * jQuery &Bootstrap
    * Server: Ubuntu 16.x(Ali cloud server)
