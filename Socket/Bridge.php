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

use P2\Bundle\RatchetBundle\Socket\Connection\ConnectionManagerInterface;
use P2\Bundle\RatchetBundle\Socket\Event\ConnectionEvent;
use P2\Bundle\RatchetBundle\Socket\Event\MessageEvent;
use P2\Bundle\RatchetBundle\Socket\Exception\ClientAuthenticationException;
use P2\Bundle\RatchetBundle\Socket\Exception\UnknownConnectionException;
use P2\Bundle\RatchetBundle\Socket\Payload\EventPayload;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * Class Bridge
 * @package P2\Bundle\RatchetBundle\Socket
 */
class Bridge implements MessageComponentInterface
{
    /**
     * @var ConnectionManagerInterface
     */
    protected $connectionManager;

    /**
     * @var array
     */
    protected $allowedEvents = array();

    /**
     * @var ConsoleOutput
     */
    protected $output;

    /**
     * @param ConnectionManagerInterface $connectionManager
     */
    public function __construct(ConnectionManagerInterface $connectionManager)
    {
        $this->connectionManager = $connectionManager;
        $this->allowedEvents = array(ConnectionEvent::SOCKET_AUTH_REQUEST);
        $this->output = new ConsoleOutput();
    }

    /**
     * @param ConnectionInterface $conn
     */
    public function onOpen(ConnectionInterface $conn)
    {
        $this->connectionManager->addConnection($conn);
        $this->log('NEW', sprintf('<info>#%d</info>', $conn->resourceId));
    }

    /**
     * @param ConnectionInterface $conn
     * @throws \RuntimeException
     */
    public function onClose(ConnectionInterface $conn)
    {
        if (null === $connection = $this->connectionManager->getConnection($conn)) {
            $conn->close();

            throw new \RuntimeException('Unknown connection');
        }

        $this->connectionManager->closeConnection($conn);

        $this->log(
            'CLOSE',
            sprintf(
                '<info>#%d</info> %s',
                $connection->getId(),
                $connection->getRemoteAddress()
            )
        );
    }

    /**
     * @param ConnectionInterface $conn
     * @param \Exception $e
     */
    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $this->log('ERROR', $e->getMessage());
    }

    /**
     * @param ConnectionInterface $from
     * @param string $msg
     * @throws \Exception
     */
    public function onMessage(ConnectionInterface $from, $msg)
    {
        try {
            if (null === $payload = EventPayload::createFromJson($msg)) {
                $this->log('INFO', sprintf('Invalid request: %s', $msg));

                return;
            }

            switch ($payload->getEvent()) {
                case ConnectionEvent::SOCKET_AUTH_REQUEST:
                    if (false === $connection = $this->connectionManager->authenticate($from, $payload->getData())) {
                        throw new ClientAuthenticationException(
                            sprintf(
                                'Could not find client #%s',
                                $payload->getData()
                            )
                        );
                    }

                    $this->log(
                        'MSG',
                        sprintf(
                            '<info>%s (#%s)</info> %s - %s',
                            $connection->getRemoteAddress(),
                            $connection->getId(),
                            ConnectionEvent::SOCKET_AUTH_SUCCESS,
                            $connection->getClient()->getAccessToken()
                        )
                    );

                    break;
                default:

                    /**
                     * Event handling
                     *
                     * Need to handle list of accepted events (events that event subscribers for socket events subscribe
                     * on) somehow.
                     *
                     * Create SocketApplicationInterface that extends the EventSubscriberInterface to be implemented
                     * from custom socket applications, e.g: "class MyChat implements SocketApplicationInterface {}"
                     *
                     * Add a di compiler pass to hook into tagged socket applications ("p2_ratchet.socket.application"),
                     * adding this list to the bridge service definition on extension loading.
                     *
                     * Also ensure all socket application service definitions are tagged as "kernel.event_subscriber"
                     *
                     * Fix Events in general: Remove the SOCKET_AUTH_SUCCESS and SOCKET_AUTH_FAILURE events from
                     * ConnectionEvent and add them to MessageEvent, cause they are only dispatched to the client.
                     *
                     *
                     */

                    if (! in_array($payload->getEvent(), $this->allowedEvents)) {
                        $this->log('INFO', sprintf('Unregistered event: %s', $payload->getEvent()));

                        return;
                    }

                    $this->connectionManager
                        ->getEventDispatcher()
                        ->dispatch(
                            $payload->getEvent(),
                            new MessageEvent(
                                $this->connectionManager->getConnection($from),
                                $payload
                            )
                        );

                    $this->log('EVT', sprintf('<info>%s</info> - %s', $payload->getEvent(), $payload->encode()));
            }
        } catch (ClientAuthenticationException $e) {
            $this->log('ERR', $e->getMessage());
        } catch (UnknownConnectionException $e) {
            $this->log('ERR', $e->getMessage());
        } catch (\Exception $e) {
            throw $e;
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
