<?php /*
#
# $Id$
# $HeadURL$
#

[TwitterSettings]
ConsumerKey=xwONpUbHRNQaMC35jyNLw
ConsumerSecret=QJCWPOeiCrnkzvvs67YhH82S5TgExtIwxaJtPRDno
SiteURL=https://api.twitter.com/oauth

[IdenticaSettings]
ConsumerKey=0e09a4c893ae12cfc180262e31d7ffbe
ConsumerSecret=43e4894e6c7f5ce720adfc914d47a95e
SiteURL=https://identi.ca/api/oauth

[AutoStatusSettings]
# array of datatypes that can be used to store
# a status message
StatusDatatype[]
StatusDatatype[]=ezstring
StatusDatatype[]=eztext

# array of datatypes that can be used to trigger
# the sending of a status message
# If you add a new type here, make sure the return value
# of the content() method of the corresponding eZContentObjectAttribute
# object can be interpreted as a boolean value. Check l.287 in
# eventtypes/event/autostatus/autostatustype.php
StatusTriggerDatatype[]
StatusTriggerDatatype[]=ezboolean

# available social networks
SocialNetworks[]
SocialNetworks[]=twitter
SocialNetworks[]=identica
# alwayserror is a test social network that always throws
# an exception when trying to update the status
#SocialNetworks[]=alwayserror

# when Debug is set to enabled, no status update is send
# status updates are just logged
Debug=disabled
LogFile=autostatus.log

[AutoStatusLogSettings]
Limit=20

*/ ?>
