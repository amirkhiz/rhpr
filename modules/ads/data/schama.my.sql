/*==============================================================*/
/* Table: Ads                                                   */
/*==============================================================*/

CREATE TABLE IF NOT EXISTS `ads` (
  `ads_id` 	int(11) 	NOT NULL,
  `usr_id` 	int(11) 	NOT NULL,
  `title` 	varchar(100) 	NOT NULL,
  `description` text 		NOT NULL,
  `start_date` 	date 		NOT NULL,
  `end_date` 	date 		NOT NULL,
  `weight` 	int(11) 	NOT NULL,
  `image` 	text 		NOT NULL,
  `url` 	varchar(255) 	NOT NULL,
  `block_id` 	int(11) 	NOT NULL,
  `hits` 	int(11) 	NOT NULL,
  `clicks` 	int(11) 	NOT NULL,
  PRIMARY KEY (`ads_id`)
);
