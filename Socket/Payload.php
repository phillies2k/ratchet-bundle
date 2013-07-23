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
 * Class Payload
 * @package P2\Bundle\RatchetBundle\Socket
 */
class Payload
{
    /**
     * @var array
     */
    protected $data;

    /**
     * @var string
     */
    protected $event;

    public static function createFromJson($json)
    {
        list($event, $data) = static::decode($json);

        return new static($event, $data);
    }

    public static function decode($json)
    {
        try {
            $data = json_decode($json, true);
        } catch (\Exception $e) {
            throw new \InvalidArgumentException('Invalid json format');
        }

        if (! isset($data['event'])) {
            throw new \InvalidArgumentException('Invalid json data: no event');
        }

        if (! isset($data['data'])) {
            throw new \InvalidArgumentException('Invalid json data: no data');
        }

        return array(
            $data['event'],
            $data['data'],
        );
    }

    /**
     * @param string $event
     * @param mixed $data
     * @throws \InvalidArgumentException
     */
    public function __construct($event, $data = null)
    {
        $this->event = $event;
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return mixed
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @return string
     */
    public function encode()
    {
        return json_encode(array('event' => $this->event, 'data' => $this->data));
    }
}
