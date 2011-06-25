<?php
/**
 * $Id: autostatustwitter.php 8 2009-10-27 19:24:15Z dpobel $
 * $HeadURL: http://svn.projects.ez.no/autostatus/trunk/extension/autostatus/classes/autostatustwitter.php $
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

class autostatusAlwayserror extends autostatusSocialNetwork
{

    public function __construct()
    {
        $this->identifier = 'alwayserror';
        $this->name = 'AlwaysError';
    }


    public function update( $message, $options )
    {
        throw new Exception( 'ALWAYS ERROR' );
    }

    public function oauthConfig( $callbackURI = '' )
    {
        throw new BadMethodException( 'OAuth is not supported' );
    }

    public function requireOauth()
    {
        return false;
    }


}


?>
