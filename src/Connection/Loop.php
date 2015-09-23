<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/9/23
 * Time: 19:20
 */

namespace Nsq\Connection;


use Nsq\Exception\ConnectionException;

class Loop
{
    protected $timers = array();

    protected $readers = array();

    public function addTimer($callback, $start = 0, $between = 1, Connection $con = null)
    {
        if (!is_callable($callback)) {
            throw new ConnectionException("param callback is not callable");
        }

        $timer = new \EvTimer($start, $between, function ($watcher) use ($con, $callback) {
            return call_user_func($callback, $con, $watcher);
        });

        array_push($this->timers, $timer);

        return $this;
    }

    public function addReader(Connection $con, $callback)
    {
        if (!is_callable($callback)) {
            throw new ConnectionException("param callback is not callable");
        }

        $reader = new \EvIo($con->getSocket(), \Ev::READ, function ($watcher) use ($con, $callback) {
            return call_user_func($callback, $con, $watcher);
        });

        array_push($this->readers, $reader);

        return $this;
    }

    public static function run()
    {
        return \Ev::run();
    }

    public static function stop()
    {
        return Ev::stop();
    }
}