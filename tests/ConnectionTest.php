<?php

/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/9/23
 * Time: 16:19
 */
class ConnectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Nsq\Connection\Connection
     */
    protected $connection;

    /**
     * @var \Nsq\Encoding\Encoder
     */
    protected $encoder;

    public function setUp()
    {
        $this->connection = new \Nsq\Connection\Connection("127.0.0.1", 4150);
        $this->encoder = new \Nsq\Encoding\Encoder();
    }

    public function testWrite()
    {
        $this->assertTrue($this->connection->write($this->encoder->magic()));
        $this->assertTrue($this->connection->write($this->encoder->pub("phpnsq_1", "test")));
        $frame = $this->connection->readFrame();
        $this->assertEquals("OK", $frame->getContent());
    }
}