<?php
/**
 * This file is part of the RatchetBundle project.
 *
 * (c) 2013 Philipp Boes <mostgreedy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace P2\Bundle\RatchetBundle\Socket\Connection;

use P2\Bundle\RatchetBundle\Event\ConnectionEvent;
use P2\Bundle\RatchetBundle\Exception\UnknownConnectionException;
use P2\Bundle\RatchetBundle\Socket\ClientProviderInterface;
use P2\Bundle\RatchetBundle\Socket\Events;
use P2\Bundle\RatchetBundle\Socket\Payload\EventPayload;
use Ratchet\ConnectionInterface as SocketConnection;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Class ConnectionManager
 * @package P2\Bundle\RatchetBundle\Socket\Connection
 */
class ConnectionManager implements ConnectionManagerInterface
{
    /**
     * @var ClientProviderInterface
     */
    protected $clientProvider;

    /**
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @var \SplObjectStorage
     */
    protected $connections;

    /**
     * @param ClientProviderInterface $clientProvider
     * @param EventDispatcher $eventDispatcher
     */
    public function __construct(ClientProviderInterface $clientProvider, EventDispatcher $eventDispatcher)
    {
        $this->clientProvider = $clientProvider;
        $this->eventDispatcher = $eventDispatcher;
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
        return (array) $this->connections;
    }

    /**
     * {@inheritDoc}
     */
    public function addConnection(SocketConnection $socketConnection)
    {
        if (! $this->connections->offsetExists($socketConnection)) {
            $connection = new Connection($this, $socketConnection);

            $this->connections->offsetSet($socketConnection, $connection);

            $this->eventDispatcher->dispatch(Events::SOCKET_OPEN, new ConnectionEvent($connection));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function closeConnection(SocketConnection $socketConnection)
    {
        if ($this->connections->offsetExists($socketConnection)) {
            if (null !== $connection = $this->connections->offsetGet($socketConnection)) {
                $this->eventDispatcher->dispatch(Events::SOCKET_CLOSE, new ConnectionEvent($connection));
            }

            $this->connections->detach($socketConnection);
            $socketConnection->close();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function authenticate(SocketConnection $socketConnection, $accessToken)
    {
        if (! $this->connections->offsetExists($socketConnection)) {
            throw new UnknownConnectionException('Connection is not managed.');
        }

        $connection = $this->connections->offsetGet($socketConnection);

        if (null !== $client = $this->clientProvider->findByAccessToken($accessToken)) {
            $connection->setClient($client);

            $this->eventDispatcher->dispatch(Events::SOCKET_AUTH_SUCCESS, new ConnectionEvent($connection));

            $connection->emit(
                EventPayload::createFromArray(
                    array(
                        'event' => Events::SOCKET_AUTH_SUCCESS,
                        'data' => $client->jsonSerialize()
                    )
                )
            );

            return $connection;
        }

        $this->eventDispatcher->dispatch(Events::SOCKET_AUTH_FAILURE, new ConnectionEvent($connection));

        $connection->emit(
            EventPayload::createFromArray(
                array(
                    'event' => Events::SOCKET_AUTH_FAILURE,
                    'data' => 'Invalid access token'
                )
            )
        );

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }
}
