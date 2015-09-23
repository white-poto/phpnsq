<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/9/21
 * Time: 15:35
 */

namespace Nsq\Connection;


use Nsq\Encoding\Decoder;
use Nsq\Encoding\Frame;
use Nsq\Exception\ConnectionException;
use Nsq\Exception\RuntimeException;

class Connection
{
    /**
     * @var resource
     */
    protected $socket;

    /**
     * @var string
     */
    protected $buffer;

    /**
     * @var
     */
    protected $callback;

    /**
     * @var Decoder
     */
    protected $decoder;

    protected $read_watcher;

    /**
     * @param $host
     * @param $port
     * @throws ConnectionException
     */
    public function __construct($host, $port)
    {
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if ($this->socket === FALSE) {
            $message = "socket_create() failed: reason: "
                . socket_strerror(socket_last_error());
            throw new ConnectionException($message);
        }

        $connect_result = socket_connect($this->socket, $host, $port);
        if ($connect_result === false) {
            $message = "socket_connect failed. reason:"
                . socket_strerror(socket_last_error());
            throw new ConnectionException($message);
        }

        $non_block = socket_set_nonblock($this->socket);
        if ($non_block === false) {
            $message = "socket_set_nonblock faild. reason:"
                . socket_strerror(socket_last_error());
            throw new ConnectionException($message);
        }

        $this->buffer = '';
        $this->decoder = new Decoder();
    }

    public function write($data)
    {
        $write_result = socket_write($this->socket, $data, strlen($data));
        if ($write_result === false) {
            $message = "write to socket failed. reason:"
                . socket_strerror(socket_last_error());
            echo $message . PHP_EOL;
            throw new ConnectionException($message);
        }

        return true;
    }

    /**
     * @param $callback
     * @throws ConnectionException
     */
    public function onRead($callback)
    {
        if (!is_callable($callback)) {
            throw new ConnectionException("params callback is not callable");
        }

        $this->read_watcher = new \EvIo($this->socket, \Ev::READ, function ($watcher, $reader) use ($callback) {
            $frame = $this->readFrame();
            if ($frame === false) {
                return false;
            }
            if ($frame->getType() == Decoder::FRAME_TYPE_ERROR) {
                throw new RuntimeException("frame type error. type:" . $frame->getType());
            }

            return call_user_func($callback, $frame->getMessage());
        });
    }

    /**
     * @return bool|Frame
     * @throws \Nsq\Exception\EncodingException
     */
    public function readFrame()
    {

        $data = $size = $type = $content = NULL;
        if (!empty($this->buffer)) {
            $data = $this->buffer;
        }
        $data_length = strlen($data);

        // read size
        if ($data_length < 4) {
            $ret = $this->read(4 - $data_length);
            if ($ret === false) {
                return false;
            }
            $data .= $ret;
            $this->appendBuffer($ret);
        }
        $size = $this->decoder->readSize($data);

        // read type
        if ($data_length < 8) {
            $ret = $this->read(8 - $data_length);
            if ($ret === false) {
                return false;
            }
            $data .= $ret;
            $this->appendBuffer($ret);

        }
        $type = $this->decoder->readType($data);

        // read content
        if ($data_length < 8 + $size) {
            $ret = $this->read(4 + $size - $data_length);
            if ($ret === false) {
                return false;
            }
            $data .= $ret;
            $this->appendBuffer($ret);
        }
        $content = $this->decoder->readContent($data);

        return new Frame($size, $type, $content);
    }


    /**
     * @param int $length
     * @return bool
     */
    protected function read($length = 4)
    {
        $ret = socket_recv($this->socket, $data, $length, MSG_DONTWAIT);

        $e_non_blocking = array(
            11,/*EAGAIN or EWOULDBLOCK*/
            115/*EINPROGRESS*/
        );
        // Caught EINPROGRESS, EAGAIN, or EWOULDBLOCK
        if (in_array(socket_last_error(), $e_non_blocking)) {
            return false;
        }

        return $data;
    }

    /**
     * @param $data
     */
    protected function appendBuffer($data)
    {
        $this->buffer .= $data;
    }

    /**
     *
     */
    public static function run()
    {
        \Ev::run();
    }
}