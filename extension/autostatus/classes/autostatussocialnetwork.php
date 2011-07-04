<?php
/**
 * $Id$
 * $HeadURL$
 *
 * SOFTWARE NAME: autostatus
 * SOFTWARE RELEASE: 0.2
 * COPYRIGHT NOTICE: Copyright (C) 2009-2011 Damien POBEL
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

abstract class autostatusSocialNetwork
{
    const TOKEN_SESSION_VAR = 'OAUTH_TOKEN';

    /**
     * Indicates if include path has already been
     * updated for Zend classes loading
     *
     * @static
     * @var boolean
     * @access public
     */
    static $includePathFixed = false;

    /**
     * identifier of the social network
     *
     * @var string
     * @access protected
     */
    protected $identifier = '';

    /**
     * name of the social network
     *
     * @var string
     * @access protected
     */
    protected $name = '';


    /**
     * Set the status message to $message in social network
     * using $login and $password
     *
     * @param string $message
     * @param array $options
     * @abstract
     * @access public
     * @return void
     */
    abstract public function update($message, $options);

    /**
     * Returns the OAuth config for Zend_Oauth_Consumer 
     * 
     * @param string $callbackURI 
     * @return array
     */
    abstract public function oauthConfig( $callbackURI = '' );

    /**
     * Returns true if the social network requires OAuth 
     *
     * @return boolean
     */
    abstract public function requireOauth();

    /**
     * Check if the attribute $name exist
     *
     * @param mixed $name
     * @access public
     * @return void
     */
    public function hasAttribute( $name )
    {
        return in_array( $name, $this->attributes() );
    }

    /**
     * Return an array of the available attribute identifiers
     *
     * @access public
     * @return array
     */
    public function attributes()
    {
        return array( 'identifier', 'name', 'require_oauth' );
    }

    /**
     * Return the attribute $name
     *
     * @param string $name
     * @access public
     * @return string|null
     */
    public function attribute( $name )
    {
        if ( $name === 'identifier' )
        {
            return $this->identifier;
        }
        else if ( $name === 'name' )
        {
            return $this->name;
        }
        else if ( $name === 'require_oauth' )
        {
            return $this->requireOauth();
        }
        else
        {
            eZDebug::writeError( 'Cannot find attribute ' . $name, __METHOD__ );
            return null;
        }
    }

    /**
     * Fetch the social network object associated with the identifier.
     *
     * @param string $identifier
     * @static
     * @access public
     * @return object|null
     */
    static public function fetchByIdentifier( $identifier )
    {
        $className = 'autostatus' . ucfirst( $identifier );
        if ( !class_exists( $className ) )
        {
            eZDebug::writeError( 'Cannot find class ' . $className, __METHOD__ );
            return null;
        }
        return new $className;
    }

    /**
     * Change the include path so that Zend classes
     * can be loaded.
     *
     * @static
     * @access public
     * @return void
     */
    static public function fixIncludePath()
    {
        if ( !self::$includePathFixed )
        {
            $includePath = get_include_path();
            $includePath .= PATH_SEPARATOR . eZExtension::baseDirectory() . '/autostatus/classes';
            set_include_path( $includePath );
            self::$includePathFixed = true;
        }
    }

    /**
     * Returns the maximum message length for the given social network, if any, null otherwise.
     *
     * @access public
     * @return int The maximum message length for the given social network, if any, null otherwise.
     */
    abstract public function getMaxMessageLength();
}


?>
