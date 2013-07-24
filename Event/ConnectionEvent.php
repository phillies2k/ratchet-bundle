<?php
/**
 * This file is part of the RatchetBundle project.
 *
 * (c) 2013 Philipp Boes <mostgreedy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace P2\Bundle\RatchetBundle\Event;

use P2\Bundle\RatchetBundle\Socket\ClientInterface;
use Ratchet\ConnectionInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class ConnectionEvent
 * @package P2\Bundle\RatchetBundle\Event
 */
class ConnectionEvent extends Event
{
    /**
     * @var \Ratchet\ConnectionInterface
     */
    protected $connection;

    /**
     * @var \P2\Bundle\RatchetBundle\Socket\ClientInterface
     */
    protected $client;

    /**
     * @param ConnectionInterface $connection
     * @param ClientInterface $client
     */
    public function __construct(ConnectionInterface $connection, ClientInterface $client = null)
    {
        $this->connection = $connection;
        $this->client = $client;
    }

    /**
     * @return \Ratchet\ConnectionInterface
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @return \P2\Bundle\RatchetBundle\Socket\ClientInterface
     */
    public function getClient()
    {
        return $this->client;
    }
}
