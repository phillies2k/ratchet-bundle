<?php
/**
 * This file is part of the RatchetBundle project.
 *
 * (c) 2013 Philipp Boes <mostgreedy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace P2\Bundle\RatchetBundle\WebSocket\Application;

use Doctrine\Common\Util\Inflector;
use P2\Bundle\RatchetBundle\WebSocket\Client;
use P2\Bundle\RatchetBundle\WebSocket\Event\CloseEvent;
use P2\Bundle\RatchetBundle\WebSocket\Event\ErrorEvent;
use P2\Bundle\RatchetBundle\WebSocket\Event\MessageEvent;
use P2\Bundle\RatchetBundle\WebSocket\Event\OpenEvent;
use P2\Bundle\RatchetBundle\WebSocket\Events;
use Ratchet\ConnectionInterface;

/**
 *
 * echo.error
 *
 * Class Application
 * @package P2\Bundle\RatchetBundle\WebSocket\Application
 */
abstract class Application implements ApplicationInterface
{
    /**
     * @var \SplObjectStorage|Client[]
     */
    protected $clients = array();

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            Events::SOCKET_DATA => 'onData',
            Events::SOCKET_CLOSE => 'onClose',
            Events::SOCKET_OPEN => 'onOpen',
            Events::SOCKET_ERROR => 'onError',
        );
    }

    public function __construct()
    {
        $this->clients = new \SplObjectStorage();
    }

    public function getClient(ConnectionInterface $connection)
    {
        foreach ($this->clients as $client) {
            if ($client->getConnection() === $connection) {
                return $client;
            }
        }

        return null;
    }

    public function onData(MessageEvent $event)
    {
        list($namespace, $name) = explode('.', $event->getPayload()->getEvent());

        if ($namespace === $this->getName()) {
            $method = 'on' . Inflector::classify(Inflector::tableize($name));

            if (method_exists($this, $method)) {
                call_user_func_array(
                    array($this, $method),
                    array(
                        $event->getPayload(),
                        $this->getClient($event->getConnection())
                    )
                );
            }
        }
    }

    public function onError(ErrorEvent $event)
    {
        $event->getConnection()->close();
    }

    public function onOpen(OpenEvent $event)
    {
        $client = new Client($event->getConnection());
        $this->clients->attach($client);
    }

    public function onClose(CloseEvent $event)
    {
        if (null !== $client = $this->getClient($event->getConnection())) {
            $this->clients->detach($client);
        }
    }
}
