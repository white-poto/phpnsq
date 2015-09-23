<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/9/19
 * Time: 10:32
 */

namespace Nsq\Encoding;


use Nsq\Exception\EncodingException;

class Decoder
{
    const FRAME_TYPE_RESPONSE = 0;
    const FRAME_TYPE_ERROR = 1;
    const FRAME_TYPE_MESSAGE = 2;

    /**
     * @var integer
     */
    protected $size;

    /**
     * @var integer
     */
    protected $type;

    /**
     * @var string
     */
    protected $content;

    /**
     * @param $data
     * @return bool|int
     */
    public function readSize($data)
    {
        if (strlen($data) < 1) {
            return false;
        }

        $size = unpack("N", $data);
        $this->size = intval($size[1]);

        return $this->size;
    }

    /**
     * @param $data
     * @return bool|int
     * @throws EncodingException
     */
    public function readType($data)
    {
        if (strlen($data) < 8) {
            return false;
        }

        $type_data = substr($data, 4, 4);
        $type = unpack("N", $type_data);
        $this->type = intval($type[1]);
        if (
            $this->type != self::FRAME_TYPE_ERROR &&
            $this->type != self::FRAME_TYPE_MESSAGE &&
            $this->content != self::FRAME_TYPE_RESPONSE
        ) {
            throw new EncodingException("error frame type. type:" . $this->type);
        }

        return $this->type;
    }

    /**
     * @param $data
     * @return bool|string
     */
    public function readContent($data)
    {
        if (strlen($data) < $this->size) {
            return false;
        }
        $this->content = substr($data, 8, $this->size);

        return $this->content;
    }

    public function getFrame()
    {
        return new Frame($this->size, $this->type, $this->content);
    }
}