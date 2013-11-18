/*==============================================================*/
/* Table: poll                                                  */
/*==============================================================*/

CREATE TABLE IF NOT EXISTS `poll` (
  `poll_id` 		int(11) 	NOT NULL,
  `title` 		varchar(128) 	NOT NULL,
  `poll_type` 		varchar(64) 	NOT NULL,
  `date_created` 	datetime 	NOT NULL,
  `order_id` 		int(11) 	NOT NULL,
  PRIMARY KEY (`poll_id`)
);

/*==============================================================*/
/* Table: poll_question                                         */
/*==============================================================*/

CREATE TABLE IF NOT EXISTS `poll_question` (
  `poll_question_id` 	int(11) 	NOT NULL,
  `poll_id` 		int(11) 	NOT NULL,
  `title` 		varchar(128)	NOT NULL,
  `order_id` 		int(11) 	NOT NULL,
  PRIMARY KEY (`poll_question_id`)
);

/*==============================================================*/
/* Table: poll_answer                                           */
/*==============================================================*/

CREATE TABLE IF NOT EXISTS `poll_answer` (
  `poll_answer_id` 	int(11) 	NOT NULL,
  `poll_question_id` 	int(11) 	NOT NULL,
  `usr_id` 		int(11) 	NOT NULL,
  `date_created` 	datetime 	NOT NULL,
  PRIMARY KEY (`poll_answer_id`)
);
