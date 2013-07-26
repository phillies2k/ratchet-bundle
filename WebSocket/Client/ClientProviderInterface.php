<?php
/**
 * This file is part of the RatchetBundle project.
 *
 * (c) 2013 Philipp Boes <mostgreedy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace P2\Bundle\RatchetBundle\WebSocket\Client;

/**
 * Interface ClientProviderInterface
 * @package P2\Bundle\RatchetBundle\WebSocket\Client
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
     * Updates the given client in the underlying data layer.
     *
     * @param ClientInterface $client
     * @return void
     */
    public function updateClient(ClientInterface $client);
}
