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

use P2\Bundle\RatchetBundle\WebSocket\Client\ClientInterface;
use P2\Bundle\RatchetBundle\WebSocket\Payload;

/**
 * Interface ConnectionInterface
 * @package P2\Bundle\RatchetBundle\WebSocket\Connection
 */
interface ConnectionInterface
{
    /**
     * Returns the resource identifier for this connection
     *
     * @return int
     */
    public function getId();

    /**
     * Returns the remote address of this connection.
     *
     * @return string
     */
    public function getRemoteAddress();

    /**
     * Sets the client for this connection.
     *
     * @param ClientInterface $client
     *
     * @return ConnectionInterface
     */
    public function setClient(ClientInterface $client);

    /**
     * Returns the client for this connection.
     *
     * @return ClientInterface
     */
    public function getClient();

    /**
     * Sends the given payload to this connection.
     *
     * @param Payload $payload
     * @return void
     */
    public function emit(Payload $payload);

    /**
     * Emits the given payload to all managed connections.
     *
     * @param Payload $payload
     * @return void
     */
    public function broadcast(Payload $payload);
}
