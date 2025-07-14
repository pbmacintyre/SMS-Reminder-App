<?php
/** Copyright (C) 2019-2025 Paladin Business Solutions */

require_once(__DIR__ . '/includes/ringcentral-php-functions.inc');
require_once(__DIR__ . '/includes/ringcentral-db-functions.inc');
require_once(__DIR__ . '/includes/ringcentral-functions.inc');

show_errors();

require(__DIR__ . '/includes/vendor/autoload.php');

Dotenv\Dotenv::createImmutable(__DIR__ . "/includes")->load();

$client_id = $_ENV['RC_APP_CLIENT_ID'];
$client_secret = $_ENV['RC_APP_CLIENT_SECRET'];

$destination_array = array();

$today = date("Y-m-d");

// get all reminders for today
$table = "events";
$columns_data = "*" ;
$where_info = array ("reminder_date", $today);
$event_reminders_db_result = db_record_select($table, $columns_data, $where_info);

//echo_spaces("reminders found for 'today'", $event_reminders_db_result);

// now find all reminders that we set for clients
$message_array = array();
$i = 0 ;
foreach ($event_reminders_db_result as $value) {

	$table = "reminders";
	$columns_data = array ("client_id", );
	$where_info = array("event_id", $value['event_id']);
	$reminders_db_result = db_record_select($table, $columns_data, $where_info);

//	echo_spaces("found reminders", $reminders_db_result, 2);

	if ($reminders_db_result) {
		// build outgoing message for each found client per event
		foreach ($reminders_db_result as $reminder_value) {
			// client info
			$table = "clients";
			$columns_data = "*";
			$where_info = array("client_id", $reminder_value['client_id']);
			$client_db_result = db_record_select($table, $columns_data, $where_info);

			$message = "Dear " . $client_db_result[0]['first_name'] . " " . $client_db_result[0]['last_name'] . ": ";
			$message .= "You have requested that we send you a reminder on the following event information - " ;
			$message .=	"Event Summary: '" . $value['event_summary'] . "' "; ;
			$message .= "Event Date: " . date("M d, Y", strtotime($value['event_date']) ). " " ;
			$message .= "Event Details: '" . $value['event_deets'] . "' REPLY STOP to discontinue receiving SMS messages from this app. "; ;
//			echo_spaces("$i - building message", $message, 2);
			// add to message array
			$message_array[$i]["mobile"] = $client_db_result[0]['mobile'];
			$message_array[$i]["message"] = $message;
			$i++;
		}
	}
}

//echo_spaces("built message array", $message_array, 2);

// now send out the SMS messages.
foreach ($message_array as $sms_info) {
	send_sms($sms_info["mobile"], $sms_info["message"]);
}

//$message = "CRON runs every day";
echo_spaces("CRON code finished running.");
