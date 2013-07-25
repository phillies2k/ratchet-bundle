<?php
/**
 * This file is part of the RatchetBundle project.
 *
 * (c) 2013 Philipp Boes <mostgreedy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace P2\Bundle\RatchetBundle\DependencyInjection;

use P2\Bundle\RatchetBundle\Socket\Server;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('p2_ratchet');

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('provider')->cannotBeEmpty()->end()
                ->scalarNode('address')->defaultValue(Server::ADDRESS)->end()
                ->scalarNode('port')->defaultValue(Server::PORT)->end()
            ->end();

        return $treeBuilder;
    }
}
