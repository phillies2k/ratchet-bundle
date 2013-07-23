<?php
/**
 * This file is part of the RatchetBundle project.
 *
 * (c) 2013 Philipp Boes <mostgreedy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace P2\Bundle\RatchetBundle\Socket\Event;

use Ratchet\ConnectionInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class ErrorEvent
 * @package P2\Bundle\RatchetBundle\Socket\Event
 */
class ErrorEvent extends Event
{
    /**
     * @var \Ratchet\ConnectionInterface
     */
    protected $connection;

    /**
     * @var \Exception
     */
    protected $exception;

    /**
     * @param ConnectionInterface $connection
     * @param \Exception $exception
     */
    public function __construct(ConnectionInterface $connection, \Exception $exception)
    {
        $this->connection = $connection;
        $this->exception = $exception;
    }

    /**
     * @return \Ratchet\ConnectionInterface
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @return \Exception
     */
    public function getException()
    {
        return $this->exception;
    }
}
