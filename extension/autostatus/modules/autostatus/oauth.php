<?php
/*
 * $Id$
 * $HeadURL$
 *
 */

autostatusSocialNetwork::fixIncludePath();

$http = eZHTTPTool::instance();
$Module = $Params['Module'];

if ( !isset( $Params['Network'] ) )
{
    eZDebug::writeError( 'Network identifier is missing', 'autostatus/oauth' );
    eZExecution::cleanExit();
}

$network = autostatusSocialNetwork::fetchByIdentifier( $Params['Network'] );

if ( !$network instanceof autostatusSocialNetwork )
{
    eZDebug::writeError( "Invalid network {$Params['Network']}", 'autostatus/oauth' );
    eZExecution::cleanExit();
}

if ( !isset( $Params['WorkflowEventID'] ) )
{
    eZDebug::writeError( 'WorkflowEventID is missing', 'autostatus/oauth' );
    eZExecution::cleanExit();
}

$WorkflowEventID = intval( $Params['WorkflowEventID'] );
$workflowEvent = eZWorkflowEvent::fetch( $WorkflowEventID, true, 1 );
if ( !$workflowEvent instanceof eZWorkflowEvent )
{
    eZDebug::writeError( "WorkflowEventID {$WorkflowEventID} is invalid", 'autostatus/oauth' );
    eZExecution::cleanExit();
}

$config = $network->oauthConfig();

try
{
    $consumer = new Zend_Oauth_Consumer( $config );

    $token = $consumer->getAccessToken( $_GET, unserialize( $http->sessionVariable(autostatusSocialNetwork::TOKEN_SESSION_VAR ) ) );
    $token->social_network = $network->attribute( 'identifier' );
    eZDebug::writeDebug( $token );
    $http->setSessionVariable( autostatusSocialNetwork::TOKEN_SESSION_VAR, null );

    $workflowEvent->setAttribute( 'data_text2', $token->screen_name );
    $workflowEvent->setAttribute( 'data_text5', serialize( $token ) );
    $workflowEvent->store();
}
catch( Exception $e )
{
    eZDebug::writeWarning( 'Not authorized', __METHOD__ );
    $workflowEvent->setAttribute( 'data_text2', '' );
    $workflowEvent->setAttribute( 'data_text5', '' );
    $workflowEvent->store();
}

$editWorkflowURI = 'workflow/edit/' . $workflowEvent->attribute( 'workflow_id' );
eZURI::transformURI( $editWorkflowURI, false, 'full' );
$Module->setExitStatus( eZModule::STATUS_REDIRECT );
$Module->setRedirectURI( $editWorkflowURI );
?>
