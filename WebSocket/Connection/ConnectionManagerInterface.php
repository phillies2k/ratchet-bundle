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

use Ratchet\ConnectionInterface as SocketConnection;

/**
 * Interface ConnectionManagerInterface
 * @package P2\Bundle\RatchetBundle\WebSocket\Connection
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
     *
     * @param SocketConnection $socketConnection
     *
     * @return ConnectionManagerInterface
     */
    public function addConnection(SocketConnection $socketConnection);

    /**
     * Close and remove a managed client connection identified by the given connection.
     *
     * @param SocketConnection $socketConnection
     *
     * @return ConnectionManagerInterface
     */
    public function closeConnection(SocketConnection $socketConnection);

    /**
     * Authenticates a managed socket connection by the given token. Returns the created client connection on success,
     * false otherwise. Throws NotManagedConnectionException when the given connection is not managed by this manager.
     *
     * @param ConnectionInterface $connection
     * @param string $accessToken
     *
     * @return boolean
     */
    public function authenticate(ConnectionInterface $connection, $accessToken);
}
