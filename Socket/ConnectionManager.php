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
     * @param ConnectionInterface $connection
     * @param string $token
     * @return ClientInterface
     * @throws \InvalidArgumentException
     */
    public function attachConnection(ConnectionInterface $connection, $token)
    {
        if (! $this->connections->offsetExists($connection)) {
            if (null === $client = $this->clientProvider->findByAccessToken($token)) {
                throw new \InvalidArgumentException();
            }

            $this->connections->offsetSet($connection, $client);
        }

        return $this->connections->offsetGet($connection);
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
