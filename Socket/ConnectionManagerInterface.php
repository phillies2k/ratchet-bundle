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
 * Interface ConnectionManagerInterface
 * @package P2\Bundle\RatchetBundle\Socket
 */
interface ConnectionManagerInterface 
{
    /**
     * @param ConnectionInterface $connection
     * @return ConnectionManagerInterface
     */
    public function removeConnection(ConnectionInterface $connection);

    /**
     * @param ConnectionInterface $connection
     * @param string $token
     * @return ClientInterface
     */
    public function attachConnection(ConnectionInterface $connection, $token);

    /**
     * @param ConnectionInterface $connection
     * @return ClientInterface|null
     */
    public function getClientForConnection(ConnectionInterface $connection);
}
