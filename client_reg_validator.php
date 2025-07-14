<?php
/**
 * Copyright (C) 2019-2025 Paladin Business Solutions
 *
 */
require_once('includes/ringcentral-functions.inc');
require_once('includes/ringcentral-php-functions.inc');
require_once('includes/ringcentral-db-functions.inc');

ob_start();
session_start();

page_header();

function show_form($message, $print_again = false) { ?>
    <form action="" method="post">
        <table class="EditTable" >
			<?php place_logo(); ?>
            <tr >
                <td colspan="2" class="CustomTableFullCol">
                    <h2> Client Registration Process</h2>
                    <h3> Registration Confirmation page </h3>
					<?php
					if ($print_again == true) {
						echo "<p class='msg_bad'>" . $message . "</p>";
					} else {
						echo "<p class='msg_good'>" . $message . "</p>";
					} ?>
                    <hr>
                </td>
            </tr>
            <tr >
                <td >
                    <p class="right_text" >SMS Validation code:</p>
                </td>
                <td >
                    <input type="text" name="six_digit_code" >
                </td>
            </tr>
            <tr >
                <td colspan="2" class="CustomTableFullCol">
                    <br/>
                    <input type="submit" class="submit_button" value="Submit">
                </td>
            </tr>
        </table>
    </form>
	<?php
}

function check_form() {

    show_errors();

	$print_again = false;
	$message = "";

	$entered_code = htmlspecialchars($_POST['six_digit_code']);
	$sesh_code = $_SESSION['six_digit_code'];
	$mobile = $_SESSION['mobile'];

	/* =============================================================================== */

	if ($entered_code != $sesh_code) {
		$print_again = true;
		$message = "The entered 6 digit code does not match the code sent to your mobile.";
	}

	if ($print_again) {
		show_form($message, $print_again);
	} else {
        // check that client is not already in DB, if already in DB then use the client id from the found record
		$eventID = $_SESSION['eventID'];

		$columns_data = array("client_id");
		$where_info = array("mobile", $mobile);
		$db_result = db_record_select("clients", $columns_data, $where_info);

		if ($db_result) {
			$client_id = $db_result[0]['client_id'];
		} else {
            $table = "clients";
			$columns_data = array(
				"first_name" => $_SESSION['firstname'],
				"last_name" => $_SESSION['lastname'],
				"mobile" => $_SESSION['mobile'],
				"mobile_consent" => 1,
				"email" => $_SESSION['email'],
			);
			$client_id = db_record_insert($table, $columns_data, "client_id");
        }

//        // get the needed event information
//		$table = "events";
//		$columns_data = array ("reminder_date");
//        $where_info = array ("event_id", $eventID);
//		$db_result = db_record_select ($table, $columns_data, $where_info);

        // save reminder data to DB
		$table = "reminders";
		$columns_data = array(
			"event_id" => $eventID,
			"client_id" => $client_id,
//			"reminder_date" => $db_result[0]['reminder_date'],
		);
		db_record_insert($table, $columns_data);

		?>

        <table class="CustomTable">
            <tr class="CustomTable">
                <td colspan="2" class="CustomTableFullCol">
                    <img src="images/rc-logo.png"/>
                    <h3 class="msg_good"> Registration is Confirmed ! </h3>
                    <hr>
                    <a href="index.php"> Return to home page </a>
                </td>
            </tr>
        </table>
		<?php
	}

}

/* ============= */
/*  --- MAIN --- */
/* ============= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	check_form();
} else {
	$message = "Please provide the 6 digit code that was sent to the phone number <br/> that you provided on the registration page ending in: " . $_SESSION['last_four'];
	show_form($message);
}

ob_end_flush();
?>