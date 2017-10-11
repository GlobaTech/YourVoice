<?php session_start() ?>
<?php
	include_once("Database.php");
	include_once("UserControl.php");

	// Check whether we already got the voting packet:
		if ($_GET["Packet"])
		{	// We received the voting packet!		
				// Create the certificate page
				$VotePacket = mysql_real_escape_string($_GET["Packet"]);
				$Checksum   = md5($VotePacket);
				
				// Now we should generate the certificates - values
					// Let's hash the database
					$Check = md5("");
					$Result = mysql_query("SELECT Checksum FROM Ballot", $_SESSION["ConDB"]);
					while ($Row = mysql_fetch_array($Result))
					{	// Now proceed all entries
						$Check = md5($Check . $Row["Checksum"]);
					}
				
				$Signature="0";
				// Now we obtained the users Checksum, the Check(sum) for the database - show these values to the user as a plain text
				echo "$Checksum,$Check,$Signature";
				$_SESSION["Packet"] = $VotePacket;
		}
						
		if ($_GET["BallotTan"])
		{				
			// Now we can really add the users vote
			// First of all, check the TAN
			$TVN = CheckTANBallot($_SESSION["UserID"], $_GET["BallotTan"]);
			if ($TVN == false)
			{	// Tan TAN is incorrect - abort the process
				echo "false";
				return;
			}
			// First of all, add the users data to the database
			$VotePacket = mysql_real_escape_string($_GET["Packet"]);
			$Checksum   = md5($VotePacket);
			
			mysql_query("INSERT INTO Ballot (MixingStep, Data, Checksum) VALUES ('0', '$VotePacket', '$Checksum')", $_SESSION["ConDB"]);
			echo mysql_error();
			echo $TVN;
			// Now we can logout the user
			session_destroy();
			
			// TODO: Now we can send an email containing the database hash to the election workers
			// In case of bigger elections, these email can be send all 3 - times, 5 times, ... a vote arrives
		}
?>