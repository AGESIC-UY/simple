##Changelog

###v1.3.1 (2015-05-11)

* [fix] Update php-resque-ex required version

###v1.3.0 (2015-01-27)

* [new] Add support for Resque-Scheduler
* [new] Merge #36: Install phpunit using Composer
* [fix] Fix #40: Fix `kill` when user differs to process owner
* Various doc typos fixes

###v1.2.5 (2013-10-30)

* [fix] Fix wrong method name

###v1.2.4 (2013-10-30)

* [new] Don't use `sudo` to start workers if the current user is already the target user

###v1.2.3 (2013-10-26)

* [new] Fix #21: Add new tmpdir to fresque.ini to specify where to save the PID files [@pwhelan]

###v1.2.2 (2013-10-23)

* [fix] Fix #20: the bin script inside composer bin folder can't find library files

###v1.2.1 (2013-06-08)

* [new] `load` command now supports starting workers polling multiple queues
* [fix] Fix bug where multiple workers will not start correctly
* [fix] Fix load command: each queues should run under its own settings

###v1.2.0 (2013-06-20)

* [new] Add `pause` and `resume` commands
* [new] Add `--debug` option
*

###v1.1.5 (2013-04-15)

* [fix] Fix composer

###v1.1.4 (2013-04-15)

* [fix] Move php-resque library to suggest, to avoid conflict when using a forked php-resque

###v1.1.3 (2013-04-14)

* [fix] Add log verbose option
* [fix] `--help` only, without arguments will display the global help/usage

###v1.1.2 (2013-02-07)

* [fix] Fix error while starting multiple workers
* [change] More accurate way of checking started workers
* [change] More descriptive error message when starting workers

###v1.1.2 (2013-01-22)

* [fix] Update missing composer library

###v1.1.0 (2012-09-11)

* [new] `stop` now stop individual workers. Will display a workers menu to select from. Use `--all` to skip the menu and stop all workers at once.
* [new] `tail` can now tail other logs if exists. Will display a log file menu to select from
* [new] You can now select the Redis Database to use with the `DATABASE` option in the config
* [new] You can now set the Redis keys namespace with the `NAMESPACE` option in the config
* [fix] Auto-detect Composer autoloader if Fresque is installed as a dependency
* [fix] Check that the `--user` is a valid system user
* [fix] Starting worker return if the worker was really created
* [fix] `Enqueue()` display help when arguments are not valid
* [change] Various UI fixes and add more colors
* [change] `-t` (tail) has been removed from `start`. Use `tail` instead

###v0.2.6 (2012-08-21)

* [new] Support use of relatives and absolute path everywhere
* [new] Add `--loghandler` and `handlertarget` options, for [php-resque-ex](https://github.com/kamisama/php-resque-ex)
* [change] Code formatted to PSR2 standard

###v0.2.5 (2012-08-21)

* [change] Demote php-resque to suggest on Composer

###v0.2.4 (2012-08-05)

* [fix] Fix restart now working properply

###v0.2.3 (2012-08-05)

* [fix] Bugfixes

###v0.2.2 (2012-08-01)

* [change] Remove php-resque submodule

###v0.2.1 (2012-08-01)

* [change] Add php-resque as a Composer dependency

###v0.2.0 (2012-07-30)

* [new] Code now namespaced
* [new] Add ZetaComponents as a Composer dependencies
* [change] Add version number in code
* [change] Code now follows PSR-2 Standard

###v0.1.0 (2012-07-04)
* [change] Moving the php-resque vendor library to git submodule
* [change] Removing the ZetaComponent libraries, now requires that the libraries installed via pear
