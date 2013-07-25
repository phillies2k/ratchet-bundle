<?php
/**
 * This file is part of the RatchetBundle project.
 *
 * (c) 2013 Philipp Boes <mostgreedy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace P2\Bundle\RatchetBundle\Socket;

use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;

/**
 * Class Server
 * @package P2\Bundle\RatchetBundle\Socket
 */
class Server
{
    /**
     * @var string
     */
    const ADDRESS = '0.0.0.0';

    /**
     * @var int
     */
    const PORT = 80;

    /**
     * @var string
     */
    protected $address;

    /**
     * @var int
     */
    protected $port;

    /**
     * @var Bridge
     */
    protected $bridge;

    /**
     * @param Bridge $bridge
     * @param int $port
     * @param string $address
     */
    public function __construct(Bridge $bridge, $port = self::PORT, $address = self::ADDRESS)
    {
        $this->bridge = $bridge;
        $this->port = $port;
        $this->address = $address;
    }

    public function run()
    {
        $server = IoServer::factory(
            new WsServer($this->bridge),
            $this->getPort(),
            $this->getAddress()
        );

        $server->run();
    }

    /**
     * @param string $address
     *
     * @return Server
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param int $port
     *
     * @return Server
     */
    public function setPort($port)
    {
        $this->port = $port;

        return $this;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }
}


