<?php

namespace P2\Bundle\RatchetBundle\DependencyInjection;

use P2\Bundle\RatchetBundle\Socket\Bridge;
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
                ->scalarNode('address')->defaultValue(Bridge::ADDRESS)->end()
                ->scalarNode('port')->defaultValue(Bridge::PORT)->end()
            ->end();

        return $treeBuilder;
    }
}
