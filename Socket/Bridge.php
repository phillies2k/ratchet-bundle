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

use P2\Bundle\RatchetBundle\Event\MessageEvent;
use P2\Bundle\RatchetBundle\Exception\ClientAuthenticationException;
use P2\Bundle\RatchetBundle\Exception\UnknownConnectionException;
use P2\Bundle\RatchetBundle\Socket\Connection\ConnectionManagerInterface;
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
     * @var ConsoleOutput
     */
    protected $output;

    /**
     * @param ConnectionManagerInterface $connectionManager
     */
    public function __construct(ConnectionManagerInterface $connectionManager)
    {
        $this->connectionManager = $connectionManager;
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
                $connection->getClient()->getAccessToken()
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
            $payload = EventPayload::createFromJson($msg);

            switch ($payload->getEvent()) {
                case Events::SOCKET_AUTH_REQUEST:
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
                            Events::SOCKET_AUTH_SUCCESS,
                            $connection->getClient()->jsonSerialize()
                        )
                    );

                    break;
                default:
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
