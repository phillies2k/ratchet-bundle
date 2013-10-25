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
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class AddPeriodicTimerPass
 * @package P2\Bundle\RatchetBundle\DependencyInjection\Compiler
 */
class AddPeriodicTimerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     *
     * @throws \InvalidArgumentException
     */
    public function process(ContainerBuilder $container)
    {
        $taggedServiceIds = $container->findTaggedServiceIds('p2_ratchet.periodic_timer');
        $factoryService = $container->getDefinition('p2_ratchet.websocket.server_factory');

        foreach ($taggedServiceIds as $serviceId => $attributes) {
            $factoryService->addMethodCall('addPeriodicTimer', array(new Reference($serviceId)));
        }
    }
}
