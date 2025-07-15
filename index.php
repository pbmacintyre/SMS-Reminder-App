<?php
/**
 * Copyright (C) 2019-2025 Paladin Business Solutions
 */
ob_start();
session_start();

require_once('includes/ringcentral-functions.inc');
require_once('includes/ringcentral-php-functions.inc');
require_once('includes/ringcentral-db-functions.inc');

//show_errors();

function show_form($message, $print_again = false) {
	page_header();
	?>
    <form action="" method="post" enctype="multipart/form-data">
        <table class="EditTable">
			<?php place_logo(); ?>
            <tr>
                <td colspan="2" class="EditTableFullCol">
					<?php
					if ($print_again == true) {
						echo "<p class='msg_bad'>" . $message . "</strong></font>";
					} else {
						echo "<p class='msg_good'>" . $message . "</p>";
					} ?>
                    <hr>
                </td>
            </tr>
			<?php list_existing_events(); ?>
            <tr>
                <td class="addform_left_col">
                    <p style='display: inline;'>First Name:</p>
					<?php required_field(); ?>
                </td>
                <td class="addform_right_col"><input type="text" name="firstname" value="<?php
					if ($print_again) {
						echo strip_tags($_POST['firstname']);
					}
					?>">
                </td>
            </tr>
            <tr>
                <td class="addform_left_col_even">
                    <p style='display: inline;'>Last Name:</p>
					<?php required_field(); ?>
                </td>
                <td class="addform_right_col_even"><input type="text" name="lastname" value="<?php
					if ($print_again) {
						echo strip_tags($_POST['lastname']);
					}
					?>">
                </td>
            </tr>

            <tr>
                <td class="addform_left_col">
                    <p style='display: inline;'>Mobile Number:</p>
					<?php required_field(); ?>
                </td>
                <td class="addform_right_col"><input type="text" name="mobile" value="<?php
					if ($print_again) {
						echo strip_tags($_POST['mobile']);
					}
					?>" placeholder="Format: +19991234567">
                </td>
            </tr>
            <tr>
                <td class="addform_left_col_even">
                    <p style='display: inline;'>Grant Mobile Consent:</p>
					<?php required_field(); ?>
                </td>
                <td class="addform_right_col_even"><input type="checkbox" name="mobile_consent" <?php
					if ($print_again) {
						if ($_POST['mobile_consent'] == "on") {
							echo 'CHECKED';
						}
					} ?> >
                    <p style="color: #008ec2"> When enabling Mobile consent you are agreeing to receive SMS reminder
                        messages from this application. </p>
                </td>
            </tr>
            <tr>
                <td class="addform_left_col">
                    <p style='display: inline;'>eMail Address:</p>
					<?php required_field(); ?>
                </td>
                <td class="addform_right_col"><input type="text" name="email" value="<?php
					if ($print_again) {
						echo strip_tags($_POST['email']);
					}
					?>">
                </td>
            </tr>
            <tr class="CustomTable">
                <td colspan="2" class="CustomTableFullCol">
                    <br/>
                    <input type="submit" class="submit_button" value=" Add a reminder event " name="add_reminder">
                </td>
            </tr>
            <tr class="CustomTable">
                <td colspan="2" class="CustomTableFullCol">
                    <hr>
                </td>
            </tr>
            <tr class="CustomTable">
                <td class="CustomTableFullCol">
                    <a href="events_manager.php"> Manage events </a>
                </td>
                <td class="CustomTableFullCol">
                    <a href="create_webhook.php"> Create Apps Webhook </a>
                </td>
            </tr>
        </table>
    </form>
	<?php
}

function check_form() {
	$print_again = false;
	$message = "";

    $eventID = htmlspecialchars($_POST['selected_event']);
	$firstname = htmlspecialchars($_POST['firstname']);
	$lastname = htmlspecialchars($_POST['lastname']);
	$mobile = htmlspecialchars($_POST['mobile']);
	$email = htmlspecialchars($_POST['email']);
	$consent = htmlspecialchars($_POST['mobile_consent']) == "on" ? 1 : 0;

	if ($consent == 0) {
		$print_again = true;
		$message = "We cannot register you if you do not consent to SMS communications";
	}
	if ($email == "") {
		$print_again = true;
		$message = "The email address field cannot be blank.";
	}
	if ($mobile == "") {
		$print_again = true;
		$message = "The mobile number cannot be blank.";
	}
	// check the formatting of the mobile # == +19991234567
	$pattern = '/^\+\d{11}$/'; // Assumes 11 digits after the '+'
	if (!preg_match($pattern, $mobile)) {
		$print_again = true;
		$message = "The mobile number is not in the correct format of +19991234567";
	}
	if ($lastname == "") {
		$print_again = true;
		$message = "The last name field cannot be blank.";
	}
	if ($firstname == "") {
		$print_again = true;
		$message = "The first name field cannot be blank.";
	}

    // should not have multiple reminders for the same client (phone number) and the same event.
	$table = "clients" ;
	$columns_data = array("client_id");
	$where_info = array ("mobile", $mobile, );
	$client_db_result = db_record_select($table, $columns_data, $where_info);

    $table = "reminders" ;
    $columns_data = array("reminder_id");
	$where_info = array ("event_id", $eventID, "client_id", $client_db_result[0]['client_id'], );
    $condition = "AND" ;
	$reminder_db_result = db_record_select($table, $columns_data, $where_info, $condition);

    if ($reminder_db_result) {
		$print_again = true;
		$message = "You already have a reminder set for the selected event.";
	}

	if ($print_again) {
		show_form($message, $print_again);
	} else {
		ringcentral_gen_and_send_six_digit_code($mobile);
		$_SESSION['eventID'] = $eventID;
		$_SESSION['firstname'] = $firstname;
		$_SESSION['lastname'] = $lastname;
		$_SESSION['mobile'] = $mobile;
		$_SESSION['email'] = $email;
		header("Location: client_reg_validator.php");
	}
}

/* ============= */
/*  --- MAIN --- */
/* ============= */
if (isset($_POST['add_reminder'])) {
	check_form();
} else {
	$message = "Please provide the information to add yourself to an event reminder";
	show_form($message);
}

ob_end_flush();
page_footer();
