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
 * Class AnonymousClientProvider
 * @package P2\Bundle\RatchetBundle\WebSocket\Client
 */
class AnonymousClientProvider implements ClientProviderInterface
{
    /**
     * @var AnonymousClient
     */
    protected $clients = array();

    /**
     * Returns a client found by the access token.
     *
     * @param string $accessToken
     *
     * @return ClientInterface
     */
    public function findByAccessToken($accessToken)
    {
        if ($accessToken === '') {
            $client = new AnonymousClient();
            $this->clients[$accessToken] = $client;
        }

        return $this->clients[$accessToken];
    }

    /**
     * Updates the given client in the underlying data layer.
     *
     * @param ClientInterface $client
     * @return void
     */
    public function updateClient(ClientInterface $client)
    {
        $this->clients[$client->getAccessToken()] = $client;
    }
}
