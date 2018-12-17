#!/bin/bash

site="http://ec2-34-238-169-9.compute-1.amazonaws.com"
port=$1

docker container run -d -p $port:80 pcodes/lamp
sleep 4
curl $site:$port/wbsrvcs.php?cmd=setup
