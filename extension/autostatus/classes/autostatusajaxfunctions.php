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

class autostatusAjaxFunctions extends ezjscServerFunctions
{

    /**
     * Loads the template to display the auth status in the social network 
     * 
     * @param array $args array( 0 => socialNetworkIdentifier, 1 => eventID )
     * @return string
     */
    public static function auth( $args )
    {
        if ( !isset( $args[0] ) || !isset( $args[1] ) )
        {
            eZDebug::writeError( 'Invalid parameters', __METHOD__ );
            return '';
        }
        $socialNetworkIdentifier = $args[0];
        $eventID = $args[1];
        $network = autostatusSocialNetwork::fetchByIdentifier( $socialNetworkIdentifier );
        if ( !$network instanceof autostatusSocialNetwork )
        {
            eZDebug::writeError( 'Unable to load the social network', __METHOD__ );
            return '';
        }
        $event = eZWorkflowEvent::fetch( $eventID );
        if ( !$event instanceof eZWorkflowEvent )
        {
            eZDebug::writeError( 'Unable to load the workflow event', __METHOD__ );
            return '';
        }
        $tpl = eZTemplate::factory();
        $tpl->setVariable( 'event', $event );
        $tpl->setVariable( 'network', $network );
        return $tpl->fetch( 'design:autostatus/ajax/auth.tpl' );
    }

}


?>
