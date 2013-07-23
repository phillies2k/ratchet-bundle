<?php

namespace P2\Bundle\RatchetBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class P2RatchetExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        if (! isset($config['provider'])) {
            throw new InvalidArgumentException(sprintf('Missing provider config in section: %s', $this->getAlias()));
        }

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $server = $container->getDefinition('p2_ratchet.server');
        $server->setArguments(
            array(
                new Reference('p2_ratchet.connection_manager'),
                $config['port'],
                $config['address']
            )
        );

        $container->setAlias('p2_ratchet.client_provider', $config['provider']);

        $container->setParameter(
            'security.authentication.success_handler.class',
            'P2\Bundle\RatchetBundle\Security\AuthenticationSuccessHandler'
        );
    }
}
