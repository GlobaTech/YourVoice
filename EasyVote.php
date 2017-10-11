<?php //ini_set("session.save_path" , "/home/groups/e/ea/easyvote-app");
session_start(); ?>
<html>
	<head>
		<title>EasyVote - Secure, easy to use and anonymous online voting system</title>
		<script type="text/javascript" src="MD5.js"></script>
		<script type="text/javascript" src="Crypto.js"></script>
		<script type="text/javascript" src="Ajax.js"></script>
		<script type="text/javascript" src="RSA.js"></script>
		<script type="text/javascript">
			var TheAjax;
			var GotCertificate;
			var Cert;
			var TVN;
		
			function CheckTVN()
			{
				// Now let's check the certificate
				if (TheAjax.readyState == 4 || TheAjax.readyState == "complete")
				{	
					GotCertificate = true;	// We just received the certificate
					TVN = TheAjax.responseText;
					
					if (TVN=="false")
					{
						alert("The Ballot-Tan you entered is incorrect or already used! Your submitted ballot has not been counted!");
						TVN = "";
						GotCertificate=false;
						return;
					}
					
					alert("Please check your TVN number: " + TVN);
					
					// Create the output of the voting process
					var Result;
					Result  = "<html><head><title>Database - Insertion Proof</title></head>";
					Result += "<body><h1>Easy Vote - Ballot Insertion Certificate</h1>";
					Result += "<p>Safe this file for later checks of the vote database!</p>";
					Result += "<p>Check the TVN number!</p>";
					Result += "<p id='TVN' >TVN number: "+TVN +"</p>";
					Result += "<p id='Cert'>"+Cert+"</p>";
					Result += "</body></html>";
					
					document.write(Result);
				}else{
					return;
				}
			}
		
			function CheckCertificate()
			{	// Now let's check the certificate
				if (GotCertificate == true)
					return;		// We already got it, we can stop this!
				if (TheAjax.readyState == 4 || TheAjax.readyState == "complete")
				{	
					GotCertificate = true;	// We just received the certificate
					Cert = TheAjax.responseText;
				}else{
					return;
				}
					
				// Now we can almost create the insertion-certificate for the user (before send the BallotTan and receive the TVN)
					// DEBUG
					var A = RSA3("1", "5");
				
				alert("The vote packet has been send to the server!");
				
				// Finish the process by sending the Ballot - TAN
				TheAjax = AjaxGet();
				TheAjax = AjaxRequest(TheAjax, CheckTVN, "Certificate.php?BallotTan=" + document.getElementById("B").value + "?Packet="+document.getElementById("V").value);
			}
			
			
				// Now create the new response - site
				/*echo "<h1>Ballot Certificate</h1>";
				echo "<p>This ballot - insertion certificate is your proof, that your ballot has been added correctly to the database. You can use this certificate page to check later, whether your ballot has been counted or the servers database has been manipulated</p>";
				echo "<p><font color='red'>Make sure you safe this Page on your local hard drive by selecting 'File' and 'Save As' from your browsers menu!</font></p>";
				*/
			
			function CastVote()					
			{				
				// First of all, check the user inputs
				
				var Packet = document.getElementById("V").value;
				
				Result = CheckVotingTAN(Packet);
				if (!Result)
				{
					window.alert("You entered an invalid Voting-Tan! Recheck your input!");
					return false;
				}
				
				// Now do the ballot mixing
					// Check for the ballot mixing process
					if (BallotMix = true)	// This variable is beeing inserted by PHP if the mixing - keys are found
					{	// TODO: Add ballot mixing
					
					}
				
				// Now proceed, send the vote
				TheAjax = AjaxGet();
				TheAjax = AjaxRequest(TheAjax, CheckCertificate, "Certificate.php?Packet=" + Packet);
				
				return true;
			}
		</script>
	</head>
	<body>
		<h1>EasyVote - Secure, easy to use and anonymous online voting system</h1>
		<?php
			include_once("Database.php");
			include_once("UserControl.php");
			include_once("UserInterface.php");
		
			// Step 1: Login
			function VoterLogin()
			{	// Show the login screen
				if ($_POST["UserID"])
				{
					$NewUserID = mysql_real_escape_string($_POST["UserID"]);
					$NewPin    = mysql_real_escape_string($_POST["PIN"]);
					
					$Result = mysql_query("SELECT UserID FROM User WHERE UserID='$NewUserID' AND PIN='$NewPin'", $_SESSION["ConDB"]);
					
					while($Row = mysql_fetch_array($Result))
					{
						$_SESSION["User"]   = true;
						$_SESSION["UserID"] = $Row["UserID"];
						return true;
					}
					echo "<p>The entered password is incorrect, try again</p>";
				}
			
				echo  "<table><form method='post'>
						<tr><td></td><td><b>Step 1:</b> You have to login (Please enter the provided UserID / PIN)<td></tr>
						<tr><td>User - ID:</td><td><input type='text'     name='UserID' alt='User - ID'></td></tr>
						<tr><td>Pin:</td>      <td><input type='password' name='PIN'    alt='Password'> </td></tr>
						<tr><td></td><td><input type='submit' value='Login'></td></tr>
					  </form></table>";
				return false;
			}
			
			function VoterVote()
			{	// Here finally the voter enters his Voting-TAN and his CastBallot-TAN - he receives a submit vote certificate and his TVN
				
				echo  "<table><form method='post'>
						 <h3>Step 2:</b>Cast your ballot</h3>
						 <p>In order to cast your vote, look up the candidate - TAN from your Voting-Tan-List you received.\n Afterwards enter the Ballot-Cast-Tan from the other letter.</p>
						 <tr><td>Voting-Tan:</td><td><input type='text' name='VotingTan' id='V' alt='Enter here the Voting-TAN'></td></tr>
						 <tr><td>Ballot-Tan:</td><td><input type='text' name='BallotTan' id='B' alt='Enter here the Ballot-TAN'></td></tr>
						 <tr><td></td><td><input type='button' onclick='CastVote()' value='Cast the ballot' alt='Click to cast the ballot'></td></tr>
						 <tr></tr>
					  </form></table>";
			}
			
			function DoVoting()
			{	
				// Step 1: Login the voter
				if (!$_SESSION["User"])
				{	// Display the login screen
					if (!VoterLogin())
						return;
				}
				// Step 2:  Sign-In (only needed when using Mixing - Servers) 
				
				// Step 3:  Voting (only needed when using Mixing - Servers)
				VoterVote();
			}
			
			
			// Let's start
			DoVoting();
		?>
		<?php		
			if ($_SESSION["User"])
				if (SimpleButton("Logout", "Logout"))
				{	// The user logged out
					echo "<p>You logged successfully out - You can close the browser window now!</p>";
					session_destroy();
				}
		?>
		<?php include("Banner.php");?>
	</body>
</html>
	