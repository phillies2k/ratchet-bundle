<?php
/**
 * This file is part of the RatchetBundle project.
 *
 * (c) 2013 Philipp Boes <mostgreedy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace P2\Bundle\RatchetBundle\WebSocket\Server;

use P2\Bundle\RatchetBundle\WebSocket\Exception\TimerAlreadyAddedException;
use P2\Bundle\RatchetBundle\WebSocket\Server\Loop\PeriodicTimerInterface;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;

/**
 * Class Factory
 * @package P2\Bundle\RatchetBundle\WebSocket\Factory
 */
class Factory
{
    /**
     * @var string
     */
    const ADDRESS = '0.0.0.0';

    /**
     * @var int
     */
    const PORT = 8080;

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
     * @var PeriodicTimerInterface[]
     */
    protected $periodicTimers;

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

        $this->periodicTimers = array();
    }

    /**
     * Adds a periodic timer to the loop. Throws TimerAlreadyAddedException when the timer was already added to the
     * event loop.
     *
     * @param PeriodicTimerInterface $periodicTimer
     *
     * @return Factory
     * @throws \P2\Bundle\RatchetBundle\WebSocket\Exception\TimerAlreadyAddedException
     */
    public function addPeriodicTimer(PeriodicTimerInterface $periodicTimer)
    {
        if (array_key_exists($periodicTimer->getName(), $this->periodicTimers)) {
            throw new TimerAlreadyAddedException();
        }

        $this->periodicTimers[$periodicTimer->getName()] = $periodicTimer;

        return $this;
    }

    public function create()
    {
        $server = IoServer::factory(
            new WsServer($this->bridge),
            $this->getPort(),
            $this->getAddress()
        );

        $this->configure($server);

        return $server;
    }

    /**
     * Configures the io server
     *
     * @param IoServer $server
     */
    protected function configure(IoServer $server)
    {
        foreach ($this->periodicTimers as $periodicTimer) {
            $server->loop->addPeriodicTimer($periodicTimer->getInterval(), $periodicTimer->getCallback());
        }
    }

    /**
     * @param string $address
     *
     * @return Factory
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
     * @return Factory
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


