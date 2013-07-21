<?php
/**
 * This file is part of the RatchetBundle project.
 *
 * (c) 2013 Philipp Boes <mostgreedy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace P2\Bundle\RatchetBundle\WebSocket;

use Ratchet\ConnectionInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class Events
 * @package P2\Bundle\RatchetBundle\WebSocket\Event
 */
class Events
{
    const SOCKET_CLOSE = 'socket.close';
    const SOCKET_DATA = 'socket.data';
    const SOCKET_ERROR = 'socket.error';
    const SOCKET_OPEN = 'socket.open';
}
