<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/9/24
 * Time: 9:21
 */

namespace Nsq;


use Nsq\Lookup\LookupCluster;
use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;

class Consumer
{

    protected $addresses = array();

    /**
     * @var LoopInterface
     */
    protected $loop;

    /**
     * @var LookupCluster
     */
    protected $lookup;

    protected $streams;

    public function __construct()
    {
        $this->loop = Factory::create();
    }

    public function stats()
    {

    }

    public function ConnectToNSQLookupd()
    {

    }

    public function ConnectToNSQLookupds()
    {

    }

    public function consume($topic, $callback)
    {

    }

    protected function addLookupTimer()
    {
        $this->loop->addPeriodicTimer(30, function () {
            $addresses = $this->lookup->lookup();
            foreach ($addresses as $address) {
                $key = $address['ip'] . ':' . $address['port'];
            }

            // ... refresh connection
        });
    }

    protected function init($callback)
    {
        foreach ($this->addresses as $address) {
            $socket = stream_socket_client("tcp://{$address['ip']}:{$address['port']}", $error_no, $error_str, 30);
            if ($socket === false) {

            }

            $this->loop->addReadStream($socket, function ($socket) use ($callback) {
                //½âÂë

                //»Øµ÷
                call_user_func($callback);
            });
            $this->streams[$address['ip'] . ':' . $address['port']] = $socket;
        }
    }
}