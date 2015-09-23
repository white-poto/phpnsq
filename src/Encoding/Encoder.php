<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/9/19
 * Time: 10:32
 */

namespace Nsq\Encoding;


use Nsq\Exception\EncodingException;

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
     * @param $config
     * @return string
     */
    public function identify($config)
    {
        $data = json_encode($config);
        return $this->command("IDENTIFY", NULL, $data);
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
     * @param $topic
     * @param $data
     * @return string
     * @throws EncodingException
     */
    public function mpub($topic, $data)
    {
        if (!is_array($data) ||
            !($data instanceof \ArrayAccess) ||
            !($data instanceof \Iterator)
        ) {
            throw new EncodingException("param data must be a array like param");
        }
        $command = "MPUB" . ' ' . $topic . "\n";

        $messages = '';
        foreach ($data as $value) {
            $size = pack("N", strlen($value));
            $messages .= $size . $value;
        }

        $data_size = pack("N", strlen($messages));
        $message_count = pack("N", count($data));
        $command .= $data_size . $message_count . $messages;

        return $command;
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
            $params = implode(' ', $params);
        }
        if (!is_null($data)) {
            $size = pack('N', strlen($data));
            $data = $size . $data;
        }

        return $command . ' ' . $params . "\n" . $data;
    }
}