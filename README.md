wbsrvcs- Dockerized!
=======

This builds off of the original [wbsrvcs project](https://github.com/gwcloudlab/wbsrvcs), and adds docker support and a JSON topology map. Containers can be created and destroyed according to the JSON file, and requests can be sent in a chain through the hosts.

### Interacting with the JSON file
A sample topology is included in the `sample_top.json` file. To interact with a JSON file, use the `container_manager.py` file. Inside the file, change the BASE URL to whatever the physical host has as an address, and the JSON file to be the name of the file. 

### Installation and Usage:
The python file has several dependencies:
- `docker`: this is a library that provides python bindings for interacting with docker.
- `beautifulsoup`: Parses output
- This file also uses python3

Running:
- `python3 container_manager.py -c`: creates a series of containers according to the JSON file
- `python3 container_manager.py -d`: deletes containers specified in the JSON file
- `python3 container_manager.py -t`: Send a request to the containers

The bash script, `create_lamp_container.sh`, creates a docker container with whatever port is passed as the subsequent argument.
