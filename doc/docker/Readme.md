DOCKER configuration
====================

Prerequisites
-------------
- Some general knowledge of how to install and configure Docker.
- A working Docker setup.


!!!! Work in progress !!!!
 
If you are on Mac OSX, you have probably a Docker Machine, if you have already Docker for Mac, consider that you are
going to follow the Linux install. (Note: Docker for Mac, is really slow on shared folder performances)

- DOCKER_MACHINE_HOST: put the hostname of your Docker Machine
- ROOT_PATH: It is the path to the project from the Docker Machine point of view.

> Depending on your install the simplest way to get it is to go on your docker machine and run mount

```bash
docker-machine ssh $DOCKER_MACHINE_HOST
mount
```

If you are on Linux:

- DOCKER_MACHINE_HOST: You don't need that variable
- ROOT_PATH: Will be set for you (../../)

All:

- PORT_PREFIX: By default it is 13 to wish you luck, but you can override it.
 
 
To run it on Linux:

```bash
./compose  up
```

To run it on Mac with Docker Machine:

```bash
DOCKER_MACHINE_HOST=osxdock ROOT_PATH=/data/DOCKER_SOURCES/PLOPIX/eZ-Platform/ezplatform ./compose up
```

> /data/DOCKER_SOURCES/PLOPIX/eZ-Platform/ezplatform is the path on the Docker Machine that mounts the Mac.

Now you can start to work in the ezplatform folder.

By default:
 
- http://{host}:13082/ez => new BO in prod mode through Varnish
- http://{host}:13081/ez => new BO in prod mode
- http://{host}:13080/ => front in dev mode
- 13306/ => MySQL TCP Port
- http://{host}:13180/ => MailCatcher
- http://{host}:13083/ => Memcached Admin

![Architecture](http://www.plantuml.com/plantuml/proxy?src=https://raw.githubusercontent.com/Plopix/ezplatform/docker/doc/docker/Architecture.puml)




