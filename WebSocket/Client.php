<?php
/**
 * This file is part of the RatchetBundle project.
 *
 * (c) 2013 Philipp Boes <mostgreedy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace P2\Bundle\RatchetBundle\WebSocket;

use Ratchet\ConnectionInterface;

/**
 * Class Client
 * @package P2\Bundle\RatchetBundle\WebSocket
 */
class Client
{
    /**
     * @var \Ratchet\ConnectionInterface
     */
    protected $connection;


    protected $user;

    /**
     * @param ConnectionInterface $connection
     */
    function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return \Ratchet\ConnectionInterface
     */
    public function getConnection()
    {
        return $this->connection;
    }
}
