<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/9/23
 * Time: 17:57
 */

namespace Nsq\Connection;


class Buffer
{
    protected $data = '';

    public function append($data)
    {
        $this->data .= $data;
    }

    public function isEmpty()
    {
        return empty($data);
    }

    public function sub($start)
    {
        $this->data = substr($this->data, $start);
    }

    public function get()
    {
        return $this->data;
    }
}