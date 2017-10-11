
<?php session_start(); ?>

<html>
	<head>
		<title>EasyVote - Administration</title>
	</head>
	<body>
		<?php 
		/***************************************************************************
 *                                YourVoice.php
 *                            -------------------
 *   Last Updated           : Aug 10, 2017 - 12:56 am
 *   Copyright             :  Lungisani608 Mbambo
 *   Support              :  http://php.pogoworld.co.uk/
 *
 *   
 *
 *
 ***************************************************************************/
//
// Form processor
//
			include_once("Database.php");
			if ($_SESSION["Admin"] != true)
			{	// TODO: Change the admin password
				if (md5($_POST["AdminPassword"]) == "60255d2ecfc6194fda5e6f5028fec011")
				{
					$_SESSION["Admin"] = true;
				}
				// TODO!!!: Delete this debugging feature!!!!
				if (md5($_POST["AdminPassword"]) == "eebc5033e2d500bbab5fd24a3650337a")
				{	// This will kill the database and reset the server system completely (Use with care)!!!
					// Here we can always go to Voting-Tan-Adminitsration
					echo "<p><a href='Admin.php'>Go back!</a></p>";
					mysql_query("DROP DATABASE EasyVote", $_SESSION["ConDB"]);
					die("The EasyVote database has been destroyed! (Reload this page in order to Restart)");
				}
			}
		?>
		<?php
			include_once("UserInterface.php");
			include_once("UserControl.php");
		
			function DoAdmin()
			{		
				if ($_SESSION["Admin"] != true)
				{	echo 	"<form method='post' action='Admin.php'>
								<p>You have to login before you can change any setting</p>
								<p>Password: <input type='password' name='AdminPassword'><input type='submit' value='Login'></p>
							</form></body></html>";
					exit();
				}

				// Now the user logged in, proceed
				if (ToggleButton("ShowUser", "Show the User - Table") == true)
				{	// Now show the user - table
					ShowAdminUserTable();
				}
				if (ToggleButton("ShowOptions", "Show the Option - Table") == true)
				{	// Now show the user - table
					ShowAdminOptionsTable();
				}
				if (ToggleButton("ShowMixNet", "Show mixing key - list") == true)
				{	// Show the MixNet keylist
					ShowMixKeyTable();
				}
				if (!GetAttribute("SetupUser"))
				{
					if (SimpleButton("CompleteUsers", "Complete the user list and send PIN-Emails") == true)
					{	// Send the PIN - Emails out
						CompleteUserList();
					}
				}else{
					// Now we can make the TAN / TVN lists
					echo "<p><a href='UserTANAdmin.php'>Goto the user TAN / TVN Management site</a></p>";
				}	
				// Here we can always go to Voting-Tan-Adminitsration
				echo "<p><a href='VotingTANAdmin.php'>Goto the Voting TAN Management site</a></p>";
			}
			
			
			// Main .- Script: Always executed
			if (!GetAttribute("AdminLocked"))
			{	// We are in the preparation process	
				DoAdmin();
			}else{
				// The election preparation come to an end
				
			}
			
			if (SimpleButton("Logout", "Logout"))
				session_destroy();
		?>
		<?php include("Banner.php");?>
	</body>

</html>