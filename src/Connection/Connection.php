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
use Nsq\Lookup\LookupCluster;
use React\EventLoop\LoopInterface;

class Connection
{
    /**
     * @var LookupCluster
     */
    protected $lookup;

    /**
     * @var LoopInterface
     */
    protected $loop;

    protected $addresses = array();

    protected $sockets = array();

    public function __construct($addresses)
    {
        foreach ($addresses as $address) {
            $key = $address['ip'] . ':' . $address['port'];
            $this->addresses[$key] = $address;
        }
        $this->refresh($addresses);
    }

    public function refresh($addresses)
    {
        $addresses_with_key = array();
        foreach ($addresses as $address) {
            $key = $address['ip'] . ':' . $address['port'];
            $addresses_with_key[$key] = $address;
        }

        foreach ($addresses_with_key as $key => $address) {
            if (array_key_exists($key, $this->sockets)) continue;
            $socket = stream_socket_client("tcp://{$address['ip']}:{$address['port']}", $error_no, $error_str, 30);
            if ($socket === false) {
                throw new ConnectionException("stream_socket_client failed. reason:" . $error_str, $error_no);
            }
            $this->sockets[$key] = $socket;
        }

        foreach ($this->sockets as $key => $socket) {
            if (array_key_exists($key, $addresses_with_key)) continue;
            fclose($socket);
            unset($this->sockets[$key]);
        }
    }

    public function write($content)
    {

    }
}