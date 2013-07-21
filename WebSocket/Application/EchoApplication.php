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

use P2\Bundle\RatchetBundle\WebSocket\Event\CloseEvent;
use P2\Bundle\RatchetBundle\WebSocket\Event\OpenEvent;
use P2\Bundle\RatchetBundle\WebSocket\Event\ErrorEvent;
use P2\Bundle\RatchetBundle\WebSocket\Event\MessageEvent;

/**
 * Class EchoApplication
 * @package P2\Bundle\RatchetBundle\WebSocket\Application
 */
class EchoApplication extends Application
{
    public function onData(MessageEvent $event)
    {
        $event->getConnection()->send($event->getPayload()->encode());
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

    public function getName()
    {
        return 'echo';
    }
}
