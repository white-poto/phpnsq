<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/9/23
 * Time: 15:57
 */

namespace Nsq\Encoding;


class Frame
{
    protected $size;

    protected $type;

    protected $content;

    public function __construct($size, $type, $content)
    {
        $this->size = $size;
        $this->type = $type;
        $this->content = $content;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getMessage()
    {
        return new Message($this->content);
    }
}