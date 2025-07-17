<?php

/**
 * Copyright (C) 2019-2025 Paladin Business Solutions
 *
 */

// runs when a RingCentral SMS event is triggered...

require_once('includes/ringcentral-functions.inc');
require_once('includes/ringcentral-php-functions.inc');

//show_errors();

$hvt = isset($_SERVER['HTTP_VALIDATION_TOKEN']) ? $_SERVER['HTTP_VALIDATION_TOKEN'] : '';
if (strlen($hvt) > 0) {
	header("Validation-Token: {$hvt}");
}

$incoming = file_get_contents("php://input");

// use following to send incoming event data to a file for visual review
file_put_contents("received_SMS_EVENT_payload.log", $incoming);

if (empty($incoming)) {
	http_response_code(200);
	echo json_encode(array('responseType' => 'error', 'responseDescription' => 'No data provided Check SMS payload.'));
	exit();
}

$incoming_data = json_decode($incoming);

if (!$incoming_data) {
	http_response_code(200);
	echo json_encode(array('responseType' => 'error', 'responseDescription' => 'Media type not supported.  Please use JSON.'));
	exit();
}

// parse out the incoming information
$incoming_sms = htmlentities($incoming_data->body->subject);

// the clients mobile number
$toNumber = htmlentities($incoming_data->body->from->phoneNumber);
// our mobile number
$fromNumber = htmlentities($incoming_data->body->to[0]->phoneNumber);

echo_spaces("SMS Subject", $incoming_sms, 1);
echo_spaces("To Number", $toNumber, 1);
echo_spaces("From Number", $fromNumber, 1);

if (preg_match('/^(STOP)$/i', $incoming_sms)) {
	send_stop_sms($toNumber);
}


