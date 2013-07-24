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

/**
 * Interface ClientInterface
 * @package P2\Bundle\RatchetBundle\Socket
 */
interface ClientInterface
{
    /**
     * Sets the websocket access token for this client
     *
     * @param string $accessToken
     * @return ClientInterface
     */
    public function setAccessToken($accessToken);

    /**
     * Returns the websocket access token for this client if any, or null.
     *
     * @return null|string
     */
    public function getAccessToken();

    /**
     * Returns the array of public client data which will be transferred to the websocket client on successful
     * authentication. The websocket access token for this client should always be returned.
     *
     * @return array
     */
    public function jsonSerialize();
}
