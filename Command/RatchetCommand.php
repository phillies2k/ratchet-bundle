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
     * @var string
     */
    const ARG_ADDRESS = 'address';

    /**
     * @var string
     */
    const ARG_PORT = 'port';

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->addArgument(static::ARG_PORT, InputArgument::OPTIONAL, 'The port to listen on incoming connections')
            ->addArgument(static::ARG_ADDRESS, InputArgument::OPTIONAL, 'The address to listen on')
            ->setDescription('Starts a web socket server')
            ->setHelp('ratchet:start [port] [address]')
            ->setName('ratchet:start');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            if (! $this->getContainer()->has('p2_ratchet.socket.server')) {
                throw new \RuntimeException('Websocket server dic missing');
            }

            /** @var \P2\Bundle\RatchetBundle\Socket\Server $server */
            $server = $this->getContainer()->get('p2_ratchet.socket.server');

            if ($input->hasArgument(static::ARG_ADDRESS)) {
                $server->setAddress($input->getArgument(static::ARG_ADDRESS));
            }

            if ($input->hasArgument(static::ARG_PORT)) {
                $server->setPort($input->getArgument(static::ARG_PORT));
            }

            $output->writeln(
                sprintf(
                    '<info>starting websocket server %s:%s</info>',
                    $server->getAddress(),
                    $server->getPort()
                )
            );

            $server->run();

            return 0;
        } catch (\Exception $e) {
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));

            return -1;
        }
    }
}
