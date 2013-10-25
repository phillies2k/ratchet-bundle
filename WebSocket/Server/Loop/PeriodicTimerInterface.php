<?php
/**
 * This file is part of the RatchetBundle project.
 *
 * (c) 2013 Philipp Boes <mostgreedy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace P2\Bundle\RatchetBundle\WebSocket\Server\Loop;

/**
 * Interface PeriodicTimerInterface
 * @package P2\Bundle\RatchetBundle\WebSocket\Server\Loop
 */
interface PeriodicTimerInterface 
{
    /**
     * Returns the interval for this timer
     *
     * @return int
     */
    public function getInterval();

    /**
     * Returns the callback.
     *
     * @return callable
     */
    public function getCallback();

    /**
     * Returns a unique name for this timer.
     *
     * @return string
     */
    public function getName();
}
