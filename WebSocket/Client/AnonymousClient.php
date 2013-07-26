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
 * Class AnonymousClient
 * @package P2\Bundle\RatchetBundle\WebSocket\Client
 */
class AnonymousClient implements ClientInterface
{
    /**
     * Sets the websocket access token for this client
     *
     * @param string $accessToken
     * @return ClientInterface
     */
    public function setAccessToken($accessToken)
    {
        return $this;
    }

    /**
     * Returns the websocket access token for this client if any, or null.
     *
     * @return null|string
     */
    public function getAccessToken()
    {
        return null;
    }

    /**
     * Returns the array of public client data which will be transferred to the websocket client on successful
     * authentication. The websocket access token for this client should always be returned.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return array('IS_AUTHENTICATED_ANONYMOUSLY');
    }
}
