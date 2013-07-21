<?php
/**
 * This file is part of the RatchetBundle project.
 *
 * (c) 2013 Philipp Boes <mostgreedy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace P2\Bundle\RatchetBundle\WebSocket\Event;

use P2\Bundle\RatchetBundle\WebSocket\Payload\Payload;
use Ratchet\ConnectionInterface;
use Symfony\Component\EventDispatcher\Event;


/**
 * Class MessageEvent
 * @package P2\Bundle\RatchetBundle\WebSocket\Event
 */
class MessageEvent extends Event implements SocketEventInterface
{
    /**
     * @var \Ratchet\ConnectionInterface
     */
    protected $connection;

    /**
     * @var \P2\Bundle\RatchetBundle\WebSocket\Payload\Payload
     */
    protected $payload;

    /**
     * @param ConnectionInterface $connection
     * @param \P2\Bundle\RatchetBundle\WebSocket\Payload\Payload $payload
     */
    public function __construct(ConnectionInterface $connection, Payload $payload)
    {
        $this->connection = $connection;
        $this->payload = $payload;
    }

    /**
     * @return \Ratchet\ConnectionInterface
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @return \P2\Bundle\RatchetBundle\WebSocket\Payload\Payload
     */
    public function getPayload()
    {
        return $this->payload;
    }
}
