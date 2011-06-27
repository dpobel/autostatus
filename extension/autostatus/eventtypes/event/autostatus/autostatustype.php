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
 *  - date_int4 : content attribute, if any, to condition the update
 *  - data_text1 : identifier of the social network
 *  - data_text2 : login on the social network
 *  - data_text3 : password on the social network
 *  - data_text4 : siteaccess to generate URLs for
 *  - data_text5 : oauth access token
 *
 * @uses eZWorkflowEventType
 */
class autostatusType extends eZWorkflowEventType
{
    const WORKFLOW_TYPE_STRING = 'autostatus';


    function __construct()
    {
        parent::__construct( autostatusType::WORKFLOW_TYPE_STRING,
                             ezpI18n::tr( 'kernel/workflow/event', 'Auto status' ) );
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
            case 'trigger_attribute_id':
            {
                return $event->attribute( 'data_int4' );
            }
            case 'trigger_attribute_identifier':
            {
                $attributeID = $event->attribute( 'data_int4' );
                return eZContentClassAttribute::classAttributeIdentifierByID( $attributeID );
            }
            case 'trigger_attribute':
            {
                return eZContentClassAttribute::fetch( $event->attribute( 'data_int4' ) );
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
            case 'siteaccess':
            {
                return $event->attribute( 'data_text4' );
            }
            case 'access_token':
            {
                autostatusSocialNetwork::fixIncludePath();
                return unserialize( $event->attribute( 'data_text5' ) );
            }
            case 'access_token_network_identifier':
            {
                $token = $event->attribute( 'access_token' );
                if ( $token instanceof Zend_Oauth_Token_Access )
                {
                    return $token->social_network;
                }
                return '';
            }
        }
        return null;
    }

    function typeFunctionalAttributes()
    {
        return array( 'class_identifier', 'class_id', 'attribute_identifier', 'attribute_id',
                      'class', 'attribute', 'use_cronjob',
                      'trigger_attribute_id', 'trigger_attribute_identifier', 'trigger_attribute',
                      'social_network_identifier', 'social_network', 'login', 'password',
                      'siteaccess', 'access_token', 'access_token_network_identifier' );
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
        $attributeIdentifierForTriggeringPostName = 'AttributeIdentifierTrigger_' . $eventID;
        $socialNetworkPostName = 'SocialNetwork_' . $eventID;
        $loginPostName = 'Login_' . $eventID;
        $passwordPostName = 'Password_' . $eventID;
        $siteaccessPostName = 'Siteaccess_' . $eventID;

        $prefix = $event->attribute( 'workflow_type' )->attribute( 'group_name' ) . ' / '
                  . $event->attribute( 'workflow_type' )->attribute( 'name' ) . ' : ';
        $validation['processed'] = true;
        if ( !$http->hasPostVariable( $classIdentifierPostName )
                || !is_numeric( eZContentClass::classIDByIdentifier( $http->postVariable( $classIdentifierPostName ) ) ) )
        {
            $finalState = eZInputValidator::STATE_INVALID;
            $validation['groups'][] = array( 'text' => $prefix . ezpI18n::tr( 'kernel/workflow/event', 'Invalid content class' ) );
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
            $validation['groups'][] = array( 'text' => $prefix . ezpI18n::tr( 'kernel/workflow/event', 'Invalid attribute' ) );
        }
        else
        {
            $event->setAttribute( 'data_int2', eZContentClassAttribute::classAttributeIDByIdentifier( $http->postVariable( $classIdentifierPostName )
                                                                                                      . '/' . $http->postVariable( $attributeIdentifierPostName ) ) );
        }
        if ( !$http->hasPostVariable( $attributeIdentifierForTriggeringPostName ) )
        {
            $finalState = eZInputValidator::STATE_INVALID;
            $validation['groups'][] = array( 'text' => $prefix . ezpI18n::tr( 'kernel/workflow/event', 'Invalid way of triggering the udpate (none actually)' ) );
        }
        else
        {
            $value = $http->postVariable( $attributeIdentifierForTriggeringPostName );
            if ( eZContentClassAttribute::classAttributeIDByIdentifier( $http->postVariable( $classIdentifierPostName )
                                                                        . '/' . $http->postVariable( $attributeIdentifierForTriggeringPostName ) ) )
            {
                $value = eZContentClassAttribute::classAttributeIDByIdentifier( $http->postVariable( $classIdentifierPostName )
                                                                        . '/' . $http->postVariable( $attributeIdentifierForTriggeringPostName ) );
            }
            $event->setAttribute( 'data_int4', $value );
        }
        $socialNetwork = null;
        if ( !$http->hasPostVariable( $socialNetworkPostName ) )
        {
            $finalState = eZInputValidator::STATE_INVALID;
            $validation['groups'][] = array( 'text' => $prefix . ezpI18n::tr( 'kernel/workflow/event', 'You need to choose a social network' ) );
        }
        else
        {
            $socialNetwork = autostatusSocialNetwork::fetchByIdentifier( $http->postVariable( $socialNetworkPostName ) );
            if ( $socialNetwork instanceof autostatusSocialNetwork )
            {
                $event->setAttribute( 'data_text1', $http->postVariable( $socialNetworkPostName ) );
            }
            else
            {
                $finalState = eZInputValidator::STATE_INVALID;
                $validation['groups'][] = array( 'text' => $prefix . ezpI18n::tr( 'kernel/workflow/event', 'Invalid social network' ) );
            }
        }

        if ( $socialNetwork !== null && !$socialNetwork->requireOauth() )
        {
            if ( !$http->hasPostVariable( $loginPostName )
                    || $http->postVariable( $loginPostName ) == '' )
            {
                $finalState = eZInputValidator::STATE_INVALID;
                $validation['groups'][] = array( 'text' => $prefix . ezpI18n::tr( 'kernel/workflow/event', 'Login cannot be empty' ) );
            }
            else
            {
                $event->setAttribute( 'data_text2', $http->postVariable( $loginPostName ) );
            }
            if ( !$http->hasPostVariable( $passwordPostName )
                    || $http->postVariable( $passwordPostName ) == '' )
            {
                $finalState = eZInputValidator::STATE_INVALID;
                $validation['groups'][] = array( 'text' => $prefix . ezpI18n::tr( 'kernel/workflow/event', 'Password cannot be empty' ) );
            }
            else
            {
                $event->setAttribute( 'data_text3', $http->postVariable( $passwordPostName ) );
            }
        }
        else if ( $socialNetwork !== null )
        {
            $event->setAttribute( 'data_text3', '' );
            $token = $event->attribute( 'access_token' );
            if ( $token instanceof Zend_Oauth_Token_Access && $token->social_network === $socialNetwork->attribute( 'identifier' ) )
            {
                $event->setAttribute( 'data_text2', $token->screen_name );
            }
            else if ( $token instanceof Zend_Oauth_Token_Access && $token->social_network !== $socialNetwork->attribute( 'identifier' ) )
            {
                $validation['groups'][] = array( 'text' => $prefix . ezpI18n::tr( 'kernel/workflow/event', 'The OAuth access token does not correspond to the selected social network' ) );
                $finalState = eZInputValidator::STATE_INVALID;
            }
            else
            {
                $validation['groups'][] = array( 'text' => $prefix . ezpI18n::tr( 'kernel/workflow/event', 'You have to check your OAuth access' ) );
                $finalState = eZInputValidator::STATE_INVALID;
            }
        }

        if ( !$http->hasPostVariable( $siteaccessPostName )
                || $http->postVariable( $siteaccessPostName ) == '' )
        {
            $finalState = eZInputValidator::STATE_INVALID;
            $validation['groups'][] = array( 'text' => $prefix . ezpI18n::tr( 'kernel/workflow/event', 'No values given for siteaccess' ) );
        }
        else
        {
            $event->setAttribute( 'data_text4', $http->postVariable( $siteaccessPostName ) );
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
        $triggerAttributeIdentifierPostName = 'AttributeIdentifierTrigger_' . $eventID;
        $socialNetworkPostName = 'SocialNetwork_' . $eventID;
        $loginPostName = 'Login_' . $eventID;
        $passwordPostName = 'Password_' . $eventID;
        $useCronjobPostName = 'UseCronjob_' . $eventID;
        $siteaccessPostName = 'Siteaccess_' . $eventID;

        $event->setAttribute( 'data_int1', eZContentClass::classIDByIdentifier( $http->postVariable( $classIdentifierPostName ) ) );
        $event->setAttribute( 'data_int2', eZContentClassAttribute::classAttributeIDByIdentifier( $http->postVariable( $classIdentifierPostName )
                                                                                                  . '/' . $http->postVariable( $attributeIdentifierPostName ) ) );
        $value = $http->postVariable( $triggerAttributeIdentifierPostName );
        if ( eZContentClassAttribute::classAttributeIDByIdentifier( $http->postVariable( $classIdentifierPostName )
                                                                    . '/' . $http->postVariable( $triggerAttributeIdentifierPostName ) ) )
        {
            $value = eZContentClassAttribute::classAttributeIDByIdentifier( $http->postVariable( $classIdentifierPostName )
                                                                    . '/' . $http->postVariable( $triggerAttributeIdentifierPostName ) );
        }
        $event->setAttribute( 'data_int4', $value );

        $event->setAttribute( 'data_int3', intval( $http->hasPostVariable( $useCronjobPostName ) ) );
        $event->setAttribute( 'data_text1', $http->postVariable( $socialNetworkPostName ) );
        $network = autostatusSocialNetwork::fetchByIdentifier( $http->postVariable( $socialNetworkPostName ) );
        if ( !$network->requireOauth() )
        {
            $event->setAttribute( 'data_text2', $http->postVariable( $loginPostName ) );
            $event->setAttribute( 'data_text3', $http->postVariable( $passwordPostName ) );
        }
        $event->setAttribute( 'data_text4', $http->postVariable( $siteaccessPostName ) );
    }

    function customWorkflowEventHTTPAction( $http, $action, $workflowEvent )
    {
        autostatusSocialNetwork::fixIncludePath();
        if ( $action === 'OAuthCheck' )
        {
            $networkIdentifier = $http->postVariable( 'SocialNetwork_' . $workflowEvent->attribute( 'id' ) );
            $network = autostatusSocialNetwork::fetchByIdentifier( $networkIdentifier );
            if ( !$network instanceof autostatusSocialNetwork )
            {
                // TODO handle error ?
                return ;
            }
            $workflowEvent->setAttribute( 'data_text1', $networkIdentifier );
            $workflowEvent->store();
            $uri = 'autostatus/oauth/' . $networkIdentifier . '/' . $workflowEvent->attribute( 'id' );
            eZURI::transformURI( $uri, false, 'full' );
            $config = $network->oauthConfig( $uri );
            $consumer = new Zend_Oauth_Consumer( $config );
            $token = $consumer->getRequestToken();
            $http->setSessionVariable( autostatusSocialNetwork::TOKEN_SESSION_VAR, serialize( $token ) );
            $redirectURL = $consumer->getRedirectUrl();
            eZHTTPTool::redirect( $redirectURL );
            eZExecution::cleanExit();
        }
    }


    function execute( $process, $event )
    {
        $parameters = $process->attribute( 'parameter_list' );
        eZDebug::writeDebug( $parameters, __METHOD__ );

        autostatusSocialNetwork::fixIncludePath();

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

        if ( $event->attribute( 'trigger_attribute_id' ) == -1 or
             ( $event->attribute( 'trigger_attribute' ) and
               $dataMap[$event->attribute( 'trigger_attribute_identifier' )]->attribute( 'content' )
             )
           )
        {
            if ( $event->attribute( 'use_cronjob' ) && !isset( $parameters['in_cronjob'] ) )
            {
                $message = self::substituteFormats( $dataMap[$attributeIdentifier]->attribute( 'content' ), $object, $event, $socialNetwork );
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
                $message = self::substituteFormats( $dataMap[$attributeIdentifier]->attribute( 'content' ), $object, $event, $socialNetwork );
            }
            eZDebug::writeDebug( $message, __METHOD__ );

            $options = array();
            if ( $socialNetwork->attribute( 'require_oauth' ) )
            {
                $options['token'] = $event->attribute( 'access_token' );
            }
            else
            {
                $options['login'] = $event->attribute( 'login' );
                $options['password'] = $event->attribute( 'password' );
            }
            $errorMsg = false;
            try
            {
                $ini = eZINI::instance( 'autostatus.ini' );
                if ( $ini->variable( 'AutoStatusSettings', 'Debug' ) === 'disabled' )
                {
                    $result = $socialNetwork->update( $message, $options );
                    if ( $result->isError() )
                    {
                        $errorMsg = $result->error;
                    }
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
        }
        return eZWorkflowEventType::STATUS_ACCEPTED;
    }

    static function substituteFormats( $message, $contentObject, $event, $socialNetwork )
    {
        // It is important here to make sure the final message does not exceed the maximum message length.
        $initialLength = strlen( $message );
        $maxMessageLength = $socialNetwork->getMaxMessageLength();

        if ( strpos( $message, '%url' ) !== false )
        {
            require_once( 'access.php' );
            $uriAccess  = isset( $GLOBALS['eZCurrentAccess'] ) && isset( $GLOBALS['eZCurrentAccess']['type'] ) && $GLOBALS['eZCurrentAccess']['type'] == EZ_ACCESS_TYPE_URI;
            $hostAccess = isset( $GLOBALS['eZCurrentAccess'] ) && isset( $GLOBALS['eZCurrentAccess']['type'] ) && $GLOBALS['eZCurrentAccess']['type'] == EZ_ACCESS_TYPE_HTTP_HOST;

            // Prior to any handling on the access, check whether the required access
            // is different from the current one. use $GLOBALS['eZCurrentAccess']['name']
            $alterUrl = ( $event->attribute( 'siteaccess' ) == -1 or $event->attribute( 'siteaccess' ) == $GLOBALS['eZCurrentAccess']['name'] ) ? false : true ;

            if ( $alterUrl and $uriAccess )
            {
                // store access path
                $previousAccessPath = eZSys::instance()->AccessPath;
                // clear access path
                eZSys::clearAccessPath();
                // set new access path with siteaccess name
                eZSys::addAccessPath( $event->attribute( 'siteaccess' ) );
            }

            $node = $contentObject->attribute( 'main_node' );
            $currentSA = eZSiteAccess::current();
            eZSiteAccess::load( array( 'name' => $event->attribute( 'siteaccess' ),
                                       'type' => eZSiteAccess::TYPE_STATIC,
                                       'uri_part' => array() ) );
            $nodeURL = $node->attribute( 'url_alias' );
            eZSiteAccess::load( $currentSA );
            eZURI::transformURI( $nodeURL, false, 'full' );

            if ( $alterUrl and $hostAccess )
            {
                //changeAccess( $previousAccess );
                // retrieve domain name associated to the requested siteaccess :
                $ini = eZINI::instance();
                $matchMapItems = $ini->variableArray( 'SiteAccessSettings', 'HostMatchMapItems' );
                foreach ( $matchMapItems as $matchMapItem )
                {
                    if ( $matchMapItem[1] == $event->attribute( 'siteaccess' ) )
                    {
                        $host = $matchMapItem[0];
                        break;
                    }
                }
                if ( isset( $host ) )
                {
                    $uriParts = explode( eZSys::hostname(), $nodeURL );
                    $nodeURL = implode( $host, $uriParts );
                }
            }

            // Last chance, if the URL still is not properly formed
            // (can happen when run from a CLI script)
            // @FIXME : This is clumsy, does not support SSLness, may be broken :)
            if ( strpos( $nodeURL, 'http' ) === false )
            {
                $nodeURL = 'http://' . trim( eZINI::instance()->variable( 'SiteSettings', 'SiteURL' ), '/' ) . $nodeURL;
            }

            $message = str_replace( '%url', $nodeURL, $message );

            if ( $alterUrl and $uriAccess )
            {
                // clear access path
                eZSys::clearAccessPath();
                // restore previous value
                eZSys::addAccessPath( $previousAccessPath );
            }

            // Calculate the remaining message room :
            // the URL will be shortened from any size to 20
            // (see http://searchengineland.com/analysis-which-url-shortening-service-should-you-use-17204)
            //
            // @FIXME : add support for other URL-shrinking services, and take their respective URL-length into account here.
            if ( $maxMessageLength !== null )
                $maxMessageLength = $maxMessageLength - ( $initialLength - /* '%url' */ 4 + /* bit.ly URL size, automatic twitter transformation */ 20 );
        }

        if ( strpos( $message, '%title' ) !== false )
        {
            // @TODO : add length check. If shortage, shorten the name with '…'
            $title = $contentObject->attribute( 'name' );
            if ( $maxMessageLength !== null )
            {
                if ( $maxMessageLength > -6 )
                {
                    $maxMessageLength = $maxMessageLength + /* '%title' */ 6;

                    // shorten, if necessary, the title to fit the message size :
                    if ( $maxMessageLength - strlen( $title ) < 0 )
                    {
                        $title = substr( $title, 0, $maxMessageLength -1 ) . '…';
                    }
                }
                else
                    $title = '';
            }
            $message = str_replace( '%title', $title, $message );
            $maxMessageLength = $maxMessageLength - strlen( $title );
        }

        return $message;
    }

}

eZWorkflowEventType::registerEventType( autostatusType::WORKFLOW_TYPE_STRING, 'autostatusType' );

?>
