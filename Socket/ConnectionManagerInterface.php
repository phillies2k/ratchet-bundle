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

use P2\Bundle\RatchetBundle\Exception\UnknownClientException;
use P2\Bundle\RatchetBundle\Exception\UnknownConnectionException;
use Ratchet\ConnectionInterface;

/**
 * Interface ConnectionManagerInterface
 * @package P2\Bundle\RatchetBundle\Socket
 */
interface ConnectionManagerInterface
{
    /**
     * Returns the client for the given connection, or null.
     *
     * @param ConnectionInterface $connection
     *
     * @return ClientInterface|null
     */
    public function getClientForConnection(ConnectionInterface $connection);

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
    public function attachClient(ConnectionInterface $connection, $token);

    /**
     * Removes a managed client connection identified by the given connection.
     *
     * @param ConnectionInterface $connection
     *
     * @return ConnectionManagerInterface
     */
    public function removeConnection(ConnectionInterface $connection);
}
