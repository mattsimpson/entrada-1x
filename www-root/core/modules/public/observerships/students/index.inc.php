<?php
/**
 * Entrada [ http://www.entrada-project.org ]
 *
 * This file gives Entrada users the ability to update their user profile.
 *
 * @author Organisation: Queen's University
 * @author Unit: School of Medicine
 * @author Developer: Jonathan Fingland <jonathan.fingland@queensu.ca>
 * @copyright Copyright 2010 Queen's University. All Rights Reserved.
 *
 * @version $Id: profile.inc.php 1114 2010-04-09 18:15:05Z finglanj $
 */

if (!defined("IN_OBSERVERSHIPS_STUDENTS")) {
	exit;
} elseif ((!isset($_SESSION["isAuthorized"])) || (!$_SESSION["isAuthorized"])) {
	header("Location: ".ENTRADA_URL);
	exit;
} elseif(!$ENTRADA_ACL->isLoggedInAllowed('observerships', 'read',true) || $_SESSION["details"]["group"] != "student") {
	$ONLOAD[]	= "setTimeout('window.location=\\'".ENTRADA_URL."/".$MODULE."\\'', 15000)";

	$ERROR++;
	$ERRORSTR[]	= "Your account does not have the permissions required to use this module.<br /><br />If you believe you are receiving this message in error please contact <a href=\"mailto:".html_encode($AGENT_CONTACTS["administrator"]["email"])."\">".html_encode($AGENT_CONTACTS["administrator"]["name"])."</a> for assistance.";
	echo display_error();

	application_log("error", "Group [".$_SESSION["permissions"][$_SESSION[APPLICATION_IDENTIFIER]["tmp"]["proxy_id"]]["group"]."] and role [".$_SESSION["permissions"][$_SESSION[APPLICATION_IDENTIFIER]["tmp"]["proxy_id"]]["role"]."] do not have access to this module [".$MODULE."]");
}  else {
	
	
	$PAGE_META["title"]			= "Observerships";
	$PAGE_META["description"]	= "";
	$PAGE_META["keywords"]		= "";

	$PROXY_ID					= $_SESSION["details"]["id"];
	
	
	if ((is_array($_SESSION["permissions"])) && ($total_permissions = count($_SESSION["permissions"]) > 1)) {
		$sidebar_html  = "The following individual".((($total_permissions - 1) != 1) ? "s have" : " has")." given you access to their ".APPLICATION_NAME." permission levels:";
		$sidebar_html .= "<ul class=\"menu\">\n";
		foreach ($_SESSION["permissions"] as $proxy_id => $result) {
			if ($proxy_id != $_SESSION["details"]["id"]) {
				$sidebar_html .= "<li class=\"checkmark\"><strong>".html_encode($result["fullname"])."</strong><br /><span class=\"content-small\">Exp: ".(($result["expires"]) ? date("D M d/y", $result["expires"]) : "Unknown")."</span></li>\n";
			}
		}
		$sidebar_html .= "</ul>\n";

		new_sidebar_item("Delegated Permissions", $sidebar_html, "delegated-permissions", "open");
	}
	
	
	display_status_messages();

	
?>
<h1>Observerships Overview</h1>

<h2>Completed Observerships Requiring Feedback</h2>
	<table class="obs_table tableList" cellspacing="0">
		<colgroup>
			<col width="25%"></col>
			<col width="30%"></col>
			<col width="25%"></col>
			<col width="20%"></col>
		</colgroup>
		<thead>
			<tr>
				<td class="general">
					Preceptor/Specialty
				</td>
				<td class="general">
					Location
				</td>
				<td class="general">
					Date
				</td>
				<td class="general">
					Actions
				</td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>
				Dr. A. Burns<br/>Family Medicine		
				</td>
				<td>
				Kingston General Hospital 		
				</td>
				<td>
				2010-01-07 10:30 - 2010-01-07 14:00 		
				</td>
				<td>
				Cancel<br />
				Enter Feedback		
				</td>
			</tr>
		</tbody>
	</table>
	
	<br/>
	
	<h2>Pending Observerships</h2>
	<table class="obs_table tableList" cellspacing="0">
		<colgroup>
			<col width="25%"></col>
			<col width="30%"></col>
			<col width="25%"></col>
			<col width="20%"></col>
		</colgroup>
		<thead>
			<tr>
				<td class="general">
					Preceptor/Specialty
				</td>
				<td class="general">
					Location
				</td>
				<td class="general">
					Date
				</td>
				<td class="general">
					Actions
				</td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>
				Dr. M. Gibson<br/>Geriatrics		
				</td>
				<td>
				St. Mary's 		
				</td>
				<td>
				2010-09-07 10:30 - 2010-09-07 14:00 		
				</td>
				<td>
				Cancel
				</td>
			</tr>
		</tbody>
	</table>

<?php
}