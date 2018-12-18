wbsrvcs- Dockerized!
=======

This builds off of the original [wbsrvcs project](https://github.com/gwcloudlab/wbsrvcs), and adds docker support and a JSON topology map. Containers can be created and destroyed according to the JSON file, and requests can be sent in a chain through all of the hosts.

## Installation and Usage:
### Building the Docker Image
Before creating the containers, the Docker image needs to be built first. The image contains a full Ubuntu 14-based LAMP stack, as well as the wbsrcvcs PHP files.
- `docker build -t pcodes/lamp .` will build the image and give it the tag used in subsequent files.

### Creating a Topology
A sample topology is included in the `sample_top.json` file. The outer name in each section represents what the container will be called. `port` is what host port will be mapped back to the container (i.e. 80 or 8080). No two containers should have the same port. `comp` is how many computations each host will do in the chain.

### Python Setup
The python management script is based off of Python 3, so that's an initial dependency. Additionally, the following Python libraries must be installed:
- [Docker](https://docker-py.readthedocs.io/en/stable/): library that provides python bindings for interacting with docker.
- [BeautifulSoup 4](https://pypi.org/project/beautifulsoup4/): Parses HTML output from cURL requests

### Running the Scripts
The `container_manager.py` script handles all of the topology script interaction. It takes one of the following arguments.
- `python3 container_manager.py -c`: creates a series of containers according to the JSON file
- `python3 container_manager.py -d`: deletes containers specified in the JSON file
- `python3 container_manager.py -t`: Send a request to the containers

If a generic container is desired instead of a full topology, the `create_lamp_container.sh` bash script can be used instead.
- `./create_lamp_container.sh <PORT>`: creates a container with the specified port
