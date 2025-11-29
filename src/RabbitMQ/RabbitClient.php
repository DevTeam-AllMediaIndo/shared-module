<?php
namespace Allmedia\Shared\RabbitMQ;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitClient {
    protected AMQPStreamConnection $connection;
    protected  AMQPChannel $channel;

    public function __construct(string $host, int $port, string $user, string $pass, string $vhost)
    {
        $this->connection = new AMQPStreamConnection($host, $port, $user, $pass, $vhost);
        $this->channel = $this->connection->channel();
    }

    public function declareQueue(string $queueName, bool $durable = true): void 
    {
        $this->channel->queue_declare(
            $queueName, 
            false, 
            $durable, 
            false, 
            false
        );
    }

    public function publish(string $exchange, string $routingKey, array|string $payload): void 
    {
        if(is_array($payload)) {
            $payload = json_encode($payload);
        }

        $msg = new AMQPMessage(
            $payload, 
            ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]
        );

        $this->channel->basic_publish($msg, $exchange, $routingKey);
    }

    public function close(): void 
    {
        $this->channel->close();
        $this->connection->close();
    }
}