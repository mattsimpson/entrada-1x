<?php
/**
 * Entrada [ http://www.entrada-project.org ]
 *
 * Allows administrators to edit user incidents in the entrada_auth.user_incidents table.
 *
 * @author Organisation: Queen's University
 * @author Unit: School of Medicine
 * @author Developer: James Ellis <james.ellis@queensu.ca>
 * @copyright Copyright 2010 Queen's University. All Rights Reserved.
 *
 * @version $Id: edit.inc.php 1187 2010-05-06 13:44:57Z finglanj $
*/

if ((!defined("PARENT_INCLUDED")) || (!defined("IN_USERS"))) {
	exit;
} elseif ((!isset($_SESSION["isAuthorized"])) || (!$_SESSION["isAuthorized"])) {
	header("Location: ".ENTRADA_URL);
	exit;
} elseif (!$ENTRADA_ACL->amIAllowed("incident", "update", false)) {
	$ERROR++;
	$ERRORSTR[]	= "Your account does not have the permissions required to use this feature of this module.<br /><br />If you believe you are receiving this message in error please contact <a href=\"mailto:".html_encode($AGENT_CONTACTS["administrator"]["email"])."\">".html_encode($AGENT_CONTACTS["administrator"]["name"])."</a> for assistance.";

	echo display_error();

	application_log("error", "Group [".$_SESSION["permissions"][$_SESSION[APPLICATION_IDENTIFIER]["tmp"]["proxy_id"]]["group"]."] and role [".$_SESSION["permissions"][$_SESSION[APPLICATION_IDENTIFIER]["tmp"]["proxy_id"]]["role"]."] does not have access to this module [".$MODULE."]");
} else {
	if (isset($_GET["incident-id"]) && ($incident_id = clean_input($_GET["incident-id"], array("nows", "int")))) {
		$query = "SELECT * FROM `".AUTH_DATABASE."`.`user_incidents` WHERE `incident_id` = ".$db->qstr($incident_id);
		$incident_record = $db->GetRow($query);
		if ($incident_record) {
			$BREADCRUMB[] = array("url" => ENTRADA_URL."/admin/users?section=edit&id=".$incident_record["proxy_id"], "title" => html_encode(get_account_data("firstlast", $incident_record["proxy_id"])));
			$BREADCRUMB[] = array("url" => "", "title" => "Editing Incident");
			
			echo "<h1>Editing Incident</h1>\n";
		
			// Error Checking
			switch ($STEP) {
				case 2 :
					/**
					 * Required field "incident_title" / Incident Title.
					 */
					if ((isset($_POST["incident_title"])) && ($incident_title = clean_input($_POST["incident_title"], array("trim", "notags")))) {
							if ((strlen($incident_title) >= 3) && (strlen($incident_title) <= 64)) {
								$PROCESSED["incident_title"] = $incident_title;
							} else {
								$ERROR++;
								$ERRORSTR[] = "The new incident title must be between 3 and 64 characters.";	
							}
					} else {
						$ERROR++;
						$ERRORSTR[] = "You must provide a valid title for this incident so it may be referenced later.";
					}

					/*
					 * Required field "incident_severity" / Incident Severity.
					 */
					if ((isset($_POST["incident_severity"])) && ($incident_severity = clean_input($_POST["incident_severity"], array("trim", "int")))) {
						$PROCESSED["incident_severity"] = $incident_severity;
					} else {
						$PROCESSED["incident_severity"] = 1;
					}
					
					/*
					 * Required field "incident_status" / Incident Status.
					 */
					if ((isset($_POST["incident_status"])) && $_POST["incident_status"]) {
						$PROCESSED["incident_status"] = 1;
					} else {
						$PROCESSED["incident_status"] = 0;
					}
					
					/**
					 * Required field "incident_date" / Incident Start (validated through validate_calendar function).
					 * Non-required field "follow_up_date" / Incident Finish (validated through validate_calendar function).
					 */
					$incident_date = validate_calendar("incident", true, false);
					if ((isset($incident_date["start"])) && ((int) $incident_date["start"])) {
						$PROCESSED["incident_date"] = (int) $incident_date["start"];
					}
					
					if ((isset($incident_date["finish"])) && ((int) $incident_date["finish"])) {
						$PROCESSED["follow_up_date"] = (int) $incident_date["finish"];
					} else {
						$PROCESSED["follow_up_date"] = 0;
					}

					/**
					 * Non-required field "incident_description" / Comments.
					 */
					if ((isset($_POST["incident_description"])) && ($incident_description = clean_input($_POST["incident_description"], array("trim", "notags")))) {
						$PROCESSED["incident_description"] = $incident_description;
					} else {
						$PROCESSED["incident_description"] = "";
					}

					if (!$ERROR) {
						if ($db->AutoExecute(AUTH_DATABASE.".user_incidents", $PROCESSED, "UPDATE", "incident_id = ".$db->qstr($incident_id))) {
							$url = ENTRADA_URL."/admin/users/manage?id=".$PROXY_ID;

							$SUCCESS++;
							$SUCCESSSTR[] = "You have successfully update the incident in the system.<br /><br />You will now be redirected to the user edit page for user id [".$PROXY_ID."]; this will happen <strong>automatically</strong> in 5 seconds or <a href=\"".$url."\" style=\"font-weight: bold\">click here</a> to continue.";
							
							$ONLOAD[] = "setTimeout('window.location=\\'".$url."\\'', 5000)";
							
							application_log("success", "Proxy ID [".$_SESSION["details"]["id"]."] successfully updated the incident id [".$incident_id."].");
						} else {
							$ERROR++;
							$ERRORSTR[] = "Unable to update this user incident at this time. The MEdTech Unit has been informed of this error, please try again later.";

							application_log("error", "Unable to update user incident [".$incident_id."]. Database said: ".$db->ErrorMsg());
						}
					}
					
					if ($ERROR) {
						$STEP = 1;
					}
				break;
				case 1 :
				default :
					$PROCESSED = $incident_record;
				break;
			}
			
			// Display Page.
			switch ($STEP) {
				case 2 :
					if ($NOTICE) {
						echo display_notice();
					}

					if ($SUCCESS) {
						echo display_success();
					}
				break;
				case 1 :
				default :
					if ($ERROR) {
						echo display_error();	
					}
					
					if ($NOTICE) {
						echo display_notice();	
					}
					?>
					<form action="<?php echo ENTRADA_URL; ?>/admin/users/manage/incidents?section=edit&amp;id=<?php echo $PROXY_ID; ?>&amp;incident-id=<?php echo $incident_id; ?>&amp;step=2" method="post">
						<table style="width: 100%" cellspacing="1" cellpadding="1" border="0" summary="Edit Incident">
							<colgroup>
								<col style="width: 3%" />
								<col style="width: 25%" />
								<col style="width: 72%" />
							</colgroup>
							<tfoot>
								<tr>
									<td colspan="3">&nbsp;</td>
								</tr>
								<tr>
									<td colspan="3" style="border-top: 2px #CCCCCC solid; padding-top: 5px; text-align: right">
										<input type="submit" class="button" value="Save" />
									</td>
								</tr>
							</tfoot>
							<tbody>
								<tr>
									<td colspan="3">
										<h2>Incident Details</h2>
									</td>
								</tr>
								<tr>
									<td>&nbsp;</td>
									<td><label for="incident_title" class="form-required">Incident Title</label></td>
									<td>
										<input type="text" id="incident_title" name="incident_title" value="<?php echo ((isset($PROCESSED["incident_title"])) ? html_encode($PROCESSED["incident_title"]) : ""); ?>" style="width: 250px" maxlength="64" />
									</td>
								</tr>
								<tr>
									<td colspan="2">&nbsp;</td>
									<td class="content-small" style="padding-top: 5px">
										Incident originally reported by <strong><?php echo get_account_data("firstlast", $incident_record["incident_author_id"]); ?></strong>.
									</td>
								</tr>
								<tr>
									<td colspan="3">&nbsp;</td>
								</tr>
								<tr>
									<td>&nbsp;</td>
									<td><label for="incident_severity" class="form-nrequired">Severity</label></td>
									<td>
										<select id="incident_severity" name="incident_severity" style="width: 65px">
										<?php
										for ($i = 1; $i <= 5; $i++) {
											echo "<option value=\"".$i."\"".(isset($PROCESSED["incident_severity"]) && ((int) $PROCESSED["incident_severity"]) == $i ? " selected=\"selected\"" : " ").">".$i."</option>";
										}
										?>
										</select>
									</td>
								</tr>
								<tr>
									<td>&nbsp;</td>
									<td><label for="incident_status" class="form-nrequired">Status</label></td>
									<td>
										<select id="incident_status" name="incident_status" style="width: 65px">
											<option value="1"<?php echo (isset($PROCESSED["incident_status"]) && ((int) $PROCESSED["incident_status"]) == 1 ? " selected=\"selected\"" : ""); ?>>Open</option>
											<option value="0"<?php echo (isset($PROCESSED["incident_status"]) && ((int) $PROCESSED["incident_status"]) == 0 ? " selected=\"selected\"" : ""); ?>>Closed</option>
										</select>
									</td>
								</tr>
								<tr>
									<td colspan="3">&nbsp;</td>
								</tr>
								<?php echo generate_calendars("incident", "Incident", true, true, ((isset($PROCESSED["incident_date"])) ? $PROCESSED["incident_date"] : time()), true, false, ((isset($PROCESSED["follow_up_date"])) ? $PROCESSED["follow_up_date"] : 0), true, false, " Date", " Follow Up"); ?>
								<tr>
									<td colspan="3">&nbsp;</td>
								</tr>
								<tr>
									<td>&nbsp;</td>
									<td colspan="2"><label for="incident_description" class="form-nrequired">Detailed Incidient Description / Information</label></td>
								</tr>
								<tr>
									<td>&nbsp;</td>
									<td colspan="2">
										<textarea id="incident_description" name="incident_description" style="width: 99%; height: 200px"><?php echo ((isset($PROCESSED["incident_description"])) ? html_encode($PROCESSED["incident_description"]) : ""); ?></textarea>
									</td>
								</tr>
							</tbody>
						</table>
					</form>
					<?php
				break;
			}
		} else {
			$ERROR++;
			$ERRORSTR[] = "In order to edit a user incident you must provide a valid identifier. The provided ID does not exist in this system.";

			echo display_error();

			application_log("notice", "Failed to provide a valid incident identifer when attempting to edit a user incident.");
		}
	} else {
		$ERROR++;
		$ERRORSTR[] = "In order to edit a user incident you must provide a incident identifier.";

		echo display_error();

		application_log("notice", "Failed to provide incident identifer when attempting to edit a user incident.");
	}
}