<?php
/**
 * This file is part of the RatchetBundle project.
 *
 * (c) 2013 Philipp Boes <mostgreedy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace P2\Bundle\RatchetBundle\Event;

use P2\Bundle\RatchetBundle\Socket\Connection\ConnectionInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class ConnectionEvent
 * @package P2\Bundle\RatchetBundle\Event
 */
class ConnectionEvent extends Event
{
    /**
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * @param ConnectionInterface $connection
     */
    function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return ConnectionInterface
     */
    public function getConnection()
    {
        return $this->connection;
    }
}
