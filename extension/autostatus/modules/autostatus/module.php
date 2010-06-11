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
                          'ui_context' => 'view',
                          'unordered_params' => array( 'offset' => 'Offset' ) );

$FunctionList = array();
$FunctionList['log'] = array();

?>
