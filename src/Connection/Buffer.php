<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/9/23
 * Time: 17:57
 */

namespace Nsq\Connection;


class Buffer
{
    /**
     * @var string buffer data
     */
    protected $data = '';

    /**
     * @param $data
     */
    public function append($data)
    {
        $this->data .= $data;
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return empty($data);
    }

    /**
     * @param $start
     */
    public function sub($start)
    {
        $this->data = substr($this->data, $start);
    }

    /**
     * @return string
     */
    public function get()
    {
        return $this->data;
    }
}