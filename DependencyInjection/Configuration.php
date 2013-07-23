<?php

namespace P2\Bundle\RatchetBundle\DependencyInjection;

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
                ->scalarNode('address')->defaultValue('0.0.0.0')->end()
                ->scalarNode('port')->defaultValue(8080)->end()
            ->end();

        return $treeBuilder;
    }
}
