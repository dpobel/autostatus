{*
   $Id$
   $HeadURL$
*}
{ezscript_require( 'ezjsc::jquery' )}
{def $classes=fetch( 'class', 'list', hash( 'sort_by', array( 'name', true() ) ) )
     $attributes = array()
     $social_networks = fetch( autostatus, network_list )
     $selected_social_network = false
     $allowed_datatypes = ezini( 'AutoStatusSettings', 'StatusDatatype', 'autostatus.ini',, true() )
     $allowed_datatypes_for_trigger = ezini( 'AutoStatusSettings', 'StatusTriggerDatatype', 'autostatus.ini',, true() )     
     }
<script type="text/javascript">
<!--
    // TODO : rewrite this ugly JS
    var socialNetworksOAuth = {ldelim}
    {foreach $social_networks as $network}
        {$network.identifier}: {cond( $network.require_oauth, 'true', 'false' )}
        {delimiter},{/delimiter}
    {/foreach}
    {rdelim};

{literal}
    function updateAuthForm( select, eventID, oauth )
    {
        var selected = jQuery( select ).val();
        var oauthid = '#oauth_' + eventID;
        var basicid = '#basic_' + eventID;
        if ( selected != '' )
        {
            if ( oauth[selected] )
            {
                jQuery( oauthid ).show();
                jQuery( basicid ).hide();
            }
            else
            {
                jQuery( oauthid ).hide();
                jQuery( basicid ).show();
            }
        }
        else
        {
            jQuery( oauthid ).hide();
            jQuery( basicid ).hide();
        }
    }
{/literal}

    var classIdentifierAttributesArray = new Array();
    var classIdentifierAttributesArrayForTrigger = new Array();    
    {foreach $classes as $class}
        {set $attributes = fetch( 'class', 'attribute_list', hash( 'class_id', $class.id ) )}

    classIdentifierAttributesArray["{$class.identifier}"] = new Array();
    classIdentifierAttributesArrayForTrigger["{$class.identifier}"] = new Array();
        {foreach $attributes as $attribute}
            {if $allowed_datatypes|contains( $attribute.data_type_string )}

    classIdentifierAttributesArray["{$class.identifier}"].push( {ldelim}identifier: "{$attribute.identifier}",
                                                                        datatype: "{$attribute.data_type_string}",
                                                                        name: "{$attribute.name|wash( 'javascript' )}"{rdelim} );{/if}
            {if $allowed_datatypes_for_trigger|contains( $attribute.data_type_string )}

    classIdentifierAttributesArrayForTrigger["{$class.identifier}"].push( {ldelim}identifier: "{$attribute.identifier}",
                                                                        datatype: "{$attribute.data_type_string}",
                                                                        name: "{$attribute.name|wash( 'javascript' )}"{rdelim} );{/if}                                                                        
        {/foreach}

    {/foreach}

    {literal}
    function updateAttributes( classSelect, idAttributesList, attributesArray, noAttributeMsg, noAttributeValue, alwaysDisplayNoAttributeOption )
    {
        if ( typeof idAttributesList != 'object' )
            var idAttributesList = [idAttributesList];
            
        if ( typeof attributesArray != 'object' )
            var attributesArray = [attributesArray];
            
        if ( typeof noAttributeMsg != 'object' )
            var noAttributeMsg = [noAttributeMsg];                        
            
        if ( typeof noAttributeValue != 'object' )
            var noAttributeValue = [noAttributeValue];                                    
            
	    if ( typeof alwaysDisplayNoAttributeOption != 'object' )
	        var alwaysDisplayNoAttributeOption = [alwaysDisplayNoAttributeOption];            
            
            
        for ( var j=0; j < idAttributesList.length; j++ )
        {
	        var attributeSelect = document.getElementById( idAttributesList[j] );
	        var classIdentifier = classSelect.options[classSelect.selectedIndex].value;
	        var attributes = attributesArray[j][classIdentifier];
	        if ( !attributes || attributes.length == 0 )
	        {
	            attributeSelect.innerHTML = '<option value="' + noAttributeValue[j] + '">' + noAttributeMsg[j] + '</option>';
	            attributeSelect.disabled = true;
	        }
	        else
	        {
                attributeSelect.innerHTML = '<option value=""></option>';
	            if ( alwaysDisplayNoAttributeOption[j] )
	            {
                    attributeSelect.innerHTML += '<option value="' + noAttributeValue[j] + '">' + noAttributeMsg[j] + '</option>';	            
	            }

	            attributeSelect.disabled = false;
	            for( var i=0; i!=attributes.length; i++)
	            {
	                attributeSelect.innerHTML += '<option value="' + attributes[i]['identifier'] + '">' + attributes[i]['name'] + ' (' + attributes[i]['datatype'] + ')</option>';
	            }
	        }
        }
    }
    {/literal}
-->
</script>
<div class="block">
    <fieldset>
        <legend>{'Account informations'|i18n( 'design/admin/workflow/eventtype/edit' )}</legend>
        <p>
            <label class="radio" for="SocialNetwork_{$event.id}">{'Social network'|i18n( 'design/admin/workflow/eventtype/edit' )}</label>
            <select id="SocialNetwork_{$event.id}" name="SocialNetwork_{$event.id}" onchange="updateAuthForm(this, {$event.id}, socialNetworksOAuth);">
                <option value="">{'Choose a social network'|i18n( 'design/admin/workflow/eventtype/edit' )}</option>
            {foreach $social_networks as $network}
                <option value="{$network.identifier}"{if $event.social_network_identifier|eq( $network.identifier )}{set $selected_social_network = $network} selected="selected"{/if}>{$network.name|wash()}</option>
            {/foreach}
            </select>
        </p>
        <p id="oauth_{$event.id}"{if or( $selected_social_network|is_object|not, $selected_social_network.require_oauth|not )} style="display:none;"{/if}>
            {if is_object( $event.access_token )}
                {if $event.login|eq( '' )}
                    {'You have already requested access.'|i18n( 'design/admin/workflow/eventtype/edit', '', hash( '%login', $event.login ) )}
                {else}
                    {'You have already requested access with the login <strong>%login</strong>.'|i18n( 'design/admin/workflow/eventtype/edit', '', hash( '%login', $event.login ) )}
                {/if}
                <input type="submit" value="Manage OAuth access" name="CustomActionButton[{$event.id}_OAuthCheck]" class="button" />
            {else}
                {'Please click on the following button to authorize autostatus to update your status.'|i18n( 'design/admin/workflow/eventtype/edit' )}
                <input type="submit" value="Check OAuth access" name="CustomActionButton[{$event.id}_OAuthCheck]" class="button defaultbutton" />
            {/if}
        </p>
        <p id="basic_{$event.id}"{if or( $selected_social_network|is_object|not, $selected_social_network.require_oauth )} style="display:none;"{/if}>
            <label class="radio" for="Login_{$event.id}">{'Login'|i18n( 'design/admin/workflow/eventtype/edit' )}</label>
            <input type="text" name="Login_{$event.id}" id="Login_{$event.id}" value="{$event.login|wash}" size="20" />
            &nbsp;&nbsp;&nbsp;
            <label class="radio" for="Password_{$event.id}">{'Password'|i18n( 'design/admin/workflow/eventtype/edit' )}</label>
            <input type="password" name="Password_{$event.id}" id="Password_{$event.id}" value="" size="20" />
        </p>

    </fieldset>
    <br />
    <fieldset>
        <legend>{'Class attribute to use for status'|i18n( 'design/admin/workflow/eventtype/edit' )}</legend>

        <p>
            <label class="radio" for="ClassIdentifier_{$event.id}">{'Content class'|i18n( 'design/admin/workflow/eventtype/edit' )}</label>
            <select id="ClassIdentifier_{$event.id}" name="ClassIdentifier_{$event.id}" onchange="updateAttributes( this, 
                                                                                                                    ['AttributeIdentifier_{$event.id}', 'AttributeIdentifierTrigger_{$event.id}'], 
                                                                                                                    [classIdentifierAttributesArray, classIdentifierAttributesArrayForTrigger], 
                                                                                                                    ['{'No suitable attribute in the selected content class'|i18n( 'design/admin/workflow/eventtype/edit' )|wash( 'javascript' )}', '{'Send every time'|i18n( 'design/admin/workflow/eventtype/edit' )|wash( 'javascript' )}' ],
                                                                                                                    ['',-1],
                                                                                                                    [false, true] 
                                                                                                                  )">
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
        <legend>{'Class attribute to trigger the sending'|i18n( 'design/admin/workflow/eventtype/edit' )}</legend>
        <p>
            <label class="radio" for="AttributeIdentifierTrigger_{$event.id}">{'Attribute used to trigger the sending'|i18n( 'design/admin/workflow/eventtype/edit' )}</label>
            <select id="AttributeIdentifierTrigger_{$event.id}" name="AttributeIdentifierTrigger_{$event.id}"{cond( $event.class_id|eq( '' ), ' disabled="disabled"', '' )}>
            <optgroup label="{'Do not use an attribute'|i18n( 'design/admin/workflow/eventtype/edit' )}">
                <option value="-1" {if or( $event.trigger_attribute_id|not, eq( $event.trigger_attribute_id, -1 ) )} selected="selected"{/if}><em>{'Send every time'|i18n( 'design/admin/workflow/eventtype/edit' )}</em></option>
            </optgroup>
            {if $event.class_id|ne( '' )}
            <optgroup label="{'Available attributes'|i18n( 'design/admin/workflow/eventtype/edit' )}">                         
                {foreach fetch( class, attribute_list, hash( class_id, $event.class_id ) ) as $attribute}
                    {if $allowed_datatypes|contains( $attribute.data_type_string )}
                <option value="{$attribute.identifier}"{if $attribute.id|eq( $event.attribute_id )} selected="selected"{/if}>{$attribute.name|wash}</option>
                    {/if}
                {/foreach}
            </optgroup>                
            {/if}
            </select>
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
    <br />
    <fieldset>
        <legend>{'Siteaccess to generate URLs for'|i18n( 'design/admin/workflow/eventtype/edit' )} <em>({'only works for "host" and "uri" MatchOrder values'|i18n( 'design/admin/workflow/eventtype/edit' )})</em></legend>
        <p>
            <label for="Siteaccess_{$event.id}" class="radio">{'Choose siteaccess'|i18n( 'design/admin/workflow/eventtype/edit' )}</label>
            <select id="Siteaccess_{$event.id}" name="Siteaccess_{$event.id}">
                <option value="-1">{'Current one'|i18n( 'design/admin/workflow/eventtype/edit' )}</option>
                {foreach ezini( 'SiteAccessSettings', 'RelatedSiteAccessList' ) as $siteaccess}
                    <option value="{$siteaccess}">{$siteaccess}</option>    
                {/foreach}
            </select>            
        </p>
    </fieldset>    
</div>
{undef $classes $attributes $social_networks $allowed_datatypes}
