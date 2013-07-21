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
use P2\Bundle\RatchetBundle\WebSocket\Event\ErrorEvent;
use P2\Bundle\RatchetBundle\WebSocket\Event\MessageEvent;
use P2\Bundle\RatchetBundle\WebSocket\Event\OpenEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Interface ApplicationInterface
 * @package P2\Bundle\RatchetBundle\WebSocket
 */
interface ApplicationInterface extends EventSubscriberInterface
{
    public function onData(MessageEvent $event);

    public function onError(ErrorEvent $event);

    public function onOpen(OpenEvent $event);

    public function onClose(CloseEvent $event);

    public function getName();
}
