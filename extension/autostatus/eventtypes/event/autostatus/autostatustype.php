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
 * Workflow event type to automatically update status
 * in social networks.
 *
 * Informations are stored as followed :
 *  - data_int1 : content class id
 *  - data_int2 : attribute id
 *  - date_int3 : use cronjob
 *  - data_text1 : identifier of the social network
 *  - data_text2 : login on the social network
 *  - data_text3 : password on the social network
 * 
 * @uses eZWorkflowEventType
 */
class autostatusType extends eZWorkflowEventType
{
    const WORKFLOW_TYPE_STRING = 'autostatus';


    function __construct()
    {
        parent::__construct( autostatusType::WORKFLOW_TYPE_STRING,
                             ezi18n( 'kernel/workflow/event', 'Auto status' ) );
        $this->setTriggerTypes( array( 'content' => array( 'publish' => array( 'after' ) ) ) );
    }


    function attributeDecoder( $event, $attr )
    {
        switch( $attr )
        {
            case 'use_cronjob':
            {
                return $event->attribute( 'data_int3' ) != 0;
            }
            case 'class_id':
            {
                return $event->attribute( 'data_int1' );
            }
            case 'class_identifier':
            {
                $classID = $event->attribute( 'data_int1' );
                return eZContentClass::classIdentifierByID( $classID );
            }
            case 'class':
            {
                return eZContentClass::fetch( $event->attribute( 'data_int1' ) );
            }
            case 'attribute_id':
            {
                return $event->attribute( 'data_int2' );
            }
            case 'attribute_identifier':
            {
                $attributeID = $event->attribute( 'data_int2' );
                return eZContentClassAttribute::classAttributeIdentifierByID( $attributeID );
            }
            case 'attribute':
            {
                return eZContentClassAttribute::fetch( $event->attribute( 'data_int2' ) );
            }
            case 'social_network_identifier':
            {
                return $event->attribute( 'data_text1' );
            }
            case 'social_network':
            {
                return autostatusSocialNetwork::fetchByIdentifier( $event->attribute( 'data_text1' ) );
            }
            case 'login':
            {
                return $event->attribute( 'data_text2' );
            }
            case 'password':
            {
                return $event->attribute( 'data_text3' );
            }
        }
        return null;
    }

    function typeFunctionalAttributes()
    {
        return array( 'class_identifier', 'class_id', 'attribute_identifier', 'attribute_id',
                      'class', 'attribute', 'use_cronjob',
                      'social_network_identifier', 'social_network', 'login', 'password' );
    }


    function validateHTTPInput( $http, $base, $event, &$validation )
    {
        $finalState = eZInputValidator::STATE_ACCEPTED;
        if ( !$http->hasPostVariable( 'StoreButton' ) )
        {
            return $finalState;
        }
        $eventID = $event->attribute( 'id' );
        $classIdentifierPostName = 'ClassIdentifier_' . $eventID;
        $attributeIdentifierPostName = 'AttributeIdentifier_' . $eventID;
        $socialNetworkPostName = 'SocialNetwork_' . $eventID;
        $loginPostName = 'Login_' . $eventID;
        $prefix = $event->attribute( 'workflow_type' )->attribute( 'group_name' ) . ' / '
                  . $event->attribute( 'workflow_type' )->attribute( 'name' ) . ' : ';
        $validation['processed'] = true;
        if ( !$http->hasPostVariable( $classIdentifierPostName )
                || !is_numeric( eZContentClass::classIDByIdentifier( $http->postVariable( $classIdentifierPostName ) ) ) )
        {
            $finalState = eZInputValidator::STATE_INVALID;
            $validation['groups'][] = array( 'text' => $prefix . ezi18n( 'kernel/workflow/event', 'Invalid content class' ) );
        }
        else
        {
            $event->setAttribute( 'data_int1', eZContentClass::classIDByIdentifier( $http->postVariable( $classIdentifierPostName ) ) );
        }
        if ( !$http->hasPostVariable( $attributeIdentifierPostName )
                || !is_numeric( eZContentClassAttribute::classAttributeIDByIdentifier( $http->postVariable( $classIdentifierPostName )
                                                                                       . '/' . $http->postVariable( $attributeIdentifierPostName ) ) ) )
        {
            $finalState = eZInputValidator::STATE_INVALID;
            $validation['groups'][] = array( 'text' => $prefix . ezi18n( 'kernel/workflow/event', 'Invalid attribute' ) );
        }
        else
        {
            $event->setAttribute( 'data_int2', eZContentClassAttribute::classAttributeIDByIdentifier( $http->postVariable( $classIdentifierPostName )
                                                                                                      . '/' . $http->postVariable( $attributeIdentifierPostName ) ) );
        }
        if ( !$http->hasPostVariable( $socialNetworkPostName )
                || !is_object( autostatusSocialNetwork::fetchByIdentifier( $http->postVariable( $socialNetworkPostName ) ) ) )
        {
            $finalState = eZInputValidator::STATE_INVALID;
            $validation['groups'][] = array( 'text' => $prefix . ezi18n( 'kernel/workflow/event', 'Invalid social network' ) );
        }
        else
        {
            $event->setAttribute( 'data_text1', $http->postVariable( $socialNetworkPostName ) );
        }
        if ( !$http->hasPostVariable( $loginPostName )
                || $http->postVariable( $loginPostName ) == '' )
        {
            $finalState = eZInputValidator::STATE_INVALID;
            $validation['groups'][] = array( 'text' => $prefix . ezi18n( 'kernel/workflow/event', 'Login cannot be empty' ) );
        }
        else
        {
            $event->setAttribute( 'data_text2', $http->postVariable( $loginPostName ) );
        }
        return $finalState;
    }


    function fetchHTTPInput( $http, $base, $event )
    {
        if ( !$http->hasPostVariable( 'StoreButton' ) )
        {
            return;
        }
        $eventID = $event->attribute( 'id' );
        $classIdentifierPostName = 'ClassIdentifier_' . $eventID;
        $attributeIdentifierPostName = 'AttributeIdentifier_' . $eventID;
        $socialNetworkPostName = 'SocialNetwork_' . $eventID;
        $loginPostName = 'Login_' . $eventID;
        $passwordPostName = 'Password_' . $eventID;
        $useCronjobPostName = 'UseCronjob_' . $eventID;

        $event->setAttribute( 'data_int1', eZContentClass::classIDByIdentifier( $http->postVariable( $classIdentifierPostName ) ) );
        $event->setAttribute( 'data_int2', eZContentClassAttribute::classAttributeIDByIdentifier( $http->postVariable( $classIdentifierPostName )
                                                                                                  . '/' . $http->postVariable( $attributeIdentifierPostName ) ) );
        $event->setAttribute( 'data_int3', intval( $http->hasPostVariable( $useCronjobPostName ) ) );
        $event->setAttribute( 'data_text1', $http->postVariable( $socialNetworkPostName ) );
        $event->setAttribute( 'data_text2', $http->postVariable( $loginPostName ) );
        $event->setAttribute( 'data_text3', $http->postVariable( $passwordPostName ) );
    }


    function execute( $process, $event )
    {
        $parameters = $process->attribute( 'parameter_list' );
        eZDebug::writeDebug( $parameters, __METHOD__ );

        $classIdentifier = $event->attribute( 'class_identifier' );
        $object = eZContentObject::fetch( $parameters['object_id'] );
        if ( !is_object( $object ) )
        {
            eZDebug::writeError( 'Object id ' . $parameters['object_id']
                                              . ' does not exist...', __METHOD__ );
            return eZWorkflowEventType::STATUS_WORKFLOW_CANCELLED;
        }
        if ( $object->attribute( 'class_identifier' ) != $classIdentifier )
        {
            eZDebug::writeDebug( $classIdentifier . ' != '
                                 . $object->attribute( 'class_identifier' ), __METHOD__ );
            return eZWorkflowEventType::STATUS_ACCEPTED;
        }

        $socialNetwork = $event->attribute( 'social_network' );
        if ( !is_object( $socialNetwork ) )
        {
            eZDebug::writeError( 'Cannot load autostatus object', __METHOD__ );
            return eZWorkflowEventType::STATUS_ACCEPTED;
        }
        $dataMap = $object->attribute( 'data_map' );
        $attributeIdentifier = $event->attribute( 'attribute_identifier' );
        if ( !isset( $dataMap[$attributeIdentifier] ) )
        {
            eZDebug::writeError( 'Cannot find ' . $attributeIdentifier . ' attribute', __METHOD__ );
            return eZWorkflowEventType::STATUS_ACCEPTED;
        }
        if ( !$dataMap[$attributeIdentifier]->hasContent() )
        {
            eZDebug::writeDebug( 'Attribute "' . $attributeIdentifier . '" is empty', __METHOD__ );
            return eZWorkflowEventType::STATUS_ACCEPTED;
        }

        if ( $event->attribute( 'use_cronjob' ) && !isset( $parameters['in_cronjob'] ) )
        {
            $message = self::replaceURL( $dataMap[$attributeIdentifier]->attribute( 'content' ), $object );
            $parameters['in_cronjob'] = true;
            $parameters['message'] = $message;
            $process->setParameters( $parameters );
            $process->store();
            return eZWorkflowEventType::STATUS_DEFERRED_TO_CRON_REPEAT;
        }
        else if ( $event->attribute( 'use_cronjob' ) && isset( $parameters['in_cronjob'] ) )
        {
            $message = $parameters['message'];
        }
        else
        {
            $message = self::replaceURL( $dataMap[$attributeIdentifier]->attribute( 'content' ), $object );
        }
        eZDebug::writeDebug( $message, __METHOD__ );

        $login = $event->attribute( 'login' );
        $password = $event->attribute( 'password' );
        $errorMsg = false;
        try
        {
            $ini = eZINI::instance( 'autostatus.ini' );
            if ( $ini->variable( 'AutoStatusSettings', 'Debug' ) === 'disabled' )
            {
                $socialNetwork->update( $message, $login, $password );
            }
            else
            {
                $logFile = $ini->variable( 'AutoStatusSettings', 'LogFile' );
                $logMsg = '[DEBUG] status=' . $message . ' with ' . $login
                            . '@' . $event->attribute( 'social_network_identifier' );
                eZLog::write( $logMsg, $logFile );
            }
        }
        catch( Exception $e )
        {
            $errorMsg = $e->getMessage();
            eZDebug::writeError( 'An error occured when updating status in '
                                 . $socialNetwork->attribute( 'name' ) . ' : '
                                 . $e->getMessage(), 'Auto status workflow' );
        }
        $statusEvent = statusUpdateEvent::create( $event->attribute( 'id' ), $message, $errorMsg );
        $statusEvent->store();
        return eZWorkflowEventType::STATUS_ACCEPTED;
    }

    static function replaceURL( $message, $contentObject )
    {
        if ( strpos( $message, '%url' ) !== false )
        {
            $node = $contentObject->attribute( 'main_node' );
            $nodeURL = $node->attribute( 'url_alias' );
            eZURI::transformURI( $nodeURL, false, 'full' );
            $message = str_replace( '%url', $nodeURL, $message );
        }
        if ( strpos( $message, '%title' ) !== false )
        {
            $message = str_replace( '%title', $contentObject->attribute( 'name' ), $message );
        }
        return $message;
    }

}

eZWorkflowEventType::registerEventType( autostatusType::WORKFLOW_TYPE_STRING, 'autostatusType' );

?>
