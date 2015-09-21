<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/9/19
 * Time: 10:32
 */

namespace Nsq\Encoding;


class Reader
{
    const FRAME_TYPE_RESPONSE = 0;
    const FRAME_TYPE_ERROR = 1;
    const FRAME_TYPE_MESSAGE = 2;

    protected $size;

    protected $type;

    protected $content;

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

    public function decode($data)
    {
        $this->size = $this->readSize($data);
        if ($this->size === false) {
            return false;
        }

        $this->type = $this->readType($data);
        if ($this->type === false) {
            return false;
        }

        $this->content = $this->readContent($data);
        if ($this->content === false) {
            return false;
        }
        var_dump($this->size);
        var_dump($this->type);
        var_dump($this->content);
    }

    public function readSize($data)
    {
        if (strlen($data) < 1) {
            return false;
        }
        $size = unpack("N", $data);

        return $size[1];
    }

    public function readType($data)
    {
        if (strlen($data) < 1) {
            return false;
        }
        $type = unpack("N", $data);

        return $type[1];
    }

    protected function readContent($data)
    {
        echo $this->size . PHP_EOL;
        if (strlen($data) < $this->size + 8) {
            return false;
        }

        return substr($data, 8, $this->size);
    }
}