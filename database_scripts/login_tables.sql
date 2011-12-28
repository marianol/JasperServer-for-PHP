-- Basic Tables for the ETSZONE Login Class
--
-- Replace 'ecm_' for the table prefix of your choice.
--
-- Table structure for table `ecm_users`
--

CREATE TABLE IF NOT EXISTS `ecm_users` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `firstname` varchar(60) NOT NULL,
  `lastname` varchar(60) NOT NULL,
  `email` varchar(50) NOT NULL default '',
  `password` varchar(255) default NULL,
  `userlevel` tinyint(1) unsigned NOT NULL,
  `creation_date` int(10) unsigned NOT NULL,
  `access_date` int(10) default NULL,
  `active` tinyint(1) NOT NULL,
  `salt` varchar(10) NOT NULL,
  `login_token` varchar(64) NOT NULL,
  `login_token_time` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `email` (`email`)
) DEFAULT CHARSET=latin1;

--
-- Insert The Admin Account for table `ecm_users`
-- Pasword is Ho3idiseef
--
INSERT INTO `ecm_users` (`id`, `firstname`, `lastname`, `email`, `password`, `userlevel`, `creation_date`, `access_date`, `active`, `salt`, `login_token`, `login_token_time` ) VALUES
(1, 'Administrator', 'ETSZONE', 'support@etszone.com', '467671b22d310b098878fe9e6d5b736cd18b01dab3bf7d7660c5836e2cdb1b72', 5, 1256940966, NULL, 1, '5LtDiDAnB1', '', 0);

--
-- Table structure for table `ecm_failed_logins`
--

CREATE TABLE IF NOT EXISTS `ecm_failed_logins` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(255) NOT NULL,
  `ip` varchar(40) NOT NULL,
  `timestamp` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
)