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

use P2\Bundle\RatchetBundle\WebSocket\Server\Factory;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class SocketServerCommand
 * @package P2\Bundle\RatchetBundle\Command
 */
class SocketServerCommand extends ContainerAwareCommand
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
            ->setName('socket:server:start')
            ->setDescription('Starts a web socket server')
            ->addArgument(static::ARG_PORT, InputArgument::OPTIONAL, 'The port to listen on incoming connections')
            ->addArgument(static::ARG_ADDRESS, InputArgument::OPTIONAL, 'The address to listen on')
            ->setHelp(<<<EOT
<info>app/console socket:server:start</info>

  The basic command starts a new websocket server listening on any connections on port 8080
EOT
            );
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            /** @var \P2\Bundle\RatchetBundle\WebSocket\Server\Factory $factory */
            $factory = $this->getContainer()->get('p2_ratchet.websocket.server_factory');

            if (null !== $address = $input->getArgument(static::ARG_ADDRESS)) {
                $factory->setAddress($address);
            }

            if (null !== $port = $input->getArgument(static::ARG_PORT)) {
                $factory->setPort($port);
            }

            $server = $factory->create();

            $output->writeln(
                sprintf(
                    '<info><comment>Ratchet</comment> - listening on %s:%s</info>',
                    $factory->getAddress(),
                    $factory->getPort()
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
