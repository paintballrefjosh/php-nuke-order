ALTER TABLE `nuke_hosting_order_options` CHANGE `price` `price_monthly` DECIMAL( 253, 2 ) NOT NULL DEFAULT '0.00' ;
ALTER TABLE `nuke_hosting_order_options` ADD `price_setup` DECIMAL( 253, 2 ) NOT NULL ;
ALTER TABLE `nuke_hosting_order` ADD `baseprice` DECIMAL( 253, 2 ) NOT NULL ;

CREATE TABLE `nuke_hosting_order_config` (
`admin_email` VARCHAR( 255 ) NOT NULL ,
`allow_nochex` TINYINT( 1 ) NOT NULL ,
`allow_paypal` TINYINT( 1 ) NOT NULL ,
`allow_cc` TINYINT( 1 ) NOT NULL ,
`allow_paypal_subscriptions` TINYINT( 1 ) NOT NULL ,
`prorate` TINYINT( 1 ) NOT NULL ,
`prorate_date` TINYINT( 2 ) NOT NULL ,
`currency` VARCHAR( 255 ) NOT NULL ,
`currency_code` VARCHAR( 255 ) NOT NULL , 
`version` varchar( 255 ) NOT NULL
) TYPE = MYISAM ;

INSERT INTO `nuke_hosting_order_config` ( `admin_email` , `allow_nochex` , `allow_paypal` , `allow_cc` , `allow_paypal_subscriptions` , `prorate` , `prorate_date` , `currency` , `currency_code` , `version` )
VALUES (
'sales@domain.com', '0', '1', '1', '1', '0', '1', '$', 'USD', '3.0'
);

