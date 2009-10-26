{*
   $Id$
   $HeadURL$
*}
{def $classes=fetch( 'class', 'list', hash( 'sort_by', array( 'name', true() ) ) )
     $attributes = array()
     $social_networks = fetch( autostatus, network_list )
     $allowed_datatypes = ezini( 'AutoStatusSettings', 'StatusDatatype', 'autostatus.ini',, true() )}
<script type="text/javascript">
<!--
    var classIdentifierAttributesArray = new Array();
    {foreach $classes as $class}
        {set $attributes = fetch( 'class', 'attribute_list', hash( 'class_id', $class.id ) )}

    classIdentifierAttributesArray["{$class.identifier}"] = new Array();
        {foreach $attributes as $attribute}
            {if $allowed_datatypes|contains( $attribute.data_type_string )}

    classIdentifierAttributesArray["{$class.identifier}"].push( {ldelim}identifier: "{$attribute.identifier}",
                                                                        datatype: "{$attribute.data_type_string}",
                                                                        name: "{$attribute.name|wash( 'javascript' )}"{rdelim} );{/if}
        {/foreach}

    {/foreach}

    {literal}
    function updateAttributes( classSelect, idAttributesList, attributesArray, noAttributeMsg )
    {
        var attributeSelect = document.getElementById( idAttributesList );
        var classIdentifier = classSelect.options[classSelect.selectedIndex].value;
        var attributes = attributesArray[classIdentifier];
        if ( !attributes || attributes.length == 0 )
        {
            attributeSelect.innerHTML = '<option value="">' + noAttributeMsg + '</option>';
            attributeSelect.disabled = true;
        }
        else
        {
            attributeSelect.innerHTML = '<option value=""></option>';
            attributeSelect.disabled = false;
            for( var i=0; i!=attributes.length; i++)
            {
                attributeSelect.innerHTML += '<option value="' + attributes[i]['identifier'] + '">' + attributes[i]['name'] + ' (' + attributes[i]['datatype'] + ')</option>';
            }
        }
    }
    {/literal}
-->
</script>
<div class="block">
    <fieldset>
        <legend>{'Class attribute to use for status'|i18n( 'design/admin/workflow/eventtype/edit' )}</legend>

        <p>
            <label class="radio" for="ClassIdentifier_{$event.id}">{'Content class'|i18n( 'design/admin/workflow/eventtype/edit' )}</label>
            <select id="ClassIdentifier_{$event.id}" name="ClassIdentifier_{$event.id}" onchange="updateAttributes( this, 'AttributeIdentifier_{$event.id}', classIdentifierAttributesArray, '{'No suitable attribute in the selected content class'|i18n( 'design/admin/workflow/eventtype/edit' )|wash( 'javascript' )}' )">
                <option value="">{'Choose a content class'|i18n( 'design/admin/workflow/eventtype/edit' )}</option>
            {foreach $classes as $class}
                <option value="{$class.identifier}"{if $event.class_identifier|eq( $class.identifier )} selected="selected"{/if}>{$class.name|wash}</option>
            {/foreach}
            </select>
            &nbsp;&nbsp;&nbsp;
            <label class="radio" for="AttributeIdentifier_{$event.id}">{'Attribute to use for status message'|i18n( 'design/admin/workflow/eventtype/edit' )}</label>
            <select id="AttributeIdentifier_{$event.id}" name="AttributeIdentifier_{$event.id}"{cond( $event.class_id|eq( '' ), ' disabled="disabled"', '' )}>
                <option value="">{'Choose an attribute'|i18n( 'design/admin/workflow/eventtype/edit' )}</option>
            {if $event.class_id|ne( '' )}
                {foreach fetch( class, attribute_list, hash( class_id, $event.class_id ) ) as $attribute}
                    {if $allowed_datatypes|contains( $attribute.data_type_string )}
                <option value="{$attribute.identifier}"{if $attribute.id|eq( $event.attribute_id )} selected="selected"{/if}>{$attribute.name|wash}</option>
                    {/if}
                {/foreach}
            {/if}
            </select>
        </p>

    </fieldset>
    <br />
    <fieldset>
        <legend>{'Account informations'|i18n( 'design/admin/workflow/eventtype/edit' )}</legend>
        <p>
            <label class="radio" for="SocialNetwork_{$event.id}">{'Social network'|i18n( 'design/admin/workflow/eventtype/edit' )}</label>
            <select id="SocialNetwork_{$event.id}" name="SocialNetwork_{$event.id}">
                <option value="">{'Choose a social network'|i18n( 'design/Admin/workflow/eventtype/edit' )}</option>
            {foreach $social_networks as $network}
                <option value="{$network.identifier}"{if $event.social_network_identifier|eq( $network.identifier )} selected="selected"{/if}>{$network.name|wash()}</option>
            {/foreach}
            </select>
        </p>

        <p>
            <label class="radio" for="Login_{$event.id}">{'Login'|i18n( 'design/admin/workflow/eventtype/edit' )}</label>
            <input type="text" name="Login_{$event.id}" id="Login_{$event.id}" value="{$event.login|wash}" size="20" />
            &nbsp;&nbsp;&nbsp;
            <label class="radio" for="Password_{$event.id}">{'Password'|i18n( 'design/admin/workflow/eventtype/edit' )}</label>
            <input type="password" name="Password_{$event.id}" id="Password_{$event.id}" value="" size="20" />
        </p>

    </fieldset>
    <br />
    <fieldset>
        <legend>{'Defer status update to cronjob'|i18n( 'design/admin/workflow/eventtype/edit' )}</legend>
        <p>
            <label for="UseCronjob_{$event.id}" class="radio">{'Use cronjob to update status'|i18n( 'design/admin/workflow/eventtype/edit' )}</label>
            <input type="checkbox" value="1" name="UseCronjob_{$event.id}" id="UseCronjob_{$event.id}"{if $event.use_cronjob} checked="checked"{/if} />
        </p>
    </fieldset>
</div>
{undef $classes $attributes $social_networks $allowed_datatypes}
