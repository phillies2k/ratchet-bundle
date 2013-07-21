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
use P2\Bundle\RatchetBundle\WebSocket\Event\CloseEvent;
use P2\Bundle\RatchetBundle\WebSocket\Event\ErrorEvent;
use P2\Bundle\RatchetBundle\WebSocket\Event\MessageEvent;
use P2\Bundle\RatchetBundle\WebSocket\Event\OpenEvent;
use P2\Bundle\RatchetBundle\WebSocket\Events;

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

    public function getName()
    {
        return 'app';
    }

    public function onData(MessageEvent $event)
    {
        list($namespace, $name) = explode('.', $event->getName());

        if ($namespace === $this->getName()) {
            $method = 'on' . Inflector::classify(Inflector::tableize($name));
            // onSendMessage(ConnectionInterface $connection, Payload $payload);
            if (method_exists($this, $method)) {
                call_user_func_array(array($this, $method), array($event->getPayload()));
            }
        }
    }

    public function onError(ErrorEvent $event)
    {
        $event->getConnection()->send($event->getException()->getMessage());
    }

    public function onOpen(OpenEvent $event)
    {
        $event->getConnection()->send('connection established');
    }

    public function onClose(CloseEvent $event)
    {
        $event->getConnection()->send('connection closed');
    }
}
