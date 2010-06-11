{*
   $Id$
   $HeadURL$
*}
{def $class = $event.class
     $attribute = $event.attribute
     $triggerAttribute = $event.trigger_attribute 
     $network = $event.social_network
     $login = $event.login}
<div class="element">
    <ul>
        <li><strong>{'Content class'|i18n( 'design/admin/workflow/eventtype/edit' )}</strong> : {$class.name|wash}</li>
        <li><strong>{'Attribute used for status'|i18n( 'design/admin/workflow/eventtype/edit' )}</strong> : {$attribute.name|wash}</li>
        <li><strong>{'Attribute used for triggering the update'|i18n( 'design/admin/workflow/eventtype/edit' )}</strong> : {if $triggerAttribute}{$triggerAttribute.name|wash}{else}<em>{'No attribute dependancy, sending every time'|i18n( 'design/admin/workflow/eventtype/edit' )}</em>{/if}</li>        
        <li><strong>{'Social network'|i18n( 'design/admin/workflow/eventtype/edit' )}</strong> : {$login|wash}@{$network.name|wash}</li>
        <li><strong>{'Defer status update to cronjob'|i18n( 'design/admin/workflow/eventtype/edit' )}</strong> : {cond( $event.use_cronjob, 'Yes'|i18n( 'design/admin/workflow/eventtype/edit' ), 'No'|i18n( 'design/admin/workflow/eventtype/edit' ) )}</li>
    </ul>
</div>
{undef $class $attribute $network $login}
