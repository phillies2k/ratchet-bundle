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

use Doctrine\Common\Persistence\ObjectManager;

/**
 * Interface ClientProviderInterface
 * @package P2\Bundle\RatchetBundle\Socket
 */
interface ClientProviderInterface 
{
    /**
     * Returns a client found by the access token.
     *
     * @param string $accessToken
     *
     * @return ClientInterface
     */
    public function findByAccessToken($accessToken);

    /**
     * Returns the object manager this client provider uses to persist changes made to the client.
     *
     * @return ObjectManager
     */
    public function getManager();
}
