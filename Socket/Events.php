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

/**
 * Class Events
 * @package P2\Bundle\RatchetBundle\Socket
 */
class Events
{
    /**
     * @var string
     */
    const SOCKET_CLOSE = 'socket.close';

    /**
     * @var string
     */
    const SOCKET_MESSAGE = 'socket.message';

    /**
     * @var string
     */
    const SOCKET_ERROR = 'socket.error';

    /**
     * @var string
     */
    const SOCKET_OPEN = 'socket.open';

    /**
     * @var string
     */
    const SOCKET_AUTH_REQUEST = 'socket.auth.request';

    /**
     * @var string
     */
    const SOCKET_AUTH_SUCCESS = 'socket.auth.success';

    /**
     * @var string
     */
    const SOCKET_AUTH_FAILURE = 'socket.auth.failure';
}
