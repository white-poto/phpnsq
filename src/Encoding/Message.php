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
    protected $timestamp;

    protected $id;

    protected $body;

    protected $attempts;

    public function __construct($data)
    {
        $this->timestamp = substr($data, 0, 8);
        $this->attempts = substr($data, 8, 2);
        $this->id = substr($data, 10, 16);
        $this->body = substr($data, 26);
    }

    public function getTimestamp()
    {
        return $this->timestamp;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function getAttempts()
    {
        return $this->getAttempts();
    }


}