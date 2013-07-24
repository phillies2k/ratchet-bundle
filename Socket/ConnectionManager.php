<?php
/**
 * This file is part of the RatchetBundle project.
 *
 * (c) 2013 Philipp Boes <mostgreedy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace P2\Bundle\RatchetBundle\Socket;

use P2\Bundle\RatchetBundle\Socket\Exception\UnknownClientException;
use P2\Bundle\RatchetBundle\Socket\Exception\UnknownConnectionException;
use Ratchet\ConnectionInterface;

/**
 * Class ConnectionManager
 * @package P2\Bundle\RatchetBundle\Socket
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
     * Attaches a client for the given connection, The client is identified by the given access token.
     *
     * @param ConnectionInterface $connection The connection to use
     * @param string $token The access token
     *
     * @return ClientInterface The attached client
     * @throws UnknownConnectionException When the given connection is not managed.
     * @throws UnknownClientException When no client could be found for the token.
     */
    public function attachClient(ConnectionInterface $connection, $token)
    {
        if (! $this->connections->offsetExists($connection)) {
            throw new UnknownConnectionException();
        }

        if (null === $client = $this->clientProvider->findByAccessToken($token)) {
            throw new UnknownClientException();
        }

        $this->connections->offsetSet($connection, $client);

        return $client;
    }

    /**
     * @param ConnectionInterface $connection
     * @return ConnectionManagerInterface
     */
    public function removeConnection(ConnectionInterface $connection)
    {
        if ($this->connections->offsetExists($connection)) {
            $this->connections->offsetUnset($connection);
        }

        return $this;
    }

    /**
     * @param ConnectionInterface $connection
     * @return ClientInterface|null
     */
    public function getClientForConnection(ConnectionInterface $connection)
    {
        if ($this->connections->offsetExists($connection)) {

            return $this->connections->offsetGet($connection);
        }

        return null;
    }
}
