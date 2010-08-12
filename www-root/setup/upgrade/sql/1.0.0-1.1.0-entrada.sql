CREATE TABLE IF NOT EXISTS `assessments` (
  `assessment_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `course_id` int(10) unsigned NOT NULL,
  `grad_year` int(4) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `type` varchar(255) NOT NULL,
  `marking_scheme_id` int(10) unsigned NOT NULL,
  `numeric_grade_points_total` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`assessment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `assessment_grades` (
  `grade_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `assessment_id` int(10) unsigned NOT NULL,
  `proxy_id` int(10) unsigned NOT NULL,
  `value` int(10) NOT NULL,
  PRIMARY KEY (`grade_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `assessment_marking_schemes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `handler` varchar(255) NOT NULL DEFAULT 'Boolean',
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `assessment_marking_schemes` (`id`,`name`,`handler`,`enabled`) VALUES
(1, 'Pass/Fail', 'Boolean', 1),
(2, 'Percentage', 'Percentage', 1),
(3, 'Numeric', 'Numeric', 1),
(4, 'Complete/Incomplete', 'IncompleteComplete', 1);

ALTER TABLE `courses` ADD COLUMN `objective_type` enum('event','course') DEFAULT 'course' AFTER `importance`;

ALTER TABLE `course_objectives` ADD COLUMN `objective_type` enum('event','course') DEFAULT 'course' AFTER `importance`;

CREATE TABLE IF NOT EXISTS `settings` (
  `shortname` VARCHAR( 64 ) NOT NULL ,
  `value` TEXT NOT NULL ,
  PRIMARY KEY ( `shortname` )
) ENGINE = MYISAM DEFAULT CHARSET=utf8;

INSERT INTO `settings` (`shortname`, `value`) VALUES ('version_db', '1.1.0');