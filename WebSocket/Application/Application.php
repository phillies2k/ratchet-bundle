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
use P2\Bundle\RatchetBundle\WebSocket\Events;

/**
 * Class Application
 * @package P2\Bundle\RatchetBundle\WebSocket\Application
 */
abstract class Application implements ApplicationInterface
{
    public function getName()
    {
        return 'application';
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     *
     * @api
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
}
