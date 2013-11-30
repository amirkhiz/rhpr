--
-- Table structure for table `category`
--

CREATE TABLE IF NOT EXISTS `category` (
  `category_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `level_id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `metakeys` varchar(255) NOT NULL,
  `order_id` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `created_date` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_date` datetime NOT NULL,
  `updated_by` int(11) NOT NULL,
  PRIMARY KEY (`category_id`)
) ;
