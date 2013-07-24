<?php
/**
 * This file is part of the RatchetBundle project.
 *
 * (c) 2013 Philipp Boes <mostgreedy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace P2\Bundle\RatchetBundle\Socket\Event;

use P2\Bundle\RatchetBundle\Socket\Connection\ConnectionInterface;
use P2\Bundle\RatchetBundle\Socket\Payload\EventPayload;

/**
 * Class MessageEvent
 * @package P2\Bundle\RatchetBundle\Event
 */
class MessageEvent extends ConnectionEvent
{
    /**
     * @var EventPayload
     */
    protected $payload;

    /**
     * @param ConnectionInterface $connection
     * @param EventPayload $payload
     */
    public function __construct(ConnectionInterface $connection, EventPayload $payload)
    {
        $this->connection = $connection;
        $this->payload = $payload;
    }

    /**
     * @return EventPayload
     */
    public function getPayload()
    {
        return $this->payload;
    }
}
