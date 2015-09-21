<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/9/19
 * Time: 10:32
 */

namespace Nsq\Encoding;


class Encoder
{
    /**
     *
     */
    const MAGIC_V2 = '  V2';

    /**
     * @return string
     */
    public function magic()
    {
        return self::MAGIC_V2;
    }

    /**
     * @param $topic
     * @param $data
     * @return string
     */
    public function publish($topic, $data)
    {
        return $this->command("PUB", $topic, $data);
    }

    /**
     * @return string
     */
    public function close()
    {
        return $this->command('CLS');
    }

    /**
     * @param $command
     * @param null $params
     * @param null $data
     * @return string
     */
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