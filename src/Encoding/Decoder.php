<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/9/19
 * Time: 10:32
 */

namespace Nsq\Encoding;


class Decoder
{
    /**
     *
     */
    const FRAME_TYPE_RESPONSE = 0;
    /**
     *
     */
    const FRAME_TYPE_ERROR = 1;
    /**
     *
     */
    const FRAME_TYPE_MESSAGE = 2;

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

    /**
     * @param $data
     * @return bool
     */
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

        return true;
    }

    /**
     * @param $data
     * @return bool
     */
    public function readSize($data)
    {
        if (strlen($data) < 1) {
            return false;
        }
        $size = unpack("N", $data);

        return $size[1];
    }

    /**
     * @param $data
     * @return bool
     */
    public function readType($data)
    {
        if (strlen($data) < 8) {
            return false;
        }
        $type_data = substr($data, 4, 4);
        $type = unpack("N", $type_data);

        return $type[1];
    }

    /**
     * @param $data
     * @return bool|string
     */
    protected function readContent($data)
    {
        echo $this->size . PHP_EOL;
        if (strlen($data) < $this->size) {
            return false;
        }

        return substr($data, 8, $this->size);
    }
}