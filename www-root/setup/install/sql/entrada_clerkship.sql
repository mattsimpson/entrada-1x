SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

CREATE TABLE IF NOT EXISTS `apartments` (
  `apartment_id` int(12) NOT NULL auto_increment,
  `region_id` int(12) NOT NULL default '0',
  `apartment_title` varchar(86) NOT NULL default '',
  `apartment_number` varchar(12) NOT NULL default '',
  `apartment_address` varchar(86) NOT NULL default '',
  `apartment_city` varchar(48) NOT NULL default '',
  `apartment_province` varchar(24) NOT NULL default '',
  `apartment_postcode` varchar(12) NOT NULL default '',
  `apartment_country` varchar(48) NOT NULL default '',
  `apartment_phone` varchar(24) NOT NULL default '',
  `apartment_email` varchar(128) NOT NULL default '',
  `max_occupants` int(8) NOT NULL default '0',
  `apartment_longitude` varchar(24) NOT NULL default '',
  `apartment_latitude` varchar(24) NOT NULL default '',
  `available_start` bigint(64) NOT NULL default '0',
  `available_finish` bigint(64) NOT NULL default '0',
  `updated_last` bigint(64) NOT NULL default '0',
  `updated_by` int(12) NOT NULL default '0',
  `apartment_status` varchar(12) NOT NULL default '',
  PRIMARY KEY  (`apartment_id`),
  KEY `region_id` (`region_id`),
  KEY `apartment_title` (`apartment_title`),
  KEY `apartment_address` (`apartment_address`),
  KEY `apartment_city` (`apartment_city`),
  KEY `apartment_province` (`apartment_province`),
  KEY `apartment_country` (`apartment_country`),
  KEY `max_occupants` (`max_occupants`),
  KEY `apartment_longitude` (`apartment_longitude`),
  KEY `apartment_latitude` (`apartment_latitude`),
  KEY `available_start` (`available_start`),
  KEY `available_finish` (`available_finish`),
  KEY `apartment_status` (`apartment_status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `apartment_accounts` (
  `aaccount_id` int(12) NOT NULL auto_increment,
  `apartment_id` int(12) NOT NULL default '0',
  `aaccount_company` varchar(128) NOT NULL default '',
  `aaccount_custnumber` varchar(128) NOT NULL default '',
  `aaccount_details` text NOT NULL,
  `updated_last` bigint(64) NOT NULL default '0',
  `updated_by` int(12) NOT NULL default '0',
  `account_status` varchar(12) NOT NULL default '',
  PRIMARY KEY  (`aaccount_id`),
  KEY `apartment_id` (`apartment_id`),
  KEY `aaccount_company` (`aaccount_company`),
  KEY `aaccount_custnumber` (`aaccount_custnumber`),
  KEY `account_status` (`account_status`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `apartment_photos` (
  `aphoto_id` int(12) NOT NULL auto_increment,
  `apartment_id` int(12) NOT NULL default '0',
  `aphoto_name` varchar(64) NOT NULL default '',
  `aphoto_type` varchar(32) NOT NULL default '',
  `aphoto_size` int(32) NOT NULL default '0',
  `aphoto_desc` text NOT NULL,
  PRIMARY KEY  (`aphoto_id`),
  KEY `apartment_id` (`apartment_id`),
  KEY `aphoto_name` (`aphoto_name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `apartment_schedule` (
  `aschedule_id` int(12) NOT NULL auto_increment,
  `apartment_id` int(12) NOT NULL default '0',
  `event_id` int(12) NOT NULL default '0',
  `econtact_id` int(12) NOT NULL default '0',
  `econtact_notes` text NOT NULL,
  `inhabiting_start` bigint(64) NOT NULL default '0',
  `inhabiting_finish` bigint(64) NOT NULL default '0',
  `updated_last` bigint(64) NOT NULL default '0',
  `updated_by` int(12) NOT NULL default '0',
  `aschedule_status` varchar(12) NOT NULL default '',
  PRIMARY KEY  (`aschedule_id`),
  KEY `apartment_id` (`apartment_id`),
  KEY `event_id` (`event_id`),
  KEY `econtact_id` (`econtact_id`),
  KEY `inhabiting_start` (`inhabiting_start`),
  KEY `inhabiting_finish` (`inhabiting_finish`),
  KEY `aschedule_status` (`aschedule_status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `categories` (
  `category_id` int(12) NOT NULL auto_increment,
  `category_parent` int(12) NOT NULL default '0',
  `category_code` varchar(12) default NULL,
  `category_type` int(12) NOT NULL default '0',
  `category_name` varchar(128) NOT NULL default '',
  `category_desc` text,
  `category_min` int(12) default NULL,
  `category_max` int(12) default NULL,
  `category_buffer` int(12) default NULL,
  `category_start` bigint(64) NOT NULL default '0',
  `category_finish` bigint(64) NOT NULL default '0',
  `subcategory_strict` int(1) NOT NULL default '0',
  `category_expiry` bigint(64) NOT NULL default '0',
  `category_status` varchar(12) NOT NULL default 'published',
  `category_order` int(3) NOT NULL default '0',
  `rotation_id` int(12) NOT NULL default '0',
  PRIMARY KEY  (`category_id`),
  KEY `category_parent` (`category_parent`),
  KEY `category_code` (`category_code`),
  KEY `category_type` (`category_type`),
  KEY `category_name` (`category_name`),
  KEY `category_min` (`category_min`),
  KEY `category_max` (`category_max`),
  KEY `category_start` (`category_start`),
  KEY `category_finish` (`category_finish`),
  KEY `subcategory_strict` (`subcategory_strict`),
  KEY `category_expiry` (`category_expiry`),
  KEY `category_status` (`category_status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `category_departments` (
  `cdep_id` int(12) NOT NULL auto_increment,
  `category_id` int(12) NOT NULL default '0',
  `department_id` int(12) NOT NULL default '0',
  `contact_id` int(12) NOT NULL default '0',
  PRIMARY KEY  (`cdep_id`),
  KEY `category_id` (`category_id`),
  KEY `department_id` (`department_id`),
  KEY `contact_id` (`contact_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `category_type` (
  `ctype_id` int(12) NOT NULL auto_increment,
  `ctype_parent` int(12) NOT NULL default '0',
  `ctype_name` varchar(128) NOT NULL default '',
  `ctype_desc` text NOT NULL,
  `require_min` int(11) NOT NULL default '0',
  `require_max` int(11) NOT NULL default '0',
  `require_buffer` int(11) NOT NULL default '0',
  `require_start` int(11) NOT NULL default '0',
  `require_finish` int(11) NOT NULL default '0',
  `require_expiry` int(11) NOT NULL default '0',
  `ctype_filterable` int(11) NOT NULL default '0',
  `ctype_order` int(3) NOT NULL default '0',
  PRIMARY KEY  (`ctype_id`),
  KEY `ctype_parent` (`ctype_parent`),
  KEY `require_start` (`require_start`),
  KEY `require_finish` (`require_finish`),
  KEY `require_expiry` (`require_expiry`),
  KEY `ctype_filterable` (`ctype_filterable`),
  KEY `ctype_order` (`ctype_order`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `category_type` (`ctype_id`, `ctype_parent`, `ctype_name`, `ctype_desc`, `require_min`, `require_max`, `require_buffer`, `require_start`, `require_finish`, `require_expiry`, `ctype_filterable`, `ctype_order`) VALUES
(1, 30, 'Institution', '', 0, 0, 0, 0, 0, 0, 0, 0),
(2, 30, 'Faculty', '', 0, 0, 0, 0, 0, 0, 0, 0),
(12, 30, 'School', '', 0, 0, 0, 0, 0, 0, 0, 0),
(13, 30, 'Graduating Year', '', 0, 0, 0, 0, 0, 0, 0, 0),
(14, 30, 'Phase', '', 0, 0, 0, 0, 0, 0, 0, 0),
(15, 30, 'Unit', '', 0, 0, 0, 0, 0, 0, 0, 0),
(16, 30, 'Block', '', 0, 0, 0, 0, 0, 0, 0, 0),
(17, 30, 'Stream', '', 0, 0, 0, 0, 0, 0, 0, 0),
(19, 30, 'Selective', '', 0, 0, 0, 0, 0, 0, 0, 0),
(20, 30, 'Course Grouping', '', 0, 0, 0, 0, 0, 0, 0, 0),
(21, 30, 'Course', '', 0, 0, 0, 0, 0, 0, 0, 0),
(22, 30, 'Date Period', '', 0, 0, 0, 0, 0, 0, 0, 0),
(23, 0, 'Downtime', '', 0, 0, 0, 0, 0, 0, 0, 1),
(24, 23, 'Holiday Period', '', 0, 0, 0, 0, 0, 0, 0, 0),
(25, 23, 'Vacation Period', '', 0, 0, 0, 0, 0, 0, 0, 0),
(26, 23, 'Sick Leave', '', 0, 0, 0, 0, 0, 0, 0, 0),
(27, 23, 'Maternity Leave', '', 0, 0, 0, 0, 0, 0, 0, 0),
(28, 23, 'Personal Leave', '', 0, 0, 0, 0, 0, 0, 0, 0),
(29, 23, 'Leave Of Absense', '', 0, 0, 0, 0, 0, 0, 0, 0),
(30, 0, 'Default Types', '', 0, 0, 0, 0, 0, 0, 0, 0),
(31, 30, 'Elective', '', 0, 0, 0, 0, 0, 0, 0, 0),
(32, 30, 'Rotation', '', 0, 0, 0, 0, 0, 0, 0, 0);

CREATE TABLE IF NOT EXISTS `electives` (
  `electives_id` int(12) NOT NULL auto_increment,
  `event_id` int(12) NOT NULL,
  `geo_location` varchar(15) NOT NULL default 'National',
  `department_id` int(12) NOT NULL,
  `discipline_id` int(11) NOT NULL,
  `sub_discipline` varchar(100) default NULL,
  `schools_id` int(11) NOT NULL,
  `other_medical_school` varchar(150) default NULL,
  `objective` text NOT NULL,
  `preceptor_first_name` varchar(50) default NULL,
  `preceptor_last_name` varchar(50) NOT NULL,
  `address` varchar(250) NOT NULL,
  `countries_id` int(12) NOT NULL,
  `city` varchar(100) NOT NULL,
  `prov_state` varchar(200) NOT NULL,
  `region_id` int(12) NOT NULL default '0',
  `postal_zip_code` varchar(20) default NULL,
  `fax` varchar(25) default NULL,
  `phone` varchar(25) default NULL,
  `email` varchar(150) NOT NULL,
  `updated_date` bigint(64) NOT NULL,
  `updated_by` int(12) NOT NULL,
  PRIMARY KEY  (`electives_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `evaluations` (
  `item_id` int(12) NOT NULL auto_increment,
  `form_id` int(12) NOT NULL default '0',
  `category_id` int(12) NOT NULL default '0',
  `category_recurse` int(2) NOT NULL default '1',
  `item_title` varchar(128) NOT NULL default '',
  `item_maxinstances` int(4) NOT NULL default '1',
  `item_start` int(12) NOT NULL default '1',
  `item_end` int(12) NOT NULL default '30',
  `item_status` varchar(12) NOT NULL default 'published',
  `modified_last` bigint(64) NOT NULL default '0',
  `modified_by` int(12) NOT NULL default '0',
  PRIMARY KEY  (`item_id`),
  KEY `form_id` (`form_id`),
  KEY `category_id` (`category_id`),
  KEY `item_status` (`item_status`),
  KEY `item_end` (`item_end`),
  KEY `item_start` (`item_start`),
  KEY `item_maxinstances` (`item_maxinstances`),
  KEY `category_recurse` (`category_recurse`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `eval_answers` (
  `answer_id` int(12) NOT NULL auto_increment,
  `question_id` int(12) NOT NULL default '0',
  `answer_type` varchar(50) NOT NULL default '',
  `answer_label` varchar(50) NOT NULL default '',
  `answer_value` varchar(50) NOT NULL default '',
  `answer_lastmod` bigint(64) default NULL,
  PRIMARY KEY  (`answer_id`),
  KEY `question_id` (`question_id`),
  KEY `answer_value` (`answer_value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `eval_approved` (
  `approved_id` int(12) NOT NULL auto_increment,
  `notification_id` int(12) NOT NULL default '0',
  `completed_id` int(12) NOT NULL default '0',
  `modified_last` bigint(64) NOT NULL default '0',
  `modified_by` int(12) NOT NULL default '0',
  PRIMARY KEY  (`approved_id`),
  KEY `notification_id` (`notification_id`),
  KEY `completed_id` (`completed_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `eval_completed` (
  `completed_id` int(12) NOT NULL auto_increment,
  `notification_id` int(12) NOT NULL default '0',
  `instructor_id` varchar(24) NOT NULL default '0',
  `completed_status` varchar(12) NOT NULL default 'pending',
  `completed_lastmod` bigint(64) NOT NULL default '0',
  PRIMARY KEY  (`completed_id`),
  KEY `notification_id` (`notification_id`),
  KEY `instructor_id` (`instructor_id`),
  KEY `completed_status` (`completed_status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `eval_forms` (
  `form_id` int(12) NOT NULL auto_increment,
  `form_type` varchar(12) NOT NULL default '',
  `nmessage_id` int(12) NOT NULL default '0',
  `form_title` varchar(128) NOT NULL default '',
  `form_author` int(12) NOT NULL default '0',
  `form_desc` text NOT NULL,
  `form_status` varchar(12) NOT NULL default 'published',
  `form_lastmod` bigint(64) default NULL,
  PRIMARY KEY  (`form_id`),
  KEY `form_type` (`form_type`),
  KEY `nmessage_id` (`nmessage_id`),
  KEY `form_status` (`form_status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `eval_questions` (
  `question_id` int(12) NOT NULL auto_increment,
  `form_id` int(12) NOT NULL default '0',
  `question_text` text NOT NULL,
  `question_style` varchar(50) NOT NULL default '',
  `question_required` varchar(50) NOT NULL default '',
  `question_lastmod` bigint(64) default NULL,
  PRIMARY KEY  (`question_id`),
  KEY `form_id` (`form_id`),
  KEY `question_required` (`question_required`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `eval_results` (
  `result_id` int(12) NOT NULL auto_increment,
  `completed_id` int(12) NOT NULL default '0',
  `answer_id` int(12) NOT NULL default '0',
  `result_value` text NOT NULL,
  `result_lastmod` bigint(64) NOT NULL default '0',
  PRIMARY KEY  (`result_id`),
  KEY `completed_id` (`completed_id`),
  KEY `answer_id` (`answer_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `events` (
  `event_id` int(12) NOT NULL auto_increment,
  `category_id` int(12) NOT NULL default '0',
  `rotation_id` int(12) NOT NULL default '0',
  `region_id` int(12) NOT NULL default '0',
  `event_title` varchar(255) NOT NULL default '',
  `event_desc` text,
  `event_start` bigint(64) NOT NULL default '0',
  `event_finish` bigint(64) NOT NULL default '0',
  `event_expiry` bigint(64) NOT NULL default '0',
  `accessible_start` bigint(64) NOT NULL default '0',
  `accessible_finish` bigint(64) NOT NULL default '0',
  `modified_last` bigint(64) NOT NULL default '0',
  `modified_by` int(12) NOT NULL default '0',
  `event_type` varchar(12) NOT NULL default 'academic',
  `event_access` varchar(12) NOT NULL default 'public',
  `event_status` varchar(12) NOT NULL default 'published',
  PRIMARY KEY  (`event_id`),
  KEY `category_id` (`category_id`),
  KEY `region_id` (`region_id`),
  KEY `event_type` (`event_type`),
  KEY `event_access` (`event_access`),
  KEY `event_status` (`event_status`),
  KEY `accessible_finish` (`accessible_finish`),
  KEY `accessible_start` (`accessible_start`),
  KEY `event_expiry` (`event_expiry`),
  KEY `event_finish` (`event_finish`),
  KEY `event_start` (`event_start`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `event_contacts` (
  `econtact_id` int(12) NOT NULL auto_increment,
  `event_id` int(12) NOT NULL default '0',
  `econtact_type` varchar(12) NOT NULL default 'student',
  `etype_id` int(12) NOT NULL default '0',
  `econtact_parent` int(12) NOT NULL default '0',
  `econtact_desc` text,
  `econtact_start` bigint(64) NOT NULL default '0',
  `econtact_finish` bigint(64) NOT NULL default '0',
  `econtact_status` varchar(12) NOT NULL default 'published',
  `econtact_order` int(3) NOT NULL default '0',
  PRIMARY KEY  (`econtact_id`),
  KEY `event_id` (`event_id`),
  KEY `econtact_type` (`econtact_type`),
  KEY `etype_id` (`etype_id`),
  KEY `econtact_parent` (`econtact_parent`),
  KEY `econtact_order` (`econtact_order`),
  KEY `econtact_status` (`econtact_status`),
  KEY `econtact_finish` (`econtact_finish`),
  KEY `econtact_start` (`econtact_start`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `event_locations` (
  `elocation_id` int(12) NOT NULL auto_increment,
  `event_id` int(12) NOT NULL default '0',
  `location_id` int(12) NOT NULL default '0',
  `elocation_start` bigint(64) NOT NULL default '0',
  `elocation_finish` bigint(64) NOT NULL default '0',
  `elocation_status` varchar(12) NOT NULL default 'published',
  `elocation_order` int(3) NOT NULL default '0',
  PRIMARY KEY  (`elocation_id`),
  KEY `event_id` (`event_id`),
  KEY `location_id` (`location_id`),
  KEY `elocation_start` (`elocation_start`),
  KEY `elocation_finish` (`elocation_finish`),
  KEY `elocation_status` (`elocation_status`),
  KEY `elocation_order` (`elocation_order`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `notifications` (
  `notification_id` int(12) NOT NULL auto_increment,
  `user_id` int(12) NOT NULL default '0',
  `event_id` int(12) NOT NULL default '0',
  `category_id` int(12) NOT NULL default '0',
  `item_id` int(12) NOT NULL default '0',
  `item_maxinstances` int(4) NOT NULL default '1',
  `notification_status` varchar(16) NOT NULL default 'initiated',
  `notified_last` bigint(64) NOT NULL default '0',
  PRIMARY KEY  (`notification_id`),
  KEY `user_id` (`user_id`),
  KEY `event_id` (`event_id`),
  KEY `category_id` (`category_id`),
  KEY `item_id` (`item_id`),
  KEY `item_maxinstances` (`item_maxinstances`),
  KEY `notification_status` (`notification_status`),
  KEY `notified_last` (`notified_last`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `notification_log` (
  `nlog_id` int(12) NOT NULL auto_increment,
  `notification_id` int(12) NOT NULL default '0',
  `user_id` int(12) NOT NULL default '0',
  `nlog_timestamp` bigint(64) NOT NULL default '0',
  `nlog_address` varchar(128) NOT NULL default '',
  `nlog_message` text NOT NULL,
  `prune_after` bigint(64) NOT NULL default '0',
  PRIMARY KEY  (`nlog_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `notification_messages` (
  `nmessage_id` int(12) NOT NULL auto_increment,
  `form_type` varchar(12) NOT NULL default 'rotation',
  `nmessage_title` varchar(128) NOT NULL default '',
  `nmessage_version` int(4) NOT NULL default '0',
  `nmessage_from_email` varchar(128) NOT NULL default 'eval@meds.queensu.ca',
  `nmessage_from_name` varchar(64) NOT NULL default 'Evaluation System',
  `nmessage_reply_email` varchar(128) NOT NULL default 'eval@meds.queensu.ca',
  `nmessage_reply_name` varchar(64) NOT NULL default 'Evaluation System',
  `nmessage_subject` varchar(255) NOT NULL default '',
  `nmessage_body` text NOT NULL,
  `modified_last` bigint(64) NOT NULL default '0',
  `modified_by` int(12) NOT NULL default '0',
  `nmessage_status` varchar(12) NOT NULL default 'published',
  PRIMARY KEY  (`nmessage_id`),
  KEY `form_type` (`form_type`),
  KEY `nmessage_version` (`nmessage_version`),
  KEY `nmessage_status` (`nmessage_status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `notification_monitor` (
  `nmonitor_id` int(12) NOT NULL auto_increment,
  `item_id` int(12) NOT NULL default '0',
  `form_id` int(12) NOT NULL default '0',
  `category_id` int(12) NOT NULL default '0',
  `category_recurse` int(2) NOT NULL default '1',
  `item_title` varchar(128) NOT NULL default '',
  `item_maxinstances` int(12) NOT NULL default '1',
  `item_start` int(12) NOT NULL default '1',
  `item_end` int(12) NOT NULL default '30',
  `item_status` varchar(12) NOT NULL default 'published',
  `modified_last` bigint(64) NOT NULL default '0',
  `modified_by` int(12) NOT NULL default '0',
  PRIMARY KEY  (`nmonitor_id`),
  KEY `item_id` (`item_id`),
  KEY `form_id` (`form_id`),
  KEY `category_id` (`category_id`),
  KEY `category_recurse` (`category_recurse`),
  KEY `item_start` (`item_start`),
  KEY `item_end` (`item_end`),
  KEY `item_status` (`item_status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `other_teachers` (
  `oteacher_id` int(12) NOT NULL auto_increment,
  `proxy_id` int(12) NOT NULL default '0',
  `firstname` varchar(50) NOT NULL default '',
  `lastname` varchar(50) NOT NULL default '',
  `email` varchar(150) NOT NULL default '',
  PRIMARY KEY  (`oteacher_id`),
  KEY `proxy_id` (`proxy_id`),
  KEY `firstname` (`firstname`),
  KEY `lastname` (`lastname`),
  KEY `email` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `regions` (
  `region_id` int(12) NOT NULL auto_increment,
  `region_name` varchar(64) NOT NULL default '',
  `manage_apartments` int(1) NOT NULL default '0',
  `is_core` int(1) NOT NULL default '0',
  `countries_id` int(12) default NULL,
  `prov_state` varchar(200) default NULL,
  PRIMARY KEY  (`region_id`),
  KEY `region_name` (`region_name`),
  KEY `manage_apartments` (`manage_apartments`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `global_lu_rotations` (
  `rotation_id` int(12) unsigned NOT NULL auto_increment,
  `rotation_title` varchar(24) default NULL,
  `percent_required` int(3) NOT NULL,
  `percent_period_complete` int(3) NOT NULL,
  `course_id` int(12) NOT NULL default '0',
  PRIMARY KEY  (`rotation_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `global_lu_rotations` (`rotation_id`, `rotation_title`, `percent_required`, `percent_period_complete`, `course_id`) VALUES
(1, 'Family Medicine', 50, 50, 0),
(2, 'Medicine', 50, 50, 0),
(3, 'Obstetrics & Gynecology', 50, 50, 0),
(4, 'Pediatrics', 50, 50, 0),
(5, 'Perioperative', 50, 50, 0),
(6, 'Psychiatry', 50, 50, 0),
(7, 'Surgery-Urology', 50, 50, 0),
(8, 'Surgery-Orthopedic', 50, 50, 0),
(9, 'Integrated', 50, 50, 0);

CREATE TABLE IF NOT EXISTS `logbook_entries` (
  `lentry_id` int(12) unsigned NOT NULL auto_increment,
  `proxy_id` int(12) unsigned NOT NULL,
  `encounter_date` int(12) NOT NULL,
  `updated_date` bigint(64) unsigned NOT NULL default '0',
  `patient_info` varchar(30) NOT NULL,
  `agerange_id` int(12) unsigned NOT NULL default '0',
  `gender` varchar(1) NOT NULL default '0',
  `rotation_id` int(12) unsigned NOT NULL default '0',
  `llocation_id` int(12) unsigned NOT NULL default '0',
  `lsite_id` int(11) NOT NULL,
  `comments` text default NULL,
  `reflection` text NOT NULL default '',
  `participation_level` int(2) NOT NULL default '2',
  `entry_active` int(1) NOT NULL default '1',
  PRIMARY KEY  (`lentry_id`),
  KEY `proxy_id` (`proxy_id`,`updated_date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `logbook_entry_evaluations` (
  `leevaluation_id` int(11) unsigned NOT NULL auto_increment,
  `levaluation_id` int(12) unsigned NOT NULL,
  `item_status` int(1) NOT NULL default '0',
  `updated_date` bigint(64) NOT NULL,
  PRIMARY KEY  (`leevaluation_id`),
  UNIQUE KEY `levaluation_id` (`levaluation_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `logbook_entry_checklist` (
  `lechecklist_id` int(12) unsigned NOT NULL auto_increment,
  `proxy_id` int(12) unsigned NOT NULL,
  `rotation_id` int(12) unsigned NOT NULL,
  `checklist` bigint(64) unsigned NOT NULL,
  `updated_date` bigint(64) NOT NULL,
  PRIMARY KEY  (`lechecklist_id`),
  UNIQUE KEY `proxy_id` (`proxy_id`,`rotation_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `logbook_entry_objectives` (
  `leobjective_id` int(12) unsigned NOT NULL auto_increment,
  `lentry_id` int(12) unsigned NOT NULL default '0',
  `objective_id` int(12) unsigned NOT NULL default '0',
  PRIMARY KEY  (`leobjective_id`,`lentry_id`,`objective_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `logbook_entry_procedures` (
  `leprocedure_id` int(12) unsigned NOT NULL auto_increment,
  `lentry_id` int(12) unsigned NOT NULL default '0',
  `lprocedure_id` int(12) unsigned NOT NULL default '0',
  `level` smallint(6) NOT NULL COMMENT 'Level of involvement',
  PRIMARY KEY  (`leprocedure_id`,`lentry_id`,`lprocedure_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `logbook_lu_agerange` (
  `agerange_id` int(12) unsigned NOT NULL auto_increment,
  `rotation_id` int(12) unsigned NOT NULL default '0',
  `age` varchar(8) default NULL,
  PRIMARY KEY  (`agerange_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `logbook_lu_agerange` (`agerange_id`, `rotation_id`, `age`) VALUES
(1, 0, '  < 1'),
(2, 0, ' 1 - 4'),
(3, 0, ' 5 - 14'),
(4, 0, '15 - 24'),
(5, 0, '25 - 34'),
(6, 0, '35 - 44'),
(7, 0, '45 - 54'),
(8, 0, '55 - 64'),
(9, 0, '65 - 74'),
(10, 0, '  75+'),
(11, 5, '  < 1m'),
(12, 5, '  < 1w'),
(13, 5, '  < 6m'),
(14, 5, '  < 12m'),
(15, 5, '  < 60m'),
(16, 5, '  5-12'),
(17, 5, '13 - 19'),
(18, 5, '20 - 64'),
(19, 6, ' 5 - 11'),
(20, 6, '12 - 17'),
(21, 6, '18 - 34');

CREATE TABLE IF NOT EXISTS `logbook_lu_evaluations` (
  `levaluation_id` int(12) unsigned NOT NULL auto_increment,
  `rotation_id` int(12) unsigned NOT NULL,
  `line` int(11) default NULL,
  `type` int(11) NOT NULL,
  `indent` int(11) default NULL,
  `item` varchar(255) NOT NULL,
  PRIMARY KEY  (`levaluation_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `logbook_lu_checklist` (
  `lchecklist_id` int(12) unsigned NOT NULL auto_increment,
  `rotation_id` int(12) unsigned NOT NULL,
  `line` int(11) default NULL,
  `type` int(11) NOT NULL,
  `indent` int(11) default NULL,
  `item` varchar(255) NOT NULL,
  PRIMARY KEY  (`lchecklist_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `logbook_lu_checklist` (`lchecklist_id`, `rotation_id`, `line`, `type`, `indent`, `item`) VALUES
(1, 2, 1, 1, 0, 'Checklist for Family Medicine:'),
(2, 2, 2, 2, 2, 'Learning Plan (see core doc, p. 26)'),
(3, 2, 3, 1, 1, 'Midpoint Review (to be completed by 3rd Friday of rotation)'),
(4, 2, 5, 2, 2, 'Review Logbook with Preceptor'),
(5, 2, 6, 2, 2, 'Review Learning Plan'),
(6, 2, 7, 2, 2, 'Formative MCQ Exam (on 3rd Friday of rotation)'),
(7, 2, 8, 1, 1, 'In the final (6th) week:'),
(8, 2, 9, 2, 2, 'Show completed logbook'),
(9, 2, 10, 2, 2, 'Present Project  to clinic (by 6th Friday; see core doc, p. 28)'),
(10, 2, 11, 2, 2, 'Present Project to peers/examiners (by 6th Fri; core doc, p.28)'),
(11, 2, 12, 2, 2, 'Have completed 4 mini-CEX (see core doc, p. 28)'),
(12, 2, 13, 2, 2, 'Final ITER (due on 6th Friday of rotation)'),
(13, 2, 14, 2, 2, 'Summative MCQ Exam (on 6th Friday of rotation)'),
(14, 1, 1, 1, 0, 'Checklist for Emergency Medicine:'),
(15, 1, 2, 2, 2, 'Daily Shift Reports'),
(16, 1, 3, 1, 1, 'Midpoint Review '),
(17, 1, 4, 2, 2, 'Review Logbook with Preceptor'),
(18, 1, 5, 1, 1, 'In the final week:'),
(19, 1, 6, 2, 2, 'Show completed logbook'),
(20, 1, 7, 2, 2, 'Final ITER '),
(21, 1, 8, 2, 2, 'Summative MCQ Exam '),
(22, 3, 1, 1, 0, 'Checklist for Internal Medicine:'),
(23, 3, 2, 1, 1, 'Midpoint Review (to be completed by 6th Friday of rotation)'),
(24, 3, 3, 2, 2, 'Review Logbook with Preceptor'),
(25, 3, 4, 2, 2, 'Formative MCQ Exam (on 6th Friday of rotation)'),
(26, 3, 5, 2, 2, 'Formative Mid-term OSCE'),
(27, 3, 6, 1, 1, 'In the final (12th) week:'),
(28, 3, 7, 2, 2, 'Show completed logbook'),
(29, 3, 8, 2, 2, 'Final ITER (due on 12th Friday of rotation)'),
(30, 3, 9, 2, 2, 'Summative MCQ Exam'),
(31, 4, 1, 1, 0, 'Checklist for Obstetrics & Gynecology:'),
(32, 4, 2, 1, 1, 'Midpoint Review (to be completed by 2nd week of rotation)'),
(33, 4, 3, 2, 2, 'Review Logbook with Preceptor'),
(34, 4, 4, 2, 2, 'Formative MCQ Exam (on 2nd week of rotation)'),
(35, 4, 5, 1, 1, 'In the final (4th) week:'),
(36, 4, 6, 2, 2, 'Show completed logbook'),
(37, 4, 7, 2, 2, 'Final ITER (due on 4th Friday of rotation)'),
(38, 4, 8, 2, 2, 'Summative MCQ Exam'),
(39, 5, 1, 1, 0, 'Checklist for Pediatrics:'),
(40, 5, 2, 1, 1, 'Midpoint Review (to be completed by mid rotation)'),
(41, 5, 3, 2, 2, 'Review Logbook with Preceptor'),
(42, 5, 4, 2, 2, 'Formative OSCE (mid-point of rotation)'),
(43, 5, 5, 1, 1, 'In the final week:'),
(44, 5, 6, 2, 2, 'Show completed logbook'),
(45, 5, 7, 2, 2, 'Final ITER'),
(46, 5, 8, 2, 2, 'Summative MCQ Exam'),
(47, 6, 1, 1, 0, 'Checklist for Psychiatry:'),
(48, 6, 2, 1, 1, 'Midpoint Review (to be completed by mid rotation)'),
(49, 6, 3, 2, 2, 'Review Logbook with Preceptor'),
(50, 6, 4, 2, 2, 'Formative VOSCE (mid-point of rotation)'),
(51, 6, 5, 1, 1, 'In the final week:'),
(52, 6, 6, 2, 2, 'Show completed logbook'),
(53, 6, 7, 2, 2, 'Final ITER'),
(54, 6, 8, 2, 2, 'Summative MCQ Exam'),
(55, 6, 9, 2, 2, 'Evaluation of Psychiatric Interviewing Skills'),
(56, 7, 1, 1, 0, 'Checklist for Surgery / Anesthesia:'),
(57, 7, 2, 1, 1, 'Midpoint Review (to be completed by mid rotation)'),
(58, 7, 3, 2, 2, 'Review Logbook with Preceptor'),
(59, 7, 4, 2, 2, 'Formative MCQ (mid-point of rotation)'),
(60, 7, 5, 1, 1, 'In the final week:'),
(61, 7, 6, 2, 2, 'Show completed logbook'),
(62, 7, 7, 2, 2, 'Final ITER'),
(63, 7, 8, 2, 2, 'Summative MCQ Exam');

CREATE TABLE IF NOT EXISTS `logbook_lu_locations` (
  `llocation_id` int(12) unsigned NOT NULL auto_increment,
  `location` varchar(64) default NULL,
  PRIMARY KEY  (`llocation_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `logbook_lu_locations` (`llocation_id`, `location`) VALUES
(1, 'Office / Clinic'),
(2, 'Hospital Ward'),
(3, 'Emergency'),
(4, 'OR'),
(5, 'OSCE'),
(6, 'Bedside Teaching Rounds'),
(7, 'Case Base Teaching Rounds'),
(8, 'Patients Home'),
(9, 'Nursing Home'),
(10, 'Community Site'),
(11, 'Computer Interactive Case'),
(12, 'Day Surgery'),
(13, 'Mega code'),
(14, 'Seminar Blocks'),
(15, 'HPS'),
(16, 'Nursery');

CREATE TABLE IF NOT EXISTS `logbook_lu_procedures` (
  `lprocedure_id` int(12) unsigned NOT NULL auto_increment,
  `procedure` varchar(60) NOT NULL,
  PRIMARY KEY  (`lprocedure_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `logbook_lu_procedures` (`lprocedure_id`, `procedure`) VALUES
(1, 'ABG'),
(2, 'Dictation-discharge'),
(3, 'Dictation-letter'),
(4, 'Cervical exam/labour'),
(5, 'Delivery, norm vaginal'),
(6, 'Delivery, placenta'),
(7, 'PAP smear'),
(8, 'Pelvic exam'),
(9, 'Perineal repair'),
(10, 'Pessary insert/remove'),
(11, 'Growth curve'),
(12, 'Infant/child immun'),
(13, 'Otoscopy, child'),
(14, 'Cast/splint'),
(15, 'ETT intubation'),
(16, 'Facemask ventilation'),
(17, 'IV catheter'),
(18, 'IV setup'),
(19, 'OR monitors'),
(20, 'PCA setup'),
(21, 'Slit lamp exam'),
(22, 'Suturing'),
(23, 'Venipuncture'),
(24, 'NG tube'),
(25, 'Surgical technique/OR assist');

CREATE TABLE IF NOT EXISTS `logbook_lu_sites` (
  `lsite_id` int(11) NOT NULL auto_increment,
  `site_type` int(11) default NULL,
  `site_name` varchar(64) NOT NULL,
  PRIMARY KEY  (`lsite_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `logbook_lu_sites` (`lsite_id`, `site_type`, `site_name`) VALUES
(1, 1, 'Brockville General Hospital'),
(2, 1, 'Brockville Pyschiatric Hospital'),
(3, 1, 'CHEO'),
(4, 1, 'Hotel Dieu Hospital'),
(5, 1, 'Kingston General Hospital'),
(6, 1, 'Lakeridge Health'),
(7, 1, 'Markam Stouffville Hospital'),
(8, 1, 'Ongwanada'),
(9, 1, 'Peterborough Regional Health Centre'),
(10, 1, 'Providence Continuing Care Centre'),
(11, 1, 'Queensway Carleton Hospital'),
(12, 1, 'Quinte Health Care'),
(13, 1, 'Weenebayko General Hospital'),
(14, 1, 'Queen''s University');

CREATE TABLE IF NOT EXISTS `logbook_mandatory_objectives` (
  `lmobjective_id` int(12) unsigned NOT NULL auto_increment,
  `rotation_id` int(12) unsigned default NULL,
  `objective_id` int(12) unsigned default NULL,
  `number_required` int(2) NOT NULL default '1',
  PRIMARY KEY  (`lmobjective_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `logbook_mandatory_objectives` (`lmobjective_id`, `rotation_id`, `objective_id`, `number_required`) VALUES
(1, 8, 201, 1),
(2, 8, 202, 1),
(3, 8, 203, 1),
(4, 8, 207, 1),
(5, 8, 208, 1),
(6, 8, 209, 1),
(7, 8, 210, 1),
(8, 8, 211, 1),
(9, 8, 212, 1),
(10, 8, 213, 1),
(11, 8, 214, 1),
(12, 6, 215, 1),
(13, 8, 216, 1),
(14, 7, 204, 1),
(15, 7, 205, 1),
(16, 7, 206, 1),
(17, 7, 207, 1),
(18, 7, 208, 1),
(19, 7, 209, 1),
(20, 7, 210, 1),
(21, 7, 211, 1),
(22, 7, 212, 1),
(23, 7, 213, 1),
(24, 7, 214, 1),
(25, 7, 216, 1),
(26, 6, 217, 1),
(27, 6, 218, 1),
(28, 6, 219, 1),
(29, 5, 220, 1),
(30, 6, 221, 1),
(31, 5, 222, 1),
(32, 5, 223, 1),
(33, 5, 224, 1),
(34, 5, 225, 1),
(35, 5, 226, 1),
(36, 5, 227, 2),
(37, 5, 228, 1),
(38, 5, 229, 2),
(39, 5, 230, 1),
(40, 5, 201, 1),
(41, 5, 202, 1),
(42, 5, 233, 1),
(43, 4, 234, 1),
(44, 4, 235, 1),
(45, 4, 236, 1),
(46, 4, 237, 1),
(47, 4, 238, 1),
(48, 4, 239, 1),
(49, 4, 240, 1),
(50, 4, 241, 1),
(51, 4, 242, 1),
(52, 4, 243, 1),
(53, 4, 244, 1),
(54, 4, 245, 1),
(55, 4, 246, 1),
(56, 4, 247, 1),
(57, 4, 248, 1),
(58, 4, 249, 1),
(59, 4, 250, 1),
(60, 4, 251, 1),
(61, 4, 252, 1),
(62, 4, 253, 1),
(63, 4, 254, 1),
(64, 4, 255, 1),
(65, 4, 256, 1),
(66, 3, 257, 1),
(67, 3, 258, 1),
(68, 3, 259, 1),
(69, 3, 260, 1),
(70, 3, 261, 1),
(71, 3, 262, 1),
(72, 3, 263, 1),
(73, 3, 264, 1),
(74, 3, 265, 1),
(75, 3, 266, 1),
(76, 3, 267, 1),
(77, 2, 268, 1),
(78, 2, 269, 1),
(79, 2, 270, 1),
(80, 2, 271, 1),
(81, 2, 272, 1),
(82, 2, 273, 1),
(83, 2, 274, 1),
(84, 2, 275, 1),
(85, 2, 276, 1),
(86, 2, 277, 1),
(87, 2, 278, 1),
(88, 2, 279, 1),
(89, 2, 280, 1),
(90, 2, 281, 1),
(91, 2, 282, 1),
(92, 2, 283, 1),
(93, 2, 284, 1),
(94, 2, 285, 1),
(95, 2, 286, 1),
(96, 2, 287, 1),
(97, 2, 288, 1),
(98, 1, 289, 1),
(99, 1, 290, 1),
(100, 1, 291, 1),
(101, 1, 292, 1),
(102, 1, 293, 1),
(103, 1, 294, 1),
(104, 1, 295, 3),
(105, 1, 296, 1),
(106, 1, 221, 1),
(107, 1, 276, 1),
(108, 1, 299, 1),
(109, 1, 300, 1),
(110, 1, 242, 1),
(111, 1, 281, 1),
(112, 1, 303, 1),
(113, 1, 284, 1);

CREATE TABLE IF NOT EXISTS `logbook_notification_history` (
  `lnhistory_id` int(12) NOT NULL auto_increment,
  `clerk_id` int(12) NOT NULL,
  `proxy_id` int(12) NOT NULL,
  `rotation_id` int(12) NOT NULL,
  `notified_date` int(12) NOT NULL,
  PRIMARY KEY  (`lnhistory_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `logbook_overdue` (
  `lologging_id` int(12) NOT NULL auto_increment,
  `proxy_id` int(12) NOT NULL,
  `rotation_id` int(12) NOT NULL,
  `event_id` int(12) NOT NULL,
  `logged_required` int(12) NOT NULL,
  `logged_completed` int(12) NOT NULL default '0',
  PRIMARY KEY  (`lologging_id`),
  UNIQUE KEY `lologging_id` (`lologging_id`,`proxy_id`,`event_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `logbook_preferred_procedures` (
  `lpprocedure_id` int(12) unsigned NOT NULL auto_increment,
  `rotation_id` int(12) unsigned NOT NULL,
  `order` smallint(6) NOT NULL,
  `lprocedure_id` int(12) unsigned NOT NULL,
  `number_required` int(2) NOT NULL default '1',
  PRIMARY KEY  (`lpprocedure_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `logbook_preferred_procedures` (`lpprocedure_id`, `rotation_id`, `order`, `lprocedure_id`, `number_required`) VALUES
(1, 2, 0, 1, 1),
(2, 2, 0, 2, 1),
(3, 2, 0, 3, 1),
(4, 3, 0, 4, 1),
(5, 3, 0, 5, 1),
(6, 3, 0, 6, 1),
(7, 3, 0, 7, 1),
(8, 3, 0, 8, 1),
(9, 3, 0, 9, 1),
(10, 3, 0, 10, 1),
(11, 4, 0, 11, 1),
(12, 4, 0, 12, 1),
(13, 4, 0, 13, 1),
(14, 5, 0, 14, 1),
(15, 5, 0, 15, 1),
(16, 5, 0, 16, 3),
(17, 5, 0, 17, 3),
(18, 5, 0, 18, 1),
(19, 5, 0, 19, 1),
(20, 5, 0, 20, 1),
(21, 5, 0, 21, 1),
(22, 5, 0, 22, 1),
(23, 5, 0, 23, 2),
(24, 7, 0, 24, 1),
(25, 7, 0, 25, 4),
(26, 7, 0, 22, 1),
(27, 8, 0, 24, 1),
(28, 8, 0, 25, 4),
(29, 8, 0, 22, 1);

CREATE TABLE IF NOT EXISTS `logbook_rotation_comments` (
  `lrcomment_id` int(12) NOT NULL auto_increment,
  `clerk_id` int(12) NOT NULL,
  `proxy_id` int(12) NOT NULL default '0',
  `rotation_id` int(12) NOT NULL default '0',
  `comments` text NOT NULL,
  `updated_date` int(12) NOT NULL,
  `comment_active` int(1) NOT NULL default '1',
  PRIMARY KEY  (`lrcomment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `logbook_rotation_notifications` (
  `lrnotification_id` int(12) NOT NULL auto_increment,
  `proxy_id` int(12) NOT NULL,
  `rotation_id` int(12) NOT NULL,
  `notified` int(1) NOT NULL default '0',
  `updated_date` int(12) NOT NULL default '0',
  PRIMARY KEY  (`lrnotification_id`),
  UNIQUE KEY `proxy_id` (`proxy_id`,`rotation_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `logbook_rotation_sites` (
  `lrsite_id` smallint(6) NOT NULL auto_increment,
  `site_description` varchar(255) default NULL,
  `rotation_id` smallint(6) default NULL,
  PRIMARY KEY  (`lrsite_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `logbook_virtual_patients` (
  `lvpatient_id` int(12) unsigned NOT NULL default '0',
  `title` varchar(30) default NULL,
  `url` varchar(200) default NULL,
  PRIMARY KEY  (`lvpatient_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `logbook_virtual_patient_objectives` (
  `lvpobjective_id` int(12) unsigned NOT NULL default '0',
  `objective_id` int(12) unsigned default NULL,
  `lvpatient_id` int(12) unsigned default NULL,
  PRIMARY KEY  (`lvpobjective_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `lottery_clerk_streams` (
  `lcstream_id` int(12) NOT NULL AUTO_INCREMENT,
  `lottery_clerk_id` int(12) NOT NULL DEFAULT '0',
  `category_id` int(12) NOT NULL DEFAULT '0',
  `rationale` text,
  `stream_order` int(12) NOT NULL DEFAULT '0',
  PRIMARY KEY (`lcstream_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `lottery_clerks` (
  `lottery_clerk_id` int(12) NOT NULL AUTO_INCREMENT,
  `proxy_id` int(12) NOT NULL DEFAULT '0',
  `discipline_1` int(12) NOT NULL DEFAULT '0',
  `discipline_2` int(12) NOT NULL DEFAULT '0',
  `discipline_3` int(12) NOT NULL DEFAULT '0',
  `chosen_stream` int(12) NOT NULL DEFAULT '0',
  `chosen_rationale` text,
  `chosen_order` int(12) NOT NULL DEFAULT '0',
  PRIMARY KEY (`lottery_clerk_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;