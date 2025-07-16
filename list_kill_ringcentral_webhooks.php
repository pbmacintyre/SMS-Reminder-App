<?php

require_once('includes/ringcentral-functions.inc');
require_once('includes/ringcentral-php-functions.inc');

show_errors();

echo_spaces("webhooks listing, if there is a blank display then there are no active webhooks", "", 2);

$controller = ringcentral_sdk();

// list subscriptions then delete the one we don't need.

try {
	$response = $controller['platform']->get("/subscription");
	$subscriptions = $response->json()->records;
} catch (Exception $e) {
	echo_spaces("catch error",  $e->getMessage());
}

foreach ($subscriptions as $subscription) {
    echo_spaces("Subscription ID", $subscription->id);
    echo_spaces("Creation Time", $subscription->creationTime);
    echo_spaces("Event Filter URI", $subscription->eventFilters[0]);
    echo_spaces("Webhook URI", $subscription->deliveryMode->address);
    echo_spaces("Webhook transport type", $subscription->deliveryMode->transportType, 2);

    if ($subscription->id == "06af07bc-169b-42dc-9a3c-4d7711b2c6d6") {
        $response = $controller['platform']->delete("/restapi/v1.0/subscription/{$subscription->id}");
        echo_spaces("Subscription ID Deleted", $subscription->id, 2);
    }
}

