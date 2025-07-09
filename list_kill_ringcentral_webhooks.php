<?php

require_once('includes/ringcentral-functions.inc');
require_once('includes/ringcentral-php-functions.inc');

show_errors();

echo_spaces("webhooks listing, if there is a blank display then there are no active webhooks");

$controller = ringcentral_sdk();

// list subscriptions then delete the one we don't need.

$response = $controller['platform']->get("/subscription");
$subscriptions = $response->json()->records;

foreach ($subscriptions as $subscription) {
    echo_spaces("Subscription ID", $subscription->id);
    echo_spaces("Creation Time", $subscription->creationTime);
    echo_spaces("Event Filter URI", $subscription->eventFilters[0]);
    echo_spaces("Webhook URI", $subscription->deliveryMode->address);
    echo_spaces("Webhook transport type", $subscription->deliveryMode->transportType, 2);

    if ($subscription->id == "06cf1db7-c948-4cfe-889d-13928126a480") {
        $response = $controller['platform']->delete("/restapi/v1.0/subscription/{$subscription->id}");
        echo_spaces("Subscription ID Deleted", $subscription->id, 2);
    }
}

