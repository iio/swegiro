<?php
/**
 * This file is part of the swegiro package
 *
 * Copyright (c) 2012-13 Hannes Forsgård
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace iio\swegiro;

use iio\swegiro\Exception\SnifferException;

/**
 * Sniff the layout type of a giro file
 *
 * @author  Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package swegiro
 */
interface SnifferInterface extends LayoutInterface
{
    /**
     * Sniff the layout type of a giro file
     *
     * @param array $lines The file contents
     * 
     * @return integer One of the LayoutInterface flags
     *
     * @throws SnifferException If sniff fails
     */
    public function sniffGiroType(array $lines);
}