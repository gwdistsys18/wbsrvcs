import docker
import json
import argparse
import subprocess
import time
from bs4 import BeautifulSoup

BASE_URL="http://ec2-3-81-10-227.compute-1.amazonaws.com"
client = docker.from_env()
def curl_request(req):
    out = subprocess.check_output(["curl", req])
    print(out)
    return out

def run_test():
    print(BASE_URL[7:])
    topology_file = 'sample_top.json'

    with open('sample_top.json', 'r') as f:
        hosts = json.load(f)

    request_url = BASE_URL + "/wbsrvcs/wbsrvcs.php"
    request_url = request_url + "?hop=" + str(len(hosts))
    h = 1
    print(request_url)

#"192.168.246.102/wbsrvcs/wbsrvcs.php?hop=1&h1name=frontend&h1comp=5&h2name=192.168.246.101&h2comp=10&hwrite2=1"

    for key, value in hosts.items():
        request_url = request_url + "&h" + str(h) + "name=" + BASE_URL[7:] + ":" + str(value['port'])
        if 'comp' in value:
            request_url = request_url + "&h" + str(h) + "comp=" + str(value['comp'])
        h = h + 1
    print(request_url)
    html_output = curl_request(request_url)

    soup = BeautifulSoup(html_output, 'html.parser')
    print(soup.get_text())


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
parser.add_argument('-t', action='store_true')

args = parser.parse_args()
selected_options = vars(args)

if selected_options['d']:
    delete_containers()

if selected_options['c']:
    create_containers()

if selected_options['t']:
    run_test()
