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
     * @param $channel
     * @return string
     */
    public function sub($topic, $channel)
    {
        return $this->command("SUB", array($topic, $channel));
    }

    /**
     * @param $topic
     * @param $data
     * @return string
     */
    public function pub($topic, $data)
    {
        return $this->command("PUB", $topic, $data);
    }

    /**
     * @param $count
     * @return string
     */
    public function rdy($count)
    {
        return $this->command("RDY", $count);
    }

    /**
     * @param $message_id
     * @return string
     */
    public function fin($message_id)
    {
        return $this->command("FIN", $message_id);
    }

    /**
     * @param $message_id
     * @return string
     */
    public function req($message_id)
    {
        return $this->command("REQ", $message_id);
    }

    /**
     * @param $message_id
     * @return string
     */
    public function touch($message_id)
    {
        return $this->command("TOUCH", $message_id);
    }

    /**
     * @return string
     */
    public function close()
    {
        return $this->command('CLS');
    }

    /**
     * @return string
     */
    public function nop()
    {
        return $this->command("NOP");
    }

    /**
     *
     */
    public function auth()
    {

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