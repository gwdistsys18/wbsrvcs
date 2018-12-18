import docker
import json
import argparse
import subprocess
import time

BASE_URL="http://ec2-3-81-10-227.compute-1.amazonaws.com"
client = docker.from_env()
def curl_request(req):
    out = subprocess.check_output(["curl", req])
    print(out)

def create_containers():
    topology_file = 'sample_top.json'

    with open('sample_top.json', 'r') as f:
        hosts = json.load(f)

    for key, value in hosts.items():
        client.containers.run("pcodes/lamp", detach=True, name=key, ports={"80/tcp": value['port']})

    time.sleep(3)

    for key, value in hosts.items():
        specific_url = BASE_URL + ":" + str(value['port']) + "/wbsrvcs/wbsrvcs.php?cmd=setup"
        print(specific_url)
        curl_request(specific_url)

def delete_containers():
    topology_file = 'sample_top.json'

    with open('sample_top.json', 'r') as f:
        hosts = json.load(f)

    for key, value in hosts.items():
        container = client.containers.get(key)
        container.stop()
        container.remove()

parser = argparse.ArgumentParser(description='Control script for a topology file')
parser.add_argument('-c', action='store_true')
parser.add_argument('-d', action='store_true')

args = parser.parse_args()
selected_options = vars(args)

if selected_options['d']:
    delete_containers()

if selected_options['c']:
    create_containers()
