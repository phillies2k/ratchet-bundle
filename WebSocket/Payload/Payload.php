<?php
/**
 * This file is part of the RatchetBundle project.
 *
 * (c) 2013 Philipp Boes <mostgreedy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace P2\Bundle\RatchetBundle\WebSocket\Payload;

/**
 * Class Payload
 * @package ${NAMESPACE}
 */
class Payload
{
    protected $data;
    protected $event;

    public function __construct($raw)
    {
        try {
            $data = json_decode($raw, true);
        } catch (\Exception $e) {
            throw new \InvalidArgumentException('Invalid data format');
        }

        if (! isset($data['event'])) {
            throw new \InvalidArgumentException('Invalid data: no event');
        }

        if (! isset($data['data'])) {
            throw new \InvalidArgumentException('Invalid data: no data');
        }

        $this->event = $data['event'];
        $this->data = $data['data'];
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

    public function encode()
    {
        return json_encode(array('event' => $this->event, 'data' => $this->data));
    }
}
