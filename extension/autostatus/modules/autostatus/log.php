<?php
/*
 * $Id$
 * $HeadURL$
 *
 */

require_once 'kernel/common/template.php';
$tpl = templateInit();
$ini = eZINI::instance( 'autostatus.ini' );

$Offset = 0;
if ( isset( $Params['Offset'] ) && is_numeric( $Params['Offset'] ) )
{
    $Offset = intval( $Params['Offset'] );
}

$limit = $ini->variable( 'AutoStatusLogSettings', 'Limit' );
$pageURI = 'autostatus/log';

$list = statusUpdateEvent::fetchList( $Offset, $limit );
$eventsCount = statusUpdateEvent::fetchListCount();

$tpl->setVariable( 'offset', $Offset );
$tpl->setVariable( 'events', $list );
$tpl->setVariable( 'events_count', $eventsCount );
$tpl->setVariable( 'limit', $limit );
$tpl->setVariable( 'page_uri', $pageURI );

$Result['path'] = array();
$Result['path'][] = array( 'text' => ezi18n( 'autostatus/log', 'Auto status log' ),
                           'url'  => $pageURI );
$Result['content'] = $tpl->fetch( 'design:autostatus/log.tpl' );
?>
