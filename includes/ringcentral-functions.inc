<?php
/** Copyright (C) 2019-2025 Paladin Business Solutions */

/* ================= */
/* Generic functions */
/* ================= */

function app_name() {
	return "SMS Reminder App";
}

/* ================== */
/* Get RingCental SDK */
/* ================== */
function ringcentral_sdk() {
	// Include Libraries
	require('includes/vendor/autoload.php');

	$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
	$dotenv->load();

	$jwt_key = $_ENV['RC_JWT_KEY'];

	$sdk = new RingCentral\SDK\SDK(
		$_ENV['RC_APP_CLIENT_ID'],
		$_ENV['RC_APP_CLIENT_SECRET'],
		$_ENV['RC_SERVER_URL']);

	$platform = $sdk->platform();

	// Login via API
	if (!$sdk->platform()->loggedIn()) {
		try {
			$platform->login(["jwt" => $jwt_key]);
		} catch (\RingCentral\SDK\Http\ApiException $e) {
			echo_spaces("SDK Failure", $e->getMessage());
		}
	}
	$controller = array('SDK' => $sdk, 'platform' => $platform);
	return $controller;
}


function list_existing_events() {
	$table = "events";
	$columns_data = array("event_id", "event_summary", "event_date");
	$db_result = db_record_select($table, $columns_data, "", "", $orderby = "event_date");
	echo "<tr >
          <td class='addform_left_col_even'>
          <p style='display: inline;'>Currently planned Events</p> 
          </td>          
          <td class='addform_right_col_even'>
          <select name='selected_event' >";
	foreach ($db_result as $row) {
		$event_info = $row['event_summary'] . ": " . date("M j, Y", strtotime($row['event_date']));
		echo "<option value='" . $row['event_id'] . "'>" . $event_info . "</option>";
	}
	echo "</select></td></tr>";
}

function ringcentral_gen_and_send_six_digit_code($to) {
	// Include Libraries
	require('includes/vendor/autoload.php');

	$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
	$dotenv->load();

	$from = $_ENV['RC_MOBILE_SENDING_NUMBER'];

	$six_digit_code = rand(100000, 999999);
	$_SESSION['six_digit_code'] = $six_digit_code;
	$_SESSION['last_four'] = substr($to, -4);

	$message = "You or another person has requested to use this number for two-factor authentication. 
        To authenticate 2FA please enter the following code on the validation page: $six_digit_code";

	// connect to SDK with credentials in the DB
	$controller = ringcentral_sdk();

	try {
		$apiResponse = $controller['platform']->post('/restapi/v1.0/account/~/extension/~/sms',
			array('from' => array('phoneNumber' => $from),
				'to' => array(array('phoneNumber' => $to)),
				'text' => $message,)
		);
	} catch (\RingCentral\SDK\Http\ApiException $e) {
		// craft a friendly message here.
		$return_message = "There was an error sending the validation code, Please try again later <br/>" . $e;
	}
}

function send_stop_sms($mobile) {
	// find the client record
	$table = "clients";
	$columns_data = array("client_id");
	$where_info = array("mobile", $mobile);
	$db_result = db_record_select($table, $columns_data, $where_info);

	$clientId = $db_result[0]['client_id'];

	// remove client record from both clients and reminders tables
	$table = "clients";
	$where_col = "client_id";
	$where_data = $clientId;
	db_record_delete($table, $where_col, $where_data);

	$table = "reminders";
	db_record_delete($table, $where_col, $where_data);

	// send out SMS that they have been removed from the reminders app
	$endpoint = 'https://platform.ringcentral.com/restapi/v1.0/account/~/extension/~/sms';

	// Include Libraries
	require('includes/vendor/autoload.php');

	$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
	$dotenv->load();

	$from = $_ENV['RC_MOBILE_SENDING_NUMBER'];
	$message = "You have been removed from the SMS reminders app with your STOP request. You will have to re-enter any reminders and renew your consent if you want to receive reminders from this app again in the future.";

	// connect to SDK with credentials in the DB
	$controller = ringcentral_sdk();

	try {
		$apiResponse = $controller['platform']->post('/restapi/v1.0/account/~/extension/~/sms',
			array('from' => array('phoneNumber' => $from),
				'to' => array(array('phoneNumber' => $mobile)),
				'text' => $message,)
		);
	} catch (\RingCentral\SDK\Http\ApiException $e) {
		// craft a friendly message here.
		$return_message = "There was an error sending the SMS stop confirmation message, Please try again later <br/>" . $e;
	}

	return;
}

function send_sms($controller, $mobile, $message) {
	// Include Libraries
	require('includes/vendor/autoload.php');

	$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
	$dotenv->load();

	$from = $_ENV['RC_MOBILE_SENDING_NUMBER'];

	try {
		$apiResponse = $controller['platform']->post('/restapi/v1.0/account/~/extension/~/sms',
			array('from' => array('phoneNumber' => $from),
				'to' => array(array('phoneNumber' => $mobile)),
				'text' => $message,)
		);
	} catch (\RingCentral\SDK\Http\ApiException $e) {
		// craft a friendly message here.
		$return_message = "There was an error sending the validation code, Please try again later <br/>" . $e;
	}

	return;
}
