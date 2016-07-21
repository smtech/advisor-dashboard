<?php

use Battis\BootstrapSmarty\NotificationMessage;

/* some sample app metadata information -- review config.xml for a panoply of options */
$metadata['APP_DESCRIPTION'] = 'A dashboard for advisors in Canvas';
$metadata['APP_DOMAIN'] = '';
$metadata['APP_ICON_URL'] = '@APP_URL/lti/icon.png';
$metadata['APP_LAUNCH_URL'] = '@APP_URL/lti/launch.php';
$metadata['APP_PRIVACY_LEVEL'] = 'public'; # /public|name_only|anonymous/
$metadata['APP_CONFIG_URL'] = '@APP_URL/lti/config.xml';
$metadata['ACCOUNT_NAVIGATION'] = true; # is_bool()
$metadata['ACCOUNT_NAVIGATION_DEFAULT'] = 'enabled'; # /enabled|disabled/
$metadata['ACCOUNT_NAVIGATION_ENABLED'] = 'true'; # /true|false/
$metadata['ACCOUNT_NAVIGATION_ICON_URL'] = '@APP_ICON_URL';
$metadata['ACCOUNT_NAVIGATION_LAUNCH_URL'] = '@APP_LAUNCH_URL';
$metadata['ACCOUNT_NAVIGATION_LINK_TEXT'] = '@APP_NAME';
$metadata['ACCOUNT_NAVIGATION_VISIBILITY'] = 'admins'; # /public|members|admins/
$metadata['COURSE_NAVIGATION'] = true; # is_bool()
$metadata['COURSE_NAVIGATION_DEFAULT'] = 'enabled'; # /enabled|disabled/
$metadata['COURSE_NAVIGATION_ENABLED'] = 'true'; # /true|false/
$metadata['COURSE_NAVIGATION_ICON_URL'] = '@APP_ICON_URL';
$metadata['COURSE_NAVIGATION_LAUNCH_URL'] = '@APP_LAUNCH_URL';
$metadata['COURSE_NAVIGATION_LINK_TEXT'] = '@APP_NAME';
$metadata['COURSE_NAVIGATION_VISIBILITY'] = 'admins'; # /public|members|admins/
$metadata['CUSTOM_FIELDS'] = true; # is_bool()
$metadata['CUSTOM_FIELD_debug'] = 'true'; # /true|false/
$metadata['EDITOR_BUTTON'] = false; # is_bool()
$metadata['HOMEWORK_SUBMISSION'] = false; # is_bool()
$metadata['RESOURCE_SELECTION'] = false; # is_bool()
$metadata['USER_NAVIGATION'] = false; # is_bool()

$smarty->addMessage(
	'App metadata updated',
	'Application metadata has been updated to create config.xml',
	NotificationMessage::GOOD
);

?>
