<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/9/24
 * Time: 18:07
 */

require dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR
    . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

$encoder = new \Nsq\Encoding\Encoder();
$conection = new \Nsq\Connection\Connection("127.0.0.1", 4150);
$conection->write($encoder->magic());