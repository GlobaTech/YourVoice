<?php session_start();

/***************************************************************************
 *                                YourVoice.php
 *                            -------------------
 *   Last Updated           : Aug 18, 2017 - 16:36 pm
 *   Copyright             :  Lungisani608 Mbambo
 *   Support              :  http://php.pogoworld.co.uk/
 *
 *   
 *
 *
 ***************************************************************************/
//
//
//
	include_once("Database.php");
	
	// TODO: Security - Params for EasyVote
	$_SESSION["PinPassword"] = "ThisIsTheTopSecretPINPasswordToGenerateThePins";
	
	function CompleteUserList()
	{	// This will complete the user list by assigning an indiviual PIN to each user
		// The PIN together with his UserID is send to each user by mail
		
		// Check if we have Admin - Rights
		if ($_SESSION["Admin"] == false)
			return;			// We are now admin
			
		// Check if the User-List has been finished
		if (GetAttribute("UsersFinished") == false)
		{
			echo "<p><font color='ff0000'>The user list must be finished before trying to send the PINs to the users</font></p>";
			return;
		}
		if (GetAttribute("SetupUser") == true)
		{
			echo "<p>The User table has already been set up and PIN - Mails already delivered!</p>";
			return;
		}

		// Now we can proceed with the List - Creation
		$Result = mysql_query("SELECT UserID, UserName, UserEmail FROM User", $_SESSION["ConDB"]);
		
		while ($Row = mysql_fetch_array($Result))
		{	// Now proceed with each user
			$UserID = $Row["UserID"];
			$PIN = $_SESSION["PinPassword"];
			$PIN = md5($PIN . $Row["UserID"]);
			$PIN = base64_encode(md5($PIN . $Row["UserName"]));
			$PIN = substr($PIN, 0, 10);	// each PIN = 40-Bit (= practically impossible to break on an online scenario - offline impossible with the PinPassword
			mysql_query("UPDATE User SET Pin='$PIN' WHERE UserID='$UserID'", $_SESSION["ConDB"]);
			echo mysql_error();
			// TODO Mail the user data (Doesn't work yet)
			//MailPINData($Row["UserID"], $Row["UserName"], $Row["UserEmail"], $PIN);
			
			// Debugging - Purposes only
			echo "<p>$UserID - $PIN</p>";
		}
		
		// The user list has completed - save this fact
		AddAttribute("SetupUser");
		return;
	}	

	function MailPINData($UserID, $UserName, $UserEmail, $UserPin)
	{	// Create a mail for the user and send him the UserID / PIN
		$to = "$UserEmail";
		$subject = "EasyVote UserID / PIN";

		$message = "
			<html>
				<head>
					<title>EasyVote UserID / PIN</title>
				</head>
				<body>
					<p>Dear $UserName,</p>
					<p>this email contains your UserID / PIN you require to login on the EasyVote - Online - Voting System. </p>
					<p>Keep this UserID / PIN pair confidential! Official election workers will never ask you for your PIN (except you choose to cast your ballot by phone)</p>
					<table> <tr><td>Your UserID is: </td><td>$UserID </td></tr>
							<tr><td>Your PIN is:    </td><td>$UserPIN</td></tr>
					</table>
					<p>Sincerely, </p>
					<p>Your EasyVote team</p>
				</body>
			</html>";

		// TODO: Change the meail settings
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
		$headers .= 'From: <admin@localhost>' . "\r\n";
		
		mail($to,$subject,$message,$headers);
	}
	
	function CheckTANSignIn($UserID, $TAN)
	{	// Returns the correct TVN if the TAN is valid and invalidates it at once
		$TheUserID = mysql_real_escape_string($UserID);
		$TheTAN    = md5($TAN);
		$TheInsertedTan = mysql_real_escape_string($TAN);
		
		echo 
		
		$Result = mysql_query("SELECT TvnSignIn, TanSignIn FROM User WHERE UserID='$TheUserID' AND HashSignIn='$TheTAN'", $_SESSION["ConDB"]);
		while($Row = mysql_fetch_array($Result))
		{	// Is the TAN already here?
			if ($Row["TanSignIn"])
			{	// Yes - the TAN has already been etered by the user -> Invalid TAN
				return false;
			}
			// Invalidate TAN
			mysql_query("UPDATE User SET TanSignIn='$TheInsertedTan' WHERE UserID='$TheUserID'", $_SESSION["ConDB"]);
			return $Row["TvnSignIn"];
		}
		return false;
	}
	function CheckTANBallot($UserID, $TAN)
	{	// Returns the correct TVN if the TAN is valid and invalidates it at once
		$TheUserID = mysql_real_escape_string($UserID);
		$TheTAN    = md5($TAN);
		$TheInsertedTan = mysql_real_escape_string($TAN);
		
		$Result = mysql_query("SELECT TvnBallot, TanBallot FROM User WHERE UserID='$TheUserID' AND HashBallot='$TheTAN'", $_SESSION["ConDB"]);
		echo mysql_error();
		while($Row = mysql_fetch_array($Result))
		{	// Is the TAN already here?
			if ($Row["TanBallot"])
			{	// Yes - the TAN has already been entered by the user -> Invalid TAN
				return false;
			}
			// Invalidate TAN
			mysql_query("UPDATE User SET TanBallot='$TheInsertedTan' WHERE UserID='$TheUserID'", $_SESSION["ConDB"]);
			return $Row["TvnBallot"];
		}
		return false;
	}
?>