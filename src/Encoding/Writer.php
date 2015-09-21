<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/9/19
 * Time: 10:32
 */

namespace Nsq\Encoding;


class Writer
{
    const MAGIC_V2 = '  V2';

    public function magic()
    {
        return self::MAGIC_V2;
    }

    public function publish($topic, $data)
    {
        return $this->command("PUB", $topic, $data);
    }

    public function close()
    {
        return $this->command('CLS');
    }

    protected function command($command, $params = null, $data = null)
    {
        if (is_array($params)) {
            $params = explode(' ', $params);
        }
        if (!is_null($data)) {
            $size = pack('N', strlen($data));
            $data = $size . $data;
        }

        return $command . ' ' . $params . "\n" . $data;
    }
}