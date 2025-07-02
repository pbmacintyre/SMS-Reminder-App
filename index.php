<?php
/**
 * Copyright (C) 2019-2025 Paladin Business Solutions
 */
ob_start() ;
session_start();

require_once('includes/ringcentral-functions.inc');
require_once('includes/ringcentral-php-functions.inc');

show_errors();

function show_form () {
    ?>
    <form action="" method="post" enctype="multipart/form-data">
        <table class="EditTable" >
            <tr class="CustomTable">
                <td colspan="2" class="CustomTableFullCol">
					<?php echo "<p class='msg_good'> Please select the names of those who you want to invite to a meeting</p>";	 ?>
                    <hr>
                </td>
            </tr>
        <?php
		    $tm_names = list_tm_names();
            $col_count = 1;

            foreach ($tm_names as $key => $tm_name) {
				if ($col_count == 1) { // first row
					echo "<tr >";
				}
				echo "<td class=''>";
				echo "<input type='checkbox' name='people[$key]' ";
				echo "> Convo between you and: " . $tm_name;
				echo "<br/><br/></td>";
				if ($col_count == 2) {
					$col_count = 0;
					echo "</tr>";
                }
                $col_count++;
            }
            ?>
            <tr class="CustomTable">
                <td class="CustomTableFullCol">
                    <br/>
                    <input type="submit" class="submit_button" value=" Send Meeting Invitation by TM " name="send_invite">
                </td>
            </tr>
            <tr class="CustomTable">
                <td colspan="2" class="CustomTableFullCol"><hr></td>
            </tr>
        </table>
    </form>
    <?php
}

/* ============= */
/*  --- MAIN --- */
/* ============= */
if (isset($_POST['send_invite'])) {
	gen_meeting($_POST['people']);
} else {
    show_form();
}

ob_end_flush();
page_footer();

function gen_meeting($people) {
	$controller = ringcentral_sdk();
	$platform = $controller['platform'];

	$endpoint = "/rcvideo/v2/account/~/extension/~/bridges";
	$bodyParams = array(
		'name' => "Test Meeting to discuss the Meetings API",
		'type' => "Instant"
	);
	try {
		$resp = $platform->post($endpoint, $bodyParams);
		$jsonObj = $resp->json();
		$meeting_link =	$jsonObj->discovery->web ;
	} catch (Exception $e) {
		echo_spaces("Unable to create an instant RCV meeting", $e->getMessage() );
	}
    post_to_tm($people, $meeting_link);
    echo_spaces("Meeting invitation(s)s have been delivered", $meeting_link);
    echo_link("This is the meeting link", $meeting_link);
    echo_link("return to Home page", "HOME_PAGE");

    return ;
}
