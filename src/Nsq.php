<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/9/19
 * Time: 10:28
 */

namespace Nsq;

use Nsq\Connection\ConnectionPool;
use Nsq\Connection\Loop;
use Nsq\Lookup\LookupCluster;
use Psr\Log\LoggerInterface;


class Nsq
{
    const VERSION = "0.1";

    /**
     * @var LookupCluster
     */
    protected $lookup;

    protected $connections;

    protected $topic;

    protected $lookup_hosts;

    protected $nsq_hosts;

    /**
     * @var Loop
     */
    protected $loop;


    public function __construct($nsq_hosts = null)
    {

    }

    public function lookup($lookup_hosts)
    {
        $this->lookup_hosts = $lookup_hosts;
    }

    public function consume($topic)
    {
        if (!empty($this->lookup_hosts)) {
            $this->lookup = new LookupCluster($this->lookup_hosts, $topic);
            $connection_pool = new ConnectionPool($this->lookup);
        }
    }

}