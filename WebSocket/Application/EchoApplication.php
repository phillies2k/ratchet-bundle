<?php
/**
 * This file is part of the RatchetBundle project.
 *
 * (c) 2013 Philipp Boes <mostgreedy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace P2\Bundle\RatchetBundle\WebSocket\Application;

use P2\Bundle\RatchetBundle\WebSocket\Client;
use P2\Bundle\RatchetBundle\WebSocket\Payload\Payload;

/**
 * Class EchoApplication
 * @package P2\Bundle\RatchetBundle\WebSocket\Application
 */
class EchoApplication extends Application
{
    public function onMessage(Payload $payload, Client $client)
    {
        foreach ($this->clients as $socketClient) {
            if ($client->getConnection() !== $socketClient->getConnection()) {
                $socketClient->getConnection()->send($payload->encode());
            }
        }
    }

    public function getName()
    {
        return 'echo';
    }
}
