CREATE TABLE `nuke_hosting_order` (
  `id` int(255) unsigned NOT NULL auto_increment,
  `cat_id` int(255) unsigned NOT NULL default '0',
  `name` text NOT NULL,
  `description` text NOT NULL,
  `status` tinyint(1) NOT NULL default '0',
  `baseprice` decimal(253,2) NOT NULL default '0.00',
  PRIMARY KEY  (`id`),
  KEY `status` (`status`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `nuke_hosting_order_category`
-- 

CREATE TABLE `nuke_hosting_order_category` (
  `id` int(255) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `nuke_hosting_order_config`
-- 

CREATE TABLE `nuke_hosting_order_config` (
  `admin_email` varchar(255) NOT NULL default '',
  `allow_nochex` tinyint(1) NOT NULL default '0',
  `allow_paypal` tinyint(1) NOT NULL default '0',
  `allow_cc` tinyint(1) NOT NULL default '0',
  `allow_paypal_subscriptions` tinyint(1) NOT NULL default '0',
  `prorate` tinyint(1) NOT NULL default '0',
  `prorate_date` tinyint(2) NOT NULL default '0',
  `currency` varchar(255) NOT NULL default '',
  `currency_code` varchar(255) NOT NULL default '',
  `version` varchar(255) NOT NULL default ''
) TYPE=MyISAM;

-- --------------------------------------------------------

INSERT INTO `nuke_hosting_order_config` VALUES ('sales@domain.com', 0, 1, 1, 1, 0, 1, '$', 'USD', '3.0');

-- 
-- Table structure for table `nuke_hosting_order_coupons`
-- 

CREATE TABLE `nuke_hosting_order_coupons` (
  `id` int(255) NOT NULL auto_increment,
  `order_id` int(255) NOT NULL default '0',
  `description` varchar(255) NOT NULL default '',
  `code` varchar(255) NOT NULL default '',
  `discount` decimal(253,2) NOT NULL default '0.00',
  `type` tinyint(1) NOT NULL default '0',
  `parent` int(255) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `discounttype` (`type`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `nuke_hosting_order_data`
-- 

CREATE TABLE `nuke_hosting_order_data` (
  `id` int(255) unsigned NOT NULL auto_increment,
  `pid` int(255) NOT NULL default '0',
  `description` text NOT NULL,
  `type` varchar(255) NOT NULL default 'UserInput',
  `required` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `nuke_hosting_order_options`
-- 

CREATE TABLE `nuke_hosting_order_options` (
  `id` int(255) unsigned NOT NULL auto_increment,
  `pid` int(255) NOT NULL default '0',
  `description` text NOT NULL,
  `price_monthly` decimal(253,2) NOT NULL default '0.00',
  `price_setup` decimal(253,2) NOT NULL default '0.00',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `nuke_hosting_order_term`
-- 

CREATE TABLE `nuke_hosting_order_term` (
  `id` int(255) unsigned NOT NULL auto_increment,
  `pid` int(255) NOT NULL default '0',
  `term` int(255) NOT NULL default '0',
  `setupfee` int(255) NOT NULL default '0',
  `discount` int(255) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;