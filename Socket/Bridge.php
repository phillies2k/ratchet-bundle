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

use P2\Bundle\RatchetBundle\Socket\Event\CloseEvent;
use P2\Bundle\RatchetBundle\Socket\Event\ConnectionEvent;
use P2\Bundle\RatchetBundle\Socket\Event\ErrorEvent;
use P2\Bundle\RatchetBundle\Socket\Event\MessageEvent;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Class Bridge
 * @package P2\Bundle\RatchetBundle\Socket
 */
class Bridge implements MessageComponentInterface
{
    /**
     * @var string
     */
    const ADDRESS = '0.0.0.0';

    /**
     * @var int
     */
    const PORT = 8080;

    /**
     * @var ConnectionManagerInterface
     */
    protected $connectionManager;

    /**
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @param ConnectionManagerInterface $connectionManager
     * @param EventDispatcher $eventDispatcher
     */
    function __construct(ConnectionManagerInterface $connectionManager, EventDispatcher $eventDispatcher)
    {
        $this->connectionManager = $connectionManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->output = new ConsoleOutput();
    }

    /**
     * When a new connection is opened it will be passed to this method
     * @param  ConnectionInterface $conn The socket/connection that just connected to your application
     * @throws \Exception
     */
    public function onOpen(ConnectionInterface $conn)
    {
        $payload = new Payload(Events::SOCKET_OPEN, 'require_authentication');
        $conn->send($payload->encode());

        $this->eventDispatcher->dispatch(Events::SOCKET_OPEN, new ConnectionEvent($conn));
        $this->output->writeln(sprintf('new connection: <info>#%s</info>', $conn->resourceId));
    }

    /**
     * This is called before or after a socket is closed (depends on how it's closed).  SendMessage to $conn will not result in an error if it has already been closed.
     * @param  ConnectionInterface $conn The socket/connection that is closing/closed
     * @throws \Exception
     */
    function onClose(ConnectionInterface $conn)
    {
        if (null !== $client = $this->connectionManager->getClientForConnection($conn)) {
            $this->connectionManager->removeConnection($conn);
            $conn->close();

            $this->eventDispatcher->dispatch(Events::SOCKET_CLOSE, new CloseEvent($conn, $client));
        }
    }

    /**
     * If there is an error with one of the sockets, or somewhere in the application where an Exception is thrown,
     * the Exception is sent back down the stack, handled by the Server and bubbled back up the application through this method
     * @param  ConnectionInterface $conn
     * @param  \Exception $e
     * @throws \Exception
     */
    function onError(ConnectionInterface $conn, \Exception $e)
    {
        $this->eventDispatcher->dispatch(Events::SOCKET_ERROR, new ErrorEvent($conn, $e));
    }

    /**
     * Triggered when a client sends data through the socket
     * @param  \Ratchet\ConnectionInterface $from The socket/connection that sent the message to your application
     * @param  string $msg  The message received
     * @throws \Exception
     */
    function onMessage(ConnectionInterface $from, $msg)
    {
        try {
            $payload = Payload::createFromJson($msg);

            switch ($payload->getEvent()) {
                case Events::SOCKET_AUTH_REQUEST:
                    $client = $this->connectionManager->attachConnection($from, $payload->getData());

                    $this->eventDispatcher->dispatch(
                        Events::SOCKET_AUTH_SUCCESS,
                        new ConnectionEvent($from, $client)
                    );

                    $response = new Payload(Events::SOCKET_AUTH_SUCCESS, array('success' => true));
                    $from->send($response->encode());

                    break;
                default:
                    $this->eventDispatcher->dispatch($payload->getEvent(), new MessageEvent($from, $payload));
            }
        } catch (\Exception $e) {
        }
    }
}
