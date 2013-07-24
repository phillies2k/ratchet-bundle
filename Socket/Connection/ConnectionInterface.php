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

use P2\Bundle\RatchetBundle\Socket\ClientInterface;
use P2\Bundle\RatchetBundle\Socket\Payload\EventPayload;

/**
 * Interface ConnectionInterface
 * @package P2\Bundle\RatchetBundle\Socket\Connection
 */
interface ConnectionInterface
{
    /**
     * Returns the resource identifier for this socket connection
     *
     * @return int
     */
    public function getId();

    /**
     * Returns the remote address of this socket connection.
     *
     * @return string
     */
    public function getRemoteAddress();

    /**
     * Returns the client for this connection.
     *
     * @return ClientInterface
     */
    public function getClient();

    /**
     * Sends the given event payload to this socket connection.
     *
     * @param EventPayload $payload
     * @return void
     */
    public function emit(EventPayload $payload);

    /**
     * Emits the given event payload to all managed connections.
     *
     * @param EventPayload $payload
     * @return void
     */
    public function broadcast(EventPayload $payload);
}
