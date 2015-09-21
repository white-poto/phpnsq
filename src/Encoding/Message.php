<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/9/21
 * Time: 16:36
 */

namespace Nsq\Encoding;


class Message
{
    /**
     * @var string
     */
    protected $timestamp;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $body;

    /**
     * @var string
     */
    protected $attempts;

    /**
     * @param $data
     */
    public function __construct($data)
    {
        $this->timestamp = substr($data, 0, 8);
        $this->attempts = substr($data, 8, 2);
        $this->id = substr($data, 10, 16);
        $this->body = substr($data, 26);
    }

    /**
     * @return string
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @return string
     */
    public function getAttempts()
    {
        return $this->getAttempts();
    }
}