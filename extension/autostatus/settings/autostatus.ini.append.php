<?php /*
#
# $Id$
# $HeadURL$
#

[AutoStatusSettings]
# array of datatypes that can be used to store
# a status message
StatusDatatype[]
StatusDatatype[]=ezstring
StatusDatatype[]=eztext

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

*/ ?>
