## Introduction

This README describes how to setup the development environment for the Prototype of User Interfaces (PIU).
It was prepared to run on Linux but it should be fairly easy to follow and adapt for other operating systems.

* [Installing Docker](#installing-docker)
* [Generate a Nginx Image](#generate-a-nginx-image)
* [Publishing the image](#publishing-your-image)


## Installing Docker

Before starting you'll need to have __Docker__ installed on your PC. 

Docker is a tool that allows you to run containers (similar to virtual machines, but much lighter). 
The official instructions are in [Install Docker](https://docs.docker.com/install/).

    # install docker-ce
    sudo apt-get update
    sudo apt-get install apt-transport-https ca-certificates curl gnupg-agent software-properties-common
    curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo apt-key add -
    sudo add-apt-repository "deb [arch=amd64] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable"
    sudo apt-get update
    sudo apt-get install docker-ce # docker-ce-cli containerd.io
    docker run hello-world # make sure that the installation worked


## Generate a Nginx image

A simple `Dockerfile` is provided to generate a new Nginx Docker image.
The [Nginx HTTP server](https://www.nginx.com/) uses the directory __html/__ to host the PIU static HTML pages.

Later we'll need more Docker containers.

## Publishing your image

You should keep your git's master branch always functional and frequently build and deploy your code. 
To do so, you will create a _docker_ image for your project and publish it at [docker hub](https://hub.docker.com/). 
LBAW's production machine will frequently pull all these images and make them available at http://<YOUR_GROUP>.lbaw-prod.fe.up.pt/. 
This demo repository is available at [http://piu18.lbaw-prod.fe.up.pt/](http://piu18.lbaw-prod.fe.up.pt/). 
Make sure you are inside FEUP's network or VPN to access it.

First thing you need to do is create a [docker hub](https://hub.docker.com/) account and get your username from it. 
Once you have a username, let your docker know who you are by executing:

    docker login

Once your docker is able to communicate with the docker hub using your credentials configure the `upload_image.sh` script with your username and group's identification as well. 
Example configuration:

    DOCKER_USERNAME=johndoe # Replace by your docker hub username
    IMAGE_NAME=lbaw18GG # Replace by your lbaw group name

Afterwards, you can build and upload the docker image by executing that script from the project root:

    ./upload_image.sh

Note that your HTML source code should be inside the `html` folder or you need to adjust the `Dockerfile`.
You can test the locally by running:

    docker run -it -p 8000:80 <DOCKER_USERNAME>/<IMAGE NAME>

The above command exposes your application on http://localhost:8000. 

There should be only one image per group. 
One team member should create the image initially and add his team to the **public** repository at docker hub. 
You should provide your teacher the details for accessing your docker image, namely, docker username and repository (DOCKER_USERNAME/lbaw18GG).
