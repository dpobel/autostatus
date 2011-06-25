<?php
/**
 * $Id$
 * $HeadURL$
 *
 * SOFTWARE NAME: autostatus
 * SOFTWARE RELEASE: 0.1
 * COPYRIGHT NOTICE: Copyright (C) 2009 Damien POBEL
 * SOFTWARE LICENSE: GNU General Public License v2.0
 * NOTICE: >
 *   This program is free software; you can redistribute it and/or
 *   modify it under the terms of version 2.0  of the GNU General
 *   Public License as published by the Free Software Foundation.
 *
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of version 2.0 of the GNU General
 *   Public License along with this program; if not, write to the Free
 *   Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 *   MA 02110-1301, USA.
 */

/**
 * Clients class for Identi.ca social network.
 * 
 * @uses autostatusTwitterClient
 * @author Damien Pobel
 */
class autostatusIdenticaClient extends autostatusTwitterClient
{

    public function __construct( $options = null, Zend_Oauth_Consumer $consumer = null )
    {
        parent::__construct( $options, $consumer );
        $this->setUri('http://identi.ca/api');
    }


    /**
     * Overload of the Zend_Service_Twitter method
     * in order to not overwrite the path of the server
     * 
     * @param string $path 
     * @access protected
     * @return void
     */
    protected function _prepare( $path )
    {
        // Get the URI object and configure it
        if ( !$this->_uri instanceof Zend_Uri_Http )
        {
            throw new Zend_Rest_Client_Exception( 'URI object must be set before performing call' );
        }

        $uri = $this->_uri->getUri();

        if ( $path[0] != '/' && $uri[strlen( $uri )-1] != '/' )
        {
            $path = '/' . $path;
        }
        $this->_uri->setPath( $this->_uri->getPath() . $path );

        /**
         * Get the HTTP client and configure it for the endpoint URI.  Do this each time
         * because the Zend_Http_Client instance is shared among all Zend_Service_Abstract subclasses.
         */
        $this->_localHttpClient->resetParameters()->setUri( $this->_uri );
    }

}

?>
