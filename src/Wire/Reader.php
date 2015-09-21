<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/9/19
 * Time: 10:32
 */

namespace Nsq\Wire;


class Reader
{
    const FRAME_TYPE_RESPONSE = 0;
    const FRAME_TYPE_ERROR = 1;
    const FRAME_TYPE_MESSAGE = 2;

    protected $size;

    protected $type;

    protected $content;

    public function readSize($data, $connection)
    {
        if (strlen($data) < 4) {
            return false;
        }
        $size_data = substr($data, 0, 4);
        $size = unpack("N", $size_data);

        return $size[1];
    }

    public function readType($data){
        if(strlen($data) < 8){
            return false;
        }
    }

    protected function readInt($data){

    }
}