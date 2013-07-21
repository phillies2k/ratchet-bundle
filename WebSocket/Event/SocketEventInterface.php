<?php
/**
 * This file is part of the RatchetBundle project.
 *
 * (c) 2013 Philipp Boes <mostgreedy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace P2\Bundle\RatchetBundle\WebSocket\Event;

/**
 * Interface SocketEventInterface
 * @package P2\Bundle\RatchetBundle\WebSocket\Event
 */
interface SocketEventInterface 
{
    /**
     * @return \Ratchet\ConnectionInterface
     */
    public function getConnection();

    /**
     * @return string
     */
    public function getName();
}
