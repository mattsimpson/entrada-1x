<?php
/**
 * Online Course Resources [Pre-Clerkship]
 * @author Unit: Medical Education Technology Unit
 * @author Director: Dr. Benjamin Chen <bhc@post.queensu.ca>
 * @author Developer: Matt Simpson <simpson@post.queensu.ca>
 * @version 3.0
 * @copyright Copyright 2006 Queen's University, MEdTech Unit
 *
 * $Id: help.inc.php 1043 2010-02-12 21:19:55Z simpson $
*/

if(!defined("PARENT_INCLUDED")) exit;

?>
<h1><?php echo APPLICATION_NAME; ?> Help</h1>
<p>
Welcome to the <?php echo APPLICATION_NAME; ?> Help section where you will find screencasts and tutorials about how to use the system as well as information sheets on new technologies.  If you would like to request a screencast or tutorial that is not part of the list, please use the feedback button located in sidebar (if logged in) or email <a href="mailto:medtech@queensu.ca">medtech@queensu.ca</a>.
</p>

<h2>Screencasts</h2>

<ul style="line-height: 150%; color: #666666; padding-left: 0px;">
	<li style="padding-bottom: 10px; list-style-type:none;">
		<img src="<?php echo ENTRADA_URL; ?>/images/icon-quicktime.gif" style="background-color: #333333" width="32" height="32" align="left" hspace="5" alt="Quicktime File" title="Quicktime File" />
		<a href="http://meds.queensu.ca/courses/assets/help/ocr_login_screencast.mov" style="font-weight: bold" target="_blank">Screencast: Logging In to <?php echo APPLICATION_NAME; ?></a><br />
		Step by step instructions for logging in to <?php echo APPLICATION_NAME; ?>.
	</li>
	<li style="padding-bottom: 10px; list-style-type:none;">
		<img src="<?php echo ENTRADA_URL; ?>/images/icon-quicktime.gif" style="background-color: #333333" width="32" height="32" align="left" hspace="5" alt="Quicktime File" title="Quicktime File" />
		<a href="http://meds.queensu.ca/courses/assets/help/ocr_myprofile_screencast.mov" style="font-weight: bold" target="_blank">Screencast: Viewing and Editing Your Profile</a><br />
		Instructions on how to view and update your profile page.
	</li>
	<li style="padding-bottom: 10px; list-style-type:none;">
		<img src="<?php echo ENTRADA_URL; ?>/images/icon-quicktime.gif" style="background-color: #333333" width="32" height="32" align="left" hspace="5" alt="Quicktime File" title="Quicktime File" />
		<a href="http://meds.queensu.ca/courses/assets/help/ocr_forgotpass_screencast.mov" style="font-weight: bold" target="_blank">Screencast: Help! I Forgot My Password</a><br />
		Instructions on how to reset your password if you have forgotten it.
	</li>
	<li style="padding-bottom: 10px; list-style-type:none;">
		<img src="<?php echo ENTRADA_URL; ?>/images/icon-quicktime.gif" style="background-color: #333333" width="32" height="32" align="left" hspace="5" alt="Quicktime File" title="Quicktime File" />
		<a href="http://meds.queensu.ca/courses/assets/help/ocr_changepass_screencast.mov" style="font-weight: bold" target="_blank">Screencast: How to Change Your Password</a><br />
		Instructions on how to change your password.
	</li>

	<li style="padding-bottom: 10px; list-style-type:none;">
		<img src="<?php echo ENTRADA_URL; ?>/images/icon-quicktime.gif" style="background-color: #333333" width="32" height="32" align="left" hspace="5" alt="Quicktime File" title="Quicktime File" />
		<a href="http://meds.queensu.ca/courses/assets/help/ocr_dashboard_screencast.mov" style="font-weight: bold" target="_blank">Screencast: Overview of the Dashboard</a><br />
		Overview of the dashboard.
	</li>
	<li style="padding-bottom: 10px; list-style-type:none;">
		<img src="<?php echo ENTRADA_URL; ?>/images/icon-quicktime.gif" style="background-color: #333333" width="32" height="32" align="left" hspace="5" alt="Quicktime File" title="Quicktime File" />
		<a href="http://meds.queensu.ca/courses/assets/help/ocr_courses_screencast.mov" style="font-weight: bold" target="_blank">Screencast: Overview of the Courses Page</a><br />
		Overview of the courses page.
	</li>
	<li style="padding-bottom: 10px; list-style-type:none;">
		<img src="<?php echo ENTRADA_URL; ?>/images/icon-quicktime.gif" style="background-color: #333333" width="32" height="32" align="left" hspace="5" alt="Quicktime File" title="Quicktime File" />
		<a href="http://meds.queensu.ca/courses/assets/help/ocr_learningevents_screencast.mov" style="font-weight: bold" target="_blank">Screencast: Overview of the Learning Events Page</a><br />
		Overview of how to find, sort, and view learning events.
	</li>
	<li style="padding-bottom: 10px; list-style-type:none;">
		<img src="<?php echo ENTRADA_URL; ?>/images/icon-quicktime.gif" style="background-color: #333333" width="32" height="32" align="left" hspace="5" alt="Quicktime File" title="Quicktime File" />
		<a href="http://meds.queensu.ca/courses/assets/help/ocr_podcastingfeed_screencast.mov" style="font-weight: bold" target="_blank">Screencast: How to Subscribe to the Podcasting Feed</a><br />
		Instructions on how to subscribe to the podcasting feed.
	</li>
	<li style="padding-bottom: 10px; list-style-type:none;">
		<img src="<?php echo ENTRADA_URL; ?>/images/icon-quicktime.gif" style="background-color: #333333" width="32" height="32" align="left" hspace="5" alt="Quicktime File" title="Quicktime File" />
		<a href="http://meds.queensu.ca/courses/assets/help/ocr_library_screencast.mov" style="font-weight: bold" target="_blank">Screencast: How to Access Library Resources From Off-Campus</a><br />
		Instructions on how to access library resources from off-campus.
	</li>
</ul>

<h2>Tutorials</h2>
<ul style="line-height: 150%; color: #666666; ">
	<li style="padding-bottom: 10px;">
		<a href="http://meds.queensu.ca/medtech/help/article.php?id=002" style="font-weight: bold" target="_blank">Tutorial: What do I do if I forgot my password?</a><br />
		Instructions on how to reset your password if you have forgotten it.
	</li>
		<li style="padding-bottom: 10px;">
		<a href="http://meds.queensu.ca/medtech/help/article.php?id=004" style="font-weight: bold" target="_blank">Tutorial: How do I change my privacy settings?</a><br />
		Instructions on how to change your privacy settings.
	</li>
</ul>


<h2>Info Sheets</h2>
<ul style="line-height: 150%; color: #666666; ">
	<li style="padding-bottom: 10px;">
		<a href="http://meds.queensu.ca/medtech/resources/info_podcasting" style="font-weight: bold" target="_blank">Info Sheet: Podcasting</a><br />
		Information on podcasting and how to subscribe to the School of Medicine podcasts.
	</li>
	<li style="padding-bottom: 10px;">
		<a href="http://meds.queensu.ca/medtech/resources/info_rss_explained" style="font-weight: bold" target="_blank">Info Sheet: RSS</a><br />
		Information on RSS feeds and links to some useful feeds.
	</li>
	<li style="padding-bottom: 10px;">
		<a href="http://meds.queensu.ca/medtech/resources/info_icalendar_subscription" style="font-weight: bold" target="_blank">Info Sheet: iCalendar Subscriptions</a><br />
		Information on how to keep an updated copy of the learning event calendar on your computer, personal digital assistant (PDA), cell phone, and/or iPod at all times - even when you are not connected to the Internet.
	</li>
</ul>