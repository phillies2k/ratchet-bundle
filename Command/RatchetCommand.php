<?php
/**
 * This file is part of the RatchetBundle project.
 *
 * (c) 2013 Philipp Boes <mostgreedy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace P2\Bundle\RatchetBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class RatchetCommand
 * @package P2\Bundle\WebsocketBundle\Command
 */
class RatchetCommand extends ContainerAwareCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('Starts a web socket server')
            ->setHelp('ratchet:start')
            ->setName('ratchet:start');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            if (! $this->getContainer()->has('p2_ratchet.server')) {
                throw new \RuntimeException('Websocket server dic missing');
            }

            /** @var \Ratchet\Server\IoServer $server */
            $server = $this->getContainer()->get('p2_ratchet.server');

            $output->writeln('<info>server starting</info>');

            $server->run();

            return 0;
        } catch (\Exception $e) {
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));

            return -1;
        }
    }
}
