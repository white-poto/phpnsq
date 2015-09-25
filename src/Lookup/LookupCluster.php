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

    protected $topic;

    /**
     * @param $host_list
     */
    public function __construct($host_list, $topic)
    {
        $this->host_list = $host_list;
        $this->topic = $topic;
        $this->initLookupInstances();
    }

    /**
     *
     */
    protected function initLookupInstances()
    {
        $host_list = explode(',', $this->host_list);
        foreach ($host_list as $host) {
            $host_info = explode(':', $host);
            $lookup = new Lookup($host_info[0], $host_info[1]);
            array_push($this->lookup_instances, $lookup);
        }
    }

    /**
     * @param $topic
     * @return array
     */
    public function lookup()
    {
        $nsq_host_lists = array();
        foreach ($this->lookup_instances as $lookup) {
            $host_list = $lookup->lookup($this->topic);
            $hosts = $host_list['data']['producers'];
            foreach ($hosts as $host) {
                $nsq_host_lists[] = array('host' => $host['broadcast_address'], 'port' => $host['tcp_port']);
            }
        }

        return $nsq_host_lists;
    }
}