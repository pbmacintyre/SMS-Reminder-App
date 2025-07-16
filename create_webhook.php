<?php
// create watching webhook subscription for opt Out messages

require_once('includes/ringcentral-functions.inc');
require_once('includes/ringcentral-php-functions.inc');

show_errors();
page_header(); ?>

<table class="CustomTable">
    <tr class="CustomTable">
        <td colspan="2" class="CustomTableFullCol">
            <img src="images/rc-logo.png"/>
			<?php

			$subscription_url = "https://" . $_SERVER['HTTP_HOST'];
			require(__DIR__ . '/includes/vendor/autoload.php');
			$dotenv = Dotenv\Dotenv::createImmutable(__DIR__."/includes")->load();

			$url_suffix = $_ENV['RC_WEBHOOK_URL_SUFFIX'];
			$subscription_url .= $url_suffix ;

			$controller = ringcentral_sdk();

			$response = $controller['platform']->get("/subscription");
			$subscriptions = $response->json()->records;
			if (count($subscriptions) > 0) {
				echo_spaces("<br/>One or more webhooks already exist for this app, they are listed below", "", 2);
				foreach ($subscriptions as $subscription) {
					echo_spaces("Webhook ID", $subscription->id, 2);
				}
			} else {
				try {
					$api_call = $controller['platform']->post('/subscription',
						array(
							"eventFilters" => array(
								"/restapi/v1.0/account/~/extension/~/message-store/instant?type=SMS",
							),
							"expiresIn" => "315360000",
							"deliveryMode" => array(
								"transportType" => "WebHook",
								// need full URL for this to work as well
								"address" => $subscription_url,
							)
						)
					);
					$webhook_id = $api_call->json()->id;
					echo_spaces("<br/>Webhook successfully created ! Webhook ID", $webhook_id, 2);
				} catch (\RingCentral\SDK\Http\ApiException $e) {
					echo_spaces("create_webhook API Exception", $e->getMessage());
				}
			}
			?>
            <hr>
            <a href="index.php"> Return to home page </a>
        </td>
    </tr>
</table>