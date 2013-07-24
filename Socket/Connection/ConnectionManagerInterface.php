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

use P2\Bundle\RatchetBundle\Exception\UnknownConnectionException;
use Ratchet\ConnectionInterface as SocketConnection;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Interface ConnectionManagerInterface
 * @package P2\Bundle\RatchetBundle\Socket\Connection
 */
interface ConnectionManagerInterface
{
    /**
     * Returns the client connection for the given connection.
     *
     * @param SocketConnection $socketConnection
     *
     * @return ConnectionInterface
     */
    public function getConnection(SocketConnection $socketConnection);

    /**
     * Returns all managed connections.
     *
     * @return ConnectionInterface[]
     */
    public function getConnections();

    /**
     * Registers the given socket connection if not managed already.
     * Fires SOCKET_OPEN event on success.
     *
     * @param SocketConnection $socketConnection
     *
     * @return ConnectionManagerInterface
     */
    public function addConnection(SocketConnection $socketConnection);

    /**
     * Close and remove a managed client connection identified by the given connection.
     * Fires SOCKET_CLOSE event on success.
     *
     * @param SocketConnection $socketConnection
     *
     * @return ConnectionManagerInterface
     */
    public function closeConnection(SocketConnection $socketConnection);

    /**
     * Authenticates a managed socket connection by the given token. Returns the created client connection on success,
     * false otherwise. Fires SOCKET_AUTH_SUCCESS event on successful authentication, SOCKET_AUTH_FAILURE on error.
     * Throws UnknownConnectionException when the given connection is not managed by this connection manager.
     *
     * @param SocketConnection $socketConnection
     * @param string $accessToken
     *
     * @return boolean|ConnectionInterface
     * @throws UnknownConnectionException
     */
    public function authenticate(SocketConnection $socketConnection, $accessToken);

    /**
     * Returns the event dispatcher this manager uses to fire socket events.
     *
     * @return EventDispatcher
     */
    public function getEventDispatcher();
}
