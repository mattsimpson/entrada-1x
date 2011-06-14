<?php

/**
 * Entrada [ http://www.entrada-project.org ]
 *
 * Cron job responsible for emailing occupants 30 days prior to their starting
 * inhabiting date
 *
 * @author Organisation: Queen's University
 * @author Unit: Faculty of Medicine
 * @author Developer: Brandon Thorn <brandon.thorn@queensu.ca>
 * @copyright Copyright 2011 Queen's Univerity. All Rights Reserved.
 *
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

$search = array("%LEARNER_NAME%", "%ACCOMMODATION_TITLE%", "%ACCOMMODATION_NUMBER%", "%ACCOMMODATION_STREET%", "%ACCOMMODATION_REGION%","%INHABITING_START%","%INHABITING_FINISH%","%ACCOMMODATION_CONTACT_NAME%","%ACCOMMODATION_CONTACT_INFO%","%APPLICATION_NAME%");


$mail = new Zend_Mail();
$mail->addHeader("X-Section", "Regional Education Notification System", true);
$mail->setFrom($AGENT_CONTACTS["agent-regionaled"]["email"], $AGENT_CONTACTS["agent-regionaled"]["name"]);


$query = "	SELECT c.`username`, 
				CONCAT_WS(' ', c.`firstname`, c.`lastname`) AS `fullname`, 
				c.`email`, 
				a.`apartment_title`, 
				a.`apartment_number`, 
				a.`apartment_address`, 
				d.`region_name`, 
				a.`apartment_province`,
				FROM_UNIXTIME(b.inhabiting_start) AS inhabiting_start,
				FROM_UNIXTIME(b.inhabiting_finish) AS inhabiting_finish
			FROM `".CLERKSHIP_DATABASE."`.`apartments` AS a 
			LEFT JOIN `".CLERKSHIP_DATABASE."`.`apartment_schedule` AS b 
			ON a.`apartment_id` = b.`apartment_id` 
			LEFT JOIN `".AUTH_DATABASE."`.`user_data` as c 
			ON b.`proxy_id` = c.`id` 
			LEFT JOIN `".CLERKSHIP_DATABASE."`.`regions` as d 
			ON a.`region_id` = d.`region_id` 
			WHERE b.`occupant_title` = '' 
			AND DATEDIFF(FROM_UNIXTIME(b.`inhabiting_start`), NOW()) = 30";
$occupants = $db->GetAll($query);
if ($occupants) {
	$email_body = file_get_contents(ENTRADA_ABSOLUTE . "/templates/" . DEFAULT_TEMPLATE . "/email/regionaled-learner-accommodation-notification.txt");
	
	foreach ($occupants as $occupant) {
		$mail->clearSubject();
		$mail->setSubject("Occupancy Reminder Notification: ".$occupant["apartment_title"]);
		$replace = array($occupant["fullname"], $occupant["apartment_title"], $occupant["apartment_number"], $occupant["apartment_address"], $occupant["region_name"],date("l F j Y @ g:i A",  strtotime($occupant["inhabiting_start"])),date("l F j Y @ g:i A",  strtotime($occupant["inhabiting_finish"])), $AGENT_CONTACTS["agent-regionaled"]["name"], $AGENT_CONTACTS["agent-regionaled"]["email"],"Entrada");
		$mail->setBodyText(str_replace($search, $replace, $email_body));
		$mail->clearRecipients();
		$mail->addTo($occupant["email"],$occupant["fullname"]);
		$mail->send();

	}
}