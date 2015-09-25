<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/9/23
 * Time: 19:48
 */

namespace Nsq\Connection;


use Nsq\Lookup\LookupCluster;

class ConnectionPool implements \Iterator
{
    /**
     * @var array
     */
    protected $connections = array();

    /**
     * @var LookupCluster
     */
    protected $lookup = null;

    protected $topic;

    /**
     * @var array
     */
    protected $host_list = array();


    public function __construct(LookupCluster $lookup = null)
    {
        $this->lookup = $lookup;
        $this->refreshConnections();
    }

    public function addConnection($host, $port)
    {
        array_push($this->connections, new Connection($host, $port));
    }

    /**
     *
     */
    protected function refreshConnections()
    {
        if (is_null($this->lookup)) {
            return;
        }

        $this->host_list = $this->lookup->lookup();
        // add new connections
        foreach ($this->host_list as $host) {
            $host_flag = false;
            $host_info = explode(':', $host);
            foreach ($this->connections as $conn) {
                if ($conn->equal($host_info[0], $host_info[1])) {
                    $host_flag = true;
                }
            }
            if ($host_flag === false) {
                array_push($this->connections, new Connection($host_info[0], $host_info[1]));
            }
        }

        // remove connections
        foreach ($this->connections as $key => $conn) {
            $conn_flag = false;
            foreach ($this->host_list as $host) {
                $host_info = explode(':', $host);
                if ($conn->equal($host_info[0], $host_info[1])) {
                    $conn_flag = true;
                }
            }
            if ($conn_flag === false) {
                $conn->close();
                unset($this->connections[$key]);
            }
        }
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     */
    public function current()
    {
        return current($this->connections);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        next($this->connections);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        return key($this->connections);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        return (current($this->connections) === false ? false : true);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        reset($this->connections);
    }
}