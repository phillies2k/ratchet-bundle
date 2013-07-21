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

use P2\Bundle\RatchetBundle\WebSocket\Event\CloseEvent;
use P2\Bundle\RatchetBundle\WebSocket\Event\ErrorEvent;
use P2\Bundle\RatchetBundle\WebSocket\Event\MessageEvent;
use P2\Bundle\RatchetBundle\WebSocket\Event\OpenEvent;
use P2\Bundle\RatchetBundle\WebSocket\Payload\Payload;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Class Bridge
 * @package P2\Bundle\RatchetBundle\WebSocket
 */
class Bridge implements MessageComponentInterface
{
    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @param EventDispatcher $eventDispatcher
     */
    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * When a new connection is opened it will be passed to this method
     *
     * @param  ConnectionInterface $conn The socket/connection that just connected to your application
     *
     * @throws \Exception
     */
    public function onOpen(ConnectionInterface $conn)
    {
        $this->eventDispatcher->dispatch(Events::SOCKET_OPEN, new OpenEvent($conn));
        $this->log('OPEN #' . $conn->resourceId);
    }

    /**
     * This is called before or after a socket is closed (depends on how it's closed).
     * SendMessage to $conn will not result in an error if it has already been closed.
     *
     * @param  ConnectionInterface $conn The socket/connection that is closing/closed
     *
     * @throws \Exception
     */
    public function onClose(ConnectionInterface $conn)
    {
        $this->eventDispatcher->dispatch(Events::SOCKET_CLOSE, new CloseEvent($conn));
        $this->log('CLOSE #' . $conn->resourceId);
    }

    /**
     * If there is an error with one of the sockets, or somewhere in the application where an Exception is thrown,
     * the Exception is sent back down the stack, handled by the Server and bubbled back up the application through
     * this method
     *
     * @param  ConnectionInterface $conn
     * @param  \Exception $e
     *
     * @throws \Exception
     */
    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $this->eventDispatcher->dispatch(Events::SOCKET_ERROR, new ErrorEvent($conn, $e));
        $this->log('ERROR #' . $conn->resourceId . ' - ' . $e->getMessage());
    }

    /**
     * Triggered when a client sends data through the socket
     *
     * @param  \Ratchet\ConnectionInterface $from The socket/connection that sent the message to your application
     * @param  string $msg  The message received
     *
     * @throws \Exception
     */
    public function onMessage(ConnectionInterface $from, $msg)
    {
        $this->eventDispatcher->dispatch(Events::SOCKET_DATA, new MessageEvent($from, new Payload($msg)));
        $this->log('MSG #' . $from->resourceId . ' - ' . $msg);
    }

    /**
     * @param $message
     */
    protected function log($message)
    {
        echo sprintf(
            '[%s] %s' . PHP_EOL,
            date('d.m.Y H:i:s', time()),
            $message
        );
    }
}
