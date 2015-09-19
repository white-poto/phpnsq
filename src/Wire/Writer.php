<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/9/19
 * Time: 10:32
 */

namespace Nsq\Wire;


class Writer
{
    const MAGIC_V2 = '  V2';

    public function magic(){
        return self::MAGIC_V2;
    }

    public function close(){
        return $this->command('CLS');
    }

    protected function command($command, $params = null, $message = null)
    {
        if (is_array($params)) {
            $params = explode(' ', $params);
        }
        if (!is_null($message)) {
            $size = pack('N', strlen($message));
            $message = $size . $message;
        }

        return $command . ' ' . $params . "\n" . $message;
    }
}