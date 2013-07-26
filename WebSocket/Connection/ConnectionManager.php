<?php
/**
 * This file is part of the RatchetBundle project.
 *
 * (c) 2013 Philipp Boes <mostgreedy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace P2\Bundle\RatchetBundle\WebSocket\Connection;

use P2\Bundle\RatchetBundle\WebSocket\Client\ClientProviderInterface;
use Ratchet\ConnectionInterface as SocketConnection;

/**
 * Class ConnectionManager
 * @package P2\Bundle\RatchetBundle\WebSocket\Connection
 */
class ConnectionManager implements ConnectionManagerInterface
{
    /**
     * @var ClientProviderInterface
     */
    protected $clientProvider;

    /**
     * @var \SplObjectStorage
     */
    protected $connections;

    /**
     * @param ClientProviderInterface $clientProvider
     */
    public function __construct(ClientProviderInterface $clientProvider)
    {
        $this->clientProvider = $clientProvider;
        $this->connections = new \SplObjectStorage();
    }

    /**
     * {@inheritDoc}
     */
    public function getConnection(SocketConnection $socketConnection)
    {
        return $this->connections->offsetGet($socketConnection);
    }

    /**
     * {@inheritDoc}
     */
    public function getConnections()
    {
        $connections = array();

        foreach ($this->connections as $connection) {
            $connections[] = $this->connections->offsetGet($connection);
        }

        return $connections;
    }

    /**
     * {@inheritDoc}
     */
    public function addConnection(SocketConnection $socketConnection)
    {
        if (! $this->connections->offsetExists($socketConnection)) {
            $connection = new Connection($this, $socketConnection);
            $this->connections->offsetSet($socketConnection, $connection);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function closeConnection(SocketConnection $socketConnection)
    {
        if ($this->connections->offsetExists($socketConnection)) {
            $this->connections->detach($socketConnection);
            $socketConnection->close();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function authenticate(ConnectionInterface $connection, $accessToken)
    {
        if (null !== $client = $this->clientProvider->findByAccessToken($accessToken)) {
            $connection->setClient($client);

            return true;
        }

        return false;
    }
}
