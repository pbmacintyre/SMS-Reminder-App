<?php
/**
 * Copyright (C) 2019-2025 Paladin Business Solutions
 */
ob_start();
session_start();

require_once('includes/ringcentral-functions.inc');
require_once('includes/ringcentral-php-functions.inc');

//show_errors();

$controller = ringcentral_sdk();
$platform = $controller['platform'];

$endpoint = "/restapi/v1.0/glip/chats";

$params = [
	'type' => array('Personal',),
//        'type' => array( 'Everyone', 'Group', 'Personal', 'Direct', 'Team' ),
];

try {
	$response = $platform->get($endpoint, $params);
	$resp_array = json_decode(json_encode($response->json()), true);
	echo_spaces("Personal chat response array", $resp_array);
	echo_spaces("Personal chat id", $resp_array['records'][0]['members'][0]['id']);

} catch (\RingCentral\SDK\Http\ApiException $e) {
	echo_spaces("Chats Error info", $e->getMessage());
}

ob_end_flush();
page_footer();
