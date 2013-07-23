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
     * @var string
     */
    const SOCKET_CLOSE = 'socket.close';

    /**
     * @var string
     */
    const SOCKET_DATA = 'socket.data';

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
    public function __construct(ConnectionManagerInterface $connectionManager, EventDispatcher $eventDispatcher)
    {
        $this->connectionManager = $connectionManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->output = new ConsoleOutput();
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
        $payload = new Payload(static::SOCKET_OPEN, 'require_authentication');
        $conn->send($payload->encode());

        $this->eventDispatcher->dispatch(static::SOCKET_OPEN, new ConnectionEvent($conn));
        $this->log('NEW', sprintf('<info>#%s</info>', $conn->resourceId));
    }

    /**
     * This is called before or after a socket is closed (depends on how it's closed).  SendMessage to $conn will not
     * result in an error if it has already been closed.
     *
     * @param  ConnectionInterface $conn The socket/connection that is closing/closed
     *
     * @throws \Exception
     */
    public function onClose(ConnectionInterface $conn)
    {
        if (null !== $client = $this->connectionManager->getClientForConnection($conn)) {
            $this->connectionManager->removeConnection($conn);

            $this->eventDispatcher->dispatch(static::SOCKET_CLOSE, new CloseEvent($conn, $client));
            $this->log('CLOSE CLIENT', sprintf('<info>#%s</info>', $client->getAccessToken()));
        }

        $conn->close();

        $this->log('CLOSE', sprintf('<info>#%s</info>', $conn->resourceId));
    }

    /**
     * If there is an error with one of the sockets, or somewhere in the application where an Exception is thrown, the
     * Exception is sent back down the stack, handled by the Server and bubbled back up the application through this
     * method.
     *
     * @param  ConnectionInterface $conn
     * @param  \Exception $e
     *
     * @throws \Exception
     */
    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $this->eventDispatcher->dispatch(static::SOCKET_ERROR, new ErrorEvent($conn, $e));
        $this->log('ERROR', $e->getMessage());
    }

    /**
     * Triggered when a client sends data through the socket.
     *
     * @param  \Ratchet\ConnectionInterface $from The socket/connection that sent the message to your application
     * @param  string $msg  The message received
     *
     * @throws \Exception
     */
    public function onMessage(ConnectionInterface $from, $msg)
    {
        try {
            $payload = Payload::createFromJson($msg);

            switch ($payload->getEvent()) {
                case static::SOCKET_AUTH_REQUEST:
                    $client = $this->connectionManager->attachConnection($from, $payload->getData());

                    $this->eventDispatcher->dispatch(
                        static::SOCKET_AUTH_SUCCESS,
                        new ConnectionEvent($from, $client)
                    );

                    $this->log('EVT', sprintf('<info>%s</info> %s', $payload->getEvent(), $payload->getData()));

                    $response = new Payload(static::SOCKET_AUTH_SUCCESS, $client->jsonSerialize());
                    $from->send($response->encode());

                    $this->log('MSG', sprintf('<info>%s</info> %s', $from->resourceId, $response->encode()));

                    break;
                default:
                    $this->eventDispatcher->dispatch($payload->getEvent(), new MessageEvent($from, $payload));
            }
        } catch (\Exception $e) {
        }
    }

    /**
     * Logs to the server.
     *
     * @param $type
     * @param $message
     */
    protected function log($type, $message)
    {
        $timestamp = date('Y.m.d H:i', time());

        if ($type === 'ERROR') {
            $message = '<error>' . $message . '</error>';
        }

        $this->output->writeln(
            sprintf(
                '[<comment>%s</comment>] - %s %s',
                $timestamp,
                $type,
                $message
            )
        );
    }
}
