<?
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

function do_head()
{
	include("header.php");
	//GraphicAdmin();
	OpenTable();
?>
	<center><b><strong>&middot; <a href="admin.php">Administration Home</a> &middot;</strong></b></center><br>
	<center><b><strong>&middot; <a href="admin.php?op=Order">Order Admin</a> &middot;</strong></b></center><br>
<?
	if(isUpdate())
	{
?>
	<center><b><strong>&middot;&middot;&middot; <a href="http://moahosting.com/modules.php?name=Downloads&d_op=viewdownload&cid=2">An update is available! Click here to update now!</a> &middot;&middot;&middot;</strong></b></center>
<?
	}
	
	CloseTable();
}

function home()
{
	global $db, $prefix;
	do_head();
	
	OpenTable();
	if(isset($_POST['submit_order_config']))
	{
		$sql = "UPDATE ".$prefix."_hosting_order_config SET admin_email = '".$_POST['admin_email']."', allow_nochex = '".$_POST['allow_nochex']."',
			allow_paypal = '".$_POST['allow_paypal']."', allow_cc = '".$_POST['allow_cc']."', allow_paypal_subscriptions = '".$_POST['allow_paypal_subscriptions']."', 
			prorate = '".$_POST['prorate']."', prorate_date = '".$_POST['prorate_date']."', currency = '".$_POST['currency']."', currency_code = '".$_POST['currency_code']."'";
		$db->sql_query($sql);
?>
		<table width="100%" style="border: 1px solid;"><tr><td><b>Settings Saved!</b></td></tr></table>
<?
	}
	$row = $db->sql_fetchrow($db->sql_query("SELECT * FROM ".$prefix."_hosting_order_config"));
?>
	<table width="100%">
	<form method="post">
		<tr>
			<td colspan="2" align="center"><b>Order Configuration</b></td>
		</tr><tr>
			<td>Admin Email:</td>
			<td><input type="text" name="admin_email" style="width: 200px;" value="<? echo $row['admin_email'];?>"></td>
		</tr><tr>
			<td>Allow Paypal:</td>
			<td><input type="checkbox" name="allow_paypal" <? if($row['allow_paypal']) echo "checked";?> value="1"></td>
		</tr><tr>
			<td>Allow NoChex:</td>
			<td><input type="checkbox" name="allow_nochex" <? if($row['allow_nochex']) echo "checked";?> value="1"></td>
		</tr><tr>
			<td>Allow Credit Card:</td>
			<td><input type="checkbox" name="allow_cc" <? if($row['allow_cc']) echo "checked";?> value="1"></td>
		</tr><tr>
			<td>Pro-Rate Payments:</td>
			<td><input type="checkbox" name="prorate" <? if($row['prorate']) echo "checked";?> value="1"></td>
		</tr><tr>
			<td nowrap>Use Paypal Subscriptions:</td>
			<td><input type="checkbox" name="allow_paypal_subscriptions" <? if($row['allow_paypal_subscriptions']) echo "checked";?> value="1"> (setup fee's cannot be used with subscriptions)</td>
		</tr><tr>
			<td>Pro-Rate Date of Month:</td>
			<td><input type="text" name="prorate_date" style="width: 200px;" value="<? echo $row['prorate_date'];?>"></td>
		</tr><tr>
			<td>Currency Symbol:</td>
			<td><input type="text" name="currency" style="width: 200px;" value="<? echo $row['currency'];?>"></td>
		</tr><tr>
			<td>Currency Code:</td>
			<td><input type="text" name="currency_code" style="width: 200px;" value="<? echo $row['currency_code'];?>"></td>
		</tr><tr>
			<td colspan="2" align="center"><input type="submit" name="submit_order_config" value="Save Settings"></td>
		</tr>
	</form>
	</table>
<?
	CloseTable();
	
	// Display list of categories
	OpenTable();
?>
	<center><b><strong>&middot; Category List &middot;</strong></b><br><br></center>
	<table cellspacing="0" cellpadding="0" border="0">
<?
	$result = $db->sql_query("SELECT id, name FROM ".$prefix."_hosting_order_category ORDER BY name ASC");
	while($row = $db->sql_fetchrow($result))
	{
?>
		<tr>
			<td align="left">
				&middot; [<a href="admin.php?op=Order&amp;action=EditCat&amp;id=<? echo $row['id']; ?>">Edit</a> | 
				<a href="admin.php?op=Order&amp;action=DelCat&amp;id=<? echo $row['id']; ?>">Delete</a>] &middot;
				<? echo $row['name']; ?>
			</td>
		</tr>
<?
}
?>
	</table><br>
<?
	CloseTable();
	
	AddCat();
	
	// Display list of ordering services
	OpenTable();
?>
	<center><b><strong>&middot; Order List &middot;</strong></b><br><br></center>
	<table cellspacing="0" cellpadding="0" border="0">
<?
	$sql = "SELECT description, name, id, status FROM ".$prefix."_hosting_order ORDER BY name ASC";
	$result = $db->sql_query($sql);
	while($row = $db->sql_fetchrow($result))
	{
?>			<tr><td align="left">&middot; [<a href="admin.php?op=Order&amp;action=editOrder&amp;id=<? echo $row['id']; ?>">Edit</a>] &middot;
		<? echo $row['name']; ?></td></tr>
<?
	}
?>
	</table><br>
<?
	CloseTable();
	
	// Draw the Add Order table
	addOrder();
	
	// Show current coupons
	OpenTable();
?>
	<center><b><strong>&middot; Coupon List &middot;</strong></b><br><br></center>
	<table cellspacing="0" cellpadding="0" border="0">
<?
	$sql = "SELECT description, code, id FROM ".$prefix."_hosting_order_coupons WHERE parent='0' ORDER BY description ASC";
	$result = $db->sql_query($sql);
	while($row = $db->sql_fetchrow($result))
	{
?>
		<tr>
			<td align="left">
				&middot; [<a href="admin.php?op=Order&amp;action=EditCoupon&amp;id=<? echo $row['id']; ?>">Edit</a>] &middot;
				<? echo $row['description']." (".$row['code'].")"; ?>
			</td>
		</tr>
<?
}
?>
	</table><br>
<?
	CloseTable();
	
	// Draw the Add Coupon table
	AddCoupon();
	
	include("footer.php");
}

function AddCat()
{
	global $db, $prefix;
	if(isset($_POST['submit']))
	{
		// Insert the new parent coupon
		$sql = "INSERT INTO ".$prefix."_hosting_order_category SET name='".$_POST['name']."'";
		if($db->sql_query($sql))
			header("Location: admin.php?op=Order");
		else
			echo "Error: This could not be done.";

	}
	else
	{
		OpenTable();
?>
		<center><form action="admin.php?op=Order&amp;action=AddCat" method="post">
		<b><strong>&middot; Add Category &middot;</strong></b><br><br>
		<table width="100%">
			<tr>
				<td align="right">Category Name: </td>
				<td align="left"><input type="text" name="name" style="width:300;"></td>
			</tr><tr>
				<td colspan="2" align="center"><br><input type="submit" name="submit" value="Submit" style="width=70;"></td>
			</tr>
		</table>
		</form></center>
<?
		CloseTable();
	}	
}

function DelCat()
{
	global $db, $prefix;
	if(isset($_GET['value']))
	{
		$sql = "DELETE FROM ".$prefix."_hosting_order_category WHERE id='".$_GET['id']."'";
		$db->sql_query($sql);
		header("Location: ".$_SERVER['PHP_SELF']."?op=Order");
	}
	else
	{
		do_head();
		OpenTable();
?>
		<table width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
		<tr align="center"><td><br>Really delete the category?  [<a href="<? echo $_SERVER['PHP_SELF']?>?op=Order&amp;action=DelCat&id=<? echo $_GET['id'];?>&amp;value=1">Yes</a> | <a href="<? echo $_SERVER['PHP_SELF']?>?op=Order">No</a>]<br></td></tr></table>
<?
		CloseTable();
	}
	CloseTable();
	include("footer.php");
}

function EditCat()
{
	global $db, $prefix;

	if(isset($_POST['submit']))
	{
		// Update the category	
		$sql = "UPDATE ".$prefix."_hosting_order_category SET name='".$_POST['name']."' WHERE id='".$_GET['id']."'";
		if($db->sql_query($sql))
			header("Location: admin.php?op=Order");
		else
			echo "Error: This could not be done.";
	}
	else
	{
		do_head();
		OpenTable();
		$sql = "SELECT name FROM ".$prefix."_hosting_order_category WHERE id='".$_GET['id']."'";
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
?>
		<center><form action="admin.php?op=Order&amp;action=EditCat&amp;id=<? echo $_GET['id'];?>" method="post">
		<b><strong>&middot; Edit Category &middot;</strong></b><br><br>
		<table width="100%">
			<tr>
				<td align="right">Category Name: </td>
				<td align="left"><input type="text" name="name" style="width:300;" value="<? echo $row['name'];?>"></td>
			</tr><tr>
				<td colspan="2" align="center"><br><input type="submit" name="submit" value="Submit" style="width=70;"></td>
			</tr>
		</table>
		</form></center>
<?
		CloseTable();
		include("footer.php");
	}
}

function AddCoupon() // Add a New Order
{
	global $db, $prefix;
	if(isset($_POST['submit']))
	{
		// Insert the new parent coupon
		$db->sql_query("INSERT INTO ".$prefix."_hosting_order_coupons SET description='".$_POST['desc']."', code='".$_POST['code']."',
			discount='".$_POST['discount']."', type='".$_POST['type']."', parent='0'");
			
		// Fetch the ID of the inserted coupon
		$result = $db->sql_query("SELECT id FROM ".$prefix."_hosting_order_coupons");
		while($row = $db->sql_fetchrow($result))
			$insertid = $row['id'];
			
		// Insert child coupons per each order
		foreach($_POST['Order_ID'] as $order_id)
		{
			$sql = "INSERT INTO ".$prefix."_hosting_order_coupons SET order_id='".$order_id."',	parent='".$insertid."'";
			if(!$db->sql_query($sql))
				$err = 1;
		}
		if($err == 0)
			header("Location: admin.php?op=Order");
		else
			echo "Error: This could not be done.";

	}
	else
	{
		$row = $db->sql_fetchrow($db->sql_query("SELECT * FROM ".$prefix."_hosting_order_config"));
		OpenTable();
?>			<center><form action="admin.php?op=Order&amp;action=AddCoupon" method="post">
		<b><strong>&middot; Add a New Coupon &middot;</strong></b><br><br>
		<table width="100%">
			<tr>
				<td align="right">Coupon Code: </td>
				<td align="left"><input type="text" name="code" style="width:300;"></td>
			</tr><tr>
				<td align="right" width="30%">Description: </td>
				<td align="left"><textarea name="desc" style="width:300; height:100;"></textarea></td>
			</tr><tr>
				<td align="right">Coupon Amount: </td>
				<td align="left"><input type="text" name="discount" style="width:300;"></td>
			</tr><tr>
				<td align="right">Coupon Type: </td>
				<td align="left"><select name="type"><option value="0">Discount in %</option><option value="1">Discount in <? echo $row['currency'];?></option></select></td>
			</tr><tr>
				<td align="right" valign="top">Available for: </td>
				<td align="left" valign="top">
<?					$sql = "SELECT name, id FROM ".$prefix."_hosting_order ORDER BY name ASC";
					$result = $db->sql_query($sql);
					while($row = $db->sql_fetchrow($result))
					{
?>							<input type="checkbox" name="Order_ID[']" value="<? echo $row['id'];?>"><? echo $row['name'];?><br>
<?					}
?>					</td>
			</tr><tr>
			
				<td colspan="2" align="center"><br><input type="submit" name="submit" value="Submit" style="width=70;"></td>
			</tr>
		</table>
		</form></center>
<?		CloseTable();
		include("footer.php");
	}	
}

function DelCoupon()
{
	global $db, $prefix;
	if(isset($_GET['value']))
	{
		$sql = "DELETE FROM ".$prefix."_hosting_order_coupons WHERE id='".$_GET['id']."'";
		$db->sql_query($sql);
		header("Location: ".$_SERVER['PHP_SELF']."?op=Order");
	}
	else
	{
		do_head();
		OpenTable();
		?>			<table width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
		<tr align="center"><td><br>Really delete the coupon?  [<a href="<? echo $_SERVER['PHP_SELF']?>?op=Order&amp;action=DelCoupon&id=<? echo $_GET['id'];?>&amp;value=1">Yes</a> | <a href="<? echo $_SERVER['PHP_SELF']?>?op=Order">No</a>]<br></td></tr></table>
<?	}
	CloseTable();
	include("footer.php");
}

function EditCoupon()
{
	global $db, $prefix;
	
	// Initialize $err to 0
	$err = 0;

	if(isset($_POST['submit']))
	{
		// Update the parent coupon	
		$sql = "UPDATE ".$prefix."_hosting_order_coupons SET description='".$_POST['desc']."', code='".$_POST['code']."',
			discount='".$_POST['discount']."', type='".$_POST['type']."', parent='0' WHERE id='".$_GET['id']."'";
		if(!$db->sql_query($sql))
			$err = 1;
		
		// Delete the child coupons
		$db->sql_query("DELETE FROM ".$prefix."_hosting_order_coupons WHERE parent='".$_GET['id']."'");
		
		// Insert child coupons per each order
		foreach($_POST['Order_ID'] as $order_id)
		{
			$sql = "INSERT INTO ".$prefix."_hosting_order_coupons SET order_id='".$order_id."',	parent='".$_GET['id']."'";
			if(!$db->sql_query($sql))
				$err = 1;
		}
		
		if(!$err)
			header("Location: admin.php?op=Order&action=EditCoupon&id=".$_GET['id']);
		else
			echo "Error: This could not be done.";
	}
	else
	{
		do_head();
		OpenTable();
		$sql = "SELECT * FROM ".$prefix."_hosting_order_coupons WHERE id = '".$_GET['id']."' AND parent='0'";
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
		$row2 = $db->sql_fetchrow($db->sql_query("SELECT * FROM ".$prefix."_hosting_order_config"));
?>
		<center><form action="admin.php?op=Order&amp;action=EditCoupon&amp;id=<? echo $_GET['id'];?>" method="post">
		<b><strong>&middot; Edit Coupon &middot;</strong></b><br><br>
		<table width="100%">
			<tr>
				<td align="right">Coupon Code: </td>
				<td align="left"><input type="text" name="code" style="width:300;" value="<? echo $row['code'];?>"></td>
			</tr><tr>
				<td align="right" width="30%">Description: </td>
				<td align="left"><textarea name="desc" style="width:300; height:100;"><? echo $row['description'];?></textarea></td>
			</tr><tr>
				<td align="right">Coupon Amount: </td>
				<td align="left"><input type="text" name="discount" style="width:300;" value="<? echo $row['discount'];?>"></td>
			</tr><tr>
				<td align="right">Coupon Type: </td>
				<td align="left"><select name="type"><option <? if($row['type']=='0'){echo "selected";}?> value="0">Discount in %</option><option <? if($row['type']=='1'){echo "selected";}?> value="1">Discount in <? echo $row2['currency'];?></option></select></td>
			</tr><tr>
				<td align="right" valign="top">Available for: </td>
				<td align="left" valign="top">
<?
					$sql = "SELECT name, id FROM ".$prefix."_hosting_order ORDER BY name ASC";
					$result = $db->sql_query($sql);
					while($row = $db->sql_fetchrow($result))
					{
						if($row2 = $db->sql_fetchrow($db->sql_query("SELECT id FROM ".$prefix."_hosting_order_coupons WHERE parent='".$_GET['id']."' AND order_id='".$row['id']."'")))
							$checked = 1;
						else
							$checked = 0;
?>
						<input type="checkbox" <? if($checked){echo "checked";} ?> name="Order_ID[']" value="<? echo $row['id'];?>"><? echo $row['name'];?><br>
<?
					}
?>
				</td>
			</tr><tr>
			
				<td colspan="2" align="center">[<a href="admin.php?op=Order&amp;action=DelCoupon&amp;id=<? echo $_GET['id']; ?>">Delete</a>] &nbsp; &nbsp;<input type="submit" name="submit" value="Submit" style="width=70;"></td>
			</tr>
		</table>
		</form></center>
<?
		CloseTable();
		include("footer.php");
	}
}

function addOrder()
{
	global $db, $prefix;
	if(isset($_POST['submit']))
	{
		$sql = "INSERT INTO ".$prefix."_hosting_order SET description='".$_POST['description']."', name='".$_POST['name']."',
			cat_id='".$_POST['cat']."', status = '0', baseprice = '".$_POST['baseprice']."'";
		if($db->sql_query($sql))
			header("Location: admin.php?op=Order");
		else
			echo "Error: This could not be done.";
	}
	else
	{
		OpenTable();
?>			<center><form action="admin.php?op=Order&amp;action=addOrder" method="post">
		<b><strong>&middot; Add a New Order &middot;</strong></b><br><br>
		<table width="100%">
			<tr>
				<td align="right" width="30%">Name: </td>
				<td align="left"><input type="text" name="name" style="width:300;"><br></td>
			</tr><tr>
				<td align="right">Description: </td>
				<td align="left"><textarea name="description" style="width:300; height:150;"></textarea></td>
			</tr><tr>
				<td align="right">Base Price: </td>
				<td align="left"><input type="text" name="baseprice" style="width:300;"></td>
			</tr><tr>
				<td align="right">Category:</td>
				<td align="left"><select name="cat">
					<option value="0">None</option>
<?
					$sql = "SELECT id, name FROM ".$prefix."_hosting_order_category";
					$result = $db->sql_query($sql);
					while($row = $db->sql_fetchrow($result))
					{
?>
						<option value="<? echo $row['id'];?>"><? echo $row['name'];?></option>
<?
					}
?>
					</select>
				</td>
			</tr><tr>
				<td colspan="2" align="center"><br><input type="submit" name="submit" value="Submit" style="width=70;"></td>
			</tr>
		</table>
		</form></center>
<?
		CloseTable();
	}	
}

function delOrder() // Delete a Order
{
	global $db, $prefix;
	
	// Initialize $err to 0
	$err = 0;
	
	$sql = "DELETE FROM ".$prefix."_hosting_order WHERE id='".$_GET['id']."'";
	if(!$result = $db->sql_query($sql))
		$err = 1;
	
	$sql = "SELECT id FROM ".$prefix."_hosting_order_data WHERE pid = '".$_GET['id']."' AND type='Option'";
	$result = $db->sql_query($sql);
	while($row = $db->sql_fetchrow($result))
		if(!$result = $db->sql_query("DELETE FROM ".$prefix."_hosting_order_options WHERE pid='".$row['id']."'"))
			$err = 1;

	$sql = "DELETE FROM ".$prefix."_hosting_order_data WHERE pid='".$_GET['id']."'";
	
	if(!$result = $db->sql_query($sql))
		$err = 1;
		
	if(!$err)
		header("Location: admin.php?op=Order");
	else
		echo "Error: This could not be done.";
}

function editOrder() // Edit Existing a Order
{
	global $db, $prefix;
	if($_POST['submit'] == 'Save')
	{
		if(isset($_POST['PostStatus']))
			$result = $db->sql_query("UPDATE ".$prefix."_hosting_order SET status='1' WHERE id='".$_GET['id']."'");
		else
			$result = $db->sql_query("UPDATE ".$prefix."_hosting_order SET status='0' WHERE id='".$_GET['id']."'");
		
		$sql = "UPDATE ".$prefix."_hosting_order SET description='".$_POST['description']."', name='".$_POST['name']."',
			cat_id='".$_POST['cat']."', baseprice = '".$_POST['baseprice']."' WHERE id='".$_GET['id']."'";
			
		if($db->sql_query($sql))
			header("Location: admin.php?op=Order&action=editOrder&id=".$_GET['id']);
		else
			echo "Error: This could not be done.";
	}
	else
	{
		do_head();
		OpenTable();
		$sql = "SELECT id, cat_id, description, name, status, baseprice FROM ".$prefix."_hosting_order WHERE id='".$_GET['id']."'";
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
?>
		<form action="admin.php?op=Order&amp;action=editOrder&amp;id=<? echo $row['id']; ?>" method="post">
		<b><strong><center>&middot; Edit Order &middot;</center></strong></b><br><br>
		<table width="50%">
			<tr>
				<td align="right">Name: </td>
				<td align="left"><input type="text" name="name" value="<? echo $row['name']; ?>" style="width:200;"><br></td>
			</tr><tr>
				<td align="right">Description: </td>
				<td align="left"><textarea name="description" style="width:300; height:150;"><? echo $row['description']; ?></textarea></td>
			</tr><tr>
				<td align="right">Base Price: </td>
				<td align="left"><input type="text" name="baseprice" value="<? echo $row['baseprice']; ?>" style="width:200;"><br></td>
			</tr><tr>
				<td align="right">Category:</td>
				<td align="left"><select name="cat">
					<option value="0">None</option>
<?
					$sql2 = "SELECT id, name FROM ".$prefix."_hosting_order_category";
					$result2 = $db->sql_query($sql2);
					while($row2 = $db->sql_fetchrow($result2))
					{
?>
						<option <? if($row['cat_id'] == $row2['id']){echo "selected";}?> value="<? echo $row2['id'];?>"><? echo $row2['name'];?></option>
<?
					}
?>
					</select>
				</td>
			</tr><tr>
				<td align="right">Active?: </td>
				<td align="left"><input <? if($row['status'] == '1'){ echo "checked "; } ?> type="checkbox" name="PostStatus"></td>
			</tr><tr>
				<td align="right">&nbsp;</td>
				<td align="right"><br>[<a href="admin.php?op=Order&amp;action=delOrder&amp;id=<? echo $row['id']; ?>">Delete</a>] &nbsp; &nbsp;
			<input type="submit" name="submit" value="Save" style="width=70;"></td></tr></table>
		</form>
		</center>
<?
		CloseTable();
		OpenTable();
?>
		<center><b><strong>&middot; Order Data Fields &middot;</strong></b><br><br><table width="100%">
		<tr>
			<td align="left">&middot; <b>Term:</b> [<u><a href="admin.php?op=Order&amp;action=addTerm&amp;pid=<? echo $row['id']; ?>">Add Term</a></u>]</td>
		</tr>
<?
		$sql = "SELECT id, term FROM ".$prefix."_hosting_order_term WHERE pid = '".$_GET['id']."'";
		$result = $db->sql_query($sql);
		while($row = $db->sql_fetchrow($result))
		{
?>
			<tr>
				<td align="left">&nbsp;&nbsp;&nbsp; <? echo $row['term']; ?> months
				-- [<u><a href="admin.php?op=Order&amp;action=editTerm&amp;id=<? echo $row['id']; ?>&amp;sid=<? echo $_GET['id'] ?>">Edit</a></u>]</td>
			</tr>
<?
		}
		
		$sql = "SELECT id, description, required, type FROM ".$prefix."_hosting_order_data WHERE pid = '".$_GET['id']."' ORDER BY type ASC, description ASC";
		$result = $db->sql_query($sql);
		while($row = $db->sql_fetchrow($result))
		{
?>
			<tr>
				<td align="left">&middot; <b><? echo $row['description']; if($row['required'] == 1){ echo " *"; }?></b> 
				[<u><a href="admin.php?op=Order&amp;action=editData&amp;id=<? echo $row['id']; ?>&amp;sid=<? echo $_GET['id'] ?>">Edit</a></u>
<?
			if($row['type'] == 'Option'){echo " | <u><a href=\"admin.php?op=Order&amp;action=addOption&amp;pid=".$row['id']."&amp;sid=".$_GET['id']."\">Add Option</a></u>"; }?></u>]</td>
			</tr>
<?
			if($row['type'] == 'Option')
			{
				$res2 = $db->sql_query("SELECT id, description, price_monthly, price_setup FROM ".$prefix."_hosting_order_options WHERE pid = '".$row['id']."' ORDER BY description ASC");
				while($row2 = $db->sql_fetchrow($res2))
				{
?>
					<tr>
						<td align="left">&nbsp;&nbsp;&nbsp; <? echo $row2['description']." - ".$row2['price_monthly']." Monthly ".$row2['price_setup']." Setup"; ?> 
						-- [<u><a href="admin.php?op=Order&amp;action=editOption&amp;id=<? echo $row2['id']; ?>&amp;sid=<? echo $_GET['id'] ?>">Edit</a></u>]</td>
					</tr>
<?
				}
			}
		}
?>			<tr><td align="right"><br>
		Copy data from <br><form action="admin.php?op=Order&amp;action=CopyData&amp;id=<? echo $_GET['id']; ?>" method="post">
		<select name="CopyData">
<?		$sql = "SELECT id, name FROM ".$prefix."_hosting_order";
		$result = $db->sql_query($sql);
		while($row = $db->sql_fetchrow($result))
		{
?>				<option value="<? echo $row['id'];?>"><? echo $row['name'];?></option>
<?		}
?>			</select><Br><input type="submit" name="submit" value="Go"></form></td></tr></table></center>
<?		CloseTable();
		addData();
		include("footer.php");
	}
}

function CopyData()
{
	global $db, $prefix;
	$sql = "SELECT id, description, type, required FROM ".$prefix."_hosting_order_data WHERE pid = '".$_POST['CopyData']."'";
	$result = $db->sql_query($sql);
	while($row = $db->sql_fetchrow($result))
	{
		$sql = "INSERT INTO ".$prefix."_hosting_order_data SET description='".$row['description']."', type='".$row['type']."',
		required='".$row['required']."', pid='".$_GET['id']."'";
		$db->sql_query($sql);
		if($row['type'] == 'Option')
		{
			$sql2 = "SELECT id FROM ".$prefix."_hosting_order_data WHERE pid = '".$_GET['id']."' ORDER BY id ASC";
			$result2 = $db->sql_query($sql2);
			while($row2 = $db->sql_fetchrow($result2))
				$insertid = $row2['id'];

			$sql2 = "SELECT description, price FROM ".$prefix."_hosting_order_options WHERE pid = '".$row['id']."'";
			$result2 = $db->sql_query($sql2);
			while($row2 = $db->sql_fetchrow($result2))
			{
				$sql = "INSERT INTO ".$prefix."_hosting_order_options SET description='".$row2['description']."', price='".$row2['price']."',
				pid='".$insertid."'";
				$db->sql_query($sql);
			}
		}
	}
	header("Location: admin.php?op=Order&action=editOrder&id=".$_GET['id']);
}

function addData() // Add Data Field to a Order
{
	global $db, $prefix;
	if(isset($_POST['doit']))
	{
		$sql = "INSERT INTO ".$prefix."_hosting_order_data SET description = '".$_POST['PostDesc']."', pid = '".$_POST['sid']."', type = '".$_POST['PostType']."',	required = '".$_POST['PostRequired']."'";
		if($result = $db->sql_query($sql))
			header("Location: admin.php?op=Order&action=editOrder&id=".$_POST['sid']);
		else
			echo "Error: This could not be done.";
	}
	else
	{
		OpenTable();
?>
		<center>
		<form action="admin.php?op=Order&amp;action=addData" method="post">
		<input type="hidden" name="sid" value="<? echo $_GET['id']; ?>">
		<b><strong>&middot; Add a New Data Field &middot;</strong></b><br><br>
		<table width="50%">
			<tr>
				<td align="right">Description: </td>
				<td align="left"><input type="text" name="PostDesc" style="width:200;"></td>
			</tr><tr>
				<td align="right">Required?: </td>
				<td align="left"><input type="checkbox" name="PostRequired" value="1"></td>
			</tr><tr>
				<td align="right">User Input: </td>
				<td align="left"><input type="radio" name="PostType" value="UserInput"></td>
			</tr><tr>
				<td align="right">Option Field: </td>
				<td align="left"><input type="radio" name="PostType" value="Option"></td>
			</tr><tr>
				<td colspan="2" align="right"><br><input type="submit" name="doit" value="Submit" style="width=70;"></td></tr></table>
		</form></center>
<?		CloseTable();	
		include("footer.php");
	}
}

function delData() // Delete Data Field from a Order
{
	global $db, $prefix;

	// Initialize $err to 0
	$err = 0;

	$sql = "DELETE FROM ".$prefix."_hosting_order_options WHERE pid = '".$_GET['id']."'";
	if(!$result = $db->sql_query($sql))
		$err = 1;

	$sql = "DELETE FROM ".$prefix."_hosting_order_data WHERE id = '".$_GET['id']."'";
	if(!$result = $db->sql_query($sql))
		$err = 1;
	
	if(!$err)
		header("Location: admin.php?op=Order&action=editOrder&id=".$_GET['sid']);
	else
		echo "Error: This could not be done.";
}

function editData() // Edit Existing Data Field
{
	global $db, $prefix;
	if(isset($_POST['doit']))
	{
		if(isset($_POST['PostRequired']))
			$result = $db->sql_query("UPDATE ".$prefix."_hosting_order_data SET required = '1' WHERE id = '".$_POST['id']."'");
		else
			$result = $db->sql_query("UPDATE ".$prefix."_hosting_order_data SET required = '0' WHERE id = '".$_POST['id']."'");
		$sql = "UPDATE ".$prefix."_hosting_order_data
			SET description = '".$_POST['PostDesc']."',
				type = '".$_POST['PostType']."'
			WHERE id = '".$_POST['id']."'";
		if($result = $db->sql_query($sql))
			header("Location: admin.php?op=Order&action=editOrder&id=".$_POST['sid']);
		else
			echo "Error: This could not be done.";
	}
	else
	{
		do_head();
		OpenTable();
		$sql = "SELECT id, pid, description, required, type FROM ".$prefix."_hosting_order_data WHERE id = '".$_GET['id']."'";
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
?>			<center><form action="admin.php?op=Order&amp;action=editData&amp;id=<? echo $row['id']; ?>" method="post">
			<input type="hidden" name="id" value="<? echo $row['id']; ?>">
			<input type="hidden" name="sid" value="<? echo $_GET['sid']; ?>">
			<b><strong>&middot; Edit Data Field &middot;</strong></b><br><br>
			<table width="50%"><tr><td align="right">
			Description: </td><td align="left"><input type="text" name="PostDesc" value="<? echo $row['description']; ?>" style="width:200;"></td></tr><tr><td align="right">
			Required?: </td><td align="left"><input <? if($row['required'] == '1'){ echo "checked "; } ?> type="checkbox" name="PostRequired"></td></tr><tr><td align="right">
			User Input: </td><td align="left"><input <? if($row['type'] == 'UserInput'){ echo "checked "; } ?> type="radio" name="PostType" value="UserInput"></td></tr><tr><td align="right">
			Option Field: </td><td align="left"><input <? if($row['type'] == 'Option'){ echo "checked "; } ?> type="radio" name="PostType" value="Option"></td></tr><tr><td colspan="2" align="right">
			<br>[<a href="admin.php?op=Order&amp;action=delData&amp;id=<? echo $row['id']; ?>&amp;sid=<? echo $_GET['sid'] ?>">Delete</a>] &nbsp; &nbsp;
			<input type="submit" name="doit" value="Submit" style="width=70;"></td></tr></table>
		</form></center>
<?		CloseTable();
		include("footer.php");
	}
}

function addOption() // Add Option to a Order
{
	global $db, $prefix;
	if(isset($_POST['doit']))
	{
		$sql = "INSERT INTO ".$prefix."_hosting_order_options SET description = '".$_POST['description']."', pid = '".$_POST['pid']."', 
			price_monthly = '".$_POST['price_monthly']."', price_setup = '".$_POST['price_setup']."'";
		
		if($result = $db->sql_query($sql))
			header("Location: admin.php?op=Order&action=editOrder&id=".$_POST['sid']);
		else
			echo "Error: This could not be done.";
	}
	else
	{
		do_head(); 
		OpenTable();
?>
		<center>
		<form action="admin.php?op=Order&amp;action=addOption" method="post">
		<input type="hidden" name="sid" value="<? echo $_GET['sid']; ?>">
		<input type="hidden" name="pid" value="<? echo $_GET['pid']; ?>">
		<b><strong>&middot; Add a New Data Field &middot;</strong></b><br><br>
		<table width="50%">
			<tr>
				<td align="right">Description: </td>
				<td align="left"><input type="text" name="description" style="width:200;"></td>
			</tr><tr>
				<td align="right">Price Setup: </td>
				<td align="left"><input type="text" name="price_setup" style="width:200;"></td>
			</tr><tr>
				<td align="right">Price Monthly: </td>
				<td align="left"><input type="text" name="price_monthly" style="width:200;"></td>
			</tr><tr>
				<td colspan="2" align="right"><br><input type="submit" name="doit" value="Submit" style="width=70;"></td>
			</tr>
		</table>
		</form></center>
<?
		CloseTable();
		include("footer.php");
	}
}

function delOption() // Delete an Option from a Order
{
	global $db, $prefix;
	$sql = "DELETE FROM ".$prefix."_hosting_order_options WHERE id='".$_GET['id']."'";
	if($result = $db->sql_query($sql))
		header("Location: admin.php?op=Order&action=editOrder&id=".$_GET['sid']);
	else
		echo "Error: This could not be done.";
}

function editOption() // Edit Existing Option
{
	global $db, $prefix;
	if(isset($_POST['doit']))
	{
		$sql = "UPDATE ".$prefix."_hosting_order_options SET description = '".$_POST['description']."', price_setup = '".$_POST['price_setup']."', 
			price_monthly = '".$_POST['price_monthly']."' WHERE id = '".$_GET['id']."'";
			
		if($result = $db->sql_query($sql))
			header("Location: admin.php?op=Order&action=editOrder&id=".$_POST['sid']);
		else
			echo "Error: This could not be done.";
	}
	else
	{
		do_head(); 
		OpenTable();
		$sql = "SELECT id, description, price_monthly, price_setup FROM ".$prefix."_hosting_order_options WHERE id = '".$_GET['id']."'";
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
?>
		<center>
		<form action="admin.php?op=Order&amp;action=editOption&amp;id=<? echo $row['id']; ?>" method="post">
		<input type="hidden" name="sid" value="<? echo $_GET['sid'] ?>">
		<b><strong>&middot; Edit Option &middot;</strong></b><br><br>
		<table width="50%">
			<tr>
				<td align="right">Description: </td>
				<td align="left"><input type="text" name="description" value="<? echo $row['description']; ?>" style="width:200;"></td>
			</tr><tr>
				<td align="right">Price Monthly: </td>
				<td align="left"><input type="text" name="price_monthly" value="<? echo $row['price_monthly']; ?>" style="width:200;"></td>
			</tr><tr>
				<td align="right">Price Setup: </td>
				<td align="left"><input type="text" name="price_setup" value="<? echo $row['price_setup']; ?>" style="width:200;"></td>
			</tr><tr>
				<td colspan="2" align="right">
					<br>[<a href="admin.php?op=Order&amp;action=delOption&amp;id=<? echo $row['id']; ?>&amp;sid=<? echo $_GET['sid'] ?>">Delete</a>] &nbsp; &nbsp;
					<input type="submit" name="doit" value="Submit" style="width=70;">
				</td>
			</tr>
		</table>
		</form>
		</center>
<?
		CloseTable();
		include("footer.php");
	}
}

function addTerm()
{
	global $db, $prefix;
	if(isset($_POST['doit']))
	{
		$sql = "INSERT INTO ".$prefix."_hosting_order_term SET discount = '".$_POST['PostDiscount']."', pid = '".$_POST['pid']."', term = '".$_POST['PostTerm']."', setupfee = '".$_POST['PostSetup']."'";
		if($result = $db->sql_query($sql))
			header("Location: admin.php?op=Order&action=editOrder&id=".$_POST['pid']);
		else
			echo "Error: This could not be done.";
	}
	else
	{
		do_head(); 
		OpenTable();
?>			<center><form action="admin.php?op=Order&amp;action=addTerm" method="post">
			<input type="hidden" name="pid" value="<? echo $_GET['pid']; ?>">
			<b><strong>&middot; Add a New Term &middot;</strong></b><br><br>
			<table width="50%"><tr><td align="right">
			Term (In Months): </td><td align="left"><input type="text" name="PostTerm" style="width:200;"></td></tr><tr><td align="right">
			Discount (In %): </td><td align="left"><input type="text" name="PostDiscount" style="width:200;"></td></tr><tr><td align="right">
			Setup Fee: </td><td align="left"><input type="text" name="PostSetup" style="width:200;"></td></tr><tr><td colspan="2" align="right">
			<br><input type="submit" name="doit" value="Submit" style="width=70;"></td></tr></table>
		</form></center>
<?		CloseTable();
		include("footer.php");
	}	
}

function delTerm()
{
	global $db, $prefix;
	$sql = "DELETE FROM ".$prefix."_hosting_order_term WHERE id='".$_GET['id']."'";
	if($result = $db->sql_query($sql))
		header("Location: admin.php?op=Order&action=editOrder&id=".$_GET['sid']);
	else
		echo "Error: This could not be done.";
}

function editTerm()
{
	global $db, $prefix;
	if(isset($_POST['submit']))
	{
		$sql = "UPDATE ".$prefix."_hosting_order_term
			SET discount = '".$_POST['PostDiscount']."',
				term = '".$_POST['PostTerm']."',
				setupfee = '".$_POST['PostSetup']."'
			WHERE id = '".$_POST['id']."'";
		if($result = $db->sql_query($sql))
			header("Location: admin.php?op=Order&action=editOrder&id=".$_POST['pid']);
		else
			echo "Error: This could not be done.";
	}
	else
	{
		do_head(); 
		OpenTable();
		$sql = "SELECT id, pid, term, setupfee, discount FROM ".$prefix."_hosting_order_term WHERE id = '".$_GET['id']."'";
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
?>
		<center><form action="admin.php?op=Order&amp;action=editTerm&amp;id=<? echo $row['id']; ?>" method="post">
			<input type="hidden" name="pid" value="<? echo $row['pid']; ?>">
			<input type="hidden" name="id" value="<? echo $row['id']; ?>">
			<b><strong>&middot; Edit Term &middot;</strong></b><br><br>
			<table width="50%"><tr><td align="right">
			Term (In Months): </td><td align="left"><input type="text" name="PostTerm" value="<? echo $row['term']; ?>" style="width:200;"></td></tr><tr><td align="right">
			Discount (In %): </td><td align="left"><input type="text" name="PostDiscount" value="<? echo $row['discount']; ?>" style="width:200;"></td></tr><tr><td align="right">
			Setup Fee: </td><td align="left"><input type="text" name="PostSetup" value="<? echo $row['setupfee']; ?>" style="width:200;"></td></tr><tr><td colspan="2" align="right">
			<br>[<a href="admin.php?op=Order&amp;action=delTerm&amp;id=<? echo $row['id']; ?>&amp;sid=<? echo $_GET['sid'] ?>">Delete</a>] &nbsp; &nbsp;
			<input type="submit" name="submit" value="Submit" style="width=70;"></td></tr></table>
		</form></center>
<?
		CloseTable();
		include("footer.php");
	}
}

if (!eregi("admin.php", $_SERVER['PHP_SELF'])) { die ("Access Denied"); }
global $prefix, $db;
$aid = substr("$aid", 0,25);
$row = $db->sql_fetchrow($db->sql_query("SELECT radminsuper FROM " . $prefix . "_authors WHERE aid='$aid'"));
if ($row['radminsuper'] == 1)
{
	switch($_GET['action'])
	{
		case "AddCat": AddCat(); break;
	
		case "DelCat": DelCat(); break;
	
		case "EditCat": EditCat(); break;
	
		case "AddCoupon": AddCoupon(); break;
	
		case "DelCoupon": DelCoupon(); break;
	
		case "EditCoupon": EditCoupon(); break;
	
		case "addOrder": addOrder(); break;
	
		case "delOrder": delOrder(); break;
	
		case "editOrder": editOrder(); break;
	
		case "addData": addData(); break;
	
		case "delData": delData(); break;
	
		case "editData": editData(); break;
	
		case "addOption": addOption(); break;
	
		case "delOption": delOption(); break;
	
		case "editOption": editOption(); break;
	
		case "addTerm": addTerm(); break;
	
		case "delTerm": delTerm(); break;
	
		case "editTerm": editTerm(); break;
	
		case "CopyData": CopyData(); break;
		
		//case "update": header("Location: ?op=Order_Updater"); break;
	
		default: home(); break;
	}
}
else 
    echo "Access Denied";
	
function isUpdate()
{
	global $db, $prefix;
	$ver = $db->sql_fetchrow($db->sql_query("SELECT version FROM ".$prefix."_hosting_order_config"));
	$fp = fopen("http://moahosting.com/files/php-nuke_order/current.txt", "r");
	$current_version = fgets($fp, 4096);
	fclose($fp);
	$current_version = explode(":", $current_version);
	
	if($current_version[1] != $ver['version'])
		return true;
	
	return false;
}
?>