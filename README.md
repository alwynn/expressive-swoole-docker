# Zend expressive + docker + swoole
## Technology
- [Docker](https://www.docker.com/)
- Zend Expressive:
    - [Zend Expressive v3](https://docs.zendframework.com/zend-expressive/)
    - [Swoole support for Expressive](https://github.com/zendframework/zend-expressive-swoole)
- [PHP Swoole extension](http://pecl.php.net/swoole)

## Build
To built the container with application, call ```$ php ./bin/build.php```.

The process will prepare building directories, run zend-expressive skeleton that will ask questions about installations modules, build the container and install the application within it.

Additional parameters:
- **tag** - tag for container (default _test/expressive_)
- **swoole** - swoole version number (default _4.2.3_)
- **proxy** - proxy address if used (default environment $http_proxy value)

After the build is completed the container will be available on docker containers list.
```
REPOSITORY          TAG                 IMAGE ID            CREATED             SIZE
test/expressive     latest              59faa9e4d9ce        6 minutes ago       213MB
php                 7.2-alpine3.8       71bb484c0280        2 weeks ago         77.6MB
```

### Execution
To run the application on port 8080:

```$ docker run -dit --name test-expressive --init --restart=always -p 8080:80 test/expressive```
```
CONTAINER ID        IMAGE               COMMAND                  CREATED             STATUS              PORTS                  NAMES
8dea9e933fcb        test/expressive     "docker-php-entrypoi…"   3 seconds ago       Up 2 seconds        0.0.0.0:8080->80/tcp   test-expressive
```

## Dockerfile
Parameters for docker/Dockerfile:
- **swoole** - swoole library version number (default _4.2.3_)
- **proxy** - proxy address (default empty)

## Application's configuration
To properly run swoole server, an additional config file is needed that sets the server with proper host/port parameters. During the build _docker/swoole.local.php_ file is added to application's configuration directory:
```php
<?php return [
    'zend-expressive-swoole' => [
        'swoole-http-server' => [
            'host' => '0.0.0.0',
            'port' => 80,
        ]
    ]
];
```
