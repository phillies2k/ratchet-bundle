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

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * Class P2RatchetExtension
 * @package P2\Bundle\RatchetBundle\DependencyInjection
 */
class P2RatchetExtension extends Extension implements PrependExtensionInterface
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

        $container->setAlias('p2_ratchet.websocket.client_provider', $config['provider']);
        $container
            ->getDefinition('p2_ratchet.websocket.server_factory')
            ->addMethodCall('setAddress', array($config['address']))
            ->addMethodCall('setPort', array($config['port']));
    }

    /**
     * Allow an extension to prepend the extension configurations.
     *
     * @param ContainerBuilder $container
     */
    public function prepend(ContainerBuilder $container)
    {
        $bundles = $container->getParameter('kernel.bundles');

        if (isset($bundles['AsseticBundle'])) {

            $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
            $loader->load('services.yml');

            $scripts = $container->getParameter('p2_ratchet.assetic.assets_js');
            $assetPath = __DIR__ . '/../Resources/assets';

            $inputs = array_map(function($value) use ($assetPath) {
                return $assetPath . '/' . $value;
            }, $scripts);

            $container->prependExtensionConfig(
                'assetic',
                array(
                    'assets' => array(
                        'p2_ratchet_js' => array(
                            'inputs' => $inputs
                        )
                    )
                )
            );
        }
    }
}
