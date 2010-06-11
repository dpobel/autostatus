{*
 * $Id$
 * $HeadURL$
 *}

<div class="context-block">

<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">
<h2 class="context-title">{'%count events'|i18n( 'autostatus/log', , hash( '%count', $events_count ) )}</h2>
<div class="header-subline"></div>
</div></div></div></div></div></div>

<div class="box-ml"><div class="box-mr"><div class="box-content">


<div class="content-navigation-childlist">
<table class="list" cellspacing="0">
<tbody>
<tr>
    <th>{'Social network'|i18n( 'autostatus/log' )}</th>
    <th>{'Status to send'|i18n( 'autostatus/log' )}</th>
    <th class="modified">{'Created'|i18n( 'autostatus/log' )}</th>
    <th class="modified">{'Modified'|i18n( 'autostatus/log' )}</th>
    <th>{'Error message'|i18n( 'autostatus/log' )}</th>
    <th class="edit">&nbsp;</th>
</tr>
{foreach $events as $event sequence array( 'bgdark', 'bglight' ) as $style}
<tr class="{$style}{cond( $event.is_error, ' error_event', '' )}">
    <td>{$event.event.login|wash}@{$event.event.social_network.name|wash}</td>
    <td>{$event.message|wash}</td>
    <td>{$event.created|l10n( 'shortdatetime' )}</td>
    <td>{$event.modified|l10n( 'shortdatetime' )}</td>
    <td>{$event.error_msg|wash}</td>
    <td style="text-align:center;">
    {if $event.is_error}
        <input type="submit" class="button retry-button" name="Retry_{$event.id}" value="{'Retry'|i18n( 'autostatus/log' )}" />
    {/if}
    </td>
</tr>
{/foreach}
</tbody>
</table>

<div class="context-toolbar">
{include name=navigator uri='design:navigator/google.tpl'
                        page_uri=$page_uri
                        item_count=$events_count
                        view_parameters=hash( 'offset', $offset )
                        item_limit=$limit}
</div><!-- context-toolbar -->
</div></div></div>



<div class="controlbar">
    <div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">
    <div class="block">
        <div class="left">

        </div>
        <div class="right">

        </div>
        <div class="break"></div>
    </div>
    </div></div></div></div></div></div>
</div>


</div>
