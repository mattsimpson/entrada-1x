<?php
/**
 * Entrada [ http://www.entrada-project.org ]
 *
 * Serves the categories list up in a select box.
 *
 * @author Organisation: Queen's University
 * @author Unit: School of Medicine
 * @author Developer: Andrew Dos-Santos <andrew.dos-santos@queensu.ca>
 * @copyright Copyright 2010 Queen's University. All Rights Reserved.
 *
*/

/**
 * This is called via index.php when the user has verified we have their correct email address
 */

@set_include_path(implode(PATH_SEPARATOR, array(
    dirname(__FILE__) . "/../core",
    dirname(__FILE__) . "/../core/includes",
    dirname(__FILE__) . "/../core/library",
    get_include_path(),
)));

/**
 * Include the Entrada init code.
 */
require_once("init.inc.php");

date_default_timezone_set(DEFAULT_TIMEZONE);

session_start();

$proxy_id 	= $_SESSION[APPLICATION_IDENTIFIER]["tmp"]["proxy_id"];
$_SESSION["details"]["email_updated"] = true;

$PROCESSED["email_updated"] = time();
$PROCESSED["updated_by"] = $proxy_id;
if(!$db->AutoExecute(AUTH_DATABASE.".user_data", $PROCESSED, "UPDATE", "`id`=".$db->qstr($proxy_id))) {
	echo $db->ErrorMsg();
	exit;
}
?>