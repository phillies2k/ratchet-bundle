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
 * Class CloseEvent
 * @package P2\Bundle\RatchetBundle\WebSocket\Event
 */
class CloseEvent extends Event
{
    /**
     * @var \Ratchet\ConnectionInterface
     */
    protected $connection;

    /**
     * @param ConnectionInterface $connection
     */
    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return \Ratchet\ConnectionInterface
     */
    public function getConnection()
    {
        return $this->connection;
    }
}
