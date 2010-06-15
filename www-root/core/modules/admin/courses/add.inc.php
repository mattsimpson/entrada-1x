<?php
/**
 * Entrada [ http://www.entrada-project.org ]
 *
 * Entrada is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Entrada is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Entrada.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Organisation: Queen's University
 * @author Unit: School of Medicine
 * @author Developer: James Ellis <james.ellis@queensu.ca>
 * @copyright Copyright 2010 Queen's University. All Rights Reserved.
 *
 * @version $Id: add.inc.php 1169 2010-05-01 14:18:49Z simpson $
 */

if ((!defined("PARENT_INCLUDED")) || (!defined("IN_COURSES"))) {
	exit;
} elseif ((!isset($_SESSION["isAuthorized"])) || (!$_SESSION["isAuthorized"])) {
	header("Location: ".ENTRADA_URL);
	exit;
} elseif (!$ENTRADA_ACL->amIAllowed('course', 'create', false)) {
	$ONLOAD[]	= "setTimeout('window.location=\\'".ENTRADA_URL."/admin/".$MODULE."\\'', 15000)";

	$ERROR++;
	$ERRORSTR[]	= "Your account does not have the permissions required to use this feature of this module.<br /><br />If you believe you are receiving this message in error please contact <a href=\"mailto:".html_encode($AGENT_CONTACTS["administrator"]["email"])."\">".html_encode($AGENT_CONTACTS["administrator"]["name"])."</a> for assistance.";

	echo display_error();

	application_log("error", "Group [".$_SESSION["permissions"][$_SESSION[APPLICATION_IDENTIFIER]["tmp"]["proxy_id"]]["group"]."] and role [".$_SESSION["permissions"][$_SESSION[APPLICATION_IDENTIFIER]["tmp"]["proxy_id"]]["role"]."] do not have access to this module [".$MODULE."]");
} else {
	$BREADCRUMB[]	= array("url" => ENTRADA_URL."/admin/".$MODULE."?".replace_query(array("section" => "add")), "title" => "Adding Course");

	echo "<h1>Adding Course</h1>\n";

	// Error Checking
	switch($STEP) {
		case 2 :
			/**
			 * Non-required field "curriculum_type_id" / Curriculum Category
			 */
			if ((isset($_POST["curriculum_type_id"])) && ($curriculum_type_id = clean_input($_POST["curriculum_type_id"], array("int")))) {
				$PROCESSED["curriculum_type_id"] = $curriculum_type_id;
			} else {
				$PROCESSED["curriculum_type_id"] = 0;
			}

			/**
			 * Required field "course_name" / Course Name.
			 */
			if ((isset($_POST["course_name"])) && ($course_name = clean_input($_POST["course_name"], array("notags", "trim")))) {
				$PROCESSED["course_name"] = $course_name;
			} else {
				$ERROR++;
				$ERRORSTR[] = "The <strong>Course Name</strong> field is required.";
			}

			/**
			 * Required field "organisation_id" / Organisation Name.
			 */
			if ((isset($_POST["organisation_id"])) && ($organisation_id = clean_input($_POST["organisation_id"], array("int")))) {
				if ($ENTRADA_ACL->amIAllowed(new CourseResource(null, $organisation_id), 'create')) {
					$PROCESSED["organisation_id"] = $organisation_id;
				} else {
					$ERROR++;
					$ERRORSTR[] = "You do not have permission to add a course for this organisation. This error has been logged and will be investigated.";
					application_log("error", "Proxy id [".$_SESSION['details']['proxy_id']."] tried to create a course within an organisation [".$organisation_id."] they didn't have permissions on. ");
				}

			} else {
				$ERROR++;
				$ERRORSTR[] = "The <strong>Organisation Name</strong> field is required.";
			}

			/**
			 * Non-required field "course_code" / Course Code.
			 */
			if ((isset($_POST["course_code"])) && ($course_code = clean_input($_POST["course_code"], array("notags", "trim")))) {
				$PROCESSED["course_code"] = $course_code;
			} else {
				$ERROR++;
				$ERRORSTR[] = "The <strong>Course Code</strong> field is required and must be provided.";
			}

			/**
			 * Non-required field "director_id" / Course Director.
			 */
			if ((isset($_POST["director_id"])) && ($director_id = clean_input($_POST["director_id"], "int"))) {
				$PROCESSED["director_id"] = $director_id;
			} else {
				$PROCESSED["director_id"] = 0;
			}

			/**
			 * Non-required field "pcoord_id" .
			 */
			if ((isset($_POST["pcoord_id"])) && ($pcoord_id = clean_input($_POST["pcoord_id"], "int"))) {
				$PROCESSED["pcoord_id"] = $pcoord_id;
			} else {
				$PROCESSED["pcoord_id"] = 0;
			}

			/**
			 * Non-required field "evalrep_id".
			 */
			if ((isset($_POST["evalrep_id"])) && ($evalrep_id = clean_input($_POST["evalrep_id"], "int"))) {
				$PROCESSED["evalrep_id"] = $evalrep_id;
			} else {
				$PROCESSED["evalrep_id"] = 0;
			}

			/**
			 * Non-required field "studrep_id" .
			 */
			if ((isset($_POST["studrep_id"])) && ($studrep_id = clean_input($_POST["studrep_id"], "int"))) {
				$PROCESSED["studrep_id"] = $studrep_id ;
			} else {
				$PROCESSED["studrep_id"] = 0;
			}

			/**
			 * Check to see if notifications are enabled or not for events in this course.
			 */
			if ((isset($_POST["notifications"])) && (!clean_input($_POST["notifications"], "int"))) {
				$PROCESSED["notifications"] = 0;
			} else {
				$PROCESSED["notifications"] = 1;
			}

			if (isset($_POST["post_action"])) {
				switch($_POST["post_action"]) {
					case "content" :
						$_SESSION[APPLICATION_IDENTIFIER]["tmp"]["post_action"] = "content";
						break;
					case "new" :
						$_SESSION[APPLICATION_IDENTIFIER]["tmp"]["post_action"] = "new";
						break;
					case "index" :
					default :
						$_SESSION[APPLICATION_IDENTIFIER]["tmp"]["post_action"] = "index";
						break;
				}
			} else {
				$_SESSION[APPLICATION_IDENTIFIER]["tmp"]["post_action"] = "content";
			}
			
			$posted_objectives = array();
			
			if ((isset($_POST["primary_objectives"])) && ($objectives = $_POST["primary_objectives"]) && (count($objectives))) {
				$PRIMARY_OBJECTIVES = array();
				foreach ($objectives as $objective_key => $objective) {
					$PRIMARY_OBJECTIVES[] = clean_input($objective, "int");
					$posted_objectives["primary"][] = clean_input($objective, "int");
				}
			}

			if ((isset($_POST["secondary_objectives"])) && ($objectives = $_POST["secondary_objectives"]) && (count($objectives))) {
				$SECONDARY_OBJECTIVES = array();
				foreach ($objectives as $objective_key => $objective) {
					$SECONDARY_OBJECTIVES[] = clean_input($objective, "int");
					$posted_objectives["secondary"][] = clean_input($objective, "int");
				}
			}
			
			if (!$ERROR) {
				$PROCESSED["updated_date"]	= time();
				$PROCESSED["updated_by"]	= $_SESSION["details"]["id"];
				
				if ($db->AutoExecute("courses", $PROCESSED, "INSERT")) {
					if ($COURSE_ID = $db->Insert_Id()) {
						
						if ((isset($_POST["associated_director"])) && ($associated_directors = explode(",", $_POST["associated_director"])) && (@is_array($associated_directors)) && (@count($associated_directors))) {
							$order = 0;
							foreach($associated_directors as $proxy_id) {
								if ($proxy_id = clean_input($proxy_id, array("trim", "int"))) {
									if (!$db->AutoExecute("course_contacts", array("course_id" => $COURSE_ID, "proxy_id" => $proxy_id, "contact_type" => "director", "contact_order" => $order), "INSERT")) {
										$ERROR++;
										$ERRORSTR[] = "There was an error when trying to insert a &quot;Course Director&quot; into the system. The system administrator was informed of this error; please try again later.";
	
										application_log("error", "Unable to insert a new course_contact to the database when updating an event. Database said: ".$db->ErrorMsg());
									} else {
										$order++;	
									}
								}
							}
						}

						if ((isset($_POST["associated_coordinator"])) && ($associated_coordinators = explode(",", $_POST["associated_coordinator"])) && (@is_array($associated_coordinators)) && (@count($associated_coordinators))) {
							foreach($associated_coordinators as $proxy_id) {
								if ($proxy_id = clean_input($proxy_id, array("trim", "int"))) {
									if (!$db->AutoExecute("course_contacts", array("course_id" => $COURSE_ID, "proxy_id" => $proxy_id, "contact_type" => "ccoordinator"), "INSERT")) {
										$ERROR++;
										$ERRORSTR[] = "There was an error when trying to insert a &quot;Curriculum Coordinator&quot; into the system. The system administrator was informed of this error; please try again later.";
	
										application_log("error", "Unable to insert a new course_contact to the database when updating an event. Database said: ".$db->ErrorMsg());
									}
								}
							}
						}

						switch($_SESSION[APPLICATION_IDENTIFIER]["tmp"]["post_action"]) {
							case "content" :
								$url = ENTRADA_URL."/admin/".$MODULE."?section=content&id=".$COURSE_ID;
								$msg = "You will now be redirected to the course content page; this will happen <strong>automatically</strong> in 5 seconds or <a href=\"".$url."\" style=\"font-weight: bold\">click here</a> to continue.";
								break;
							case "new" :
								$url = ENTRADA_URL."/admin/".$MODULE."?section=add";
								$msg = "You will now be redirected to add a new course; this will happen <strong>automatically</strong> in 5 seconds or <a href=\"".$url."\" style=\"font-weight: bold\">click here</a> to continue.";
								break;
							case "index" :
							default :
								$url = ENTRADA_URL."/admin/".$MODULE;
								$msg = "You will now be redirected to the course index; this will happen <strong>automatically</strong> in 5 seconds or <a href=\"".$url."\" style=\"font-weight: bold\">click here</a> to continue.";
								break;
						}
							

						if (is_array($PRIMARY_OBJECTIVES) && count($PRIMARY_OBJECTIVES)) {
							foreach($PRIMARY_OBJECTIVES as $objective_id) {
								$db->Execute("INSERT INTO `course_objectives` SET `course_id` = ".$db->qstr($COURSE_ID).", `objective_id` = ".$db->qstr($objective_id).", `updated_date` = ".$db->qstr(time()).", `updated_by` = ".$db->qstr($_SESSION["details"]["id"]).", `importance` = '1'");
							}
						}
						if (is_array($SECONDARY_OBJECTIVES) && count($SECONDARY_OBJECTIVES)) {
							foreach($SECONDARY_OBJECTIVES as $objective_id) {
								$db->Execute("INSERT INTO `course_objectives` SET `course_id` = ".$db->qstr($COURSE_ID).", `objective_id` = ".$db->qstr($objective_id).", `updated_date` = ".$db->qstr(time()).", `updated_by` = ".$db->qstr($_SESSION["details"]["id"]).", `importance` = '2'");
							}
						}

						$SUCCESS++;
						$SUCCESSSTR[]	= "You have successfully added <strong>".html_encode($PROCESSED["course_name"])."</strong> to this system.<br /><br />".$msg;
						$ONLOAD[]		= "setTimeout('window.location=\\'".$url."\\'', 5000)";

						application_log("success", "New course [".$COURSE_ID."] added to the system.");
					}
				} else {
					$ERROR++;
					$ERRORSTR[] = "There was a problem inserting this course into the system. The system administrator was informed of this error; please try again later.";

					application_log("error", "There was an error updating a course. Database said: ".$db->ErrorMsg());
				}
			}

			if ($ERROR) {
				$STEP = 1;
			}
			break;
		case 1 :
		default :
			continue;
			break;
	}

	// Display Content
	switch($STEP) {
		case 2 :
			if ($SUCCESS) {
				echo display_success();
			}
			if ($NOTICE) {
				echo display_notice();
			}
			if ($ERROR) {
				echo display_error();
			}
			break;
		case 1 :
		default :
			require_once(ENTRADA_ABSOLUTE."/javascript/courses.js.php");
			
			$LASTUPDATED	= $course_details["updated_date"];

			$course_directors	= array();
			$curriculum_coordinators = array();
			$chosen_course_directors	= array();

			$query	= "	SELECT `".AUTH_DATABASE."`.`user_data`.`id` AS `proxy_id`, CONCAT_WS(', ', `".AUTH_DATABASE."`.`user_data`.`lastname`, `".AUTH_DATABASE."`.`user_data`.`firstname`) AS `fullname`, `".AUTH_DATABASE."`.`organisations`.`organisation_id`
						FROM `".AUTH_DATABASE."`.`user_data`
						LEFT JOIN `".AUTH_DATABASE."`.`user_access`
						ON `".AUTH_DATABASE."`.`user_access`.`user_id` = `".AUTH_DATABASE."`.`user_data`.`id`
						LEFT JOIN `".AUTH_DATABASE."`.`organisations`
						ON `".AUTH_DATABASE."`.`user_data`.`organisation_id` = `".AUTH_DATABASE."`.`organisations`.`organisation_id`
						WHERE `".AUTH_DATABASE."`.`user_access`.`group` = 'faculty'
						AND (`".AUTH_DATABASE."`.`user_access`.`role` = 'director' OR `".AUTH_DATABASE."`.`user_access`.`role` = 'admin')
						AND `".AUTH_DATABASE."`.`user_access`.`app_id` = '".AUTH_APP_ID."'
						AND `".AUTH_DATABASE."`.`user_access`.`account_active` = 'true'
						ORDER BY `fullname` ASC";
			$results	= ((USE_CACHE) ? $db->CacheGetAll(AUTH_CACHE_TIMEOUT, $query) : $db->GetAll($query));
			if ($results) {
				foreach($results as $result) {
					$course_directors[$result["proxy_id"]] = array('proxy_id'=>$result["proxy_id"], 'fullname'=>$result["fullname"], 'organisation_id'=>$result['organisation_id']);
				}
				$DIRECTOR_LIST = $course_directors;
			}
			
			$query	= "
							SELECT `".AUTH_DATABASE."`.`user_data`.`id` AS `proxy_id`, CONCAT_WS(', ', `".AUTH_DATABASE."`.`user_data`.`lastname`, `".AUTH_DATABASE."`.`user_data`.`firstname`) AS `fullname`, `".AUTH_DATABASE."`.`organisations`.`organisation_id`
						FROM `".AUTH_DATABASE."`.`user_data`
						LEFT JOIN `".AUTH_DATABASE."`.`user_access`
						ON `".AUTH_DATABASE."`.`user_access`.`user_id` = `".AUTH_DATABASE."`.`user_data`.`id`
							LEFT JOIN `".AUTH_DATABASE."`.`organisations`
							ON `".AUTH_DATABASE."`.`user_data`.`organisation_id` = `".AUTH_DATABASE."`.`organisations`.`organisation_id`
						WHERE `".AUTH_DATABASE."`.`user_access`.`group` = 'staff'
						AND `".AUTH_DATABASE."`.`user_access`.`role` = 'admin'
						AND `".AUTH_DATABASE."`.`user_access`.`app_id` = '".AUTH_APP_ID."'
						AND `".AUTH_DATABASE."`.`user_access`.`account_active` = 'true'
						ORDER BY `fullname` ASC";
			$results	= ((USE_CACHE) ? $db->CacheGetAll(AUTH_CACHE_TIMEOUT, $query) : $db->GetAll($query));
			if ($results) {
				foreach($results as $result) {
					$curriculum_coordinators[$result["proxy_id"]] = array('proxy_id'=>$result["proxy_id"], 'fullname'=>$result["fullname"], 'organisation_id'=>$result['organisation_id']);
				}
				$COORDINATOR_LIST = $curriculum_coordinators;
			}

			/**
			 * Non-required field "associated_faculty" / Associated Faculty (array of proxy ids).
			 * This is actually accomplished after the event is inserted below.
			 */
			if((isset($_POST["associated_director"]))) {
				$associated_director = explode(',',$_POST["associated_director"]);
				foreach($associated_director as $contact_order => $proxy_id) {
					if($proxy_id = clean_input($proxy_id, array("trim", "int"))) {
						$chosen_course_directors[(int) $contact_order] = $proxy_id;
					}
				}
			}
			
			if((isset($_POST["associated_coordinator"]))) {
				$associated_coordinator = explode(',',$_POST["associated_coordinator"]);
				foreach($associated_coordinator as $contact_order => $proxy_id) {
					if($proxy_id = clean_input($proxy_id, array("trim", "int"))) {
						$chosen_ccoordinators[] = $proxy_id;
					}
				}
			}
			// Compiles Program Coordinator list
			$programcoodinators = array();

			$query		= "	SELECT `".AUTH_DATABASE."`.`user_data`.`id` AS `proxy_id`, CONCAT_WS(', ', `".AUTH_DATABASE."`.`user_data`.`lastname`, `".AUTH_DATABASE."`.`user_data`.`firstname`) AS `fullname`,`".AUTH_DATABASE."`.`user_data`.`id`, `".AUTH_DATABASE."`.`organisations`.`organisation_title`
							FROM `".AUTH_DATABASE."`.`user_data`
							LEFT JOIN `".AUTH_DATABASE."`.`user_access`
							ON `".AUTH_DATABASE."`.`user_access`.`user_id` = `".AUTH_DATABASE."`.`user_data`.`id`
							LEFT JOIN `".AUTH_DATABASE."`.`organisations`
							ON `".AUTH_DATABASE."`.`user_data`.`organisation_id` = `".AUTH_DATABASE."`.`organisations`.`organisation_id`
							WHERE `".AUTH_DATABASE."`.`user_access`.`role` = 'pcoordinator'
							AND `".AUTH_DATABASE."`.`user_access`.`app_id` = '".AUTH_APP_ID."'
							AND `".AUTH_DATABASE."`.`user_access`.`account_active` = 'true'
							ORDER BY `fullname` ASC";
			$results	= ((USE_CACHE) ? $db->CacheGetAll(AUTH_CACHE_TIMEOUT, $query) : $db->GetAll($query));
			if ($results) {
				foreach($results as $result) {
					$programcoodinators[$result["proxy_id"]] = $result["fullname"]. ' (' . $result['organisation_title'].')';
				}
			}

			// Compiles Evaluation Representative (evalrep_id)  list
			$evaluationreps = array();

			$query		= "	SELECT `".AUTH_DATABASE."`.`user_data`.`id` AS `proxy_id`, CONCAT_WS(', ', `".AUTH_DATABASE."`.`user_data`.`lastname`, `".AUTH_DATABASE."`.`user_data`.`firstname`) AS `fullname`,`".AUTH_DATABASE."`.`user_data`.`id`, `".AUTH_DATABASE."`.`organisations`.`organisation_title`
							FROM `".AUTH_DATABASE."`.`user_data`
							LEFT JOIN `".AUTH_DATABASE."`.`user_access`
							ON `".AUTH_DATABASE."`.`user_access`.`user_id` = `".AUTH_DATABASE."`.`user_data`.`id`
							LEFT JOIN `".AUTH_DATABASE."`.`organisations`
							ON `".AUTH_DATABASE."`.`user_data`.`organisation_id` = `".AUTH_DATABASE."`.`organisations`.`organisation_id`
							WHERE `".AUTH_DATABASE."`.`user_access`.`group` = 'faculty'
							AND `".AUTH_DATABASE."`.`user_access`.`app_id` = '".AUTH_APP_ID."'
							AND `".AUTH_DATABASE."`.`user_access`.`account_active` = 'true'
							ORDER BY `fullname` ASC";
			$results	= ((USE_CACHE) ? $db->CacheGetAll(AUTH_CACHE_TIMEOUT, $query) : $db->GetAll($query));
			if ($results) {
				foreach($results as $result) {
					$evaluationreps[$result["proxy_id"]] = $result["fullname"] . ' (' . $result['organisation_title'].')';
				}
			}

			// Compiles Student Representative (evalrep_id)  list
			$studentreps = array();

			$query		= "	SELECT `".AUTH_DATABASE."`.`user_data`.`id` AS `proxy_id`, CONCAT_WS(', ', `".AUTH_DATABASE."`.`user_data`.`lastname`, `".AUTH_DATABASE."`.`user_data`.`firstname`) AS `fullname`,`".AUTH_DATABASE."`.`user_data`.`id`, `".AUTH_DATABASE."`.`organisations`.`organisation_title`
							FROM `".AUTH_DATABASE."`.`user_data`
							LEFT JOIN `".AUTH_DATABASE."`.`user_access`
							ON `".AUTH_DATABASE."`.`user_access`.`user_id` = `".AUTH_DATABASE."`.`user_data`.`id`
							LEFT JOIN `".AUTH_DATABASE."`.`organisations`
							ON `".AUTH_DATABASE."`.`user_data`.`organisation_id` = `".AUTH_DATABASE."`.`organisations`.`organisation_id`
							WHERE `".AUTH_DATABASE."`.`user_access`.`group` = 'student'
							AND `".AUTH_DATABASE."`.`user_access`.`app_id` = '".AUTH_APP_ID."'
							AND `".AUTH_DATABASE."`.`user_access`.`account_active` = 'true'
							ORDER BY `fullname` ASC";
			$results	= ((USE_CACHE) ? $db->CacheGetAll(AUTH_CACHE_TIMEOUT, $query) : $db->GetAll($query));
			if ($results) {
				foreach($results as $result) {
					$studentreps[$result["proxy_id"]] = $result["fullname"] . ' (' . $result['organisation_title'].')';
				}
			}

			if ($ERROR) {
				echo display_error();
			}
			?>
			<form action="<?php echo ENTRADA_URL; ?>/admin/<?php echo $MODULE; ?>?<?php echo replace_query(array("step" => 2)); ?>" method="post" onsubmit="$$('.PickList option').each(function (e) { e.selected = true; });">
			<h2 title="Course Details Section">Course Details</h2>
			<div id="course-details-section">
				<table style="width: 100%" cellspacing="0" cellpadding="2" border="0" summary="Adding Course Details">
				<colgroup>
					<col style="width: 3%" />
					<col style="width: 22%" />
					<col style="width: 75%" />
				</colgroup>
				<tbody>
					<tr>
						<td></td>
						<td><label for="organisation_id" class="form-required">Course Organisation</label></td>
						<td>
							<select id="organisation_id" name="organisation_id" style="width: 250px">
							<?php
							$query		= "SELECT `organisation_id`, `organisation_title` FROM `".AUTH_DATABASE."`.`organisations`";
							$results	= $db->GetAll($query);
							if ($results) {
								foreach($results as $result) {
									if ($ENTRADA_ACL->amIAllowed(new CourseResource(null, $result['organisation_id']), 'create')) {
										echo "<option value=\"".(int) $result["organisation_id"]."\"".(((isset($PROCESSED["organisation_id"])) && ($PROCESSED["organisation_id"] == $result["organisation_id"])) ? " selected=\"selected\"" : "").">".html_encode($result["organisation_title"])."</option>\n";
									}
								}
							}
							?>
							</select>
						</td>
					</tr>
					<tr>
						<td></td>
						<td style="vertical-align: top"><label for="curriculum_type_id" class="form-nrequired">Curriculum Category</label></td>
						<td>
							<select id="curriculum_type_id" name="curriculum_type_id" style="width: 250px">
							<option value="0"<?php echo (((!isset($PROCESSED["curriculum_type_id"])) || (!(int) $PROCESSED["curriculum_type_id"])) ? " selected=\"selected\"" : ""); ?>>- Select Curriculum Category -</option>
							<?php
							$query		= "SELECT * FROM `curriculum_lu_types` WHERE `curriculum_type_active` = '1' ORDER BY `curriculum_type_order` ASC";
							$results	= $db->GetAll($query);
							if ($results) {
								foreach($results as $result) {
									echo "<option value=\"".(int) $result["curriculum_type_id"]."\"".(((isset($PROCESSED["curriculum_type_id"])) && ($PROCESSED["curriculum_type_id"] == $result["curriculum_type_id"])) ? " selected=\"selected\"" : "").">".html_encode($result["curriculum_type_name"])."</option>\n";
								}
							}
							?>
							</select>
						</td>
					</tr>
					<tr>
						<td colspan="3">&nbsp;</td>
					</tr>
					<tr>
						<td></td>
						<td><label for="course_name" class="form-required">Course Name</label></td>
						<td><input type="text" id="course_name" name="course_name" value="<?php echo html_encode($PROCESSED["course_name"]); ?>" maxlength="64" style="width: 243px" /></td>
					</tr>
					<tr>
						<td></td>
						<td><label for="course_code" class="form-required">Course Code</label></td>
						<td><input type="text" id="course_code" name="course_code" value="<?php echo html_encode($PROCESSED["course_code"]); ?>" maxlength="16" style="width: 243px" /></td>
					</tr>
					<tr>
						<td colspan="3">&nbsp;</td>
					</tr>
					<tr>
						<td></td>
						<td style="vertical-align: top"><span class="form-nrequired">Reminder Notifications</span></td>
						<td style="vertical-align: top">
							<input type="radio" name="notifications" id="notification_on" value="1"<?php echo (((!isset($PROCESSED["notifications"])) || ((isset($PROCESSED["notifications"])) && ($PROCESSED["notifications"]))) ? " checked=\"checked\"" : ""); ?> /> <label for="notification_on">Send e-mail notifications to faculty for events under this course.</label><br />
							<input type="radio" name="notifications" id="notification_off" value="0"<?php echo (((isset($PROCESSED["notifications"])) && (!(int) $PROCESSED["notifications"])) ? " checked=\"checked\"" : ""); ?> /> <label for="notification_off"><strong>Do not</strong> send e-mail notifications to faculty for events under this course.</label>
						</td>
					</tr>
				</tbody>
				</table>
			</div>

			<?php
			
				$sidebar_html  = "<div style=\"margin: 2px 0px 10px 3px; font-size: 10px\">\n";
				$sidebar_html .= "	<div><img src=\"".ENTRADA_URL."/images/legend-primary-objective.gif\" width=\"14\" height=\"14\" alt=\"\" title=\"\" style=\"vertical-align: middle\" /> Primary Objective</div>\n";
				$sidebar_html .= "	<div><img src=\"".ENTRADA_URL."/images/legend-secondary-objective.gif\" width=\"14\" height=\"14\" alt=\"\" title=\"\" style=\"vertical-align: middle\" /> Secondary Objective</div>\n";
				$sidebar_html .= "</div>\n";
				
				new_sidebar_item("Objective Importance", $sidebar_html, "objective-legend", "open");
									
				$course_objectives = courses_fetch_objectives(array(0), 1, false, $posted_objectives);
				$HEAD[] = "<script type=\"text/javascript\" src=\"".ENTRADA_URL."/javascript/elementresizer.js\"></script>\n";
			?>
			<a name="course-objectives-section"></a>
			<h2 title="Course Objectives Section">Course Objectives</h2>
			<div id="course-objectives-section">
				<input type="hidden" id="objectives_head" name="course_objectives" value="" />
				<?php
				if (is_array($course_objectives["primary_ids"])) {
					foreach ($course_objectives["primary_ids"] as $objective_id) {
						echo "<input type=\"hidden\" class=\"primary_objectives\" id=\"primary_objective_".$objective_id."\" name=\"primary_objectives[]\" value=\"".$objective_id."\" />\n";
					}
				}
				if (is_array($course_objectives["secondary_ids"])) {
					foreach ($course_objectives["secondary_ids"] as $objective_id) {
						echo "<input type=\"hidden\" class=\"secondary_objectives\" id=\"secondary_objective_".$objective_id."\" name=\"secondary_objectives[]\" value=\"".$objective_id."\" />\n";
					}
				}
				?>
				<table style="width: 100%" cellspacing="0" cellpadding="2" border="0">
				<colgroup>
					<col width="3%" />
					<col width="22%" />
					<col width="75%" />
				</colgroup>
				<tbody>
					<tr>
						<td>&nbsp;</td>
						<td>
							<label for="objective_select" class="form-nrequired">Course Objectives</label>
						</td>
						<td>
							<select id="objective_select" onclick="showMultiSelect()">
							<option value="">- Select Competency -</option>
							<?php
							$objective_select = "";
							foreach ($course_objectives["objectives"] as $parent_id => $parent) {
								if ($parent["parent"] == 1) {
									echo "<optgroup label=\"".$parent["name"]."\">";
									foreach($course_objectives["objectives"] as $objective_id => $objective) {
										if ($objective["parent"] == $parent_id) {
											echo "<option value=\"id_".$objective_id."\">".$objective["name"]."</option>";
											foreach($course_objectives["objectives"] as $child_id => $child) {
												if ($child["parent"] == $objective_id) {
													if (array_search($child_id, $course_objectives["used_ids"]) !== false) {
														$checked = "checked=\"checked\"";
													} else {
														$checked = "";
													}
													$selectable_objectives[$child_id] = array("text" => $child["name"], "value" => $child_id, "checked" => $checked, "category" => true);
													foreach($course_objectives["objectives"] as $grandkid_id => $grandkid) {
														if ($grandkid["parent"] == $child_id) {
															if (array_search($grandkid_id, $course_objectives["used_ids"]) !== false) {
																$checked = "checked=\"checked\"";
															} else {
																$checked = "";
															}
															if ($grandkid["parent"] == $child_id) {
																$selectable_objectives[$grandkid_id] = array("text" => "<strong>".$grandkid["name"]."</strong><br />".$grandkid["description"], "value" => $grandkid_id, "checked" => $checked);
															}
														}
													}
												}
											}
											$objective_select .= course_objectives_multiple_select_options_checked("id_".$objective_id, $selectable_objectives, array("title" => "Please select program or curriculum objectives", "cancel" => true, "cancel_text" => "Close", "submit" => false, "width" => "550px"));
										}
										unset($selectable_objectives);
									}
									echo "\n</optgroup>";
								}
							}
							?>
							</select>
						</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>
							<span class="content-small">Select a Competency and a list of available course and curriculum objectives will be displayed to choose from. Once you have selected an objective, it will be placed in the list below and you may leave it as primary or change the importance to secondary.</span>
							<?php echo $objective_select; ?>
							<script type="text/javascript">
								var multiselect = [];
								var id;
								function showMultiSelect() {
									$$('select_multiple_container').invoke('hide');
									id = $F('objective_select');
									if (multiselect[id]) {
										$('objective_select').hide();
										multiselect[id].container.show();
										multiselect[id].container.down("input").activate();
									} else {
										if ($(id+'_options')) {
											$(id+'_options').addClassName('multiselect-processed');
											multiselect[id] = new Control.SelectMultiple('objectives_head',id+'_options',{
												checkboxSelector: 'table.select_multiple_table tr td input[type=checkbox]',
												nameSelector: 'table.select_multiple_table tr td.select_multiple_name label',
												resize: id+'_scroll',
												afterCheck: function(element) {
													if (element.checked) {
														addObjective(element);
													} else {
														removeObjective(element);
													}
												}
											});

											$(id+'_cancel').observe('click',function(event){
												this.container.hide();
												$('objective_select').show();
												$('objective_select').options.selectedIndex = 0;
												return false;
											}.bindAsEventListener(multiselect[id]));;

											$('objective_select').hide();
											multiselect[id].container.show();
											multiselect[id].container.down("input").activate();
										}
									}
									return false;
								}
							</script>
						</td>
					</tr>
					<tr>
						<td colspan="3">&nbsp;</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td colspan="2">
							<div id="objectives_list">
							<?php echo course_objectives_in_list($course_objectives["objectives"], 1, true); ?>
							</div>
						</td>
					</tr>
				</tbody>
				</table>
			</div>
			<h2 title="Course Contacts Section">Course Contacts</h2>
			<div id="course-contacts-section">
				<table style="width: 100%" cellspacing="0" cellpadding="2" border="0" summary="Editing Course Contacts">
				<colgroup>
					<col style="width: 3%" />
					<col style="width: 22%" />
					<col style="width: 75%" />
				</colgroup>
				<tbody>
					<tr>
						<td></td>
						<td style="vertical-align: top"><label for="director_name" class="form-nrequired">Course Directors</label></td>
						<td>
							<div style="position: relative;">
								<script type="text/javascript">
								var sortables = new Array();
								function updateOrder(type) {
									$('associated_'+type).value = Sortable.sequence(type+'_list');
								}
								function addItem(type) {
									if (($(type+'_id') != null) && ($(type+'_id').value != '') && ($(type+'_'+$(type+'_id').value) == null)) {
										var li = new Element('li', {'class':'community', 'id':type+'_'+$(type+'_id').value, 'style':'cursor: move;'}).update($(type+'_name').value);
										$(type+'_name').value = '';
										li.insert({bottom: '<img src=\"<?php echo ENTRADA_URL; ?>/images/action-delete.gif\" class=\"list-cancel-image\" onclick=\"removeItem(\''+$(type+'_id').value+'\', \''+type+'\')\" />'});
										$(type+'_id').value	= '';
										$(type+'_list').appendChild(li);
										sortables[type] = Sortable.destroy($(type+'_list'));
										Sortable.create(type+'_list', {onUpdate : function(){updateOrder(type);}});
										updateOrder(type);
									} else if ($(type+'_'+$(type+'_id').value) != null) {
										alert('Important: Each user may only be added once.');
										$(type+'_id').value = '';
										$(type+'_name').value = '';
										return false;
									} else if ($(type+'_name').value != '' && $(type+'_name').value != null) {
										alert('Important: When you see the correct name pop-up in the list as you type, make sure you select the name with your mouse, do not press the Enter button.');
										return false;
									} else {
										return false;
									}
								}
										
								function addItemNoError(type) {
									if (($(type+'_id') != null) && ($(type+'_id').value != '') && ($(type+'_'+$(type+'_id').value) == null)) {
										addItem(type);
									}
								}
				
								function copyItem(type) {
									if (($(type+'_name') != null) && ($(type+'_ref') != null)) {
										$(type+'_ref').value = $(type+'_name').value;
									}
				
									return true;
								}
				
								function checkItem(type) {
									if (($(type+'_name') != null) && ($(type+'_ref') != null) && ($(type+'_id') != null)) {
										if ($(type+'_name').value != $(type+'_ref').value) {
											$(type+'_id').value = '';
										}
									}
				
									return true;
								}
				
								function removeItem(id, type) {
									if ($(type+'_'+id)) {
										$(type+'_'+id).remove();
										Sortable.destroy($(type+'_list'));
										Sortable.create(type+'_list', {onUpdate : function (type) {updateOrder(type)}});
										updateOrder(type);
									}
								}
				
								function selectItem(id, type) {
									if ((id != null) && ($(type+'_id') != null)) {
										$(type+'_id').value = id;
									}
								}
								
								</script>
								<input type="text" id="director_name" name="fullname" size="30" autocomplete="off" style="width: 203px; vertical-align: middle" onkeyup="checkItem('director')" onblur="addItemNoError('director')" />
								<script>
									$('director_name').observe('keypress', function(event){
									    if (event.keyCode == Event.KEY_RETURN) {
									        addItem('director');
									        Event.stop(event);
									    }
									});
								</script>
								<?php
								$ONLOAD[] = "Sortable.create('director_list', {onUpdate : function() {updateOrder('director')}})";
								$ONLOAD[] = "$('associated_director').value = Sortable.sequence('director_list')";
								?>
								<div class="autocomplete" id="director_name_auto_complete"></div><script type="text/javascript">new Ajax.Autocompleter('director_name', 'director_name_auto_complete', '<?php echo ENTRADA_RELATIVE; ?>/api/personnel.api.php?type=director', {frequency: 0.2, minChars: 2, afterUpdateElement: function (text, li) {selectItem(li.id, 'director'); copyItem('director');}});</script>
								<input type="hidden" id="associated_director" name="associated_director" />
								<!-- <ul class="page-action" style="display: inline;"><li><a onclick="addItem('director');" style="cursor: pointer;">Add Director</a></li></ul><br/> -->
								<input type="button" class="button-sm" onclick="addItem('director');" value="Add" style="vertical-align: middle" />
								<span class="content-small">(<strong>Example:</strong> <?php echo html_encode($_SESSION["details"]["lastname"].", ".$_SESSION["details"]["firstname"]); ?>)</span>
								<ul id="director_list" class="menu" style="margin-top: 15px">
									<?php
									if (is_array($chosen_course_directors) && count($chosen_course_directors)) {
										foreach ($chosen_course_directors as $director) {
											if ((array_key_exists($director, $DIRECTOR_LIST)) && is_array($DIRECTOR_LIST[$director])) {
												?>
													<li class="community" id="director_<?php echo $DIRECTOR_LIST[$director]["proxy_id"]; ?>" style="cursor: move;"><?php echo $DIRECTOR_LIST[$director]["fullname"]; ?><img src="<?php echo ENTRADA_URL; ?>/images/action-delete.gif" class="list-cancel-image" onclick="removeItem('<?php echo $DIRECTOR_LIST[$director]["proxy_id"]; ?>', 'director');"/></li>								
												<?php
											}
										}
									}
									?>
								</ul>
								<input type="hidden" id="director_ref" name="director_ref" value="" />
								<input type="hidden" id="director_id" name="director_id" value="" />
							</div>
						</td>
					</tr>
					<tr>
						<td colspan="3">&nbsp;</td>
					</tr>
					<tr>
						<td></td>
						<td style="vertical-align: top"><label for="coordinator_name" class="form-nrequired">Curriculum Coordinators</label></td>
						<td>
							<div style="position: relative;">
								<input type="text" id="coordinator_name" name="fullname" size="30" autocomplete="off" style="width: 203px; vertical-align: middle" onkeyup="checkItem('coordinator')" onblur="addItemNoError('coordinator')" />
								<script>
									$('coordinator_name').observe('keypress', function(event){
									    if (event.keyCode == Event.KEY_RETURN) {
									        addItem('coordinator');
									        Event.stop(event);
									    }
									});
								</script>
								<?php
								$ONLOAD[] = "Sortable.create('coordinator_list', {onUpdate : function() {updateOrder('coordinator')}})";
								$ONLOAD[] = "$('associated_coordinator').value = Sortable.sequence('coordinator_list')";
								?>
								<div class="autocomplete" id="coordinator_name_auto_complete"></div><script type="text/javascript">new Ajax.Autocompleter('coordinator_name', 'coordinator_name_auto_complete', '<?php echo ENTRADA_RELATIVE; ?>/api/personnel.api.php?type=coordinator', {frequency: 0.2, minChars: 2, afterUpdateElement: function (text, li) {selectItem(li.id, 'coordinator'); copyItem('coordinator');}});</script>
								<input type="hidden" id="associated_coordinator" name="associated_coordinator" />
								<!-- <ul class="page-action" style="display: inline;"><li><a onclick="addItem('coordinator');" style="cursor: pointer;">Add Coordinator</a></li></ul><br/> -->
								<input type="button" class="button-sm" onclick="addItem('coordinator');" value="Add" style="vertical-align: middle" />
								<span class="content-small">(<strong>Example:</strong> <?php echo html_encode($_SESSION["details"]["lastname"].", ".$_SESSION["details"]["firstname"]); ?>)</span>
								<ul id="coordinator_list" class="menu" style="margin-top: 15px">
									<?php
									if (is_array($chosen_ccoordinators) && count($chosen_ccoordinators)) {
										foreach ($chosen_ccoordinators as $coordinator) {
											if ((array_key_exists($coordinator, $COORDINATOR_LIST)) && is_array($COORDINATOR_LIST[$coordinator])) {
												?>
													<li class="community" id="coordinator_<?php echo $COORDINATOR_LIST[$coordinator]["proxy_id"]; ?>" style="cursor: move;"><?php echo $COORDINATOR_LIST[$coordinator]["fullname"]; ?><img src="<?php echo ENTRADA_URL; ?>/images/action-delete.gif" class="list-cancel-image" onclick="removeItem('<?php echo $COORDINATOR_LIST[$coordinator]["proxy_id"]; ?>', 'coordinator');"/></li>								
												<?php
											}
										}
									}
									?>
								</ul>
								<input type="hidden" id="coordinator_ref" name="coordinator_ref" value="" />
								<input type="hidden" id="coordinator_id" name="coordinator_id" value="" />
							</div>
						</td>
					</tr>
					<tr>
						<td colspan="3">&nbsp;</td>
					</tr>
					<!-- Lising the Program Coordinator for the selected coures -->
					<tr>
						<td></td>
						<td><label for="programcoodinator_id" class="form-nrequired">Program Coordinator</label></td>
						<td>
						<?php
							if ((is_array($programcoodinators)) && (count($programcoodinators))) {
								echo "<select id=\"pcoord_id\" name=\"pcoord_id\" style=\"width: 95%\">\n";
								echo "<option value=\"\"".((!isset($PROCESSED["pcoord_id"])) ? " selected=\"selected\"" : "").">-- To Be Announced --</option>\n";
								foreach($programcoodinators as $proxy_id => $fullname) {
									echo "<option value=\"".(int) $proxy_id."\"".(($PROCESSED["pcoord_id"] == $proxy_id) ? " selected=\"selected\"" : "").">".html_encode($fullname)."</option>\n";
								}
								echo "</select>\n";
							} else {
								echo "<input type=\"hidden\" id=\"pcoord_id\" name=\"pcoord_id\" value=\"0\" />\n";
								echo "Program Coordinator Information Not Available\n";
							}
							?>
						</td>
					</tr>
					<tr>
						<td colspan="2">&nbsp;</td>
						<td>
							<span class="content-small"><strong>Important:</strong> Program Coordinators will be able to add, edit or remove learning events in this course.</span>
						</td>
					</tr>

					<tr>
						<td colspan="3">&nbsp;</td>
					</tr>

					<!-- Lising the Evaluation Rep for the selected coures -->
					<tr>
						<td></td>
						<td><label for="evaluationrep_id" class="form-nrequired">Evaluation Rep.</label></td>
						<td>
							<?php
							if ((is_array($evaluationreps)) && (count($evaluationreps))) {
								echo "<select id=\"evalrep_id\" name=\"evalrep_id\" style=\"width: 95%\">\n";
								echo "<option value=\"\"".((!isset($PROCESSED["evalrep_id"])) ? " selected=\"selected\"" : "").">-- To Be Announced --</option>\n";
								foreach($evaluationreps as $proxy_id => $fullname) {
									echo "<option value=\"".(int) $proxy_id."\"".(($PROCESSED["evalrep_id"] == $proxy_id) ? " selected=\"selected\"" : "").">".html_encode($fullname)."</option>\n";
								}
								echo "</select>\n";
							} else {
								echo "<input type=\"hidden\" id=\"evalrep_id\" name=\"evalrep_id\" value=\"0\" />\n";
								echo "Evaluation Representative Information Not Available\n";
							}
							?>
						</td>
					</tr>

					<!-- Lising the Student Rep for the selected coures -->
					<tr>
						<td></td>
						<td><label for="studentrep_id" class="form-nrequired">Student Rep.</label></td>
						<td>
							<?php
							if ((is_array($studentreps)) && (count($studentreps))) {
								echo "<select id=\"studrep_id\" name=\"studrep_id\" style=\"width: 95%\">\n";
								echo "<option value=\"\"".((!isset($PROCESSED["studrep_id"])) ? " selected=\"selected\"" : "").">-- To Be Announced --</option>\n";
								foreach($studentreps as $proxy_id => $fullname) {
									echo "<option value=\"".(int) $proxy_id."\"".(($PROCESSED["studrep_id"] == $proxy_id) ? " selected=\"selected\"" : "").">".html_encode($fullname)."</option>\n";
								}
								echo "</select>\n";
							} else {
								echo "<input type=\"hidden\" id=\"studrep_id\" name=\"studrep_id\" value=\"0\" />\n";
								echo "Student Representative Information Not Available\n";
							}
						?>
						</td>
					</tr>
				</tbody>
				</table>
			</div>
				
			<div style="padding-top: 25px">
				<table style="width: 100%" cellspacing="0" cellpadding="0" border="0">
				<tr>
					<td style="width: 25%; text-align: left">
						<input type="button" class="button" value="Cancel" onclick="window.location='<?php echo ENTRADA_URL; ?>/admin/<?php echo $MODULE; ?>'" />
					</td>
					<td style="width: 75%; text-align: right; vertical-align: middle">
						<span class="content-small">After saving:</span>
						<select id="post_action" name="post_action">
						<option value="content"<?php echo (((!isset($_SESSION[APPLICATION_IDENTIFIER]["tmp"]["post_action"])) || ($_SESSION[APPLICATION_IDENTIFIER]["tmp"]["post_action"] == "content")) ? " selected=\"selected\"" : ""); ?>>Add content to course</option>
						<option value="new"<?php echo (($_SESSION[APPLICATION_IDENTIFIER]["tmp"]["post_action"] == "new") ? " selected=\"selected\"" : ""); ?>>Add another course</option>
						<option value="index"<?php echo (($_SESSION[APPLICATION_IDENTIFIER]["tmp"]["post_action"] == "index") ? " selected=\"selected\"" : ""); ?>>Return to course list</option>
						</select>
						<input type="submit" class="button" value="Save" />
					</td>
				</tr>
				</table>
			</div>
			</form>
			<?php
			break;
	}
}
?>