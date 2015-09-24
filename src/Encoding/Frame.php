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
    /**
     * @var
     */
    protected $size;

    /**
     * @var
     */
    protected $type;

    /**
     * @var
     */
    protected $content;

    /**
     * @param $size
     * @param $type
     * @param $content
     */
    public function __construct($size, $type, $content)
    {
        $this->size = $size;
        $this->type = $type;
        $this->content = $content;
    }

    /**
     * @return mixed
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return Message
     */
    public function getMessage()
    {
        return new Message($this->content);
    }
}