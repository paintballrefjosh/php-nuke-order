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
#muajajaja
######################################################################

if (!eregi("modules.php", $_SERVER['PHP_SELF'])) {
    die ("You can't access this file directly...");
}

require_once("mainfile.php");
$module_name = basename(dirname(__FILE__));
get_lang($module_name);
$index = 1;
$pagetitle = "- $module_name";

function home()
{
	global $prefix, $db;
	include("header.php");
	OpenTable();
	
	if(isset($_GET['cat']))
	{
?>
		<b><strong>Available Options:</strong></b><br><br><table width="100%" border="0">
<?
		$config = $db->sql_fetchrow($db->sql_query("SELECT currency FROM ".$prefix."_hosting_order_config"));
		$sql = "SELECT id, name, baseprice FROM ".$prefix."_hosting_order WHERE status='1' AND cat_id='".$_GET['cat']."' ORDER BY name ASC";
		$result = $db->sql_query($sql);
		while($row = $db->sql_fetchrow($result))
		{
?>
			<tr>
				<td align="left">
					&nbsp; &nbsp; &nbsp; &middot; <a href="modules.php?name=<? echo $_GET['name']; ?>&amp;sid=<? echo $row['id']; ?>"><? echo $row['name']." : ".$config['currency'].$row['baseprice']; ?></a>
				</td>
			</tr>
<?
		}
?>
		</table><br>
<?
	}
	else
	{
?>
		<b><strong>Available Options:</strong></b><br><br><table width="100%" border="0">
<?
		$sql = "SELECT id, name FROM ".$prefix."_hosting_order_category ORDER BY name ASC";
		$result = $db->sql_query($sql);
		while($row = $db->sql_fetchrow($result))
		{
?>
			<tr>
				<td align="left">
					&nbsp; &nbsp; &nbsp; &middot; <a href="modules.php?name=<? echo $_GET['name']; ?>&amp;cat=<? echo $row['id']; ?>"><? echo $row['name']; ?></a>
				</td>
			</tr>
<?
		}
		
		$sql = "SELECT id, name FROM ".$prefix."_hosting_order WHERE status='1' AND cat_id='0' ORDER BY name ASC";
		$result = $db->sql_query($sql);
		while($row = $db->sql_fetchrow($result))
		{
?>
			<tr>
				<td align="left">
					&nbsp; &nbsp; &nbsp; &middot; <a href="modules.php?name=<? echo $_GET['name']; ?>&amp;sid=<? echo $row['id']; ?>"><? echo $row['name']; ?></a>
				</td>
			</tr>
<?
		}
?>
		</table><br>
<?
	}
	
	CloseTable();
}

function order()
{
	global $prefix, $db;
	include("header.php");
	OpenTable();
	$config = $db->sql_fetchrow($db->sql_query("SELECT * FROM ".$prefix."_hosting_order_config"));
	$sql = "SELECT name, description, baseprice FROM ".$prefix."_hosting_order WHERE id='".$_GET['sid']."'";
	$result = $db->sql_query($sql);
	$row = $db->sql_fetchrow($result);
	
?>
	<center>
	<b><strong>Purchase : <? echo $row['name']; ?> : <? echo $config['currency'].$row['baseprice'];?></strong></b><br><br>
<?
	if($row['description'] != "")
	{
?>
		<table width="100%" cellpadding="4" cellspacing="0" border="0" style="border-collapse: collapse" bordercolor="#000000">
			<tr>
				<td align="justify"><? echo $row['description'];?></td>
			</tr>
		</table>
<?
	}
?>
	<form action="modules.php?name=<? echo $_GET['name']; ?>&amp;sid=<? echo $_GET['sid']; ?>" method="post">
	<input type="hidden" name="PostName" value="<? echo $row['name']; ?>">
	<table width="100%" cellspacing="2" cellpadding="8" border="0" align="center">
<?
	$sql = "SELECT id, description, required FROM ".$prefix."_hosting_order_data WHERE pid='".$_GET['sid']."' AND type='UserInput' ORDER BY required DESC";
	$result = $db->sql_query($sql);
	while($row = $db->sql_fetchrow($result))
	{
?>
		<tr>
			<td align="right"><? echo $row['description'] . ":"; ?></td>
			<td align="left"><input type="text" name="<? echo 'option'.$row['id']; ?>" style="width:250;"><? if($row['required'] == '1'){ echo "*"; }?></td>
		</tr>
<?
	}
?>
		<tr>
			<td align="right">Select Payment Term:</td>
			<td align="left">
				<select size="1" name="PostTerm" style="width:250; ">
<?
				$sql = "SELECT term, discount, setupfee FROM ".$prefix."_hosting_order_term WHERE pid='".$_GET['sid']."' ORDER BY term ASC";
				$result = $db->sql_query($sql);
				while($row = $db->sql_fetchrow($result))
				{
?>
					<option value="<? echo $row['term']; ?>"><? echo $row['term']; ?> Month Term 
<?
					if($row['discount'] != '0')
						echo " - ".$row['discount']."% Discount";
					else
						echo " - ".$config['currency'].$row['setupfee']." Setup";
?>
					</option>
<?
				}
?>
				</select>
			</td>
		</tr>
<?
	$sql = "SELECT id, description FROM ".$prefix."_hosting_order_data WHERE pid='".$_GET['sid']."' AND type='Option' ORDER BY description ASC";
	$result = $db->sql_query($sql);
	while($row = $db->sql_fetchrow($result))
	{
?>
		<tr>
			<td align="right">
				<? echo $row['description'] . ":"; ?>
			</td>
			<td align="left">
				<select size="1" name="option<? echo $row['id']; ?>" style="width:250; ">
<?
					$sql2 = "SELECT id, description, price_monthly, price_setup FROM ".$prefix."_hosting_order_options WHERE pid='".$row['id']."' ORDER BY price_monthly ASC, description ASC";
					$result2 = $db->sql_query($sql2);
					while($row2 = $db->sql_fetchrow($result2))
					{
?>
						<option value="<? echo $row2['id']; ?>">
<?
						echo $row2['description'];
						if($row2['price_monthly'] != 0)
							echo " : ".$config['currency'].$row2['price_monthly']."/mo";
						if($row2['price_setup'] != 0)
							echo " : ".$config['currency'].$row2['price_setup']." Setup";
?>
						</option>
<?
					}
?>
				</select>
			</td>
		</tr>
<?
	}
?>
		<tr>
			<td align="right">Coupon Code:</td>
			<td align="left"><input type="text" name="coupon" style="width:250;"></td>
		</tr><tr>
			<td align="right">Pay By:</td>
			<td align="left"><select name="pay_method">
<?
			$row = $db->sql_fetchrow($db->sql_query("SELECT allow_paypal, allow_cc, allow_nochex FROM ".$prefix."_hosting_order_config"));
			if($row['allow_paypal'])
			{
?>
				<option value="paypal">Paypal</option>
<?
			}
			if($row['allow_cc'])
			{
?>
				<option value="cc">Credit Card</option>
<?
			}
			if($row['allow_nochex'])
			{
?>
				<option value="nochex">NoChex</option>
<?
			}
?>
			</select></td>
		</tr>
	</table><br>
	<input type="submit" name="submit" value="Proceed to Step 2 --->"></form></center><br><br>
<?
	CloseTable();
}

function Pay()
{
	global $user, $cookie, $prefix, $db;

	if(!is_user($user))
	{
		header("Location: modules.php?name=Your_Account");
	}
	else
	{
		cookiedecode($user);
		$user_info = $db->sql_fetchrow($db->sql_query("SELECT user_email, user_id, username FROM ".$prefix."_users WHERE username='".$cookie['1']."'"));
		$config = $db->sql_fetchrow($db->sql_query("SELECT * FROM ".$prefix."_hosting_order_config"));
		$sess_uid = $user_info['user_id'];

		include("header.php");
		OpenTable();

		$total = 0;
		$tprice = 0;
		$term = $_POST['PostTerm'];
		$date = getdate();
		$date = $date['mday'];
		mt_srand ((double)microtime()*1000000);
		$maxran = 1000000;
		$rnum = mt_rand(0, $maxran);

		$row = $db->sql_fetchrow($db->sql_query("SELECT discount, setupfee FROM ".$prefix."_hosting_order_term WHERE pid='".$_GET['sid']."' AND term='".$term."'"));
		$setupfee = $row['setupfee'];
		$discount = $row['discount'];

		$row = $db->sql_fetchrow($db->sql_query("SELECT name, description, baseprice FROM ".$prefix."_hosting_order WHERE id='".$_GET['sid']."'"));
		$tprice += $row['baseprice'];
		$desc = $row['name'];
		$servicedesc = $row['name'];

		// Build the info email
		$msg = "New Purchase Order: " . $desc . "\n\n";
		$msg .= "Transaction ID: " . $rnum . "\n\n";
		$msg .= "Client Username: " . $user_info['username'] . "\n";
		$msg .= "Term: " . $term . " months\n";
		$msg .= "Pay by: " . $_POST['pay_method']."\n\n";

		$result = $db->sql_query("SELECT id, type, description FROM ".$prefix."_hosting_order_data WHERE pid='".$_GET['sid']."'");
		while($row = $db->sql_fetchrow($result))
		{
			if($row['type'] == 'Option')
			{
				// Query to get the pricing and description for each selected option
				$row2 = $db->sql_fetchrow($db->sql_query("SELECT id, description, price_monthly, price_setup FROM ".$prefix."_hosting_order_options
					WHERE pid='".$row['id']."' AND id='".$_POST['option'.$row['id']]."'"));
				
				// Add the description of the option to the info email
				$msg .= $row['description'].": ".$row2['description'] . " : ".$config['currency'].$row2['price_monthly']." Monthly : ".$config['currency'].$row2['price_setup']." Setup \n";
				
				// Add the description to $desc to be used for the payment description
				$desc .= " -- ".$row2['description'];
				
				// Update the price of the options
				$tprice += $row2['price_monthly'];
				$setupfee += $row2['price_setup'];
			}
			else
				$msg .= $row['description'] . ": " . $_POST['option'.$row['id']] . "\n";
		}

		// If any coupons are used apply them now
		$discount_total = 0;
		if($row = $db->sql_fetchrow($db->sql_query("SELECT id, discount, type FROM ".$prefix."_hosting_order_coupons WHERE code='".$_POST['coupon']."'"))) // First see if the coupon code entered is valid
		{
			if($row2 = $db->sql_fetchrow($db->sql_query("SELECT * FROM ".$prefix."_hosting_order_coupons WHERE order_id='".$_GET['sid']."' AND parent='".$row['id']."'"))) // Verify the coupon applies to the service in checkout
			{
				if($row['type'] == 0) // Discount in %
				{
					$discount_total = $tprice * $row['discount'];
					$tprice = round($tprice * (100 - $row['discount']) / 100);
				}
				else // Discount by currency
				{
					$discount_total = $row['discount'];
					$tprice -= $row['discount'];
				}
			}
		}
		
		// We can't have a setup fee when using paypal's subscriptions
		if($config['allow_paypal_subscriptions'] == 1 && $_POST['pay_method'] == "paypal")
			$setupfee = 0;

		// Calculate the total price the customer owes
		$total = CalcPrice($tprice, $date, $term);
		
		// Apply discount if exists
		$discount_total += $total - round($total * (100 - $discount) / 100);
		if($discount_total < 0)
			$discount_total = 0;
			
		if($discount != 0)
			$total = round($total * (100 - $discount) / 100);
		
		// Attach final price to the admin info email
		$msg .= "\nTotal Monthly: " . $config['currency'] . $total . "\n";
		$msg .= "Total Discounts: " .$config['currency'] . $discount_total . "\n";
		$msg .= "Total Setup: " .$config['currency'] . $setupfee . "\n";
		$msg .= "Total Initial Charge: " . $config['currency'] . ($setupfee + $total)."\n";
		
		// Attach clients IP address to the admin info email
		$msg .= "\nClient IP: " . $_SERVER['REMOTE_ADDR'] . "\n";
		$msg .= "http://".$_SERVER['HTTP_HOST']."/";
		
		// Create the headers for the admin info email
		$mailheaders = "From: ".$user_info['user_email']."\n";
		$mailheaders .= "Reply-To: ".$user_info['user_email']."\n\n";
		
		// Create the subject for the admin info email
		$subject = "New Purchase Order: " . $servicedesc . "\n\n";
		
		// Send the information email to admin
		mail($config['admin_email'], $subject, $msg, $mailheaders);
	
?>
 		<center><br>Please click the button at the bottom of this page to complete your purchase.<br>We thank you for your business!<br><br></center>
<?
		CloseTable();
		echo "<BR>";
		OpenTable();
?>
		<center><br>
		<table border="1" style="border-collapse: collapse" cellpadding="2" cellspacing="0" width="93%">
			<tr bgcolor="<? echo $tr_color1;?>">
				<td width="100%" colspan="2"><center><B>&middot; Purchase - <? echo $servicedesc; ?> Overview &middot;</B></center></td>
			</tr>
			<tr bgcolor="<? echo $tr_color2;?>">
				<td width="50%"><center>Order Number:</center></td>
				<td width="50%"><center><? echo "#".$rnum; ?></center></td>
			</tr>
			<tr bgcolor="<? echo $tr_color2;?>">
				<td width="50%"><center>Price Per Month:</center></td>
				<td width="50%"><center><? echo $config['currency'].$tprice; ?></center></td>
			</tr>
			<tr bgcolor="<? echo $tr_color2;?>">
				<td width="50%"><center>Setup Fee:</center></td>
				<td width="50%"><center><? echo $config['currency'].$setupfee; ?></center></td>
			</tr>
			<tr bgcolor="<? echo $tr_color2;?>">
				<td width="50%"><center>Discount:</center></td>
				<td width="50%"><center><? echo "- ".$config['currency'].$discount_total; ?></center></td>
			</tr>
			<tr bgcolor="<? echo $tr_color2;?>">
				<td width="50%"><center>Term:</center></td>
				<td width="50%"><center><? echo $term; ?> months</center></td>
			</tr>
			<tr bgcolor="<? echo $tr_color2;?>">
				 <td width="50%"><center>Total Initial Payment:</center></center></td>
				 <td width="50%"><center><? echo $config['currency'].($total + $setupfee); ?></td>
			</tr>
 		</table>
		<BR>
		<form action="modules.php?name=<? echo $_GET['name']?>&amp;action=PayMethod&amp;rnum=<? echo $rnum;?>" method="post" target="_blank">
			<input type="hidden" name="description" value="<? echo $desc; ?>">
			<input type="hidden" name="trans_id" value="<? echo $rnum; ?>">
			<input type="hidden" name="term" value="<? echo $term; ?>">
			<input type="hidden" name="setupfee" value="<? echo $setupfee; ?>">
			<input type="hidden" name="amount" value="<? echo $total; ?>">
			<input type="hidden" name="pay_method" value="<? echo $_POST['pay_method'];?>">
			<input type="submit" value="Continue to Checkout" name="submit"><br><br>
		</form>
<?
		CloseTable();
	}
}

function PayMethod()
{
	global $db, $prefix, $cookie, $user;
	$link = "";
	$row = $db->sql_fetchrow($db->sql_query("SELECT * FROM ".$prefix."_hosting_order_config"));
	// Physical checks aren't supported yet, look for them in a future version :)
	
	if(isset($_POST['pay_by_cc']))
	{
		cookiedecode($user);
		$user_info = $db->sql_fetchrow($db->sql_query("SELECT user_email, user_id, username FROM ".$prefix."_users WHERE username='".$cookie['1']."'"));

		$msg = "Payment info for order: " . $_GET['rnum'] . "\n\n";
		$msg .= "Name: " . $_POST['NAME'] . "\n\n";
		$msg .= "Address: " . $_POST['STREET'] . "\n";
		$msg .= "City: " . $_POST['CITY'] . "\n";
		$msg .= "State: " . $_POST['STATE'] . "\n";
		$msg .= "Zip Code: " . $_POST['ZipCode'] . "\n\n";
		$msg .= "Country: " . $_POST['Country'] . "\n";
		$msg .= "Card Type: " . $_POST['CardType'] . "\n";
		$msg .= "CC Number: " . $_POST['CCNumber'] . "\n";
		$msg .= "Exp Month: " . $_POST['Month'] . "\n";
		$msg .= "Exp Year: " . $_POST['Year'] . "\n";

		// Create the headers for the admin info email
		$mailheaders = "From: ".$user_info['user_email']."\r\n";
		$mailheaders .= "Reply-To: ".$user_info['user_email']."\r\n\n";
		
		// Create the subject for the admin info email
		$subject = "Payment Info for Order #" . $_GET['rnum'] . "\r\n\n";
		
		// Send the information email to admin
		mail($row['admin_email'], $subject, $msg, $mailheaders);

		include("header.php");
		OpenTable();
?>
		Your order has been placed. We will be contacting you shortly when your order is complete.<br><br>
		Your order number is #<? echo $_GET['rnum'];?>.<br><br>
		We thank you for your business!
<?
		CloseTable();
		include("footer.php");
	}
	elseif($_POST['pay_method'] == 'paypal')
	{
		if($row['allow_paypal_subscriptions'] == 1)
		{
			// Use paypal's subscritpion system
			// $p3 : length of the billing cycle
			// $t3 : M=month(s); Y=year(s)
			$link = "https://www.paypal.com/subscriptions/business=".$row['admin_email']."&item_number=".$_POST['trans_id']."&item_name=".$_POST['description']."&no_shipping=1&no_note=1&currency_code=".$row['currency_code']."&a3=".$_POST['amount']."&p3=".$_POST['term']."&t3=M&src=1&sra=1";
		}
		else
		{
			// Use regular paypal payments
			$total = $_POST['amount'] + $_POST['setupfee'];
			$link = "https://www.paypal.com/xclick/business=".$row['admin_email']."&item_name=".$_POST['description']."&amount=".$total."&no_shipping=1&no_note=1&currency_code=".$row['currency_code'];
		}
	}
	elseif($_POST['pay_method'] == 'nochex')// Use NoChex
	{
		$link = "https://www.nochex.com/nochex.dll/checkout?amount=".$_POST['amount']."&email=".$row['admin_email']."&description=".$_POST['description'];
	}
	elseif($_POST['pay_method'] == "cc")
	{
		include("header.php");
		OpenTable();
?>
		<table width="100%" border="0">
		<form method="post">
			<tr>
				<td align="left"><b>Name on Card:</b> </td>
				<td align="left"><input type="text" name="NAME" size="20" maxlength="100"></td>
			</tr><tr>
				<td align="left"><b>Address:</b></td> 
				<td align="left"> <input type="text" name="STREET" size="20" maxlength="100"></td>
			</tr><tr>
				<td align="left"><b>City:</b></td>
				<td align="left"> <input type="text" name="CITY" size="20" maxlength="40"></td>
			</tr><tr>
				<td align="left"><B>State/Province</B></td>
				<td>
					<select name="STATE">
						<option selected value="AL">AL</option><option value="AK">AK</option><option value="AZ">AZ</option>
						<option value="AR">AR</option><option value="CA">CA</option><option value="CO">CO</option><option value="CT">CT</option>
						<option value="DC">DC</option><option value="DE">DE</option><option value="FL">FL</option><option value="GA">GA</option>
						<option value="HI">HI</option><option value="ID">ID</option><option value="IL">IL</option><option value="IN">IN</option>
						<option value="IA">IA</option><option value="KS">KS</option><option value="KY">KY</option><option value="LA">LA</option>
						<option value="ME">ME</option><option value="MD">MD</option><option value="MA">MA</option><option value="MI">MI</option>
						<option value="MN">MN</option><option value="MS">MS</option><option value="MO">MO</option><option value="MT">MT</option>
						<option value="NE">NE</option><option value="NV">NV</option><option value="NH">NH</option><option value="NJ">NJ</option>
						<option value="NM">NM</option><option value="NY">NY</option><option value="NC">NC</option><option value="ND">ND</option>
						<option value="OH">OH</option><option value="OK">OK</option><option value="OR">OR</option><option value="PA">PA</option>
						<option value="RI">RI</option><option value="SC">SC</option><option value="SD">SD</option><option value="TN">TN</option>
						<option value="TX">TX</option><option value="UT">UT</option><option value="VT">VT</option><option value="VI">VI</option>
						<option value="VA">VA</option><option value="WA">WA</option><option value="WV">WV</option><option value="WI">WI</option>
						<option value="WY">WY</option><option value="SP">SIPAN</option><option value="GU">GUAM</option><option value="PR">Puerto Rico</option>
						<option value="AB">Alberta-CA</option><option value="BC">British Columbia-CA</option><option value="MB">Manitoba-CA</option>
						<option value="NB">New Brunswick-CA</option><option value="NF">Newfoundland-CA</option><option value="NT">Northwest Territory-CA</option>
						<option value="NS">Nova Scotia-CA</option><option value="NT">Nunavut Territory-CA</option><option value="ON">Ontario-CA</option>
						<option value="PE">Prince Edward-CA</option><option value="QC">Quebec-CA</option><option value="SK">Saskatchewan-CA</option>
						<option value="YT">Yukon Territory-CA</option><option value="NONUS">Non-US</option>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="2"><font size=2><center><B><i>(Select Non-US for International Orders) </i></b></center></font></td>
			</tr>

			<tr>
				<td><b>Country:</b></td>
				<td>
					<select name="Country">
						<option value=Algeria>Algeria</option>
						<option value="American Samoa">American Samoa</option> 
						<option value=Anguilla>Anguilla</option>
						<option value="Antigua &amp; Barbuda">Antigua &amp; Barbuda</option>
						<option value=Argentina>Argentina</option> 
						<option value=Armenia>Armenia</option> 
						<option value=Australia>Australia</option> 
						<option value=Azerbaijan>Azerbaijan</option> 
						<option value=Austria>Austria</option> 
						<option value=Bahamas>Bahamas</option> 
						<option value=Bahrain>Bahrain</option>
						<option value=Bangladesh>Bangladesh</option> 
						<option value=Barbados>Barbados</option>
						<option value=Belarus>Belarus</option>
						<option value=Belguim>Belguim</option> 
						<option value=Belize>Belize</option> 
						<option value=Bequia>Bequia</option> 
						<option value=Bermuda>Bermuda</option> 
						<option value=Bolivia>Bolivia</option>
						<option value="Bosnia and Herzegovina">Bosnia and Herzegovina</option> 
						<option value="British Virgin Islands">British Virgin Islands</option>
						<option value=Brazil>Brazil</option> 
						<option value=Cameroon>Cameroon</option> 
						<option value=Canada>Canada</option>
						<option value="Cayman Islands">Cayman Islands</option>
						<option value=Chile>Chile</option> 
						<option value=China>China</option>
						<option value=Colombia>Colombia</option> 
						<option value=Congo>Congo</option>
						<option value="Costa Rica">Costa Rica</option> 
						<option value=Croatia>Croatia</option>
						<option value=Cyprus>Cyprus</option> 
						<option value="Czech Republic">Czech Republic</option> 
						<option value=Denmark>Denmark</option>
						<option value=Dominica>Dominica</option> 
						<option value="Dominican Republic">Dominican Republic</option> 
						<option value=Ecuador>Ecuador</option> 
						<option value=Egypt>Egypt</option>
						<option value="El Salvador">El Salvador</option>
						<option value=Estonia>Estonia</option>
						<option value=Ethiopia>Ethiopia</option> 
						<option value=Fiji>Fiji</option> 
						<option value=Finland>Finland</option>
						<option value=France>France</option> 
						<option value=Gabon>Gabon</option>
						<option value=Germany>Germany</option> 
						<option value=Ghana>Ghana</option> 
						<option value=Greece>Greece</option> 
						<option value=Guadaloupe>Guadaloupe</option>
						<option value=Guam>Guam</option> 
						<option value=Guatamala>Guatamala</option> 
						<option value=Guyana>Guyana</option> 
						<option value=Haiti>Haiti</option> 
						<option value=Honduras>Honduras</option> 
						<option value="Hong Kong">Hong Kong</option> 
						<option value=Hungary>Hungary</option> 
						<option value=Iceland>Iceland</option> 
						<option value=India>India</option> 
						<option value=Indonesia>Indonesia</option>
						<option value=Iran>Iran</option> 
						<option value=Iraq>Iraq</option> 
						<option value=Ireland>Ireland</option> 
						<option value=Israel>Israel</option> 
						<option value=Italy>Italy</option> 
						<option value="Ivory Coast">Ivory Coast</option> 
						<option value=Jamaica>Jamaica</option>
						<option value=Japan>Japan</option> 
						<option value=Jordan>Jordan</option> 
						<option value=Kazakhstan>Kazakhstan</option>
						<option value=Kenya>Kenya</option>
						<option value=Korea>Korea</option>
						<option value=Kuwait>Kuwait</option> 
						<option value=Latvia>Latvia</option> 
						<option value=Liberia>Liberia</option> 
						<option value=Libya>Libya</option> 
						<option value=Lichtenstein>Lichtenstein</option> 
						<option value=Lithuania>Lithuania</option> 
						<option value=Luxembourg>Luxembourg</option> 
						<option value=Macedonia>Macedonia</option>
						<option value=Malawi>Malawi</option> 
						<option value=Malaysia>Malaysia</option> 
						<option value=Mali>Mali</option> 
						<option value=Malta>Malta</option> 
						<option value=Mexico>Mexico</option> 
						<option value=Manaco>Manaco</option>
						<option value=Moldavia>Moldavia</option>
						<option value=Montserrat>Montserrat</option>
						<option value=Morocco>Morocco</option> 
						<option value=Mustique>Mustique</option>
						<option value=Namibia>Namibia</option> 
						<option value=Nepal>Nepal</option> 
						<option value=Netherlands>Netherlands</option> 
						<option value="New Zealand">New Zealand</option> 
						<option value=Nicaragua>Nicaragua</option> 
						<option value=Nigeria>Nigeria</option>
						<option value=Norway>Norway</option>
						<option value=Oman>Oman</option>
						<option value=Pakistan>Pakistan</option>
						<option value=Panama>Panama</option> 
						<option value="Papua New Guinea">Papua New Guinea</option> 
						<option value=Paraguay>Paraguay</option> 
						<option value=Peru>Peru</option>
						<option value=Philipines>Philipines</option> 
						<option value=Poland>Poland</option>
						<option value=Portugal>Portugal</option>
						<option value=Qatar>Qatar</option> 
						<option value=Romania>Romania</option> 
						<option value=Russia>Russia</option> 
						<option value=Saipan>Saipan</option>
						<option value="San Marino">San Marino</option> 
						<option value="Saudia Arabia">Saudia Arabia</option>
						<option value=Senegal>Senegal</option> 
						<option value=Singapore>Singapore</option> 
						<option value=Slovakia>Slovakia</option> 
						<option value=Slovenia>Slovenia</option> 
						<option value="South Africa">South Africa</option> 
						<option value=Spain>Spain</option> 
						<option value="St. Kitts">St. Kitts</option> 
						<option value="St. Vincent">St. Vincent</option> 
						<option value=Suriname>Suriname</option> 
						<option value=Sweden>Sweden</option> 
						<option value=Switzerland>Switzerland</option> 
						<option value=Taiwan>Taiwan</option> 
						<option value=Tanzania>Tanzania</option> 
						<option value=Thailand>Thailand</option> 
						<option value="Trinadad and Tobago">Trinadad and Tobago</option> 
						<option value=Tunisia>Tunisia</option> 
						<option value=Turkey>Turkey</option> 
						<option value=Turks and Caicos Island>Turks and Caicos</option> 
						<option value=Ukraine>Ukraine</option>
						<option value="United Arab Emirates">United Arab Emirates</option> 
						<option value="United Kingdom">United Kingdom</option> 
						<option selected value="United States">United States</option> 
						<option value=Uraguay>Uraguay</option> 
						<option value=Venezuela>Venezuela</option> 
						<option value=Yemen>Yemen</option> 
						<option value=Yugoslavia>Yugoslavia</option> 
						<option value=Zimbabwe>Zimbabwe</option>
					</select>
				</td>
			</tr><tr>
				<td><b>Zip/Postal Code:</b></td>
				<td><input type="text" name="ZipCode" SIZE="25" maxlength="10"></td>
			</tr><tr>
				<td><strong>Card Type:</strong></td>
				<td>
					<select name="CardType">
						<option value="Visa">Visa</option>
						<option value="MasterCard">MasterCard</option>
						<option value="American Express">American Express</option>
						<option value="Discover">Discover</option>
					</select>
				</td>
			</tr><tr>
				<td><strong>Account Number:</strong></td>
				<td><input name="CCNumber" type="text" size="16" maxlength="16"><i>(No spaces or dashes)</i></td>
			</tr><tr>
				<td><strong>Expiration Date:</strong></td>
				<td>
					<i>Month:</i>
					<select name="Month">
						<option value="01">Jan</option>
						<option value="02">Feb</option>
						<option value="03">Mar</option>
						<option value="04">Apr</option>
						<option value="05">May</option>
						<option value="06">Jun</option>
						<option value="07">Jul</option>
						<option value="08">Aug</option>
						<option value="09">Sep</option>
						<option value="10">Oct</option>
						<option value="11">Nov</option>
						<option value="12">Dec</option>
					</select>
					
					<i>Year:</i>
					<select name="Year">
						<option value="05">2005</option>
						<option value="06">2006</option>
						<option value="07">2007</option>
						<option value="08">2008</option>
						<option value="09">2009</option>
						<option value="10">2010</option>
						<option value="11">2011</option>
						<option value="12">2012</option>
						<option value="13">2013</option>
						<option value="14">2014</option>
					</select>	  
				</td>
			</tr><tr>
				<td align="center" colspan="2"><input type="submit" name="pay_by_cc" value="Submit Order"></td>
			</tr>
		</form>
		</table>
<?
		CloseTable();
		include("footer.php");
	}
	elseif($_POST['pay_method'] == "check")
	{
?>
		Please send your payment to the address below:<br>
		&nbsp;&nbsp;&nbsp;<b><? echo $row['cname'];?></b><br>
		&nbsp;&nbsp;&nbsp;<b><? echo $row['street'];?></b><br>
		&nbsp;&nbsp;&nbsp;<b><? echo $row['city'].",".$row['state']." ".$row['zip'];?></b><br>
		<br>
		Please make checks out to <? echo $row['cname'];?>.
<?
	}
		
	if($link != "")
		header("Location: $link");
}

function CalcPrice($tprice, $date, $term)
{
	global $db;
	$row = $db->sql_fetchrow($db->sql_query("SELECT * FROM ".$prefix."_hosting_order_config"));
	if($row['allow_paypal_subscriptions'] == 1 && $_POST['pay_method'] == 'paypal')
		$finalprice = $tprice * $term;
	elseif($row['prorate'] == 0)
		$finalprice = $tprice * $term;
	else
	{
		// Pro-rate the price this month - messy :)
		if($date > 30)
			$date = 30;
		$tmpdate = 30 - $row['prorate_date'] + $date;
		if($tmpdate > 30)
			$tmpdate -= 30;
		if($tmpdate < 25)
			$finalprice = round(($tprice / 30 * (30 - $tmpdate)) + .5) + ($tprice * ($term - 1));
		else
			$finalprice = $tprice * $term;
	}
	return $finalprice;
}

function error()
{
	include("header.php");
	OpenTable();
	echo "<BR><div align=center><font color=FF0000><B>Error: Please Go Back and Complete All Required Fields!</B></font></div><BR>";
	CloseTable();
}

if($_GET['action'] == 'PayMethod')
	PayMethod();
else
	if(isset($_GET['sid']))
		if(isset($_POST['submit']))
		{
			$iserror = '0';
			$sql = "SELECT id FROM ".$prefix."_hosting_order_data WHERE pid = '".$_GET['sid']."' AND required = '1' AND type = 'UserInput'";
			$result = $db->sql_query($sql);
			
			while($row = $db->sql_fetchrow($result))
				if($_POST['option'.$row['id']] == "")
					$iserror = '1';

			if($iserror == '1')
				error();
			else
				Pay();
		}
		else
			order();
	else
		home();

include("footer.php");
?>