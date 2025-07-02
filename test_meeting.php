<?php
/**
 * Copyright (C) 2019-2025 Paladin Business Solutions
 */
ob_start();
session_start();

require_once('includes/ringcentral-functions.inc');
require_once('includes/ringcentral-php-functions.inc');

show_errors();

//page_header(0);

// $controller = array('SDK' => $sdk, 'platform' => $platform);
$controller = ringcentral_sdk();

$platform = $controller['platform'];

$endpoint = "/rcvideo/v2/account/~/extension/~/bridges";
$bodyParams = array(
	'name' => "Test Meeting 56",
	'type' => "Instant"
);
try {
	$resp = $platform->post($endpoint, $bodyParams);
	$jsonObj = $resp->json();
//	echo_spaces("response", $jsonObj);

	$meeting_link =	"<a href='" . $jsonObj->discovery->web . "'>Link</a>";
	echo_spaces("This is your meeting link", $meeting_link );
	echo_spaces("raw meeting link", $jsonObj->discovery->web );
} catch (Exception $e) {
	print_r("Unable to create an instant RCV meeting. " . $e->getMessage() . PHP_EOL);
}


ob_end_flush();
page_footer();
