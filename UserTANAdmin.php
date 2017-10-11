<?php session_start(); ?>
<html>
	<head>
		<title>EasyVote - User TAN / TVN Management</title>
		<script type="text/javascript" src="MD5.js"></script>
		<script type="text/javascript" src="Crypto.js"></script>
	</head>
	<body>
	<h1>EasyVote - User TAN / TVN Management</h1>
	<?php
		if ($_SESSION["Admin"] == false)
		{	// Exit here
			echo "<a href='Admin.php'>You have to login first! Click here to login!</a>";
			echo "</body></html>";
			exit();
		}
	?>
	<form method='post'>
		<input type='hidden' name='TANVerificationList' id="Tans">
		<table>
			<tr><td>Enter here the TAN - Password: </td><td><input type='text' name='TANPassword'></td></tr>
			<tr><td><input type='button' onclick='document.write(CreateUserTANList(50, TANPassword.value));' value='Show the user TAN / TVN list'></td></tr>
			<tr><td><input type='submit' onclick='document.getElementById("Tans").value=CreateServerTANData(50, TANPassword.value); TANPassword.value="";' value='Submit TAN verification data to server'></tr></td>
		</table>
	</form>
	<?php
		include_once("Database.php");
	
		// Now: lets see whether wee can update the databases
		$Result = $_POST["TANVerificationList"];
		if ($Result)
		{	// We have a list - now extract the necesarry TAN-Hashes and TVN's for our database
			$TheList = explode(",", $Result);
			
			$DatabaseResult = mysql_query("SELECT UserID FROM User", $_SESSION["ConDB"]);
			while ($Row = mysql_fetch_array($DatabaseResult))
			{	// Proceed each and every user and set his TAN hashes and associated TVNs
				$CurrentUserID = ($Row["UserID"] - 1) * 4;
				$CurrentTan1 = mysql_real_escape_string($TheList[$CurrentUserID + 0]);
				$CurrentTan2 = mysql_real_escape_string($TheList[$CurrentUserID + 1]);
				$CurrentTvn1 = mysql_real_escape_string($TheList[$CurrentUserID + 2]);
				$CurrentTvn2 = mysql_real_escape_string($TheList[$CurrentUserID + 3]);
				mysql_query("UPDATE User SET HashSignIn='$CurrentTan1', HashBallot='$CurrentTan2', TvnSignIn='$CurrentTvn1', TvnBallot='$CurrentTvn2' WHERE UserID='".$Row["UserID"]."'", $_SESSION["ConDB"]); 
				echo mysql_error();
			}
			echo "<p>The TAN / TVN data has been sucessfully added!</p>";
		}
	
	?>
	</body>
</html>