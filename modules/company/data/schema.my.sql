--
-- Table structure for table `company`
--

CREATE TABLE IF NOT EXISTS `company` (
  `company_id` int(11) NOT NULL DEFAULT '0',
  `usr_id` int(11) NOT NULL DEFAULT '0',
  `category_id` int(11) NOT NULL,
  `village_id` int(11) NOT NULL,
  `region_id` int(11) NOT NULL,
  `city_id` int(11) NOT NULL,
  `name` varchar(128) NOT NULL,
  `first_name` varchar(128) DEFAULT NULL,
  `last_name` varchar(128) DEFAULT NULL,
  `contact_person_1` varchar(255) NOT NULL,
  `contact_person_2` varchar(255) NOT NULL,
  `telephone_1` varchar(16) DEFAULT NULL,
  `telephone_2` varchar(16) NOT NULL,
  `fax` varchar(16) NOT NULL,
  `mobile` varchar(16) DEFAULT NULL,
  `site_name` varchar(255) NOT NULL,
  `facebook` varchar(128) NOT NULL,
  `twitter` varchar(128) NOT NULL,
  `email` varchar(128) DEFAULT NULL,
  `email_2` varchar(128) NOT NULL,
  `keywords` text NOT NULL,
  `logo` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `addr` varchar(128) DEFAULT NULL,
  `history` text NOT NULL,
  `sex` tinyint(4) NOT NULL,
  `blood` varchar(8) NOT NULL,
  `has_branch` smallint(6) NOT NULL,
  `date_created` datetime DEFAULT NULL,
  `last_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`company_id`)
);


--
-- Table structure for table `branch`
--

CREATE TABLE IF NOT EXISTS `branch` (
  `branch_id` int(11) NOT NULL,
  `usr_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `village_id` int(11) NOT NULL,
  `city_id` int(11) NOT NULL,
  `country_id` int(11) NOT NULL,
  `name` varchar(128) NOT NULL,
  `contact_person_1` varchar(255) NOT NULL,
  `contact_person_2` varchar(255) NOT NULL,
  `addr` varchar(128) NOT NULL,
  `telephone_1` varchar(16) NOT NULL,
  `telephone_2` varchar(16) NOT NULL,
  `fax` varchar(16) NOT NULL,
  `mobile` varchar(16) NOT NULL,
  `history` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `date_created` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  PRIMARY KEY (`branch_id`)
);

