<?php
/** Copyright (C) 2019-2025 Paladin Business Solutions */

require_once(__DIR__ . '/includes/ringcentral-php-functions.inc');
require_once(__DIR__ . '/includes/ringcentral-db-functions.inc');
require_once(__DIR__ . '/includes/ringcentral-functions.inc');
require_once(__DIR__ . '/includes/ringcentral-curl-functions.inc');

//show_errors();
//echo_spaces("cron running");
//exit();
require(__DIR__ . '/includes/vendor/autoload.php');

Dotenv\Dotenv::createImmutable(__DIR__ . "/includes")->load();

$client_id = $_ENV['RC_APP_CLIENT_ID'];
$client_secret = $_ENV['RC_APP_CLIENT_SECRET'];

$destination_array = array();

$table = "clients";
$columns_data = array("*",);
$db_result = db_record_select($table, $columns_data);

// cycle through each client account to send out notifications if warranted
foreach ($db_result as $key => $value) {
	// refresh the tokens before we begin
	$tokens = refresh_tokens($value['client_id'], $value['refresh']);

	// build destination array
	$destination_array[$key] = [
		"client_id" => $value['client_id'],
		"access" => $tokens['accessToken'],
		//"extension" => $row['extension_id'],
		"from_number" => $value['from_number'],
		// creates a possible sub-array of to numbers
		"to_numbers" => get_to_numbers($value['client_id']),
		// creates a possible sub-array of all chat ids
		"chat_ids" => get_chat_ids($value['client_id']),
	];
}
//echo_spaces("destination_array",$destination_array);

foreach ($destination_array as $value) {
	// now cycle through each client to send out any messaging
	// get any audit data per client based on their selected notifications
	$audit_data = get_audit_data($value["client_id"], $value["access"]);

	send_admin_sms($value, $audit_data);
	send_team_message($value, $audit_data);
}

$dateTime = new DateTime('now');
$dateTime->setTimezone(new DateTimeZone("America/Halifax")); // AST is UTC-4

$eventTime = $dateTime->format('M j, Y => g:i:s a');

//$message = "CRON runs every 30 minutes";
echo_spaces("CRON code finished running: $eventTime");
//send_basic_sms ($tokens['accessToken'], $message);
