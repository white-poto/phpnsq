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

    protected $socket_client;

    protected function setUp()
    {
        $this->encoder = new \Nsq\Encoding\Encoder();
        $this->decoder = new \Nsq\Encoding\Decoder();
        $this->socket_client = stream_socket_client('tcp://127.0.0.1:4150', $errno, $errstr, 30);
        $this->write($this->encoder->magic());
    }

    protected function write($data)
    {
        fwrite($this->socket_client, $data);
    }

    public function testClose()
    {
        fwrite($this->socket_client, $this->encoder->pub("phpnsq_1", "test"));
        $data = fread($this->socket_client, 1024);
        $this->decoder->readSize($data);
        $this->decoder->readType($data);
        $this->decoder->readContent($data);
        $frame = $this->decoder->getFrame();
        $this->assertEquals("OK", $frame->getContent());
        $this->assertEquals(6, $frame->getSize());
        $this->assertEquals(0, $frame->getType());
        fwrite($this->socket_client, $this->encoder->sub("phpnsq_1", 1));

        fwrite($this->socket_client, $this->encoder->close());
    }
}