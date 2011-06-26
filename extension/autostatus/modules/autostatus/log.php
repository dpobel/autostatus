<?php
/*
 * $Id$
 * $HeadURL$
 *
 */

$tpl = eZTemplate::factory();
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
$Result['path'][] = array( 'text' => ezpI18n::tr( 'autostatus/log', 'Auto status log' ),
                           'url'  => $pageURI );
$Result['left_menu'] = 'design:autostatus/menu.tpl';
$Result['content'] = $tpl->fetch( 'design:autostatus/log.tpl' );
?>
