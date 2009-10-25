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

abstract class autostatusSocialNetwork
{

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

    abstract public function update($message, $login, $password);

    public function hasAttribute( $name )
    {
        return in_array( $name, array( 'identifier', 'name' ) );
    }

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
        else
        {
            eZDebug::writeError( 'Cannot find attribute ' . $name, __METHOD__ );
            return null;
        }
    }

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

}


?>
