#!/bin/bash

site="ec2-3-81-10-227.compute-1.amazonaws.com"
port=$1

docker container run -d -p $port:80 pcodes/lamp
sleep 4
curl $site:$port/wbsrvcs/wbsrvcs.php?cmd=setup
