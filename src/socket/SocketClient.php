<?php
namespace toolbox\socket;
use Workerman\Lib\Timer;
use Workerman\Worker;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/1
 * Time: 9:52
 */
class SocketClient
{
    protected $connectionConfig;
    protected $connection;
    protected $activeConnection;
    /**
     * SocketClient constructor.
     * @param $connection
     */
    public function __construct($connectionConfig, \Closure $onMessageCallback, \Closure $onConnectCallback = null, \Closure $onCloseCallback = null,\Closure $onWorkerStart=null)
    {
        $this->connectionConfig = $connectionConfig;
        $this->connection = new TcpConnection($connectionConfig);
        $onMessage = function ($connection, $data) use ($onMessageCallback) {
            DLog($data);
            if ($onMessageCallback) {
                $onMessageCallback($connection, $data);
            }
        };
        $onConnect = function ($connection) use ($onConnectCallback) {
            DLog("建立连接");
            $this->activeConnection=$connection;
            if ($onConnectCallback) {
                $onConnectCallback($connection);
            }
        };
        $onClose = function ($connection) use ($onCloseCallback) {
//            $this->reConnect();
            DLog("连接掉线");
            if ($onCloseCallback) {
                $onCloseCallback($connection);
            }
            $this->connection->connect();
            sleep(3);
        };

        $this->connection->onMessage = $onMessage;
        $this->connection->onConnect = $onConnect;
        $this->connection->onClose = $onClose;
        $task = new \Workerman\Worker();
        $task->name=$connectionConfig;
        $task->onWorkerStart = function ()use($onWorkerStart) {
            DLog("Worker启动中...");
            $this->connection->connect();
            isset($onWorkerStart)&&$onWorkerStart();
        };
        \Workerman\Worker::runAll();
    }

    public function connect()
    {
        $this->connection->connect();
    }



}
