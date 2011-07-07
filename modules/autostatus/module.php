<?php
/*
 * $Id$
 * $HeadURL$
 *
 */

$Module = array( 'name' => 'Autostatus' );

$ViewList = array();
$ViewList['log'] = array( 'script' => 'log.php',
                          'functions' => array( 'log' ),
                          'default_navigation_part' => 'autostatus',
                          'ui_context' => 'view',
                          'params' => array( 'Error' ),
                          'unordered_params' => array( 'offset' => 'Offset' ) );

$ViewList['oauth'] = array( 'script' => 'oauth.php',
                            'functions' => array( 'oauth' ),
                            'default_navigation_part' => 'autostatus',
                            'ui_context' => 'view',
                            'params' => array( 'Network', 'WorkflowEventID' ),
                            'unordered_params' => array() );

$FunctionList = array();
$FunctionList['log'] = array();
$FunctionList['oauth'] = array();

?>
