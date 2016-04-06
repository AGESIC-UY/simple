<?php

require __DIR__.'/resque/lib/Resque.php';
require __DIR__.'/resque-scheduler/lib/ResqueScheduler/ResqueScheduler.php';

define('TIEMPO_DELAY', '2700');

Resque::setBackend(REDIS_HOST. ':' . REDIS_PORT);
