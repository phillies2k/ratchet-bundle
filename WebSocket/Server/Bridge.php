<?php
/**
 * This file is part of the RatchetBundle project.
 *
 * (c) 2013 Philipp Boes <mostgreedy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace P2\Bundle\RatchetBundle\WebSocket\Server;

use P2\Bundle\RatchetBundle\WebSocket\Connection\ConnectionManagerInterface;
use P2\Bundle\RatchetBundle\WebSocket\Connection\ConnectionInterface;
use P2\Bundle\RatchetBundle\WebSocket\ConnectionEvent;
use P2\Bundle\RatchetBundle\WebSocket\Exception\InvalidPayloadException;
use P2\Bundle\RatchetBundle\WebSocket\Exception\NotManagedConnectionException;
use P2\Bundle\RatchetBundle\WebSocket\Exception\InvalidEventCallException;
use P2\Bundle\RatchetBundle\WebSocket\Payload;
use Psr\Log\LoggerInterface;
use Ratchet\ConnectionInterface as SocketConnection;
use Ratchet\MessageComponentInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class Bridge
 * @package P2\Bundle\RatchetBundle\WebSocket\Factory
 */
class Bridge implements MessageComponentInterface
{
    /**
     * @var ConnectionManagerInterface
     */
    protected $connectionManager;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var array
     */
    protected $allowedEvents = array();

    /**
     * @param ConnectionManagerInterface $connectionManager
     * @param EventDispatcherInterface $eventDispatcher
     * @param LoggerInterface $logger
     */
    public function __construct(
        ConnectionManagerInterface $connectionManager,
        EventDispatcherInterface $eventDispatcher,
        LoggerInterface $logger
    ) {
        $this->connectionManager = $connectionManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->logger = $logger;

        $this->allowedEvents = array(
            ConnectionEvent::SOCKET_AUTH_REQUEST
        );
    }

    /**
     * @param SocketConnection $conn
     */
    public function onOpen(SocketConnection $conn)
    {
        $connection = $this->connectionManager->addConnection($conn);

        $this->logger->notice(
            sprintf(
                'New connection <info>#%s</info> (<comment>%s</comment>)',
                $connection->getId(),
                $connection->getRemoteAddress()
            )
        );
    }

    /**
     * @param SocketConnection $conn
     * @throws \RuntimeException
     */
    public function onClose(SocketConnection $conn)
    {
        $connection = $this->connectionManager->closeConnection($conn);

        $this->logger->notice(
            sprintf(
                'Closed connection <info>#%s</info> (<comment>%s</comment>)',
                $connection->getId(),
                $connection->getRemoteAddress()
            )
        );
    }

    /**
     * @param SocketConnection $conn
     * @param \Exception $e
     */
    public function onError(SocketConnection $conn, \Exception $e)
    {
        $this->connectionManager->closeConnection($conn);
        $this->logger->error($e->getMessage());
    }

    /**
     * @param SocketConnection $from
     * @param string $msg
     * @throws \Exception
     */
    public function onMessage(SocketConnection $from, $msg)
    {
        try {
            if (! $this->connectionManager->hasConnection($from)) {
                throw new NotManagedConnectionException('Unknown Connection');
            }

            if (null === $payload = Payload::createFromJson($msg)) {
                throw new InvalidPayloadException(sprintf('Invalid payload received: "%s"', $msg));
            }

            if (! in_array($payload->getEvent(), $this->allowedEvents)) {
                throw new InvalidEventCallException(sprintf('Unregistered event: "%s".', $payload->getEvent()));
            }

            $connection = $this->connectionManager->getConnection($from);
            $this->handle($connection, $payload);

        } catch (InvalidPayloadException $e) {
            $this->logger->debug($e->getMessage());
        } catch (InvalidEventCallException $e) {
            $this->logger->debug($e->getMessage());
        } catch (NotManagedConnectionException $e) {
            $this->logger->warning($e->getMessage());
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new \RuntimeException('An error occurred during server runtime.', 500, $e);
        }
    }

    /**
     * Handles the the given payload received by the given connection.
     *
     * @param ConnectionInterface $connection
     * @param Payload $payload
     */
    protected function handle(ConnectionInterface $connection, Payload $payload)
    {
        switch ($payload->getEvent()) {
            case ConnectionEvent::SOCKET_AUTH_REQUEST:
                $this->handleAuthentication($connection, $payload);
                break;
            default:
                $this->eventDispatcher->dispatch($payload->getEvent(), new ConnectionEvent($connection, $payload));
                $this->logger->notice(sprintf('Dispatched event: %s', $payload->getEvent()));
        }
    }

    /**
     * Handles the connection authentication.
     *
     * @param ConnectionInterface $connection
     * @param Payload $payload
     */
    protected function handleAuthentication(ConnectionInterface $connection, Payload $payload)
    {
        if (! $this->connectionManager->authenticate($connection, $payload->getData())) {
            $connection->emit(new Payload(ConnectionEvent::SOCKET_AUTH_FAILURE, 'Invalid access token.'));

            $this->eventDispatcher->dispatch(ConnectionEvent::SOCKET_AUTH_FAILURE, new ConnectionEvent($connection));

            $this->logger->notice(
                sprintf(
                    'Authentication error <info>#%s</info> (<comment>%s</comment>)',
                    $connection->getId(),
                    $connection->getRemoteAddress()
                )
            );

            return;
        }

        $response = new Payload(ConnectionEvent::SOCKET_AUTH_SUCCESS, $connection->getClient()->jsonSerialize());
        $connection->emit($response);

        $this->eventDispatcher->dispatch(ConnectionEvent::SOCKET_AUTH_SUCCESS, new ConnectionEvent($connection));

        $this->logger->notice(
            sprintf(
                'Authenticated <info>#%s</info> (<comment>%s</comment>)',
                $connection->getId(),
                $connection->getRemoteAddress()
            )
        );
    }

    /**
     * @param array $allowedEvents
     *
     * @return Bridge
     */
    public function setAllowedEvents(array $allowedEvents)
    {
        $this->allowedEvents = array_unique(array_merge($this->allowedEvents, $allowedEvents));

        return $this;
    }
}
