<?php

/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/9/19
 * Time: 10:42
 */
class WriterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Nsq\Wire\Writer
     */
    protected $writer;

    protected function setUp(){
        $this->writer = new \Nsq\Wire\Writer();
    }

    public function testClose(){

        $socket_client = stream_socket_client('tcp://127.0.0.1:4150', $errno, $errstr, 30);
        fwrite($socket_client, $this->writer->magic());
        fwrite($socket_client, $this->writer->close());
    }
}