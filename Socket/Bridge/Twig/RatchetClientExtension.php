<?php
/**
 * This file is part of the RatchetBundle project.
 *
 * (c) 2013 Philipp Boes <mostgreedy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace P2\Bundle\RatchetBundle\Socket\Bridge\Twig;

use P2\Bundle\RatchetBundle\Socket\ClientInterface;

/**
 * Class RatchetClientExtension
 * @package P2\Bundle\RatchetBundle\Socket\Bridge\Twig
 */
class RatchetClientExtension extends \Twig_Extension
{
    /**
     * @var string
     */
    protected static $script = <<<CLIENT_SCRIPT
<script type="text/javascript" src="js/websocket.js"></script>
<script type="text/javascript">
    var p2_ratchet_access_token = '%access_token%';
    Ratchet.debug = %debug%;
</script>
CLIENT_SCRIPT;

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction(
                'p2_ratchet_client',
                array($this, 'getClientScript'),
                array('is_safe' => array('html'))
            )
        );
    }

    /**
     * Returns the rendered client script.
     *
     * @param boolean $debug
     * @param ClientInterface $client
     *
     * @return string
     */
    public function getClientScript($debug = false, ClientInterface $client = null)
    {
        $accessToken = '';

        if ($client instanceof ClientInterface) {
            $accessToken = $client->getAccessToken();
        }

        return strtr(static::$script, array(
            '%access_token%' => $accessToken,
            '%debug%' => $debug
        ));
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'p2_ratchet';
    }
}
