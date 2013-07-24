<?php
/**
 * This file is part of the RatchetBundle project.
 *
 * (c) 2013 Philipp Boes <mostgreedy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace P2\Bundle\RatchetBundle\Socket\Payload;

/**
 * Interface PayloadInterface
 * @package P2\Bundle\RatchetBundle\Socket\Payload
 */
interface PayloadInterface
{
    /**
     * Validates the given data format. Returns true when the given data format is valid, false otherwise.
     *
     * @param array $json
     *
     * @return boolean
     */
    public static function isValid(array $json);

    /**
     * Decodes the given string input and returns its data.
     * Throws InvalidArgumentException on decoding errors.
     *
     * @param string $msg
     *
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public static function decode($msg);

    /**
     * Returns the encoded payload as string.
     *
     * @return string
     */
    public function encode();

    /**
     * Returns the data for this payload.
     *
     * @return mixed
     */
    public function getData();
}
