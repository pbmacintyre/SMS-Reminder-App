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
            <tr class="CustomTable">
                <td colspan="2" class="CustomTableFullCol">
                    <br/>
                    <input type="submit" class="submit_button" value=" Edit selected event " name="edit_event">
                    <input type="submit" class="submit_button" value=" Create New event " name="create_event">
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
	$_SESSION['eventID'] = htmlspecialchars($_POST['selected_event']);
	header("Location: edit_event.php");
}

/* ============= */
/*  --- MAIN --- */
/* ============= */

if (isset($_POST['edit_event'])) {
	check_form();
} elseif (isset($_POST['create_event'])) {
	header("Location: create_event.php");
} else {
	$message = "Select an event to manage or create a new one.";
	show_form($message);
}

ob_end_flush();
page_footer();
