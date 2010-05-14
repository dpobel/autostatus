<?php
/**
 * $Id: autostatustype.php 6 2009-10-26 21:54:37Z dpobel $
 * $HeadURL: http://svn.projects.ez.no/autostatus/trunk/extension/autostatus/eventtypes/event/autostatus/autostatustype.php $
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
 * eZPersistent object implementation used to store
 * updates launched by autostatus extension
 * 
 * @uses eZPersistentObject
 * @author Damien Pobel
 */
class statusUpdateEvent extends eZPersistentObject
{

    static function definition()
    {
        static $definition = array( 'fields' => array( 'id' => array( 'name' => 'ID',
                                                                      'datatype' => 'integer',
                                                                      'default' => 0,
                                                                      'required' => true ),
                                                       'event_id' => array( 'name' => 'Workflow event id',
                                                                            'datatype' => 'integer',
                                                                            'default' => 0,
                                                                            'required' => true ),
                                                       'message' => array( 'name' => 'Status message',
                                                                           'datatype' => 'string',
                                                                           'default' => '',
                                                                           'required' => true ),
                                                       'created' => array( 'name' => 'Created',
                                                                           'datatype' => 'integer',
                                                                           'default' => 0,
                                                                           'required' => true ),
                                                       'modified' => array( 'name' => 'Modified',
                                                                            'datatype' => 'integer',
                                                                            'default' => 0,
                                                                            'required' => true ),
                                                       'error_msg' => array( 'name' => 'Error message',
                                                                             'datatype' => 'string',
                                                                             'default' => '',
                                                                             'required' => true ) ),
                                    'keys' => array( 'id' ),
                                    'increment_key' => 'id',
                                    'class_name' => 'statusUpdateEvent',
                                    'name' => 'statusupdateevent' );
        return $definition;
    }

    /**
     * Create an statusUpdateEvent instance 
     * 
     * @param int $eventID workflow event id
     * @param string $message message used to update status
     * @param string $errorMsg error message
     * @static
     * @access public
     * @return statusUpdateEvent
     */
    static function create( $eventID, $message, $errorMsg )
    {
        $row = array( 'event_id' => $eventID,
                      'created' => time(),
                      'modified' => time(),
                      'message' => $message,
                      'error_msg' => (string) $errorMsg );
        return new statusUpdateEvent( $row );
    }

}



?>
