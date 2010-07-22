<?php
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

if (eregi("block-Order.php", $_SERVER['PHP_SELF'])) {
  Header("Location: index.php");
  die();
}
global $db, $prefix;
$tmpc = "";
	$sql = "SELECT id, name FROM ".$prefix."_hosting_order_category ORDER BY name ASC";
	$result = $db->sql_query($sql);
	while($row = $db->sql_fetchrow($result))
		$tmpc .= "&middot; <a href=\"modules.php?name=Order&amp;cat=".$row[id]."\">".$row[name]."</a><br>";

//	$tmpc .= "<br>";

	$sql = "SELECT id, name FROM ".$prefix."_hosting_order WHERE status='1' AND cat_id='0' ORDER BY name ASC";
	$result = $db->sql_query($sql);
	while($row = $db->sql_fetchrow($result))
		$tmpc .= "&middot; <a href=\"modules.php?name=Order&amp;sid=".$row[id]."\">".$row[name]."</a><br>";
$content = $tmpc;
?>
