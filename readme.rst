###################
What is CodeIgniter
###################

CodeIgniter is an Application Development Framework - a toolkit - for people
who build web sites using PHP. Its goal is to enable you to develop projects
much faster than you could if you were writing code from scratch, by providing
a rich set of libraries for commonly needed tasks, as well as a simple
interface and logical structure to access these libraries. CodeIgniter lets
you creatively focus on your project by minimizing the amount of code needed
for a given task.

*******************
Release Information
*******************

This repo contains in-development code for future releases. To download the
latest stable release please visit the `CodeIgniter Downloads
<https://codeigniter.com/download>`_ page.

**************************
Changelog and New Features
**************************

You can find a list of all changes for each release in the `user
guide change log <https://github.com/bcit-ci/CodeIgniter/blob/develop/user_guide_src/source/changelog.rst>`_.

*******************
Server Requirements
*******************

PHP version 5.6 or newer is recommended.

It should work on 5.3.7 as well, but we strongly advise you NOT to run
such old versions of PHP, because of potential security and performance
issues, as well as missing features.

************
Installation
************

Please see the `installation section <https://codeigniter.com/user_guide/installation/index.html>`_
of the CodeIgniter User Guide.

*******
License
*******

Please see the `license
agreement <https://github.com/bcit-ci/CodeIgniter/blob/develop/user_guide_src/source/license.rst>`_.

*********
Resources
*********

-  `User Guide <https://codeigniter.com/docs>`_
-  `Language File Translations <https://github.com/bcit-ci/codeigniter3-translations>`_
-  `Community Forums <http://forum.codeigniter.com/>`_
-  `Community Wiki <https://github.com/bcit-ci/CodeIgniter/wiki>`_
-  `Community Slack Channel <https://codeigniterchat.slack.com>`_

Report security issues to our `Security Panel <mailto:security@codeigniter.com>`_
or via our `page on HackerOne <https://hackerone.com/codeigniter>`_, thank you.

***************
Acknowledgement
***************

The CodeIgniter team would like to thank EllisLab, all the
contributors to the CodeIgniter project and you, the CodeIgniter user.



// 12-02-2026
ALTER TABLE `sameepaccounting_quotation_total` ADD `sez` VARCHAR(10) NULL AFTER `approved_by`;

//07-03-2026 -Aditya
ALTER TABLE `sameepaccounting_salesorder` CHANGE `discount` `discount` DOUBLE NULL;

//07-03-2026 -Aditya
ALTER TABLE `sameepaccounting_salesorder`
  DROP `date`,
  DROP `exp_date`;

//07-03-2026 -Aditya
ALTER TABLE `sameepaccounting_salesorder_total` ADD `date` DATE NULL AFTER `pay_terms`, ADD `exp_date` DATE NULL AFTER `date`;

//07-03-2026 -Aditya
ALTER TABLE `sameepaccounting_quotation` CHANGE `date` `date` DATE NULL, CHANGE `exp_date` `exp_date` DATE NULL;

//27-03-2026 -Aditya
-- Drawing Master Table with numeric revision
CREATE TABLE IF NOT EXISTS `sameepaccounting_drawing_master` (
  `drawing_id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id_fk` int(11) NOT NULL,
  `drawing_no` varchar(100) NOT NULL,
  `drawing_name` varchar(255) DEFAULT NULL,
  `current_revision` varchar(10) DEFAULT '001',
  `status` enum('active','obsolete') DEFAULT 'active',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`drawing_id`),
  UNIQUE KEY `unique_drawing_per_project` (`project_id_fk`,`drawing_no`),
  KEY `fk_drawing_project` (`project_id_fk`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
 
-- Drawing Revisions Table with numeric revision
CREATE TABLE IF NOT EXISTS `sameepaccounting_drawing_revisions` (
  `revision_id` int(11) NOT NULL AUTO_INCREMENT,
  `drawing_id_fk` int(11) NOT NULL,
  `revision_no` varchar(10) NOT NULL,
  `revision_date` date NOT NULL,
  `change_description` text,
  `revision_note` text,
  `uploaded_by` varchar(100) DEFAULT NULL,
  `approved_by` varchar(100) DEFAULT NULL,
  `status` enum('active','superseded') DEFAULT 'active',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`revision_id`),
  UNIQUE KEY `unique_revision_per_drawing` (`drawing_id_fk`,`revision_no`),
  KEY `fk_revision_drawing` (`drawing_id_fk`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
 
-- Drawing Files Table
CREATE TABLE IF NOT EXISTS `sameepaccounting_drawing_files` (
  `file_id` int(11) NOT NULL AUTO_INCREMENT,
  `revision_id_fk` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_type` varchar(50) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `description` text,
  `uploaded_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`file_id`),
  KEY `fk_file_revision` (`revision_id_fk`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
 
-- Add foreign keys
ALTER TABLE `sameepaccounting_drawing_master`
  ADD CONSTRAINT `fk_drawing_project` 
  FOREIGN KEY (`project_id_fk`) 
  REFERENCES `sameepaccounting_project` (`project_id`) 
  ON DELETE CASCADE;
 
ALTER TABLE `sameepaccounting_drawing_revisions`
  ADD CONSTRAINT `fk_revision_drawing` 
  FOREIGN KEY (`drawing_id_fk`) 
  REFERENCES `sameepaccounting_drawing_master` (`drawing_id`) 
  ON DELETE CASCADE;
 
ALTER TABLE `sameepaccounting_drawing_files`
  ADD CONSTRAINT `fk_file_revision` 
  FOREIGN KEY (`revision_id_fk`) 
  REFERENCES `sameepaccounting_drawing_revisions` (`revision_id`) 
  ON DELETE CASCADE;