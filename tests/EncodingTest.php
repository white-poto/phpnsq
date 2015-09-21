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
     * @var \Nsq\Encoding\Encoder
     */
    protected $encoder;

    /**
     * @var \Nsq\Encoding\Decoder
     */
    protected $decoder;

    protected function setUp(){
        $this->encoder = new \Nsq\Encoding\Encoder();
        $this->decoder = new \Nsq\Encoding\Decoder();
    }

    public function testClose(){

        $socket_client = stream_socket_client('tcp://127.0.0.1:4150', $errno, $errstr, 30);
        fwrite($socket_client, $this->encoder->magic());
        fwrite($socket_client, $this->encoder->publish("test", "test"));
        $data = fread($socket_client, 1024);
        $result = $this->decoder->decode($data);
        $this->assertTrue($result);
        $this->assertEquals("OK", $this->decoder->getContent());
        $this->assertEquals(6, $this->decoder->getSize());
        $this->assertEquals(0, $this->decoder->getType());
        sleep(5);
        fwrite($socket_client, $this->encoder->close());
    }
}