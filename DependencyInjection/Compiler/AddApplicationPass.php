<?php
/**
 * This file is part of the RatchetBundle project.
 *
 * (c) 2013 Philipp Boes <mostgreedy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace P2\Bundle\RatchetBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class AddApplicationPass
 * @package P2\Bundle\RatchetBundle\DependencyInjection\Compiler
 */
class AddApplicationPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     *
     * @throws \InvalidArgumentException
     */
    public function process(ContainerBuilder $container)
    {
        $taggedServiceIds = $container->findTaggedServiceIds('p2_ratchet.application');
        $events = array();

        foreach ($taggedServiceIds as $serviceId => $attributes) {
            $service = $container->getDefinition($serviceId);

            /** @var \P2\Bundle\RatchetBundle\WebSocket\ApplicationInterface $classname */
            $classname = $container->getParameterBag()->resolveValue($service->getClass());

            $eventList = array_keys($classname::getSubscribedEvents());
            $events = array_merge($events, $eventList);
        }

        $container
            ->getDefinition('p2_ratchet.websocket.server_bridge')
            ->addMethodCall('setAllowedEvents', array($events));
    }
}
