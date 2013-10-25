<?php
/**
 * This file is part of the RatchetBundle project.
 *
 * (c) 2013 Philipp Boes <mostgreedy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace P2\Bundle\RatchetBundle;

use P2\Bundle\RatchetBundle\DependencyInjection\Compiler\AddApplicationPass;
use P2\Bundle\RatchetBundle\DependencyInjection\Compiler\AddPeriodicTimerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class P2RatchetBundle
 * @package P2\Bundle\RatchetBundle
 */
class P2RatchetBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new AddApplicationPass());
        $container->addCompilerPass(new AddPeriodicTimerPass());
    }
}
