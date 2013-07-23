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

use P2\Bundle\RatchetBundle\Socket\Payload;
use Ratchet\ConnectionInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class MessageEvent
 * @package P2\Bundle\RatchetBundle\Socket\Event
 */
class MessageEvent extends Event
{
    /**
     * @var \Ratchet\ConnectionInterface
     */
    protected $connection;

    /**
     * @var \P2\Bundle\RatchetBundle\Socket\Payload
     */
    protected $payload;

    /**
     * @param ConnectionInterface $connection
     * @param Payload $payload
     */
    public function __construct(ConnectionInterface $connection, Payload $payload)
    {
        $this->connection = $connection;
        $this->payload = $payload;
    }

    /**
     * @return ConnectionInterface
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @return Payload
     */
    public function getPayload()
    {
        return $this->payload;
    }
}
