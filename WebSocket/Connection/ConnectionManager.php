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
use P2\Bundle\RatchetBundle\WebSocket\Exception\NotManagedConnectionException;
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
     * @var ConnectionInterface[]
     */
    protected $connections;

    /**
     * @param ClientProviderInterface $clientProvider
     */
    public function __construct(ClientProviderInterface $clientProvider)
    {
        $this->clientProvider = $clientProvider;
        $this->connections = array();
    }

    /**
     * {@inheritDoc}
     */
    public function hasConnection(SocketConnection $socketConnection)
    {
        return isset($this->connections[$socketConnection->resourceId]);
    }

    /**
     * {@inheritDoc}
     */
    public function getConnection(SocketConnection $socketConnection)
    {
        if (! $this->hasConnection($socketConnection)) {

            return null;
        }

        return $this->connections[$socketConnection->resourceId];
    }

    /**
     * {@inheritDoc}
     */
    public function getConnections()
    {
        return $this->connections;
    }

    /**
     * {@inheritDoc}
     */
    public function addConnection(SocketConnection $socketConnection)
    {
        if (! $this->hasConnection($socketConnection)) {
            $connection = new Connection($this, $socketConnection);
            $this->connections[$connection->getId()] = $connection;

            return $connection;
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function closeConnection(SocketConnection $socketConnection)
    {
        if (! $this->hasConnection($socketConnection)) {

            return false;
        }

        $connection = $this->getConnection($socketConnection);
        $connection->close();

        unset($this->connections[$connection->getId()]);

        return $connection;
    }

    /**
     * {@inheritDoc}
     */
    public function authenticate(ConnectionInterface $connection, $accessToken)
    {
        if (! isset($this->connections[$connection->getId()])) {
            throw new NotManagedConnectionException();
        }

        if (null !== $client = $this->clientProvider->findByAccessToken($accessToken)) {
            $connection->setClient($client);

            return true;
        }

        return false;
    }
}
