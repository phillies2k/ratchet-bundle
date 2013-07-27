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
use P2\Bundle\RatchetBundle\WebSocket\ConnectionEvent;
use P2\Bundle\RatchetBundle\WebSocket\Exception\InvalidPayloadException;
use P2\Bundle\RatchetBundle\WebSocket\Exception\NotManagedConnectionException;
use P2\Bundle\RatchetBundle\WebSocket\Exception\InvalidEventCallException;
use P2\Bundle\RatchetBundle\WebSocket\Payload;
use Psr\Log\LoggerInterface;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

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
     * @var EventDispatcher
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
     * @param EventDispatcher $eventDispatcher
     * @param LoggerInterface $logger
     */
    public function __construct(
        ConnectionManagerInterface $connectionManager,
        EventDispatcher $eventDispatcher,
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
     * @param ConnectionInterface $conn
     */
    public function onOpen(ConnectionInterface $conn)
    {
        $this->connectionManager->addConnection($conn);
    }

    /**
     * @param ConnectionInterface $conn
     * @throws \RuntimeException
     */
    public function onClose(ConnectionInterface $conn)
    {
        $this->connectionManager->closeConnection($conn);
        $this->logger->notice('connection closed.');
    }

    /**
     * @param ConnectionInterface $conn
     * @param \Exception $e
     */
    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $this->connectionManager->closeConnection($conn);
        $this->logger->error($e->getMessage());
    }

    /**
     * @param ConnectionInterface $from
     * @param string $msg
     * @throws \Exception
     */
    public function onMessage(ConnectionInterface $from, $msg)
    {
        try {
            if (null === $payload = Payload::createFromJson($msg)) {
                throw new InvalidPayloadException(sprintf('Invalid payload received: "%s"', $msg));
            }

            if (! in_array($payload->getEvent(), $this->allowedEvents)) {
                throw new InvalidEventCallException(sprintf('Unregistered event: "%s".', $payload->getEvent()));
            }

            if (null === $connection = $this->connectionManager->getConnection($from)) {
                throw new NotManagedConnectionException('Unknown Connection');
            }

            $event = $payload->getEvent();

            if ($event === ConnectionEvent::SOCKET_AUTH_REQUEST) {
                if (! $this->connectionManager->authenticate($connection, $payload->getData())) {
                    $this->eventDispatcher->dispatch(
                        ConnectionEvent::SOCKET_AUTH_FAILURE,
                        new ConnectionEvent($connection)
                    );

                    $connection->emit(
                        new Payload(
                            ConnectionEvent::SOCKET_AUTH_FAILURE,
                            'Invalid access token.'
                        )
                    );

                    $this->logger->notice('Client authentication error.');

                    return;
                }

                $response = new Payload(
                    ConnectionEvent::SOCKET_AUTH_SUCCESS,
                    $connection->getClient()->jsonSerialize()
                );

                $this->eventDispatcher->dispatch(
                    ConnectionEvent::SOCKET_AUTH_SUCCESS,
                    new ConnectionEvent($connection)
                );

                $connection->emit($response);

                $this->logger->notice('Client authenticated successfully.');

                return;
            }

            $this->eventDispatcher->dispatch($event, new ConnectionEvent($connection, $payload));
            $this->logger->notice(sprintf('Dispatched event: %s', $event));
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
