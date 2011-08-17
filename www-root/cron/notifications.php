<?php
/**
 * Online Course Resources [Pre-Clerkship]
 * @author Unit: Medical Education Technology Unit
 * @author Director: Dr. Benjamin Chen <bhc@post.queensu.ca>
 * @author Developer: Matt Simpson <simpson@post.queensu.ca>
 * @version 3.0
 * @copyright Copyright 2006 Queen's University, MEdTech Unit
 *
 * $Id: notifications.php 1116 2010-04-13 15:38:31Z jellis $
*/

@set_time_limit(0);
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
require_once("Entrada/phpmailer/class.phpmailer.php");

/**
 * NOTIFICATION CONFIGURATION OPTIONS
 */

/**
 * If a specific teacher does not want to receive notices, add their e-mail address to the $NOTIFICATION_BLACKLIST array.
 */
$NOTIFICATION_BLACKLIST = array();

$NOTIFICATION_MESSAGE		 	 = array();
$NOTIFICATION_MESSAGE["subject"] = "Teaching Reminder Notice: %EVENT_DATE%";

global $ENTRADA_ACTIVE_TEMPLATE;
$NOTIFICATION_MESSAGE["textbody"] = file_get_contents(ENTRADA_ABSOLUTE."/templates/".$ENTRADA_ACTIVE_TEMPLATE."/email/teaching-notification.txt");
$NOTIFICATION_MESSAGE["htmlbody"] = file_get_contents(ENTRADA_ABSOLUTE."/templates/".$ENTRADA_ACTIVE_TEMPLATE."/email/teaching-notification.html");

$NOTIFICATION_REMINDERS = array();

$NOTIFICATION_REMINDERS[30]["subject_suffix"]	= "(in thirty days)";
$NOTIFICATION_REMINDERS[30]["strtotime_string"]	= "+30 days";

$NOTIFICATION_REMINDERS[7]["subject_suffix"]	= "(in seven days)";
$NOTIFICATION_REMINDERS[7]["strtotime_string"]	= "+7 days";

$NOTIFICATION_REMINDERS[3]["subject_suffix"]	= "(in three days)";
$NOTIFICATION_REMINDERS[3]["strtotime_string"]	= "+3 days";

/**
 * END OF NOTIFICATION CONFIGURATION OPTIONS
 */

$START_OF_TODAY		= strtotime("00:00:00");

// Setup PHPMailer to do the work.
$mail				= new PHPMailer();
$mail->PluginDir	= ENTRADA_ABSOLUTE."/includes/classes/phpmailer/";
$mail->SetLanguage("en", ENTRADA_ABSOLUTE."/includes/classes/phpmailer/language/");

$mail->IsSendmail();
$mail->Sendmail		= SENDMAIL_PATH;

$mail->Priority		= 3;
$mail->CharSet		= DEFAULT_CHARSET;
$mail->Encoding		= "8bit";
$mail->WordWrap		= "76";

$mail->From     	= $AGENT_CONTACTS["agent-notifications"]["email"];
$mail->FromName		= $AGENT_CONTACTS["agent-notifications"]["name"];

$mail->Sender		= $AGENT_CONTACTS["agent-notifications"]["email"];

function fetch_event_resources_text($event_id = 0) {
	global $db;

	$output = array();
	
	if ($event_id = (int) $event_id) {
		$query		= "SELECT * FROM `event_files` WHERE `event_id` = ".$db->qstr($event_id);
		$results	= $db->GetAll($query);
		if ($results) {
			$output["html"] = "";
			$output["text"] = "";
			
			$output["html"] .= "<table style=\"margin-top: 20px; width: 100%\" cellspacing=\"0\" cellpadding=\"3\" border=\"0\">\n";
			$output["html"] .= "<thead>\n";
			$output["html"] .= "	<tr>\n";				
			$output["html"] .= "		<td style=\"background-color: #EEEEEE; border: 1px #666666 solid; font-weight: bold\">File Title</td>\n";
			$output["html"] .= "		<td style=\"background-color: #EEEEEE; border: 1px #666666 solid; border-left: none; font-weight: bold\">Last Updated</td>\n";
			$output["html"] .= "	</tr>\n";
			$output["html"] .= "</thead>\n";
			$output["html"] .= "<tbody>\n";
			foreach ($results as $result) {
				$output["html"] .= "<tr>\n";
				$output["html"] .= "	<td>\n";
				$output["html"] .= "		<a href=\"".ENTRADA_URL."/admin/events?section=content&id=".$event_id."\" title=\"Click to update ".html_encode($result["file_title"])."\" style=\"font-weight: bold\">".html_encode($result["file_title"])."</a>";
				$output["html"] .= "		<span class=\"content-small\">(".readable_size($result["file_size"]).")</span>";
				$output["html"] .= "	</td>\n";
				$output["html"] .= "	<td>".(((int) $result["updated_date"]) ? date(DEFAULT_DATE_FORMAT, $result["updated_date"]) : "Over two years ago")."</td>\n";
				$output["html"] .= "</tr>\n";
			}
			$output["html"] .= "</tbody>\n";
			$output["html"] .= "</table>\n";
			
			foreach ($results as $key => $result) {
				$output["text"] .= "   - ".$result["file_title"]." (".readable_size($result["file_size"]).")\n";
				$output["text"] .= "     Last Updated: ".(((int) $result["updated_date"]) ? date(DEFAULT_DATE_FORMAT, $result["updated_date"]) : "Over two years ago")."\n\n";
			}
		} else {
			$output["html"] .= "<div class=\"display-red\">\n";
			$output["html"] .= "<strong>There are no resources available for download.</strong>\n";
			$output["html"] .= "<br /><br />\n";
			$output["html"] .= "Please take a moment to upload any relevant documents by <a href=\"".ENTRADA_URL."/admin/events?section=content&id=".$event_id."\" style=\"font-weight: bold\">clicking here</a>.\n";
			$output["html"] .= "</div>\n";
			
			$output["text"] .= "   *There are no resources available for download.*\n\n";
			$output["text"] .= "   Please take a moment to upload any relavant documents at the following URL:\n";
			$output["text"] .= "   ".ENTRADA_URL."/admin/events?section=content&id=".$event_id."\n\n";
		}
	}
	
	return $output;
}

/**
 * Function that actually prepares and send the notifications.
 *
 * @param int $timestamp_start
 * @param int $timestamp_end
 * @param array $notice
 * @return bool
 */
function notifications_send($timestamp_start = 0, $timestamp_end = 0, $notice = array()) {
	global $db, $mail, $NOTIFICATION_MESSAGE, $NOTIFICATION_BLACKLIST, $AGENT_CONTACTS;

	if ((!$timestamp_start = (int) $timestamp_start) || (!$timestamp_end = (int) $timestamp_end)) {
		return false;
	}

	$query		= "	SELECT a.*
					FROM `events` AS a
					LEFT JOIN `courses` AS b
					ON b.`course_id` = a.`course_id`
					WHERE b.`course_id` IS NOT NULL
					AND b.`course_active` = '1'
					AND b.`notifications` = '1'
					AND a.`event_start` BETWEEN ".$db->qstr($timestamp_start)." AND ".$db->qstr($timestamp_end);
	$events	= $db->GetAll($query);
	if ($events) {
		foreach ($events as $event) {
			
			/**
			 * If you have configured the Curricular Coordinators in the $AGENT_CONTACTS array,
			 * then they are set here if available.
			 */
			if (($event["event_phase"]) && (isset($AGENT_CONTACTS["phase-".strtolower($event["event_phase"])])) && (is_array($AGENT_CONTACTS["phase-".strtolower($event["event_phase"])])) && ($AGENT_CONTACTS["phase-".strtolower($event["event_phase"])]["name"]) && ($AGENT_CONTACTS["phase-".strtolower($event["event_phase"])]["email"])) {
				$mail->From     	= $AGENT_CONTACTS["phase-".strtolower($event["event_phase"])]["email"];
				$mail->FromName		= $AGENT_CONTACTS["phase-".strtolower($event["event_phase"])]["name"];
				
				$mail->Sender		= $AGENT_CONTACTS["phase-".strtolower($event["event_phase"])]["email"];
			} else {
				$mail->From     	= $AGENT_CONTACTS["agent-notifications"]["email"];
				$mail->FromName		= $AGENT_CONTACTS["agent-notifications"]["name"];
				
				$mail->Sender		= $AGENT_CONTACTS["agent-notifications"]["email"];
			}
			
			$query			= "	SELECT a.`proxy_id`, b.`firstname`, b.`lastname`, b.`email`
								FROM `event_contacts` AS a
								LEFT JOIN `".AUTH_DATABASE."`.`user_data` AS b
								ON b.`id` = a.`proxy_id`
								WHERE a.`event_id` = ".$db->qstr($event["event_id"])."
								AND b.`contact_role` = 'teacher'
								AND b.`id` IS NOT NULL
								AND b.`email` <> ''
								ORDER BY a.`contact_order` ASC";
			$event_contacts	= $db->GetAll($query);
			if ($event_contacts) {
				$to_address_is_set		= false;
				
				$primary_contact		= array();
				$cc_contacts			= array();
				$cc_contacts_names		= array();
				$associated_faculty		= array();
				
				foreach ($event_contacts as $key => $event_contact) {
					$associated_faculty[$event_contact["proxy_id"]]	= $event_contact["firstname"]." ".$event_contact["lastname"];

					if (!(int) $key) {
						$primary_contact = array("proxy_id" => $event_contact["proxy_id"], "firstname" => $event_contact["firstname"], "lastname" => $event_contact["lastname"], "email" => $event_contact["email"]);
					} else {
						$cc_contacts[$event_contact["proxy_id"]]		= array("proxy_id" => $event_contact["proxy_id"], "firstname" => $event_contact["firstname"], "lastname" => $event_contact["lastname"], "email" => $event_contact["email"]);
						$cc_contacts_names[$event_contact["proxy_id"]]	= $event_contact["firstname"]." ".$event_contact["lastname"];
					}
					
					$query		= "	SELECT a.`assigned_to` AS `proxy_id`, b.`firstname`, b.`lastname`, b.`email`
									FROM `permissions` AS a
									LEFT JOIN `".AUTH_DATABASE."`.`user_data` AS b
									ON b.`id` = a.`assigned_to`
									WHERE a.`assigned_by` = ".$db->qstr($event_contact["proxy_id"])."
									AND a.`valid_from` <= ".$db->qstr(time())."
									AND a.`valid_until` > ".$db->qstr(time())."
									AND b.`id` IS NOT NULL
									AND b.`email` <> ''";
					$assistants	= $db->GetAll($query);
					if ($assistants) {
						foreach ($assistants as $assistant) {
							$cc_contacts[$assistant["proxy_id"]]		= array("proxy_id" => $assistant["proxy_id"], "firstname" => $assistant["firstname"], "lastname" => $assistant["lastname"], "email" => $assistant["email"]);
							$cc_contacts_names[$assistant["proxy_id"]]	= $assistant["firstname"]." ".$assistant["lastname"];
						}	
					}
				}

				$event_resources = fetch_event_resources_text($event["event_id"]);
				$search		= array(
								"%TO_FIRSTNAME%",
								"%TO_LASTNAME%",
								"%CC_FACULTY_HTML%",
								"%CC_FACULTY_TEXT%",
								"%EVENT_LINK%",
								"%EVENT_TITLE%",
								"%EVENT_PHASE%",
								"%EVENT_DATE%",
								"%EVENT_DURATION%",
								"%EVENT_LOCATION%",
								"%ASSOCIATED_FACULTY_HTML%",
								"%ASSOCIATED_FACULTY_TEXT%",
								"%RESOURCES_HTML%",
								"%RESOURCES_TEXT%",
								"%IMAGES_DIRECTORY%"
							);
				
				$replace	= array(
								$primary_contact["firstname"],
								$primary_contact["lastname"],
								((count($cc_contacts_names) > 0) ? "CC: ".implode(", ", $cc_contacts_names)."<br /><br />" : ""),
								((count($cc_contacts_names) > 0) ? "CC: ".implode(", ", $cc_contacts_names)."\n\n" : ""),
								ENTRADA_URL."/admin/events?section=content&id=".$event["event_id"],
								$event["event_title"],
								$event["event_phase"],
								date(DEFAULT_DATE_FORMAT, $event["event_start"]),
								$event["event_duration"]. " min",
								$event["event_location"],
								((count($associated_faculty) > 0) ? "<li>".implode("</li>\n<li>", $associated_faculty)."</li>" : ""),
								((count($associated_faculty) > 0) ? "   - ".implode("\n   - ", $associated_faculty)."\n" : ""),
								$event_resources["html"],
								$event_resources["text"],
								ENTRADA_URL."/templates/".$ENTRADA_ACTIVE_TEMPLATE."/images"
							);
				
				$mail->Subject	= str_replace(array("%EVENT_DATE%", "%SUBJECT_SUFFIX%"), array(date("Y-m-d", $event["event_start"]), (($notice["subject_suffix"] != "") ? " ".$notice["subject_suffix"] : "")), $NOTIFICATION_MESSAGE["subject"]);

				$mail->AltBody	= str_replace($search, $replace, $NOTIFICATION_MESSAGE["textbody"]);
				$mail->Body		= str_replace($search, $replace, $NOTIFICATION_MESSAGE["htmlbody"]);

// FOR TESTING:	$primary_contact["email"] = "simpson@qmed.ca";
//$primary_contact["email"] = "simpson@qmed.ca";
				if (in_array($primary_contact["email"], $NOTIFICATION_BLACKLIST)) {
					$to_address_is_set	= false;
				} else {
					$mail->AddAddress($primary_contact["email"], $primary_contact["firstname"]." ".$primary_contact["lastname"]);
					
					$to_address_is_set	= true;
				}
	
				if ((is_array($cc_contacts)) && (count($cc_contacts))) {
					foreach ($cc_contacts as $cc_contact) {
// FOR TESTING:	$cc_contact["email"] = "simpson@qmed.ca";
//$cc_contact["email"] = "simpson@qmed.ca";
						if (!in_array($cc_contact["email"], $NOTIFICATION_BLACKLIST)) {
							if (!$to_address_is_set) {
								$mail->AddAddress($cc_contact["email"], $cc_contact["firstname"]." ".$cc_contact["lastname"]);
								
								$to_address_is_set = true;
							} else {
								$mail->AddCC($cc_contact["email"], $cc_contact["firstname"]." ".$cc_contact["lastname"]);
							}
						}
					}
				}
				
				if ($to_address_is_set) {
					if ($mail->Send()) {
						application_log("reminder", "SUCCESS: Sent [".$notice["subject_suffix"]."] reminder to event_id [".$event["event_id"]."] contacts [".$primary_contact["firstname"]." ".$primary_contact["lastname"].((count($cc_contacts_names)) ? ", ".implode(", ", $cc_contacts_names) : "")."].");
					} else {
						application_log("reminder", "FAILURE: Unable to send [".$notice["subject_suffix"]."] reminder to event_id [".$event["event_id"]."] contacts.");
					}
				} else {
					application_log("reminder", "SKIPPED: Did not send [".$notice["subject_suffix"]."] reminder to event_id [".$event["event_id"]."] contacts.");
				}
	
				$mail->ClearAllRecipients();
				$mail->ClearAttachments(); 
			}
		}
	}
	
	return true;
}

if (is_array($NOTIFICATION_REMINDERS)) {
	foreach ($NOTIFICATION_REMINDERS as $key => $interval) {
		$events_starting	= strtotime($interval["strtotime_string"], $START_OF_TODAY);
		$events_ending		= strtotime("+1 day", $events_starting) - 1;
		
		application_log("reminder", "Sending notifications between ".date("r", $events_starting)." and ".date("r", $events_ending));
		
		notifications_send($events_starting, $events_ending, $interval);
	}
}
?>
