<?php
/**
 * Entrada [ http://www.entrada-project.org ]
 * 
 * Used to allow people to comment on existing photos within a gallery, in a
 * community.
 * 
 * @author Organisation: Queen's University
 * @author Unit: School of Medicine
 * @author Developer: Matt Simpson <matt.simpson@queensu.ca>
 * @author Developer: James Ellis <james.ellis@queensu.ca>
 * @copyright Copyright 2010 Queen's University. All Rights Reserved.
 * 
 * @version $Id: add-comment.inc.php 1092 2010-04-04 17:19:49Z simpson $
*/

if ((!defined("COMMUNITY_INCLUDED")) || (!defined("IN_GALLERIES"))) {
	exit;
} elseif (!$COMMUNITY_LOAD) {
	exit;
}

$HEAD[] = "<script type=\"text/javascript\" src=\"".COMMUNITY_URL."/javascript/galleries.js?release=".html_encode(APPLICATION_VERSION)."\"></script>";

echo "<h1>Add Photo Comment</h1>\n";

if ($RECORD_ID) {
	$query = "	SELECT a.*, b.`gallery_title`, b.`admin_notifications`
				FROM `community_gallery_photos` AS a
				LEFT JOIN `community_galleries` AS b
				ON a.`cgallery_id` = b.`cgallery_id`
				WHERE a.`community_id` = ".$db->qstr($COMMUNITY_ID)."
				AND b.`cpage_id` = ".$db->qstr($PAGE_ID)."
				AND a.`cgphoto_id` = ".$db->qstr($RECORD_ID)."
				AND a.`photo_active` = '1'
				AND b.`gallery_active` = '1'";
	$photo_record = $db->GetRow($query);
	if ($photo_record) {
		if ((int) $photo_record["photo_active"]) {
			if (galleries_module_access($photo_record["cgallery_id"], "add-comment")) {
				$BREADCRUMB[] = array("url" => COMMUNITY_URL.$COMMUNITY_URL.":".$PAGE_URL."?section=view-gallery&id=".$photo_record["cgallery_id"], "title" => limit_chars($photo_record["gallery_title"], 32));
				$BREADCRUMB[] = array("url" => COMMUNITY_URL.$COMMUNITY_URL.":".$PAGE_URL."?section=view-photo&id=".$RECORD_ID, "title" => limit_chars($photo_record["photo_title"], 32));
				$BREADCRUMB[] = array("url" => COMMUNITY_URL.$COMMUNITY_URL.":".$PAGE_URL."?section=add-comment&amp;id=".$RECORD_ID, "title" => "Add Photo Comment");

				communities_load_rte();

				// Error Checking
				switch($STEP) {
					case 2 :
						/**
						 * Required field "title" / Comment Title.
						 */
						if ((isset($_POST["comment_title"])) && ($title = clean_input($_POST["comment_title"], array("notags", "trim")))) {
							$PROCESSED["comment_title"] = $title;
						} else {
							$PROCESSED["comment_title"] = "";
						}

						/**
						 * Non-Required field "description" / Comment Body
						 *
						 */
						if ((isset($_POST["comment_description"])) && ($description = clean_input($_POST["comment_description"], array("trim", "allowedtags")))) {
							$PROCESSED["comment_description"] = $description;
						} else {
							$ERRORSTR[] = "The <strong>Comment Body</strong> field is required, this is the comment you're making.";
						}

						/**
						 * Email Notificaions.
						 */
						if(isset($_POST["member_notify"])) {
							$PROCESSED["notify"] = $_POST["member_notify"];
						}

						if (!$ERROR) {
							$PROCESSED["cgphoto_id"]		= $RECORD_ID;
							$PROCESSED["cgallery_id"]		= $photo_record["cgallery_id"];
							$PROCESSED["community_id"]		= $COMMUNITY_ID;
							$PROCESSED["proxy_id"]			= $_SESSION[APPLICATION_IDENTIFIER]["tmp"]["proxy_id"];
							$PROCESSED["comment_active"]	= 1;
							$PROCESSED["release_date"]		= time();
							$PROCESSED["updated_date"]		= time();
							$PROCESSED["updated_by"]		= $_SESSION["details"]["id"];

							if ($db->AutoExecute("community_gallery_comments", $PROCESSED, "INSERT")) {
								if ($COMMENT_ID = $db->Insert_Id()) {
									$url			= COMMUNITY_URL.$COMMUNITY_URL.":".$PAGE_URL."?section=view-photo&id=".$RECORD_ID."#comment-".$COMMENT_ID;
									$ONLOAD[]		= "setTimeout('window.location=\\'".$url."\\'', 5000)";

									$SUCCESS++;
									$SUCCESSSTR[]	= "You have successfully added a new photo comment.<br /><br />You will now be redirected back to this photo; this will happen <strong>automatically</strong> in 5 seconds or <a href=\"".$url."\" style=\"font-weight: bold\">click here</a> to continue.";

									communities_log_history($COMMUNITY_ID, $PAGE_ID, $COMMENT_ID, "community_history_add_photo_comment", 1, $RECORD_ID);
								}
							}

							if (!$SUCCESS) {
								$ERROR++;
								$ERRORSTR[] = "There was a problem adding this photo comment into the system. The MEdTech Unit was informed of this error; please try again later.";

								application_log("error", "There was an error inserting a photo comment. Database said: ".$db->ErrorMsg());
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

				// Page Display
				switch($STEP) {
					case 2 :
						if ($NOTICE) {
							echo display_notice();
						}
						if ($SUCCESS) {
							echo display_success();
							if (COMMUNITY_NOTIFICATIONS_ACTIVE) {
								community_notify($COMMUNITY_ID, $RECORD_ID, "photo-comment", COMMUNITY_URL.$COMMUNITY_URL.":".$PAGE_URL."?section=view-photo&id=".$RECORD_ID, $RECORD_ID);
							}
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
					<form action="<?php echo COMMUNITY_URL.$COMMUNITY_URL.":".$PAGE_URL; ?>?section=add-comment&amp;id=<?php echo $RECORD_ID; ?>&amp;step=2" method="post">
					<table style="width: 100%" cellspacing="0" cellpadding="2" border="0" summary="Add Photo Comment">
					<colgroup>
						<col style="width: 3%" />
						<col style="width: 20%" />
						<col style="width: 77%" />
					</colgroup>
					<tfoot>
						<tr>
							<td colspan="3" style="padding-top: 15px; text-align: right">
                                <input type="submit" class="button" value="<?php echo $translate->_("global_button_save"); ?>" />                        
							</td>
						</tr>
					</tfoot>
					<tbody>
						<tr>
							<td colspan="3"><h2>Photo Comment Details</h2></td>
						</tr>
						<tr>
							<td colspan="2"><label for="comment_title" class="form-nrequired">Comment Title</label></td>
							<td style="text-align: right"><input type="text" id="comment_title" name="comment_title" value="<?php echo ((isset($PROCESSED["comment_title"])) ? html_encode($PROCESSED["comment_title"]) : ""); ?>" maxlength="128" style="width: 95%" /></td>
						</tr>
						<tr>
							<td colspan="3"><label for="comment_description" class="form-required">Comment Body</label></td>
						</tr>
						<tr>
							<td colspan="3">
								<textarea id="comment_description" name="comment_description" style="width: 100%; height: 200px" cols="68" rows="12"><?php echo ((isset($PROCESSED["comment_description"])) ? html_encode($PROCESSED["comment_description"]) : ""); ?></textarea>
							</td>
						</tr>
					</tbody>
					</table>
					</form>
					<?php
					break;
				}
			} else {
				if ($ERROR) {
					echo display_error();
				}
				if ($NOTICE) {
					echo display_notice();
				}
			}
		} else {
			$NOTICE++;
			$NOTICESTR[] = "The photo that you are trying to comment on was deactivated <strong>".date(DEFAULT_DATE_FORMAT, $photo_record["updated_date"])."</strong> by <strong>".html_encode(get_account_data("firstlast", $photo_record["updated_by"]))."</strong>.<br /><br />If there has been a mistake or you have questions relating to this issue please contact the MEdTech Unit directly.";

			echo display_notice();

			application_log("error", "The file record id [".$RECORD_ID."] is deactivated; however, ".$_SESSION["details"]["firstname"]." ".$_SESSION["details"]["lastname"]." [".$_SESSION["details"]["id"]."] has tried to comment on it.");
		}
	} else {
		$ERROR++;
		$ERRORSTR[] = "The photo id that you have provided does not exist in the system. Please provide a valid record id to proceed.";

		echo display_error();

		application_log("error", "The provided photo id was invalid [".$RECORD_ID."] (Add Comment).");
	}
} else {
	$ERROR++;
	$ERRORSTR[] = "Please provide a valid photo id to proceed.";

	echo display_error();

	application_log("error", "No photo id was provided to edit. (Add Comment)");
}
?>