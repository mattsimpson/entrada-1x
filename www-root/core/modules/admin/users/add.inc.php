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
 * Allows administrators to add new users to the entrada_auth.user_data table.
 *
 * @author Organisation: Queen's University
 * @author Unit: School of Medicine
 * @author Developer: Matt Simpson <matt.simpson@queensu.ca>
 * @copyright Copyright 2010 Queen's University. All Rights Reserved.
 *
 * @version $Id: add.inc.php 1169 2010-05-01 14:18:49Z simpson $
*/

if ((!defined("PARENT_INCLUDED")) || (!defined("IN_USERS"))) {
	exit;
} elseif ((!isset($_SESSION["isAuthorized"])) || (!$_SESSION["isAuthorized"])) {
	header("Location: ".ENTRADA_URL);
	exit;
} elseif (!$ENTRADA_ACL->amIAllowed("user", "create", false)) {
	$ERROR++;
	$ERRORSTR[]	= "Your account does not have the permissions required to use this feature of this module.<br /><br />If you believe you are receiving this message in error please contact <a href=\"mailto:".html_encode($AGENT_CONTACTS["administrator"]["email"])."\">".html_encode($AGENT_CONTACTS["administrator"]["name"])."</a> for assistance.";

	echo display_error();

	application_log("error", "Group [".$_SESSION["permissions"][$_SESSION[APPLICATION_IDENTIFIER]["tmp"]["proxy_id"]]["group"]."] and role [".$_SESSION["permissions"][$_SESSION[APPLICATION_IDENTIFIER]["tmp"]["proxy_id"]]["role"]."] does not have access to this module [".$MODULE."]");
} else {
	$BREADCRUMB[] = array("url" => ENTRADA_URL."/admin/users?".replace_query(array("section" => "add")), "title" => "Adding User");
	
	$PROCESSED_ACCESS = array();
	$PROCESSED_ACCESS["app_id"] = AUTH_APP_ID;
	$PROCESSED_DEPARTMENTS = array();

	echo "<h1>Adding User</h1>\n";

	// Error Checking
	switch ($STEP) {
		case 2 :
			$permissions_only	= false;

			/**
			 * Required field "group" / Account Type (Group).
			 * Required field "role" / Account Type (Role).
			 */
			if ((isset($_POST["group"])) && (isset($SYSTEM_GROUPS[$group = clean_input($_POST["group"], "credentials")]))) {
				$PROCESSED_ACCESS["group"] = $group;

				if ((isset($_POST["role"])) && (@in_array($role = clean_input($_POST["role"], "credentials"), $SYSTEM_GROUPS[$PROCESSED_ACCESS["group"]]))) {
					$PROCESSED_ACCESS["role"] = $role;
				} else {
					$ERROR++;
					$ERRORSTR[] = "You must provide a valid	Account Type &gt; Role which this persons account will live under.";
				}
			} else {
				$ERROR++;
				$ERRORSTR[] = "You must provide a valid	Account Type &gt; Group which this persons account will live under.";
			}

			/*
			 * Required field "account_active" / Account Status.
			 */
			if ((isset($_POST["account_active"])) && ($_POST["account_active"] == "true")) {
				$PROCESSED_ACCESS["account_active"] = "true";
			} else {
				$PROCESSED_ACCESS["account_active"] = "false";
			}

			/**
			 * Required field "access_starts" / Access Start (validated through validate_calendar function).
			 * Non-required field "access_finish" / Access Finish (validated through validate_calendar function).
			 */
			$access_date = validate_calendar("access", true, false);
			if ((isset($access_date["start"])) && ((int) $access_date["start"])) {
				$PROCESSED_ACCESS["access_starts"] = (int) $access_date["start"];
			}

			if ((isset($access_date["finish"])) && ((int) $access_date["finish"])) {
				$PROCESSED_ACCESS["access_expires"] = (int) $access_date["finish"];
			} else {
				$PROCESSED_ACCESS["access_expires"] = 0;
			}

			/**
			 * Non-required (although highly recommended) field for staff / student number.
			 */
			if ((isset($_POST["number"])) && ($number = clean_input($_POST["number"], array("trim", "int")))) {
				$query = "SELECT * FROM `".AUTH_DATABASE."`.`user_data` WHERE `number` = ".$db->qstr($number);
				$result	= $db->GetRow($query);
				if ($result) {
					$query = "SELECT * FROM `".AUTH_DATABASE."`.`user_access` WHERE `user_id` = ".$db->qstr($result["id"])." AND `app_id` = ".$db->qstr(AUTH_APP_ID);
					$sresult = $db->GetRow($query);
					if ($sresult) {
						$ERROR++;
						$ERRORSTR[] = "The Staff / Student number that you are trying to add already exists in the system, and already has access to this application under the username <strong>".$result["username"]."</strong> [<a href=\"mailto:".$result["email"]."\">".$result["email"]."</a>].";
					} else {
						if ((isset($_POST["username"])) && ($username = clean_input($_POST["username"], "credentials")) && ($username != $result["username"])) {
							$PROCESSED["number"]	= $number;
							$PROCESSED["username"]	= $username;

							$ERROR++;
							$ERRORSTR[] = "The Staff / Student number that you have provided already exists in the system, but belongs to a different username (<strong>".html_encode($result["username"])."</strong>).";
						} else {
							/**
							 * Just add permissions for this account.
							 */
							$permissions_only = true;
							$PROCESSED_ACCESS["user_id"] = (int) $result["id"];

							$PROCESSED = $result;
						}
					}
				} else {
					$PROCESSED["number"] = $number;
				}
			} else {
				$NOTICE++;
				$NOTICESTR[] = "There was no faculty, staff or student number attached to this profile. If this user is a affiliated with the University, please make sure you add this information.";

				$PROCESSED["number"] = 0;
			}

			/**
			 * If this user already exists, and permissions just need to be set, then do not continue into
			 * this section.
			 */
			if (!$permissions_only) {
				/**
				 * Required field "username" / Username.
				 */
				if ((isset($_POST["username"])) && ($username = clean_input($_POST["username"], "credentials"))) {
					$query = "SELECT * FROM `".AUTH_DATABASE."`.`user_data` WHERE `username` = ".$db->qstr($username);
					$result	= $db->GetRow($query);
					if ($result) {
						$query		= "SELECT * FROM `".AUTH_DATABASE."`.`user_access` WHERE `user_id` = ".$db->qstr($result["id"])." AND `app_id` = ".$db->qstr(AUTH_APP_ID);
						$sresult	= $db->GetRow($query);
						if ($sresult) {
							$ERROR++;
							$ERRORSTR[] = "The username that you are trying to add already exists in the system, and already has access to this application under the staff / student number <strong>".$result["number"]."</strong> [<a href=\"mailto:".$result["email"]."\">".$result["email"]."</a>]";
						} else {
							if ((isset($_POST["number"])) && ($number = clean_input($_POST["number"], array("trim", "int"))) && ($number != $result["number"])) {
								$PROCESSED["number"]	= $number;
								$PROCESSED["username"]	= $username;

								$ERROR++;
								$ERRORSTR[] = "The username that you have provided already exists in the system, but belongs to a different Staff / Student number (<strong>".html_encode($result["number"])."</strong>).";
							} else {
								$permissions_only				= true;
								$PROCESSED_ACCESS["user_id"]	= (int) $result["id"];

								$PROCESSED						= $result;

								unset($NOTICE, $NOTICESTR);
							}
						}
					} else {
						if ((strlen($username) >= 3) && (strlen($username) <= 24)) {
							$PROCESSED["username"] = $username;
						} else {
							$ERROR++;
							$ERRORSTR[] = "The username field must be between 3 and 24 characters.";
						}
					}
				} else {
					$ERROR++;
					$ERRORSTR[] = "You must provide a valid username for this user to login with. We suggest that you use their University NetID if at all possible.";
				}

				if ((!$permissions_only) && (!$ERROR)) {
				/**
				 * Required field "password" / Password.
				 */
					if ((isset($_POST["password"])) && ($password = clean_input($_POST["password"], "trim"))) {
						if ((strlen($password) >= 6) && (strlen($password) <= 24)) {
							$PROCESSED["password"] = $password;
						} else {
							$ERROR++;
							$ERRORSTR[] = "The password field must be between 6 and 24 characters.";
						}
					} else {
						$ERROR++;
						$ERRORSTR[] = "You must provide a valid password for this user to login with.";
					}

					/**
					 * Non-required field "prefix" / Prefix.
					 */
					if ((isset($_POST["prefix"])) && (@in_array($prefix = clean_input($_POST["prefix"], "trim"), $PROFILE_NAME_PREFIX))) {
						$PROCESSED["prefix"] = $prefix;
					} else {
						$PROCESSED["prefix"] = "";
					}

					/**
					 * Non-required field "office_hours" / Office Hours.
					 */
					if ((isset($_POST["office_hours"])) && ($office_hours = clean_input($_POST["office_hours"], array("notags","encode", "trim")))) {
						$PROCESSED["office_hours"] = ((strlen($office_hours) > 100) ? substr($office_hours, 0, 97)."..." : $office_hours);
					} else {
						$PROCESSED["office_hours"] = "";
					}
						
					/**
					 * Required field "firstname" / Firstname.
					 */
					if ((isset($_POST["firstname"])) && ($firstname = clean_input($_POST["firstname"], "trim"))) {
						$PROCESSED["firstname"] = $firstname;
					} else {
						$ERROR++;
						$ERRORSTR[] = "The firstname of the user is a required field.";
					}

					/**
					 * Required field "lastname" / Lastname.
					 */
					if ((isset($_POST["lastname"])) && ($lastname = clean_input($_POST["lastname"], "trim"))) {
						$PROCESSED["lastname"] = $lastname;
					} else {
						$ERROR++;
						$ERRORSTR[] = "The lastname of the user is a required field.";
					}

					/**
					 * Required field "email" / Primary E-Mail.
					 */
					if ((isset($_POST["email"])) && ($email = clean_input($_POST["email"], "trim", "lower"))) {
						if (@valid_address($email)) {
							$query	= "SELECT * FROM `".AUTH_DATABASE."`.`user_data` WHERE `email` = ".$db->qstr($email);
							$result	= $db->GetRow($query);
							if ($result) {
								$ERROR++;
								$ERRORSTR[] = "The e-mail address <strong>".html_encode($email)."</strong> already exists in the system for username <strong>".html_encode($result["username"])."</strong>. Please provide a unique e-mail address for this user.";
							} else {
								$PROCESSED["email"] = $email;
							}
						} else {
							$ERROR++;
							$ERRORSTR[] = "The primary e-mail address you have provided is invalid. Please make sure that you provide a properly formatted e-mail address.";
						}
					} else {
						$ERROR++;
						$ERRORSTR[] = "The primary e-mail address is a required field.";
					}

					/**
					 * Non-required field "email_alt" / Alternative E-Mail.
					 */
					if ((isset($_POST["email_alt"])) && ($email_alt = clean_input($_POST["email_alt"], "trim", "lower"))) {
						if (@valid_address($email_alt)) {
							$PROCESSED["email_alt"] = $email_alt;
						} else {
							$ERROR++;
							$ERRORSTR[] = "The alternative e-mail address you have provided is invalid. Please make sure that you provide a properly formatted e-mail address or leave this field empty if you do not wish to display one.";
						}
					} else {
						$PROCESSED["email_alt"] = "";
					}

					/**
					 * Non-required field "telephone" / Telephone Number.
					 */
					if ((isset($_POST["telephone"])) && ($telephone = clean_input($_POST["telephone"], "trim")) && (strlen($telephone) >= 10) && (strlen($telephone) <= 25)) {
						$PROCESSED["telephone"] = $telephone;
					} else {
						$PROCESSED["telephone"] = "";
					}

					/**
					 * Non-required field "fax" / Fax Number.
					 */
					if ((isset($_POST["fax"])) && ($fax = clean_input($_POST["fax"], "trim")) && (strlen($fax) >= 10) && (strlen($fax) <= 25)) {
						$PROCESSED["fax"] = $fax;
					} else {
						$PROCESSED["fax"] = "";
					}

					/**
					 * Non-required field "address" / Address.
					 */
					if ((isset($_POST["address"])) && ($address = clean_input($_POST["address"], array("trim", "ucwords"))) && (strlen($address) >= 6) && (strlen($address) <= 255)) {
						$PROCESSED["address"] = $address;
					} else {
						$PROCESSED["address"] = "";
					}

					/**
					 * Non-required field "city" / City.
					 */
					if ((isset($_POST["city"])) && ($city = clean_input($_POST["city"], array("trim", "ucwords"))) && (strlen($city) >= 3) && (strlen($city) <= 35)) {
						$PROCESSED["city"] = $city;
					} else {
						$PROCESSED["city"] = "";
					}

		

					/**
					 * Non-required field "postcode" / Postal Code.
					 */
					if ((isset($_POST["postcode"])) && ($postcode = clean_input($_POST["postcode"], array("trim", "uppercase"))) && (strlen($postcode) >= 5) && (strlen($postcode) <= 12)) {
						$PROCESSED["postcode"] = $postcode;
					} else {
						$PROCESSED["postcode"] = "";
					}

					if ((isset($_POST["country_id"])) && ($tmp_input = clean_input($_POST["country_id"], "int"))) {
						$query = "SELECT * FROM `global_lu_countries` WHERE `countries_id` = ".$db->qstr($tmp_input);
						$result = $db->GetRow($query);
						if ($result) {
							$PROCESSED["country_id"] = $tmp_input;
						} else {
							$ERROR++;
							$ERRORSTR[] = "The selected country does not exist in our countries database. Please select a valid country.";
		
							application_log("error", "Unknown countries_id [".$tmp_input."] was selected. Database said: ".$db->ErrorMsg());
						}
					} else {
						$ERROR++;
						$ERRORSTR[]	= "You must select a country.";
					}
		
					if ((isset($_POST["prov_state"])) && ($tmp_input = clean_input($_POST["prov_state"], array("trim", "notags")))) {
						$PROCESSED["province_id"] = 0;
						$PROCESSED["province"] = "";
		
						if (ctype_digit($tmp_input) && ($tmp_input = (int) $tmp_input)) {
							if ($PROCESSED["country_id"]) {
								$query = "SELECT * FROM `global_lu_provinces` WHERE `province_id` = ".$db->qstr($tmp_input)." AND `country_id` = ".$db->qstr($PROCESSED["country_id"]);
								$result = $db->GetRow($query);
								if (!$result) {
									$ERROR++;
									$ERRORSTR[] = "The province / state you have selected does not appear to exist in our database. Please selected a valid province / state.";
								}
							}
		
							$PROCESSED["province_id"] = $tmp_input;
						} else {
							$PROCESSED["province"] = $tmp_input;
						}
		
						$PROCESSED["prov_state"] = ($PROCESSED["province_id"] ? $PROCESSED["province_id"] : ($PROCESSED["province"] ? $PROCESSED["province"] : ""));
					}
					
					/**
					 * Non-required field "notes" / General Comments.
					 */
					if ((isset($_POST["notes"])) && ($notes = clean_input($_POST["notes"], array("trim", "notags")))) {
						$PROCESSED["notes"] = $notes;
					} else {
						$PROCESSED["notes"] = "";
					}

					/**
					 * Required field "organisation_id" / Organisation Name.
					 */
					if ((isset($_POST["organisation_id"])) && ($organisation_id = clean_input($_POST["organisation_id"], array("int")))) {
						if ($ENTRADA_ACL->amIAllowed('resourceorganisation'.$organisation_id, 'create')) {
							$PROCESSED["organisation_id"] = $organisation_id;
						} else {
							$ERROR++;
							$ERRORSTR[] = "You do not have permission to add a user within the selected organisation. This error has been logged and will be investigated.";
							application_log("Proxy id [".$_SESSION['details']['proxy_id']."] tried to create a user within an organisation [".$organisation_id."] they didn't have permissions on. ");
						}

					} else {
						$ERROR++;
						$ERRORSTR[] = "The <strong>Organisation Name</strong> field is required.";
					}
				}
			}

			if ($ENTRADA_ACL->amIAllowed(new UserResource(null, $PROCESSED["organisation_id"]), 'create')) {
				if ($permissions_only) {
					if ($db->AutoExecute(AUTH_DATABASE.".user_access", $PROCESSED_ACCESS, "INSERT")) {
						if (($PROCESSED_ACCESS["group"] == "medtech") || ($PROCESSED_ACCESS["role"] == "admin")) {
							application_log("error", "USER NOTICE: A new user (".$PROCESSED["firstname"]." ".$PROCESSED["lastname"].") was added to ".APPLICATION_NAME." as ".$PROCESSED_ACCESS["group"]." > ".$PROCESSED_ACCESS["role"].".");
						}

						/**
						 * Handle the inserting of user data into the user_departments table
						 * if departmental information exists in the form.
						 * NOTE: This is also done below (line 375)... arg.
						 */
						if ((isset($_POST["in_departments"]))) {
							$in_departments = explode(',',$_POST['in_departments']);
							foreach($in_departments as $department_id) {
								if ($department_id = (int) $department_id) {
									$query	= "SELECT * FROM `".AUTH_DATABASE."`.`user_departments` WHERE `user_id` = ".$db->qstr($PROCESSED_ACCESS["user_id"])." AND `dep_id` = ".$db->qstr($department_id);
									$result	= $db->GetRow($query);
									if (!$result) {
										$PROCESSED_DEPARTMENTS[] = $department_id;
									}
								}
							}

							if (@count($PROCESSED_DEPARTMENTS)) {
								foreach($PROCESSED_DEPARTMENTS as $department_id) {
									if (!$db->AutoExecute(AUTH_DATABASE.".user_departments", array("user_id" => $PROCESSED_ACCESS["user_id"], "dep_id" => $department_id), "INSERT")) {
										application_log("error", "Unable to insert proxy_id [".$PROCESSED_ACCESS["user_id"]."] into department [".$department_id."]. Database said: ".$db->ErrorMsg());
									}
								}
							}
						}

						$url			= ENTRADA_URL."/admin/users";

						$SUCCESS++;
						$SUCCESSSTR[]	= "You have successfully given this existing user access to this application.<br /><br />You will now be redirected to the users index; this will happen <strong>automatically</strong> in 5 seconds or <a href=\"".$url."\" style=\"font-weight: bold\">click here</a> to continue.";

						$ONLOAD[]		= "setTimeout('window.location=\\'".$url."\\'', 5000)";

						application_log("success", "Gave [".$PROCESSED_ACCESS["group"]." / ".$PROCESSED_ACCESS["role"]."] permissions to user id [".$PROCESSED_ACCESS["user_id"]."].");
					} else {
						$ERROR++;
						$ERRORSTR[]	= "Unable to give existing user access permissions to this application. ".$db->ErrorMsg();

						application_log("error", "Error giving existing user access to application id [".AUTH_APP_ID."]. Database said: ".$db->ErrorMsg());
					}
				} else {
					if (!$ERROR) {
					/**
					 * Now change the password to the MD5 value, just before it was inserted.
					 */
						$PROCESSED["password"] = md5($PROCESSED["password"]);

						if (($db->AutoExecute(AUTH_DATABASE.".user_data", $PROCESSED, "INSERT")) && ($PROCESSED_ACCESS["user_id"] = $db->Insert_Id())) {
							if ($db->AutoExecute(AUTH_DATABASE.".user_access", $PROCESSED_ACCESS, "INSERT")) {
								if (($PROCESSED_ACCESS["group"] == "medtech") || ($PROCESSED_ACCESS["role"] == "admin")) {
									application_log("error", "USER NOTICE: A new user (".$PROCESSED["firstname"]." ".$PROCESSED["lastname"].") was added to ".APPLICATION_NAME." as ".$PROCESSED_ACCESS["group"]." > ".$PROCESSED_ACCESS["role"].".");
								}

								/**
								 * Handle the inserting of user data into the user_departments table
								 * if departmental information exists in the form.
								 */
								if ((isset($_POST["in_departments"]))) {
									$in_departments = explode(',',$_POST['in_departments']);
									foreach($in_departments as $department_id) {
										if ($department_id = (int) $department_id) {
											$query	= "SELECT * FROM `".AUTH_DATABASE."`.`user_departments` WHERE `user_id` = ".$db->qstr($PROCESSED_ACCESS["user_id"])." AND `dep_id` = ".$db->qstr($department_id);
											$result	= $db->GetRow($query);
											if (!$result) {
												$PROCESSED_DEPARTMENTS[] = $department_id;
											}
										}
									}
								
									if (@count($PROCESSED_DEPARTMENTS)) {
										foreach($PROCESSED_DEPARTMENTS as $department_id) {
											if (!$db->AutoExecute(AUTH_DATABASE.".user_departments", array("user_id" => $PROCESSED_ACCESS["user_id"], "dep_id" => $department_id), "INSERT")) {
												application_log("error", "Unable to insert proxy_id [".$PROCESSED_ACCESS["user_id"]."] into department [".$department_id."]. Database said: ".$db->ErrorMsg());
											}
										}
									}
								}

								$url			= ENTRADA_URL."/admin/users";

								$SUCCESS++;
								$SUCCESSSTR[]	= "You have successfully created a new user in the authentication system, and have given them <strong>".$PROCESSED_ACCESS["group"]."</strong> / <strong>".$PROCESSED_ACCESS["role"]."</strong> access.<br /><br />You will now be redirected to the users index; this will happen <strong>automatically</strong> in 5 seconds or <a href=\"".$url."\" style=\"font-weight: bold\">click here</a> to continue.";

								$ONLOAD[]		= "setTimeout('window.location=\\'".$url."\\'', 5000)";

								application_log("success", "Gave [".$PROCESSED_ACCESS["group"]." / ".$PROCESSED_ACCESS["role"]."] permissions to user id [".$PROCESSED_ACCESS["user_id"]."].");
							} else {
								$ERROR++;
								$ERRORSTR[]	= "Unable to give this new user access permissions to this application. ".$db->ErrorMsg();

								application_log("error", "Error giving new user access to application id [".AUTH_APP_ID."]. Database said: ".$db->ErrorMsg());
							}
						} else {
							$ERROR++;
							$ERRORSTR[] = "Unable to create a new user account at this time. The MEdTech Unit has been informed of this error, please try again later.";

							application_log("error", "Unable to create new user account. Database said: ".$db->ErrorMsg());
						}
					}
				}
			} else {
				$ERROR++;
				$ERRORSTR[] = "You do not have permission to create a user with those details. Please try again with a different organisation.";

				application_log("error", "Unable to create new user account because this user didn't have permissions to create with the selected organisation ID. This should only happen if the request is tampered with.");
			}
			if ($ERROR) {
				$STEP = 1;
			} else {
				$url			= ENTRADA_URL."/admin/users";
				$ONLOAD[]		= "setTimeout('window.location=\\'".$url."\\'', 5000)";

				/**
				 * If there are permissions only we may not have the information that we require,
				 * so query the database to get it.
				 */
				if ($permissions_only) {
					$query	= "SELECT * FROM `".AUTH_DATABASE."`.`user_data` WHERE `id` = ".$db->qstr($PROCESSED_ACCESS["user_id"]);
					$result	= $db->GetRow($query);
					if ($result) {
						$PROCESSED = array();
						$PROCESSED["firstname"]	= $result["firstname"];
						$PROCESSED["lastname"]	= $result["lastname"];
						$PROCESSED["username"]	= $result["username"];
						$PROCESSED["email"]		= $result["email"];
					}
				}

				if ((isset($_POST["send_notification"])) && ((int) $_POST["send_notification"] == 1)) {
					$PROXY_ID = $PROCESSED_ACCESS["user_id"];
					do {
						$HASH = generate_hash();
					} while($db->GetRow("SELECT `id` FROM `".AUTH_DATABASE."`.`password_reset` WHERE `hash` = ".$db->qstr($HASH)));

					if ($db->AutoExecute(AUTH_DATABASE.".password_reset", array("ip" => $_SERVER["REMOTE_ADDR"], "date" => time(), "user_id" => $PROXY_ID, "hash" => $HASH, "complete" => 0), "INSERT")) {
					// Send welcome & password reset e-mail.
						$notification_search	= array("%firstname%", "%lastname%", "%username%", "%password_reset_url%", "%application_url%", "%application_name%");
						$notification_replace	= array($PROCESSED["firstname"], $PROCESSED["lastname"], $PROCESSED["username"], PASSWORD_RESET_URL."?hash=".rawurlencode($PROXY_ID.":".$HASH), ENTRADA_URL, APPLICATION_NAME);

						$message = str_ireplace($notification_search, $notification_replace, ((isset($_POST["notification_message"])) ? html_encode($_POST["notification_message"]) : $DEFAULT_NEW_USER_NOTIFICATION));

						if (!@mail($PROCESSED["email"], "New User Account: ".APPLICATION_NAME, $message, "From: \"".$AGENT_CONTACTS["administrator"]["name"]."\" <".$AGENT_CONTACTS["administrator"]["email"].">\nReply-To: \"".$AGENT_CONTACTS["administrator"]["name"]."\" <".$AGENT_CONTACTS["administrator"]["email"].">")) {
							$NOTICE++;
							$NOTICESTR[] = "The user was successfully added; however, we could not send them a new account e-mail notice. The MEdTech Unit has been informed of this problem, please send this new user a password reset notice manually.<br /><br />You will now be redirected back to the user index; this will happen <strong>automatically</strong> in 5 seconds or <a href=\"".$url."\" style=\"font-weight: bold\">click here</a> to continue.";

							application_log("error", "New user [".$PROCESSED["username"]."] was given access to OCR but the e-mail notice failed to send.");
						}
					} else {
						$NOTICE++;
						$NOTICESTR[] = "The user was successfully added; however, we could not send them a new account e-mail notice. The MEdTech Unit has been informed of this problem, please send this new user a password reset notice manually.<br /><br />You will now be redirected back to the user index; this will happen <strong>automatically</strong> in 5 seconds or <a href=\"".$url."\" style=\"font-weight: bold\">click here</a> to continue.";

						application_log("error", "New user [".$PROCESSED["username"]."] was given access to OCR but the e-mail notice failed to send. Database said: ".$db->ErrorMsg());
					}
				}
			}
			break;
		case 1 :
		default :
			continue;
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
			$HEAD[] = "<script type=\"text/javascript\" src=\"".ENTRADA_URL."/javascript/selectchained.js\"></script>\n";
			$HEAD[] = "<script type=\"text/javascript\" src=\"".ENTRADA_URL."/javascript/picklist.js\"></script>\n";
			$HEAD[] = "<script type=\"text/javascript\" src=\"".ENTRADA_URL."/javascript/elementresizer.js\"></script>\n";

			$i = count($HEAD);
			$HEAD[$i]  = "<script type=\"text/javascript\">\n";
			$HEAD[$i] .= "addListGroup('account_type', 'cs-top');\n";
			if (is_array($SYSTEM_GROUPS)) {
				$item = 1;
				foreach($SYSTEM_GROUPS as $group => $roles) {
					$HEAD[$i] .= "addList('cs-top', '".ucwords($group)."', '".$group."', 'cs-sub-".$item."', ".(((isset($PROCESSED_ACCESS["group"])) && ($PROCESSED_ACCESS["group"] == $group)) ? "1" : "0").");\n";
					if (is_array($roles)) {
						foreach($roles as $role) {
							$HEAD[$i] .= "addOption('cs-sub-".$item."', '".ucwords($role)."', '".$role."', ".(((isset($PROCESSED_ACCESS["role"])) && ($PROCESSED_ACCESS["role"] == $role)) ? "1" : "0").");\n";
						}
					}
					$item++;
				}
			}
			$HEAD[$i] .= "</script>\n";
			$js_text  = "<script type=\"text/javascript\">\n";
			$js_text .= "var glob_type = null;\n";
			$js_text .= "function findExistingUser(type, value) {\n";
			$js_text .= "\tvar url = '".ENTRADA_URL."/admin/".$MODULE."?section=search&'+type + '=' + value;\n";
			$js_text .= "\tif (type == 'id') {\n";
			$js_text .= "\ttype = 'number';\n";
			$js_text .= "\t}\n";
			$js_text .= "\t$(type+'-default').hide();\n";
			$js_text .= "\t$(type+'-searching').show();\n";
			$js_text .= "\tglob_type = type;\n";
			$js_text .= "\tnew Ajax.Request(url, {method: 'get', onComplete: getResponse});\n";
			$js_text .= "}\n";
			$js_text .= "function getResponse(request) {\n";
			$js_text .= "\t$(glob_type+'-default').show();\n";
			$js_text .= "\t$(glob_type+'-searching').hide();\n";
			$js_text .= "\tvar data = request.responseJSON;\n";
			$js_text .= "\tif (data) {\n";
			$js_text .= "\t\t$('username').disable().value = data.username;\n";
			$js_text .= "\t\t$('firstname').disable().value = data.firstname;\n";
			$js_text .= "\t\t$('lastname').disable().value = data.lastname;\n";
			$js_text .= "\t\t$('email').disable().value = data.email;\n";
			$js_text .= "\t\t$('number').disable().value = data.number;\n";
			$js_text .= "\t\t$('password').disable().value = '********';\n";
			$js_text .= "\t\t$('prefix').disable().value = data.prefix;\n";
			$js_text .= "\t\t$('email_alt').disable().value = data.email_alt;\n";
			$js_text .= "\t\t$('telephone').disable().value = data.telephone;\n";
			$js_text .= "\t\t$('fax').disable().value = data.fax;\n";
			$js_text .= "\t\t$('address').disable().value = data.address;\n";
			$js_text .= "\t\t$('city').disable().value = data.city;\n";
			$js_text .= "\t\t$('province').disable().value = data.province;\n";
			$js_text .= "\t\t$('postcode').disable().value = data.postcode;\n";
			$js_text .= "\t\t$('country').disable().value = data.country;\n";
			$js_text .= "\t\t$('notes').disable().value = data.notes;\n";
			$js_text .= "\t\t$('send_notification_msg').hide();\n";
			$js_text .= "\t\t$('send_notification').disable().checked = false;\n";
			$js_text .= "\t\t$$('.departments').each( function (e) { e.hide(); });\n";
			$js_text .= "\t\tvar notice = document.createElement('div');\n";
			$js_text .= "\t\tnotice.id = 'display-notice';\n";
			$js_text .= "\t\tnotice.addClassName('display-notice');\n";
			$js_text .= "\t\tnotice.innerHTML = data.message;\n";
			$js_text .= "\t\t$('addUser').insert({'before' : notice});\n";
			$js_text .= "\t}\n";
			$js_text .= "}\n";
			$js_text .= "</script>\n";
			$HEAD[] = $js_text;
			$ONLOAD[] = "initListGroup('account_type', document.getElementById('group'), document.getElementById('role'))";
			$ONLOAD[] = "setMaxLength()";
			if (isset($_GET["id"]) && $_GET["id"] && ($proxy_id = clean_input($_GET["id"], array("int")))) {
				$ONLOAD[] = "findExistingUser('id', '".$proxy_id."')";
			}
			$DEPARTMENT_LIST = array();
			$query		= "
						SELECT a.`department_id`, a.`department_title`, a.`organisation_id`, b.`entity_title`
						FROM `".AUTH_DATABASE."`.`departments` AS a
						LEFT JOIN `".AUTH_DATABASE."`.`entity_type` AS b
						ON a.`entity_id` = b.`entity_id`
						ORDER BY a.`department_title`";
			$results	= $db->GetAll($query);
			if ($results) {
				foreach($results as $key => $result) {
					$DEPARTMENT_LIST[$result["organisation_id"]][] = array("department_id"=>$result['department_id'], "department_title" => $result["department_title"], "entity_title" => $result["entity_title"]);
				}
			}


			$ONLOAD[] = "toggle_visibility_checkbox($('send_notification'), 'send_notification_msg')";

			if ($ERROR) {
				echo display_error();
			}

			if ($NOTICE) {
				echo display_notice();
			}
			
			$ONLOAD[] = "provStateFunction(\$F($('addUser')['country_id']))";
			?>
			
			<script type="text/javascript">

			function provStateFunction(country_id) {
				var url='<?php echo webservice_url("province"); ?>';
				<?php
					$source_arr = $PROCESSED;

				    $province = $source_arr["province"];
				    $province_id = $source_arr["province_id"];
				    $prov_state = ($province) ? $province : $province_id;
				?>
				
				url = url + '?countries_id=' + country_id + '&prov_state=<?php echo $prov_state; ?>';
				new Ajax.Updater($('prov_state_div'), url,
					{
						method:'get',
						onComplete: function (init_run) {
							
							if ($('prov_state').type == 'select-one') {
								$('prov_state_label').removeClassName('form-nrequired');
								$('prov_state_label').addClassName('form-required');
								if (!init_run) 
									$("prov_state").selectedIndex = 0;
								
								
							} else {
								
								$('prov_state_label').removeClassName('form-required');
								$('prov_state_label').addClassName('form-nrequired');
								if (!init_run) 
									$("prov_state").clear();
								
								
							}
						}.curry(!provStateFunction.initialzed)
					});
				provStateFunction.initialzed = true;
				
			}
			provStateFunction.initialzed = false;

			</script>


			<form id="addUser" action="<?php echo ENTRADA_URL; ?>/admin/users?section=add&amp;step=2" method="post" onsubmit="$('number').enable()">
	<table style="width: 100%" cellspacing="1" cellpadding="1" border="0" summary="New MEdTech Profile">
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
					<input type="submit" class="button" value="Add User" />
				</td>
			</tr>
		</tfoot>
		<tbody>
			<tr>
				<td colspan="3">
					<h2>Account Details</h2>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><label for="number" class="form-nrequired">Staff / Student Number:</label></td>
				<td>
						<input type="text" id="number" name="number" value="<?php echo ((isset($PROCESSED["number"])) ? html_encode($PROCESSED["number"]) : ""); ?>" style="width: 250px" maxlength="25" onblur="findExistingUser('number', this.value)" onkeypress="if (event.which == 13) { findExistingUser('number', this.value); return false; }" />
						<span id="number-searching" class="content-small" style="display: none;"><img src="<?php echo ENTRADA_RELATIVE ?>/images/indicator.gif" /> Searching system for this number... </span>
						<span id="number-default" class="content-small">(<strong>Important:</strong> Required when ever possible)</span>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><label for="username" class="form-required">MEdTech Username:</label></td>
				<td>
						<input type="text" id="username" name="username" value="<?php echo ((isset($PROCESSED["username"])) ? html_encode($PROCESSED["username"]) : ""); ?>" style="width: 250px" maxlength="25" onblur="findExistingUser('username', this.value)" onkeypress="if (event.which == 13) { findExistingUser('username', this.value); return false; }" />
						<span id="username-searching" class="content-small" style="display: none;"><img src="<?php echo ENTRADA_RELATIVE ?>/images/indicator.gif" /> Searching system for this username... </span>
						<span id="username-default" class="content-small">(<strong>Important:</strong> Should be the Queen's NetID)</span>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><label for="password" class="form-required">MEdTech Password:</label></td>
				<td><input type="text" id="password" name="password" value="<?php echo ((isset($PROCESSED["password"])) ? html_encode($PROCESSED["password"]) : generate_password(8)); ?>" style="width: 250px" maxlength="25" /></td>
			</tr>
			<tr>
				<td colspan="3">&nbsp;</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td style="vertical-align: top"><label for="group" class="form-required">Account Type:</label></td>
				<td>
					<select id="group" name="group" style="width: 209px"></select> <span class="content-small"><strong>Account Group</strong></span><br />
					<select id="role" name="role" style="width: 209px; margin-top: 5px"></select> <span class="content-small"><strong>Account Role</strong></span>
				</td>
			</tr>
			<tr>
				<td colspan="3">
					<h2>Account Options</h2>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td style="vertical-align: top"><label for="account_active" class="form-required">Account Status:</label></td>
				<td>
					<select id="account_active" name="account_active" style="width: 209px">
						<option value="true"<?php echo (((!isset($PROCESSED_ACCESS["account_active"])) || ($PROCESSED_ACCESS["account_active"] == "true")) ? " selected=\"selected\"" : ""); ?>>Active</option>
						<option value="false"<?php echo (($PROCESSED_ACCESS["account_active"] == "false") ? " selected=\"selected\"" : ""); ?>>Disabled</option>
					</select>
				</td>
			</tr>
						<?php echo generate_calendars("access", "Access", true, true, ((isset($PROCESSED["access_starts"])) ? $PROCESSED["access_starts"] : time()), true, false, 0); ?>
			<tr>
				<td colspan="3">
					<h2>Personal Information</h2>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><label for="prefix" class="form-nrequired">Prefix:</label></td>
				<td>
					<select id="prefix" name="prefix" style="width: 55px; vertical-align: middle; margin-right: 5px">
						<option value=""<?php echo ((!$result["prefix"]) ? " selected=\"selected\"" : ""); ?>></option>
									<?php
									if ((@is_array($PROFILE_NAME_PREFIX)) && (@count($PROFILE_NAME_PREFIX))) {
										foreach($PROFILE_NAME_PREFIX as $key => $prefix) {
											echo "<option value=\"".html_encode($prefix)."\"".(((isset($PROCESSED["prefix"])) && ($PROCESSED["prefix"] == $prefix)) ? " selected=\"selected\"" : "").">".html_encode($prefix)."</option>\n";
										}
									}
									?>
					</select>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><label for="firstname" class="form-required">Firstname:</label></td>
				<td><input type="text" id="firstname" name="firstname" value="<?php echo ((isset($PROCESSED["firstname"])) ? html_encode($PROCESSED["firstname"]) : ""); ?>" style="width: 250px; vertical-align: middle" maxlength="35" /></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><label for="lastname" class="form-required">Lastname:</label></td>
				<td><input type="text" id="lastname" name="lastname" value="<?php echo ((isset($PROCESSED["lastname"])) ? html_encode($PROCESSED["lastname"]) : ""); ?>" style="width: 250px; vertical-align: middle" maxlength="35" /></td>
			</tr>
			<tr>
				<td colspan="3">&nbsp;</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><label for="email" class="form-required">Primary E-Mail:</label></td>
				<td>
					<input type="text" id="email" name="email" value="<?php echo ((isset($PROCESSED["email"])) ? html_encode($PROCESSED["email"]) : ""); ?>" style="width: 250px; vertical-align: middle" maxlength="128" />
					<span class="content-small">(<strong>Important:</strong> Official e-mail accounts only)</span>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><label for="email_alt" class="form-nrequired">Alternative E-Mail:</label></td>
				<td><input type="text" id="email_alt" name="email_alt" value="<?php echo ((isset($PROCESSED["email_alt"])) ? html_encode($PROCESSED["email_alt"]) : ""); ?>" style="width: 250px; vertical-align: middle" maxlength="128" /></td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><label for="telephone" class="form-nrequired">Telephone Number:</label></td>
				<td>
					<input type="text" id="telephone" name="telephone" value="<?php echo ((isset($PROCESSED["telephone"])) ? html_encode($PROCESSED["telephone"]) : ""); ?>" style="width: 250px; vertical-align: middle" maxlength="25" />
					<span class="content-small">(<strong>Example:</strong> 613-533-6000 x74918)</span>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><label for="fax" class="form-nrequired">Fax Number:</label></td>
				<td>
					<input type="text" id="fax" name="fax" value="<?php echo ((isset($PROCESSED["fax"])) ? html_encode($PROCESSED["fax"]) : ""); ?>" style="width: 250px; vertical-align: middle" maxlength="25" />
					<span class="content-small">(<strong>Example:</strong> 613-533-3204)</span>
				</td>
			</tr>
			<tr>
				<td colspan="3">&nbsp;</td>
			</tr>
			
			<tr>
				<td>&nbsp;</td>
				<td><label for="country_id" class="form-nrequired">Country</label></td>
				<td>
					<?php
					$countries = fetch_countries();
					if ((is_array($countries)) && (count($countries))) {
						echo "<select id=\"country_id\" name=\"country_id\" style=\"width: 256px\" onchange=\"provStateFunction(this.value);\">\n";
						echo "<option value=\"0\"".((!$PROCESSED["country_id"]) ? " selected=\"selected\"" : "").">-- Select Country --</option>\n";
						foreach ($countries as $country) {
							echo "<option value=\"".(int) $country["countries_id"]."\"".(($PROCESSED["country_id"] == $country["countries_id"]) ? " selected=\"selected\"" : "").">".html_encode($country["country"])."</option>\n";
						}
						echo "</select>\n";
					} else {
						echo "<input type=\"hidden\" id=\"countries_id\" name=\"countries_id\" value=\"0\" />\n";
						echo "Country information not currently available.\n";
					}
					?>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><label id="prov_state_label" for="prov_state_div" class="form-nrequired">Province / State</label></td>
				<td>
					<div id="prov_state_div">Please select a <strong>Country</strong> from above first.</div>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><label for="city" class="form-nrequired">City:</label></td>
				<td>
					<input type="text" id="city" name="city" value="<?php echo ((isset($PROCESSED["city"])) ? html_encode($PROCESSED["city"]) : "Kingston"); ?>" style="width: 250px; vertical-align: middle" maxlength="35" />
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><label for="address" class="form-nrequired">Address:</label></td>
				<td>
					<input type="text" id="address" name="address" value="<?php echo ((isset($PROCESSED["address"])) ? html_encode($PROCESSED["address"]) : ""); ?>" style="width: 250px; vertical-align: middle" maxlength="255" />
				</td>
			</tr>


			<tr>
				<td>&nbsp;</td>
				<td><label for="postcode" class="form-nrequired">Postal Code:</label></td>
				<td>
					<input type="text" id="postcode" name="postcode" value="<?php echo ((isset($PROCESSED["postcode"])) ? html_encode($PROCESSED["postcode"]) : "K7L 3N6"); ?>" style="width: 250px; vertical-align: middle" maxlength="7" />
					<span class="content-small">(<strong>Example:</strong> K7L 3N6)</span>
				</td>
			</tr>

			<tr>
				<td colspan="3">&nbsp;</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td style="vertical-align: top"><label for="office_hours">Office Hours:</label></td>
				<td>
					<textarea id="office_hours" name="office_hours" style="width: 254px; height: 40px;" maxlength="100"><?php echo (isset($PROCESSED["office_hours"]) && $PROCESSED["office_hours"] ? html_encode($PROCESSED["office_hours"]) : ""); ?></textarea>
				</td>
			</tr>
			<tr>
				<td colspan="3">&nbsp;</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td style="vertical-align: top"><label for="notes" class="form-nrequired">General Comments:</label></td>
				<td>
					<textarea id="notes" class="expandable" name="notes" style="width: 246px; height: 75px"><?php echo ((isset($PROCESSED["notes"])) ? html_encode($PROCESSED["notes"]) : ""); ?></textarea>
				</td>
			</tr>
			<tr>
				<td colspan="3">
					<h2>Departmental Options</h2>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><label for="organisation_id" class="form-required">Organisation</label></td>
				<td><select id="organisation_id" name="organisation_id" style="width: 250px" onchange="organisationChange();">
									<?php
									$query		= "SELECT `organisation_id`, `organisation_title` FROM `".AUTH_DATABASE."`.`organisations`";
									$results	= $db->GetAll($query);
									if ($results) {
										foreach($results as $result) {
											if ($ENTRADA_ACL->amIAllowed(new CourseResource(null, $result['organisation_id']), 'create')) {
												$organisation_categories[$result["organisation_id"]] = array($result["organisation_title"]);
												echo "<option value=\"".(int) $result["organisation_id"]."\"".(((isset($PROCESSED["organisation_id"])) && ($PROCESSED["organisation_id"] == $result["organisation_id"])) ? " selected=\"selected\"" : "").">".html_encode($result["organisation_title"])."</option>\n";
											}
										}
									}
									?>
					</select>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>Departments</td>
				<td>
					<div style="position: relative;">

									<?php
									if (isset($DEPARTMENT_LIST) && is_array($DEPARTMENT_LIST) && !empty($DEPARTMENT_LIST)) {
										$departments = array();

										foreach($DEPARTMENT_LIST as $organisation_id => $dlist) {
											foreach($dlist as $d) {

												if (in_array($d['department_id'], $PROCESSED_DEPARTMENTS)) {
													$checked = 'checked="checked"';
												} else {
													$checked = '';
												}

												if (isset($organisation_categories[$organisation_id])) {
													$departments[$organisation_id][] = array('text' => $d['department_title'], 'value' => $d['department_id'], 'checked' => $checked);
												}
											}
										}
										foreach($departments as $organisation_id => $dlist) {
											echo lp_multiple_select_popup('departments_'.$organisation_id, $dlist, array('title'=>'Select Departments:', 'class'=>'department_multi', 'submit_text'=>'Done', 'cancel'=>false, 'submit'=>true));
										}
									}
									?>
					</div>
					<input class="multi-picklist" id="in_departments" name="in_departments" style="display: none;">
					<div id="in_departments_list"></div>
					<input type="button" onclick="$('departments_'+$F('organisation_id')+'_options').show();" value="Select Multiple" >
					<script type="text/javascript">
						var multiselect = new Array();
						function organisationChange() {
							$$('.department_multi').invoke('hide');
							$('in_departments').value = "";
							if (multiselect[$F('organisation_id')]) {
								multiselect[$F('organisation_id')].scanCheckBoxes();
							} else {
								$('in_departments_list').immediateDescendants().invoke('remove');
							}
						}

						$$('.department_multi').each(function(element) {
							var id = element.id;
							numeric_id = id.substring(12,13);
							generic = element.id.substring(0, element.id.length - 8);
							this[numeric_id] = new Control.SelectMultiple('in_departments',id,{
								labelSeparator: '; ',
								checkboxSelector: 'table.select_multiple_table tr td input[type=checkbox]',
								nameSelector: 'table.select_multiple_table tr td.select_multiple_name label',
								overflowLength: 70,
								filter: generic+'_select_filter',
								resize: generic+'_scroll',
								afterCheck: apresCheck,
								updateDiv: updateDepartmentList
							});

							$(generic+'_close').observe('click',function(event){
								this.container.hide();
								return false;
							}.bindAsEventListener(this[numeric_id]));
						}, multiselect);

						function apresCheck(element) {
							var tr = $(element.parentNode.parentNode);
							tr.removeClassName('selected');
							if (element.checked) {
								tr.addClassName('selected');
							}
						}

						function updateDepartmentList(options) {
							ul = options.inject(new Element('ul', {'class':'associated_list'}), function(list, option) {
								list.appendChild(new Element('li').update(option));
								return list;
							});
							$('in_departments_list').update(ul);
						}
					</script>
				</td>
			</tr>
			<tr>
				<td colspan="3">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="3">
					<h2>Notification Options</h2>
				</td>
			</tr>
			<tr>
				<td><input type="checkbox" id="send_notification" name="send_notification" value="1"<?php echo (((empty($_POST)) || ((isset($_POST["send_notification"])) && ((int) $_POST["send_notification"]))) ? " checked=\"checked\"" : ""); ?> style="vertical-align: middle" onclick="toggle_visibility_checkbox(this, 'send_notification_msg')" /></td>
				<td colspan="2"><label for="send_notification" class="form-nrequired">Send this new user a password reset e-mail after adding them.</label></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td colspan="2">
					<div id="send_notification_msg" style="display: block">
						<label for="notification_message" class="form-required">Notification Message:</label><br />
						<textarea id="notification_message" name="notification_message" rows="10" cols="65" style="width: 100%; height: 200px"><?php echo ((isset($_POST["notification_message"])) ? html_encode($_POST["notification_message"]) : $DEFAULT_NEW_USER_NOTIFICATION); ?></textarea>
						<span class="content-small"><strong>Available Variables:</strong> %firstname%, %lastname%, %username%, %password_reset_url%, %application_url%, %application_name%</span>
					</div>
				</td>
			</tr>
			<tr>
				<td colspan="3">&nbsp;</td>
			</tr>
		</tbody>
	</table>
</form>
			<?php
			break;
	}
}
?>