<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/9/19
 * Time: 10:28
 */

namespace Nsq;

use Nsq\Connection\Loop;
use Nsq\Lookup\LookupCluster;
use Psr\Log\LoggerInterface;


class Nsq
{
    const VERSION = "0.1";

    /**
     * @var LookupCluster
     */
    protected $lookup_cluster;

    protected $connections;

    /**
     * @var Loop
     */
    protected $loop;


    public function __construct()
    {

    }

    public function setLookup(LookupCluster $lookup)
    {
        $this->lookup_cluster = $lookup;
    }


}