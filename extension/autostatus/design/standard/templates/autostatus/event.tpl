{*
 * $Id$
 * $HeadURL$
 *}
<td>{if $event.event.login|ne( '' )}{$event.event.login|wash}@{/if}{$event.event.social_network.name|wash}</td>
<td>{$event.message|wash|autolink}</td>
<td>{$event.created|l10n( 'shortdatetime' )}</td>
<td>{$event.modified|l10n( 'shortdatetime' )}</td>
<td class="message">{$event.error_msg|wash}</td>
<td class="retry-button">
{if $event.is_error}
    <input type="submit" class="button retry-button" name="Retry_{$event.id}" value="{'Retry'|i18n( 'autostatus/log' )}"{cond( $event.event|is_object, '', ' disabled="disabled"'} />
{else}
    <input type="submit" class="button retry-button" name="Retry_{$event.id}" value="{'Send again'|i18n( 'autostatus/log' )}"{cond( $event.event|is_object, '', ' disabled="disabled"'} />
{/if}
</td>
