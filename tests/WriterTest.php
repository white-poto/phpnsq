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
     * @var \Nsq\Encoding\Writer
     */
    protected $writer;

    /**
     * @var \Nsq\Encoding\Reader
     */
    protected $reader;

    protected function setUp(){
        $this->writer = new \Nsq\Encoding\Writer();
    }

    public function testClose(){

        $socket_client = stream_socket_client('tcp://127.0.0.1:4150', $errno, $errstr, 30);
        fwrite($socket_client, $this->writer->magic());
        var_dump($this->writer->publish("test", "test"));
        fwrite($socket_client, $this->writer->publish("test", "test"));
        $data = fread($socket_client, 1024);
        $this->reader->decode($data);
        $this->assertEquals("OK", $this->reader->getContent());
        $this->assertEquals(2, $this->reader->getSize());
        $this->assertEquals(0, $this->reader->getType());
        sleep(2);

    }
}