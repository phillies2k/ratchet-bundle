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

/**
 * Class Payload
 * @package P2\Bundle\RatchetBundle\WebSocket
 */
class Payload
{
    /**
     * @var string
     */
    protected $event;
    
    /**
     * @var array
     */
    protected $data;

    /**
     * Validates the given json data format. Returns true when the given json format is valid, false otherwise.
     *
     * @param array $json
     *
     * @return boolean
     */
    public static function isValid(array $json)
    {
        if (! isset($json['event'])) {
            return false;
        }

        if (! isset($json['data'])) {
            return false;
        }

        return true;
    }

    /**
     * Decodes the given string input and returns an array of data for this payload.
     * Throws InvalidArgumentException on decoding errors.
     *
     * @param string $msg
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    public static function decode($msg)
    {
        try {
            $data = json_decode($msg, true);

            return $data;
        } catch (\Exception $e) {
            throw new \InvalidArgumentException('Invalid json format');
        }
    }

    /**
     * @return string
     */
    public function encode()
    {
        return json_encode(
            array(
                'event' => $this->getEvent(),
                'data' => $this->getData()
            )
        );
    }

    /**
     * @param string $json
     *
     * @return Payload
     */
    public static function createFromJson($json)
    {
        return static::createFromArray(static::decode($json));
    }

    /**
     * @param array $data
     *
     * @return Payload
     */
    public static function createFromArray($data)
    {
        if ( ! is_array($data)) return null;
        
        if (static::isValid($data)) {

            return new static($data['event'], $data['data']);
        }

        return null;
    }

    /**
     * @param string $event
     * @param mixed $data
     */
    public function __construct($event, $data)
    {
        $this->event = $event;
        $this->data = $data;
    }

    /**
     * Returns the data for this payload.
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getEvent()
    {
        return $this->event;
    }
}
