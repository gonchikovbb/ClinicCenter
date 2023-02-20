<?php

namespace App\Service;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

require __DIR__ . '/../../vendor/autoload.php';

class QueueService
{
    private AMQPStreamConnection $connection;

    public function __construct()
    {
        $this->connection = new AMQPStreamConnection('rabbitmq', '5672', 'myuser', 'mypassword');
    }

    /**
     * @throws \Exception
     */
    public function produce(array $data, string $queue): void
    {
        $channel = $this->createChannel($queue);

        $msg = new AMQPMessage(json_encode($data));

        $channel->basic_publish($msg,'', $queue);

        $channel->close();

        $this->connection->close();
    }

    public function consume(string $queue, callable $callback): void
    {
        $channel = $this->createChannel($queue);

        $channel->basic_consume($queue,'',false,true,false,false, $callback);

        while ($channel->is_open()) {
            $channel->wait();
        }
    }

    private function createChannel(string $queue): AMQPChannel
    {
        $channel = $this->connection->channel();

        $channel->queue_declare($queue,false, false,false,false);

        return $channel;
    }
}