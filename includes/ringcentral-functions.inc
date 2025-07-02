<?php
/** Copyright (C) 2019-2025 Paladin Business Solutions */

/* ================= */
/* Generic functions */
/* ================= */

function app_name() {
	return "Video Meetings API";
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
			$sdk = 0;
			// exit("<br/><br/>Unable to authenticate to platform. Check your RingCentral credentials. <br/><br/>") ;
		}
	}
	$controller = array('SDK' => $sdk, 'platform' => $platform);
	return $controller;
}

function list_tm_names() {
	$controller = ringcentral_sdk();
	$platform = $controller['platform'];

	$endpoint = "/team-messaging/v1/recent/chats";

	$params = [
		'type' => array('Direct',),
//        'type' => array( 'Everyone', 'Group', 'Personal', 'Direct', 'Team' ),
		'recordCount' => 14,
	];

	try {
		$response = $platform->get($endpoint, $params);
		$resp_array = json_decode(json_encode($response->json()), true);
//		echo_spaces("chats array", $resp_array);
	} catch (\RingCentral\SDK\Http\ApiException $e) {
		echo_spaces("Chats Error info", $e->getMessage());
	}

	foreach ($resp_array as $chat) {
		foreach ($chat as $details) {
//			 echo_spaces("details", $details);
//			$member_list[$details['id']] = $details['id'];
			foreach ($details as $detail) {
				if (is_array($detail)) {
					$k = 0;
					foreach ($detail as $ids) {
						// echo_spaces("ids", $ids['id']);
						$member_list[$details['id']][$k] = $ids['id'];
						$k++;
					}
				}
			}
		}
	}

	$my_id = get_self_tm_id();

	// filter through the array to get only the other person's id and convo code
	foreach ($member_list as $key => $subArray) {
		foreach ($subArray as $value) {
			if ($value !== $my_id) {
				$trimmed_member_list[$key] = $value;
			}
		}
	}

	foreach ($trimmed_member_list as $key => $id) {
		// get the names of the conversation participants
		try {
			$person_response = $platform->get("/team-messaging/v1/persons/{$id}");
			$convo_names[$key] = $person_response->json()->firstName . " " . $person_response->json()->lastName;
		} catch (\RingCentral\SDK\Http\ApiException $e) {
			echo_spaces("Person Error info", $e->getMessage() . " " . $id);
		}
	}

	return $convo_names;
}

function post_to_tm($people, $meeting_link) {
	$controller = ringcentral_sdk();
	$platform = $controller['platform'];

	foreach ($people as $key => $value) {

//		echo_spaces("key", $key);

		$endpoint = "/team-messaging/v1/chats/{$key}/posts";
		$message = "Hi, I have created a meeting for us, the link is: " . $meeting_link;

		$body = array(
			'text' => $message,
		);

//		echo_spaces("endpoint string", $endpoint);
//		echo_spaces("message", $message, 2);

		try {
			$response = $platform->post($endpoint, $body);
		} catch (\RingCentral\SDK\Http\ApiException $e) {
			echo_spaces("chat post error info", $e->getMessage() );
		}
	}

}

function get_self_tm_id() {
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
//		echo_spaces("Personal chat response array", $resp_array);
//		echo_spaces("Personal chat id", $resp_array['records'][0]['members'][0]['id']);
		$self_id = $resp_array['records'][0]['members'][0]['id'];

	} catch (\RingCentral\SDK\Http\ApiException $e) {
		echo_spaces("Chats Error info", $e->getMessage());
	}
	return $self_id;
}