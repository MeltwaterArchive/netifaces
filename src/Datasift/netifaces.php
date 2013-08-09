<?php
/**
 * Copyright (c) 2013-present Mediasift Ltd
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the names of the copyright holders nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category  Libraries
 * @package   netifaces
 * @author    Michael Heap <michael.heap@datasift.com>
 * @copyright 2013-present Mediasift Ltd www.datasift.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://github.com/datasift/netifaces
 */

namespace Datasift;

use Datasift\netifaces\NetifacesException;

/**
 * netifaces
 *
 * @author Michael Heap <michael.heap@datasift.com>
 */
class netifaces {

    private $os;
    private $parser;

    /**
     * __construct
     *
     * @param \Datasift\Os\OsInterface $os os
     * @param \Datasift\IfconfigParser\Base $parser parser
     *
     * @return void
     */
    public function __construct(\Datasift\Os\OsInterface $os, \Datasift\IfconfigParser\Base $parser){
        $this->os = $os;
        $this->parser = $parser;
    }

    /**
     * listAdapters
     *
     *
     * @return array List of available adapters
     */
    public function listAdapters(){

        $adapters = array();

        // Loop through all of our interfaces, extracting only
        // the interface name
        foreach ($this->getParsedIfconfig() as $r){
            $adapters[] = $r['interface'];
        }

        return $adapters;
    }

    /**
     * getIpAddress
     *
     * @param string $interface Interface to get the IP address of
     *
     * @return string
     * @throws \Datasift\netifaces\Exception
     */
    public function getIpAddress($interface = null){

        // Make sure we have an interface to look for
        if (is_null($interface)){
            throw new NetifacesException("No interface supplied when asking for IP address");
        }

        // Search through our adapters
        foreach ($this->getParsedIfconfig() as $r){
            // If this is the interface we're looking for
            if ($r['interface'] == $interface){
                // If we know about the adapter but it doesn't have an IP
                // address, fall through
                if (!isset($r['ip_address'])){
                    break;
                }

                return $r['ip_address'];
            }
        }

        throw new NetifacesException("Could not retrieve IP address for '".$interface."'");

    }

    /**
     * getParsedIfconfig
     *
     * Helper method for the common task of getting ifconfig
     * output and parsing it
     *
     * @return array
     */
    private function getParsedIfconfig(){
        $ifConfig = $this->os->runCommand("ifconfig");
        $res = $this->parser->parse($ifConfig);

        return $res;
    }
}
