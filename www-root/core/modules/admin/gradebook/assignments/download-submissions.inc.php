<?php
/**
 * Entrada [ http://www.entrada-project.org ]
 * 
 * Used to view the details of / download the specified file within a folder.
 * 
 * @author Organisation: Queen's University
 * @author Unit: School of Medicine
 * @author Developer: Brandon Thorn <brandon.thorn@queensu.ca>
  * @copyright Copyright 2012 Queen's University. All Rights Reserved.
 * 
*/
if ((!defined("IN_GRADEBOOK"))) {
	exit;
} 
if (!isset($RECORD_ID) || !$RECORD_ID) {
	if (isset($_GET["id"]) && $tmp = clean_input($_GET["id"], "int")) {
		$RECORD_ID = $tmp;
	}
}

if ($RECORD_ID) {
	$query = "	SELECT a.*,b.`organisation_id`,b.`course_code` 
				FROM `assignments` a
				JOIN `courses` b
				ON a.`course_id` = b.`course_id` 
				WHERE a.`assignment_id` = ".$db->qstr($RECORD_ID)."
				AND a.`assignment_active` = '1'";
	$assignment = $db->GetRow($query);	

	/** @todo this needs to make sure the user is a teacher for the course if this way is used, otherwise students could add another student's proxy*/
	$query = "SELECT * FROM `assignment_contacts` WHERE `assignment_id` = ".$db->qstr($RECORD_ID)." AND `proxy_id` = ".$db->qstr($ENTRADA_USER->getID());
	if ($iscontact = $db->GetRow($query)) {
		$USER_ID = $tmp;
	} elseif ($assignment && $ENTRADA_ACL->amIAllowed(new CourseResource($assignment["course_id"], $assignment["organisation_id"]), "update")) {
		$iscontact = true;	
	} else {
		$iscontact = false;
	}

	if ($iscontact) {			
		if($assignment){

			/**
			 * Download the latest version.
			 */
			$query	= " SELECT a.*, CONCAT_WS('_',b.`firstname`,b.`lastname`) AS `username`, b.`number`, a.`afversion_id` FROM `assignment_file_versions` AS a
						JOIN `".AUTH_DATABASE."`.`user_data` AS b 
						ON a.`proxy_id` = b.`id` 
						WHERE `afversion_id` IN(
							SELECT MAX(`afversion_id`) FROM `assignment_file_versions` AS a
							JOIN `assignment_files` AS b
							ON a.`afile_id` = b.`afile_id`
							AND b.`file_type` = 'submission'
							WHERE a.`assignment_id` = ".$db->qstr($RECORD_ID)." 
							GROUP BY a.`afile_id`
						)";
			$results = $db->GetAll($query);
			$dir = FILE_STORAGE_PATH."/zips";
			if ( !file_exists($dir) ) {
			  mkdir ($dir, 0777);
			}
			$zip_file_name = str_replace(array("/", " ") , "_", $assignment["course_code"]."_".$assignment["assignment_title"]).'.zip';
			$zipname = $dir."/".$zip_file_name;
			if ($results) {
                $zip = new ZipArchive();
                $res = $zip->open($zipname,ZIPARCHIVE::OVERWRITE);
                if ($res !== true) {
                    $ERROR++;
                    $ERRORSTR[] = "<strong>Unable to create the file archive.</strong><br /><br />The archive of files was not created. Please try again later.";
                } else {
                        foreach ($results as $file){
                            $submission_file = FILE_STORAGE_PATH."/A".$file["afversion_id"];
                            if ((@file_exists($submission_file)) && (@is_readable($submission_file))) {
                                $zip->addFile($submission_file, $file["number"]."_".$file["username"]."_".$file["file_filename"]);	
                            }							
                        }
                        $file_version = array();
                        $file_version["file_mimetype"] = "application/zip";
                        $file_version["file_filename"] = $zipname;
                        $zip->close();
                }
			}
			if (($file_version) && (is_array($file_version))) {
				$download_file = $zipname;
				if ((@file_exists($download_file)) && (@is_readable($download_file))) {
						/**
					 * This must be done twice in order to close both of the open buffers.
					 */
					@ob_end_clean();
					@ob_end_clean();

					/**
					 * Determine method that the file should be accessed (downloaded or viewed)
					 * and send the proper headers to the client.
					 */
                    header("Pragma: public");
                    header("Expires: 0");
                    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                    header("Content-Type: ".$file_version["file_mimetype"]);
                    header("Content-Disposition: inline; filename=\"".$zip_file_name."\"");
                    header("Content-Length: ".@filesize($download_file));
                    header("Content-Transfer-Encoding: binary\n");
					echo @file_get_contents($download_file, FILE_BINARY);
                    statistic("assignment:".$RECORD_ID, "file_zip_download", "assignment_id", $RECORD_ID);
					exit;
				}

			}
			if ((!$ERROR) && (!$NOTICE)) {
				$ERROR++;
				$ERRORSTR[] = "<strong>Unable to download the selected file.</strong><br /><br />The file you have selected cannot be downloaded at this time, ".((isset($_SESSION["isAuthorized"])) && ((bool) $_SESSION["isAuthorized"]) ? "please try again later." : "Please log in to continue.");
			}

			if ($NOTICE) {
				echo display_notice();
			}
			if ($ERROR) {
				echo display_error();
			}
	
		}else{
				application_log("error", "The provided file id was invalid [".$RECORD_ID."] (View File).");
				//header("Location: ".ENTRADA_URL."/profile/gradebook/assignments?section=submit&id=".$RECORD_ID);
				echo 'Invalid id specified. No assignment found for that id.';
				exit;		
		}

	} else {
		echo 'You do not have authorization to view this resource';
	}
} else {
	application_log("error", "No assignment id was provided to view. (View File)");
	echo 'No id specified';
	
	exit;
}
?>
