wbsrvcs
=======

This benchmark represents a fake website where you can control precisely how much processing and IO is performed for each request. It also supports chaining together multiple such websites, letting you emulate a multi-tier website with an arbitrary topology.


The wbsrvcs.php script  does all the work for the fake website. Depending on the parameters sent in the URL, it can do several things:

 - Perform writes to the local mysql database
 - Perform computation (calculates Fibonacci sequences)
 - Make a request to one or more other servers that are running the same script (not described here)

For example, to have the website perform 15 computational oops and 5 DB inserts on each request, you could run:

lynx "localhost/wbsrvcs/wbsrvcs.php?hop=1&h1name=frontend&h1comp=15&h1write=5"

This will give output like:
```
   Last query in chain!

   Performing 5 DB inserts.

   Connected to MySQL... Connected to Database

   DB Write time: 0.0074570178985596 seconds.

   Performing 15 computational loops.

   Local Computation time: 0.028260946273804 seconds.
```
which tells you how long each of the operations took.

