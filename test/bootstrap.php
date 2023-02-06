<?php
declare(strict_types=1);

use SetBased\ErrorHandler\ErrorHandler;

date_default_timezone_set('Europe/Amsterdam');

require_once(__DIR__.'/../vendor/autoload.php');

$handler = new ErrorHandler();
$handler->registerErrorHandler();
