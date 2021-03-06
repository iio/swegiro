<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace ledgr\autogiro;

use ledgr\giro\SnifferInterface;
use ledgr\giro\Exception\SnifferException;

/**
 * Sniff the layout type of a autogiro file
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class Sniffer implements SnifferInterface, Layouts
{
    /**
     * @var array Identifiers used when sniffing
     */
    private static $identifiers = array(
        self::LAYOUT_AG_D => array(
            'first' => '/BET\. SPEC & STOPP TK/',
            'last'  => '//'
        ),
        self::LAYOUT_AG_E => array(
            'first' => '/AUTOGIRO.*AG-MEDAVI/',
            'last'  => '//'
        ),
        self::LAYOUT_AG_OLD_E => array(
            'first' => '/AG-MEDAVI/',
            'last'  => '//'
        ),
        self::LAYOUT_AG_OLD_F => array(
            'first' => '/FELLISTA REG\.KONTRL/',
            'last'  => '//'
        ),
        self::LAYOUT_AG_F => array(
            'first' => '/AVVISADE BET UPPDR/',
            'last'  => '//'
        ),
        self::LAYOUT_AG_OLD_G => array(
            'first' => '/MAK\/ÄNDRINGSLISTA/',
            'last'  => '//'
        ),
        self::LAYOUT_AG_G => array(
            'first' => '/MAKULERING\/ÄNDRING/',
            'last'  => '//'
        ),
        self::LAYOUT_AG_H => array(
            'first' => '/AG-EMEDGIV/',
            'last'  => '//'
        ),
        self::LAYOUT_AG_I => array(
            'first' => '/BEVAKNINGSREG/',
            'last'  => '//'
        ),
        self::LAYOUT_AG_OLD_D => array(
            'first' => '/AUTOGIRO/',
            'last'  => '/^09/'
        ),
        self::LAYOUT_AG_ABC => array(
            'first' => '/AUTOGIRO/',
            'last'  => '//'
        ),
        self::LAYOUT_AG_J => array(
            'first' => '/^([0-9]|\s)+$/',
            'last'  => '/^([0-9]|\s)+$/'
        ),
        self::LAYOUT_AG_BGMAX => array(
            'first' => '/BGMAX/',
            'last'  => '//'
        )
    );

    /**
     * Sniff the layout type of a autogiro file
     *
     * NOTE: The response is a *guess* and should not be depended upon.
     * 
     * @param  array  $lines The file contents
     * @return scalar Layout identifier
     */
    public function sniffGiroType(array $lines)
    {
        $firstLine = current($lines);
        end($lines);
        $lastLine = current($lines);

        foreach (self::$identifiers as $flag => $regexes) {
            $firstLineRegex = utf8_decode($regexes['first']);
            $lastLineRegex = utf8_decode($regexes['last']);
            
            if (preg_match($firstLineRegex, $firstLine)
                && preg_match($lastLineRegex, $lastLine)
            ) {
                return $flag;   // Match found
            }
        }

        $msg = _('Error sniffing file type: no matching type found.');
        throw new SnifferException($msg);
    }
}
