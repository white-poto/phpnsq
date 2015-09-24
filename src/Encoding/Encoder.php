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
    const MAGIC_V2 = '  V2';

    const IDENTIFY = "IDENTIFY";
    const PING = "PING";
    const SUB = "SUB";
    const PUB = "PUB";
    const MPUB = "MPUB";
    const RDY = "RDY";
    const FIN = "FIN";
    const REQ = "REQ";
    const TOUCH = "TOUCH";
    const CLS = "CLS";
    const NOP = "NOP";

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
        return $this->command(self::IDENTIFY, NULL, $data);
    }

    /**
     * @return string
     */
    public function ping()
    {
        return $this->command(self::PING);
    }

    /**
     * @param $topic
     * @param $channel
     * @return string
     */
    public function sub($topic, $channel)
    {
        return $this->command(self::SUB, array($topic, $channel));
    }

    /**
     * @param $topic
     * @param $data
     * @return string
     */
    public function pub($topic, $data)
    {
        return $this->command(self::PUB, $topic, $data);
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
        $command = self::MPUB . ' ' . $topic . "\n";

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
        return $this->command(self::RDY, $count);
    }

    /**
     * @param $message_id
     * @return string
     */
    public function fin($message_id)
    {
        return $this->command(self::FIN, $message_id);
    }

    /**
     * @param $message_id
     * @return string
     */
    public function req($message_id)
    {
        return $this->command(self::REQ, $message_id);
    }

    /**
     * @param $message_id
     * @return string
     */
    public function touch($message_id)
    {
        return $this->command(self::TOUCH, $message_id);
    }

    /**
     * @return string
     */
    public function cls()
    {
        return $this->command(self::CLS);
    }

    /**
     * @return string
     */
    public function nop()
    {
        return $this->command(self::NOP);
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