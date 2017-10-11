<?php session_start(); ?>
<html>
	<head>
		<title>EasyVote - MixNet Administration</title>
		<script type='text/javascript' src='MD5.js'></script>
		<script type='text/javascript' src='Crypto.js'></script>
	</head>
	<body onmousemove="RNDUpdate(37 * event.ScreenX + 53 * event.ScreenY);">
	<h1>EasyVote - MixNet Administration</h1>
	<p>This site lets you administrate the ballot mixing process</p>
	<?php
		// Add a new client - key
		include_once("Database.php");
		
		function DoEasyVoteMixing()
		{	// Check, whether we are in the setup stage
			if (GetAttribute("SetupFinished"))
			{	// The setup stage has been finished - You cannot add your keys any longer
				if (GetAttribute("ElectionFinished") == true)
				{	
					echo "<p>The election has been finished - Now you can start the ballot shuffling process!</p>"; 
				}else{
					echo "<p>The election isn't over yet. You cannot do anything at the moment!</p>";
				}
				return;
			}
			
			// The setup hasn't been finished yet - We cann add our mixing keys here
			echo "<script type='text/javascript'>
					document.write(CreateUserTANList(30, 'HaHa'));	
				  </script>";
			
			echo $b[0];
		}
		
		// Main - Script
		// The user has to login first: TODO Change the password)
		if ($_POST["MixPassword"] == "IAmTheBallotMixer")
			$_SESSION["Mixing"] = true;
			
		if ($_SESSION["Mixing"] == true)
		{
			DoEasyVoteMixing();
		}else{
			echo 	"<form method='post'>
							<p>You have to login before you can change any setting</p>
							<p>Password: <input type='password' name='MixPassword'><input type='submit' value='Login'></p>
					</form></body></html>";
		}
	?>
	</body>
</html>