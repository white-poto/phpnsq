<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/9/21
 * Time: 18:04
 */

namespace Nsq\Lookup;


class LookupCluster
{
    /**
     * @var array
     */
    protected $lookup_instances = array();

    /**
     * @var array
     */
    protected $host_list;

    /**
     * @param $host_list
     */
    public function __construct($host_list)
    {
        if (!is_array($host_list)) {
            $this->host_list = array($host_list);
        } else {
            $this->host_list = $host_list;
        }
        $this->initLookupInstances();
    }

    /**
     *
     */
    protected function initLookupInstances()
    {
        foreach ($this->host_list as $host) {
            $host_info = explode(':', $host);
            $lookup = new Lookup($host_info[0], $host_info[1]);
            array_push($this->lookup_instances, $lookup);
        }
    }

    /**
     * @param $host
     * @param $port
     */
    public function addLookup($host, $port)
    {
        array_push($this->host_list, "{$host}:{$port}");
        $lookup = new Lookup($host, $port);
        array_push($this->lookup_instances, $lookup);
    }

    /**
     * @param $topic
     * @return array
     */
    public function lookup($topic)
    {
        $nsq_host_lists = array();
        foreach ($this->lookup_instances as $lookup) {
            $host_list = $lookup->lookup($topic);
            $hosts = $host_list['data']['producers'];
            foreach ($hosts as $host) {
                $nsq_host_lists[] = $host['broadcast_address'] . ':' . $host['tcp_port'];
            }
        }

        return $nsq_host_lists;
    }
}