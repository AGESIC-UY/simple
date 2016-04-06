Monolog-Init
============

Very basic and light Dependency Injector Container for Monolog

Helps create the following handler

- Cube
- RotatingFile
- ChromePHP
- Syslog
- Socket
- MongoDB
- CouchDB
- HipChat
- PushOver
- ZendMonitor
- Stream
- Redis

from 2 strings : `<handlerName>` `<comma-separated list of arguments>`

The second argument will be split, and pass to the handler `__construct()`.

## Usage

Download and include the MonologInit class in your code, or install it via Composer.

Usage example :

	$logger = new MonologInit('Cube', 'udp://127.0.0.1:1080');

To pass more than one parameter, separate them with a comma

	$logger = new MonologInit('Cube', 'udp://127.0.0.1:1080,0,1');


And use it like that :

	$logger->getInstance()->addInfo('hi ! This is my first log');


## Where to use

This container is usefull for creating remote log, for instance via CLI, where you can't pass object.

There is already a SymfonyBundle that does the same thing, but it's … very heavy.