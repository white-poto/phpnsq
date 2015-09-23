<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/9/23
 * Time: 19:48
 */

namespace Nsq\Connection;


class ConnectionPool
{
    /**
     * @var array
     */
    protected $connections = array();

    /**
     * @var array
     */
    protected $host_list = array();

    /**
     * @param $host_list
     */
    public function __construct($host_list)
    {
        $this->host_list = $host_list;
    }

    /**
     * @param $host_list
     */
    public function resetHostList($host_list)
    {
        $this->host_list = $host_list;
        $this->refreshConnections();
    }

    /**
     *
     */
    protected function refreshConnections()
    {
        foreach ($this->host_list as $host) {
            $flag = false;
            $host_info = explode(':', $host);
            foreach ($this->connections as $conn) {
                if ($conn->equal($host_info[0], $host_info[1])) {
                    $flag = true;
                }
            }
            if ($flag === false) {
                array_push($this->connections, new Connection($host_info[0], $host_info[1]));
            }
        }
    }
}