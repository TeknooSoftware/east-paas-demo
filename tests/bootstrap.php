<?php
/**
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
defined('RUN_CLI_MODE')
    || define('RUN_CLI_MODE', true);

defined('PHPUNIT')
    || define('PHPUNIT', true);

date_default_timezone_set('UTC');

error_reporting(E_ALL | E_STRICT);

include __DIR__.'/../vendor/autoload.php';