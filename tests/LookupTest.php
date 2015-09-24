<?php

/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/9/22
 * Time: 9:19
 */
class LookupTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Nsq\Lookup\Lookup
     */
    protected $lookup;

    /**
     * @var \Nsq\Lookup\LookupCluster
     */
    protected $lookup_cluster;

    /**
     *
     */
    public function setUp()
    {
        $host = "127.0.0.1";
        $port = 4161;
        $this->lookup = new \Nsq\Lookup\Lookup($host, $port);
        $this->lookup_cluster = new \Nsq\Lookup\LookupCluster($host . ':' . $port, "phpnsq_1");
    }

    public function testLookup()
    {
        $hosts = $this->lookup->lookup("phpnsq_1");
        $this->assertEquals($hosts['status_code'], 200);
    }

    public function testClusterLookup()
    {
        $hosts = $this->lookup_cluster->lookup();
        $this->assertEquals($hosts[0], "waytoft:4150");
    }
}