<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/9/21
 * Time: 17:14
 */

namespace Nsq\Lookup;

use Nsq\Exception\LookupException;
use Nsq\Nsq;

class Lookup
{

    /**
     * @var string
     */
    protected $host;

    /**
     * @var integer
     */
    protected $port;

    /**
     * @var int
     */
    protected $connection_timeout;

    /**
     * @var int
     */
    protected $timeout;

    /**
     * @var string
     */
    protected $url;

    /**
     * @param string $host
     * @param int $port
     * @param int $connection_timeout
     * @param int $timeout
     */
    public function __construct($host = '127.0.0.1', $port = 4161, $connection_timeout = 1, $timeout = 2)
    {
        $this->host = $host;
        $this->port = $port;
        $this->connection_timeout = $connection_timeout;
        $this->timeout = $timeout;
        $this->url = "http://{$host}:{$port}/";
    }

    /**
     * @param $topic
     * @return mixed
     * @throws LookupException
     */
    public function lookup($topic)
    {
        $url = $this->url . "lookup?topic=" . urlencode($topic);
        return $this->get($url);
    }

    /**
     * @return mixed
     * @throws LookupException
     */
    public function topics()
    {
        $url = $this->url . "topics";
        return $this->get($url);
    }

    /**
     * @param $topic
     * @return mixed
     * @throws LookupException
     */
    public function channels($topic)
    {
        $url = $this->url . "channels?topic=" . urlencode($topic);
        return $this->get($url);
    }

    /**
     * @return mixed
     * @throws LookupException
     */
    public function nodes()
    {
        $url = $this->url . "nodes";
        return $this->get($url);
    }

    /**
     * @param $topic
     * @param $channel
     * @return mixed
     * @throws LookupException
     */
    public function delete_topic($topic, $channel)
    {
        $query_string = http_build_query(compact('topic', 'channel'));
        $url = $this->url . "delete_channel?" . $query_string;

        return $this->get($url);
    }

    /**
     * @param $topic
     * @param $node
     * @return mixed
     * @throws LookupException
     */
    public function tombstone_topic_producer($topic, $node)
    {
        $query_string = http_build_query(compact('topic', $node));
        $url = $this->url . "tombstone_topic_producer?" . $query_string;
        return $this->get($url);
    }

    /**
     * @return mixed
     * @throws LookupException
     */
    public function ping()
    {
        $url = $this->url . 'ping';
        return $this->get($url);
    }

    /**
     * @return mixed
     * @throws LookupException
     */
    public function info()
    {
        $url = $this->url . 'info';
        return $this->get($url);
    }

    /**
     * @param $url
     * @return mixed
     * @throws LookupException
     */
    protected function get($url)
    {
        $ch = curl_init($url);
        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_ENCODING => '',
            CURLOPT_USERAGENT => 'phpnsq ' . Nsq::VERSION,
            CURLOPT_CONNECTTIMEOUT => $this->connection_timeout,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_FAILONERROR => true
        );
        curl_setopt_array($ch, $options);
        $json = curl_exec($ch);
        if ($json === false) {
            $message = "request failed";
            throw new LookupException($message);
        }

        $result = json_decode($json, true);

        if ($result === false) {
            $message = "request failed:" . $json;
            throw new LookupException($message);
        }

        if ($result['status_code'] != 200) {
            $message = "request failed:" . $json;
            throw new LookupException($message);
        }

        return $result;
    }
}