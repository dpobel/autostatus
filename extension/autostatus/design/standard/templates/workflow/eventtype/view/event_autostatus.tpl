{*
   $Id$
   $HeadURL$
*}
{def $class = $event.class
     $attribute = $event.attribute
     $network = $event.social_network
     $login = $event.login}
<div class="element">
    <ul>
        <li><strong>{'Content class'|i18n( 'design/admin/workflow/eventtype/edit' )}</strong> : {$class.name|wash}</li>
        <li><strong>{'Attribute used for status'|i18n( 'design/admin/workflow/eventtype/edit' )}</strong> : {$attribute.name|wash}</li>
        <li><strong>{'Social network'|i18n( 'design/admin/workflow/eventtype/edit' )}</strong> : {$login|wash}@{$network.name|wash}</li>
    </ul>
</div>
{undef $class $attribute $network $login}
