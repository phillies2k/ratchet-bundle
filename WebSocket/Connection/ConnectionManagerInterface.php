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

use P2\Bundle\RatchetBundle\WebSocket\Exception\NotManagedConnectionException;
use Ratchet\ConnectionInterface as SocketConnection;

/**
 * Interface ConnectionManagerInterface
 * @package P2\Bundle\RatchetBundle\WebSocket\Connection
 */
interface ConnectionManagerInterface
{
    /**
     * Returns true if the given socket connection is managed by this manager, false otherwise.
     *
     * @param SocketConnection $socketConnection The socket connection to check
     *
     * @return boolean True when the given connection is managed, false otherwise.
     */
    public function hasConnection(SocketConnection $socketConnection);

    /**
     * Returns the connection for the given socket connection.
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
     *
     * @param SocketConnection $socketConnection
     *
     * @return ConnectionInterface
     */
    public function addConnection(SocketConnection $socketConnection);

    /**
     * Closes and removes a managed connection by the given socket connection. Returns the connection that was closed on
     * success or false otherwise,
     *
     * @param SocketConnection $socketConnection
     *
     * @return boolean|ConnectionInterface The connection that was closed, false otherwise.
     */
    public function closeConnection(SocketConnection $socketConnection);

    /**
     * Authenticates a managed connection by the given access token. Throws NotManagedConnectionException when the given
     * connection is not managed by this manager.
     *
     * @param ConnectionInterface $connection The connection to authenticate
     * @param string $accessToken The token used to authenticate this connection
     *
     * @return boolean True on success, false otherwise
     * @throws NotManagedConnectionException
     */
    public function authenticate(ConnectionInterface $connection, $accessToken);
}
