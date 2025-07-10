<?php
/**
 * Copyright (C) 2019-2025 Paladin Business Solutions
 *
 */

/* ========================== */
/* Call HTML wrappers section */
/* ========================== */
function page_header() {
    echo "<html class='SiteWide'><head >";
    echo "<title >" . app_name() . "</title></head>";
    echo "<link rel='stylesheet' href='css/ringcentral-custom.css'>";
}
function page_footer() {
    echo "</html>";
}
function place_logo() {
echo <<<SHOWLOGO
		<tr class="CustomTable">
          <td colspan="2" class="CustomTableFullCol">
             <img src="images/rc-logo.png"/>
          </td>
        </tr>
SHOWLOGO;
}
/* =================== */
/* Turn on Error codes */
/* =================== */
function show_errors() {
     error_reporting();
//     error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
    ini_set('display_errors', 1);
}
function echo_spaces($text, $value="", $lines=0) {
    echo "<br /><strong><p style='color: red; display: inline'>$text";
	if ($value !== "") { echo ": " ; }
	echo "</p></strong> ";
    if (is_string($value) || is_int($value))  { echo $value; }
    if (is_object($value) || is_array($value)) { ringcentral_dump_obj($value); }
    if (is_bool($value)) { if ($value) echo "TRUE / Success"; else echo 'FALSE / Failure'; }
    $i = 1 ;
    while ($i <= $lines) {
        echo "<br />" ;
        $i++;
    }
}
/* ======================== */
/* Dump the provided object */
/* ======================== */
function ringcentral_dump_obj($object) {
    echo "<pre>";
    var_dump($object);
    echo "</pre>";
}

function echo_plain_text($text, $color, $font_size="large", $lines=0) {
    echo "<br /><p style='color: $color; display: inline; font-size: $font_size'>$text</p>" ;
    if ($lines > 0 ) {
        $i = 0;
        while ($i <= $lines) {
            echo "<br />";
            $i++;
        }
    }
}
function echo_link($text, $url) {
	if ($url == "HOME_PAGE") {
		require('includes/vendor/autoload.php');
		$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
		$dotenv->load();
		$url = $_ENV['RC_HOME_ADDRESS'];
	}
	echo "<br /><br /><a href='$url'>$text</a>" ;
}
function required_field() {
	echo "<p style='color: red; display: inline'>*</p>" ;
}