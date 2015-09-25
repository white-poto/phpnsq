<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/9/21
 * Time: 15:35
 */

namespace Nsq\Connection;


use Nsq\Encoding\Buffer;
use Nsq\Encoding\Decoder;
use Nsq\Exception\ConnectionException;
use Nsq\Exception\RuntimeException;

class Connection
{

    /**
     * @var string
     */
    protected $host;

    /**
     * @var integer
     */
    protected $port;

    protected $reconnect_count;

    /**
     * @var resource
     */
    protected $socket;

    /**
     * @var Buffer
     */
    protected $buffer;

    /**
     * @var Decoder
     */
    protected $decoder;

    /**
     * @var
     */
    protected $read_watcher;

    /**
     * @param $host
     * @param int $port
     * @param int $reconnect_count
     * @throws ConnectionException
     */
    public function __construct($host, $port = 4150, $reconnect_count = 3)
    {
        $this->host = $host;
        $this->port = $port;
        $this->reconnect_count = $reconnect_count;
        $this->reconnect();

        $this->buffer = new Buffer();
        $this->decoder = new Decoder();
    }

    /**
     * reconnect to nsq server
     * @throws ConnectionException
     */
    public function reconnect()
    {
        $count = 0;
        while (true) {
            try {
                $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
                if ($this->socket === FALSE) {
                    $message = "socket_create() failed: reason: "
                        . socket_strerror(socket_last_error());
                    throw new ConnectionException($message);
                }

                $connect_result = socket_connect($this->socket, $this->host, $this->port);
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

                break;
            } catch (\Exception $e) {
                $count++;
                echo $count . PHP_EOL;
                if ($count > $this->reconnect_count) throw $e;

                sleep(4 * $count);
            }
        }
    }

    /**
     * @param $data
     * @return bool
     * @throws ConnectionException
     */
    public function write($data)
    {
        $write_result = socket_write($this->socket, $data, strlen($data));
        if ($write_result === false) {
            $message = "write to socket failed. reason:"
                . socket_strerror(socket_last_error());
            throw new ConnectionException($message);
        }

        return true;
    }

    /**
     * @param $callback
     * @return $this
     * @throws ConnectionException
     */
    public function onMessage($callback)
    {
        if (!is_callable($callback)) {
            throw new ConnectionException("params callback is not callable");
        }

        $this->read_watcher = new \EvIo($this->socket, \Ev::READ, function ($watcher, $reader) use ($callback) {
            $frame = $this->decoder->readFame($this);
            if ($frame === false) {
                return false;
            }
            if ($frame->getType() == Decoder::FRAME_TYPE_ERROR) {
                throw new RuntimeException("frame type error. type:" . $frame->getType());
            }

            return call_user_func($callback, $frame->getMessage());
        });

        return $this;
    }

    /**
     * @param int $length
     * @param bool|true $block
     * @return bool
     * @throws ConnectionException
     */
    public function read($length = 4, $block = true)
    {
        if ($block === false) {
            socket_recv($this->socket, $data, $length, MSG_DONTWAIT);
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

        $ret = socket_recv($this->socket, $data, $length, MSG_WAITALL);
        if ($ret === false) {
            $message = "socket_recv failed. reason:"
                . socket_strerror(socket_last_error());
            throw new ConnectionException($message);
        }

        return $data;
    }

    public function equal($host, $port)
    {
        return $this->host == $host && $this->port == $port;
    }

    public function getSocket()
    {
        return $this->socket;
    }

    public function close()
    {
        socket_close($this->socket);
    }

    public function __destruct()
    {
        $this->close();
    }
}