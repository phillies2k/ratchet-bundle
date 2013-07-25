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

use P2\Bundle\RatchetBundle\Socket\ApplicationInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class ApplicationCompilerPass
 * @package P2\Bundle\RatchetBundle\DependencyInjection\Compiler
 */
class ApplicationCompilerPass implements CompilerPassInterface
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

            if (! $service->hasTag('kernel.event_subscriber')) {
                $service->addTag('kernel.event_subscriber');
            }

            $interface = $container->getParameter('p2_ratchet.application.interface');
            $classname = $container->getParameter(trim($service->getClass(), '%'));
            $reflection = new \ReflectionClass($classname);

            if (! $reflection->implementsInterface($interface)) {

                throw new \InvalidArgumentException(
                    sprintf(
                        'The tagged service definition "%s" must implement the interface: %s',
                        $serviceId,
                        $interface
                    ));
            }

            $eventList = array_keys($classname::getSubscribedEvents());
            $events = array_merge($events, $eventList);
        }

        $container
            ->getDefinition('p2_ratchet.socket.bridge')
            ->addMethodCall('setAllowedEvents', array($events));
    }
}
