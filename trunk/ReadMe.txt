#####################################################################
#
#	Online Ordering - A PHP-Nuke module for customers to use to 
#		sign-up and purchase services from your company. Uses 
#		PayPal as final payment method.
#
#	Copyright © 2004-2005 Joshua Scarbrough (JoshS@moahosting.com)
#
#	This program is free software; you can redistribute it and/or
#	modify it under the terms of the GNU General Public License
#	as published by the Free Software Foundation; either version 2
#	of the License, or (at your option) any later version.
#
#	This program is distributed in the hope that it will be useful,
#	but WITHOUT ANY WARRANTY; without even the implied warranty of
#	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#	GNU General Public License for more details.
#	You should have received a copy of the GNU General Public License
#	long with this program; if not, write to:
#			Free Software Foundation, Inc.
#			59 Temple Place - Suite 330
#			Boston, MA  02111-1307, USA.
#
######################################################################

Check out www.moahosting.com for updates!
Find a bug? Submit it to us via:
   Email: JoshS@moahosting.com
   Forums: www.moahosting.com/modules.php?name=Forums


Installation:

1) To install this simply copy over all of the files within the zip
2) Insert the new tables provided in order.sql file.
3) Goto your admin panel and click on the Order icon to administer
   the orders and other info.


Administration:

1) The first page you will see in the admin section will display
   all of the services you currently offer. From here you
   can edit the existing services or add a new one!

2) [Edit Order] Here you can edit the name and description for
   each of your orders. As well you can edit whether the order
   is active or not. If the order is not active then it will not
   show up in the public list.

3) [Order Data Fields] Here is where you can insert User Input,
   drop down options, and the term entries for each order. The 
   User Input fields will simply request info from the user such
   as and username/password they would like. The drop down options
   will provide different options for each package such as the
   number of players per game server or which version of server
   the client would like to run. The term is how many months the
   client would like to prepay for, you can add/delete as you like.
   Data fields can be added from the bottom of the page. You will
   also notice the Required? option. This specifieds whether the
   client must provide input for this field or not.


ToDo:

1) Integrate Paypal's IPN feature, if you know how or have an example
   that would greatly speed up the process! Send it to Joshs@moahosting.com


ChangeLog:

v3.0b :: 03.14.2006
 - Fixed bug for paypal payments not querying the email for payment_to.

v3.0 :: 02.28.2006
 - Added base price to each item.
 - Added ability to delete categories.
 - Added monthly/setup prices to each option on orders.
 - Added notifications when a newer version is available.
 - All configuration variables are now stored in the database and accessible
   via admin portal.
 - Users now pick from a list of payment types, credit card (which the cc info
   is simply emailed to the admin), paypal, or nochex. In the configuration the
   admin specifies which of these 3 are available for selection and which aren't.
 - Discounts for terms now show up in the discount row on the overview page.
 - More information is passed in the emails to the admin.

v2.2b :: 06.12.2005
 - Updated the order overview page. It now displays any discounts
   applied and the order number of the purchse.

v2.2 :: 03.19.2005
 - Fixed bug to allow deletion of coupons
 - Added categories - you can now organize different orders into categories

v2.1c :: 03.16.2005
 - Added option to require or not require user registration when ordering.
 - Cleaned up the subscription and regular paypal ordering descriptions.
 - Paypal's "Item Number" is now where the transaction ID is placed.

v2.1b :: 03.13.2005
 - Added a coupon system that will let you create coupon codes for special
   discounts on your products.

v2.1 :: 03.03.2005
 - Added support for Paypal's subscriptions. Note: you cannot have a 
   setup fee when using this option. Settings are in the config.php file.

v2.0c :: 02.25.2005
 - Fixed a bug when sending information to the admin the description
   of the server would not show up.

v2.0b :: 02.14.2005
 - Users can now view pricing/options without being logged in. If
   they want to checkout the must register first however.

v2.0 :: 01.31.2005
 - Module now uses the PHP-Nuke user database and sends the
   clients username in the info email. This makes setup much
   easier on both the client and admins.

v1.0e :: 01.18.2005
 - Fixed a bug in the Options when sending emails to the admin
   the selected options would always show first in list.

v0.1d :: 01.02.2005
 - Can now copy data fields from one service to another, saves
   much typing :)
 - Added $currency in config.php to change currency symbols
 - Added $currencycode in config.php for paypal currency payment
 - Added support for either paypal or nochex in config.php

v0.1c :: 12.20.2004
 - Fixed Name and Email bug that requires the client to submit
   a name and email address 
 - Created 2 new variables for the purchase overview table colors
   in the config.php file so the application can be wrapped into
   your site easier
 - Fixed code to allow for 7.x versions
 - Cleaned up the admin interface, took the meta refreshes out to 
   provide faster editing

v0.1b :: 12.03.2004
 - First Release available!

