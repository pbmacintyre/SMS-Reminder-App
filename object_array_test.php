<?php
/**
 * Copyright (C) 2019-2025 Paladin Business Solutions
 */
ob_start() ;
session_start();

require_once('includes/ringcentral-functions.inc');
require_once('includes/ringcentral-php-functions.inc');

show_errors();

//page_header(0);

// $controller = array('SDK' => $sdk, 'platform' => $platform);
$controller = ringcentral_sdk();
//echo_spaces("Controller array", $controller['platform']);

$platform = $controller['platform'];

$resp = $platform->get("/rcvideo/v2/account/~/extension/~/bridges/default");
$jsonObj = $resp->json();

$resp_array = json_decode(json_encode($jsonObj), true);
echo_spaces("Response as an array", $resp_array);

echo_spaces("Response as an object", $jsonObj);
// print("Your personal meeting URL is: " . $resp->json()->discovery->web . PHP_EOL);

ob_end_flush();
page_footer();
