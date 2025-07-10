<?php
/**
 * Copyright (C) 2019-2025 Paladin Business Solutions
 */
ob_start();
session_start();

require_once('includes/ringcentral-functions.inc');
require_once('includes/ringcentral-php-functions.inc');
require_once('includes/ringcentral-db-functions.inc');

show_errors();
page_header();

function show_form($message, $print_again = false) {

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
            <tr>
                <td class="addform_left_col">
                    <p style='display: inline;'>Event Summary:</p>
					<?php required_field(); ?>
                </td>
                <td class="addform_right_col"><input type="text" name="event_summary" value="<?php
					if ($print_again) {
						echo strip_tags($_POST['event_summary']);
					}
					?>">
                </td>
            </tr>
            <tr>
                <td class="addform_left_col_even">
                    <p style='display: inline;'>Event Date [YYYY-MM-DD] format:</p>
					<?php required_field(); ?>
                </td>
                <td class="addform_right_col_even"><input type="text" name="event_date" value="<?php
					if ($print_again) {
						echo strip_tags($_POST['event_date']);
					}
					?>">
                </td>
            </tr>
            <tr>
                <td class="addform_left_col">
                    <p style='display: inline;'>Reminder Date [YYYY-MM-DD] format:</p>
					<?php required_field(); ?>
                </td>
                <td class="addform_right_col"><input type="text" name="reminder_date" value="<?php
					if ($print_again) {
						echo strip_tags($_POST['reminder_date']);
					}
					?>">
                </td>
            </tr>
            <tr>
                <td class="addform_left_col_even">
                    <p style='display: inline;'>Event Details:</p>
					<?php required_field(); ?>
                </td>
                <td class="addform_right_col_even"><textarea name="event_deets" rows="6" cols="50"
                    <?php if ($print_again) {
						echo strip_tags($_POST['event_deets']);
					}
                    ?>></textarea>
                </td>
            </tr>
            <tr class="CustomTable">
                <td colspan="2" class="CustomTableFullCol">
                    <br/>
                    <input type="submit" class="submit_button" value=" Save new event " name="add_event">
                </td>
            </tr>
            <tr class="CustomTable">
                <td colspan="2" class="CustomTableFullCol">
                    <hr>
                </td>
            </tr>
            <tr class="CustomTable">
                <td colspan="2" class="CustomTableFullCol">
                    <a href="index.php"> Return to home page </a>
                </td>
            </tr>
        </table>
    </form>
	<?php
}

function check_form() {
	$print_again = false;
	$message = "";

	$event_summary = htmlspecialchars($_POST['event_summary']);
	$event_date = htmlspecialchars($_POST['event_date']);
	$reminder_date = htmlspecialchars($_POST['reminder_date']);
	$event_deets = htmlspecialchars($_POST['event_deets']);

	if ($event_summary == "") {
		$print_again = true;
		$message = "The event summary is required.";
	}
	if ($event_date == "") {
		$print_again = true;
		$message = "The event date is required.";
	}
	if ($reminder_date == "") {
		$print_again = true;
		$message = "The reminder date is required.";
	}
	if ($event_deets == "") {
		$print_again = true;
		$message = "The event details are required.";
	}

	if ($print_again) {
		show_form($message, $print_again);
	} else {
		$table = "events";
		$columns_data = array(
			"event_summary" => $event_summary,
			"event_date" => $event_date,
			"reminder_date" => $reminder_date,
			"event_deets" => $event_deets,
		);
		db_record_insert($table, $columns_data); ?>
		<table class="CustomTable">
            <tr class="CustomTable">
                <td colspan="2" class="CustomTableFullCol">
                    <img src="images/rc-logo.png"/>
                    <h3 class="msg_good"> The new event has been saved ! </h3>
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
if (isset($_POST['add_event'])) {
	check_form();
} else {
	$message = "Please provide the information to create a new event.";
	show_form($message);
}

ob_end_flush();
page_footer();
