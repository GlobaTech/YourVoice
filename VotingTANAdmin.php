<?php session_start(); ?>
<html>
	<head>
		<title>EasyVote - Voting TAN</title>
		<script type="text/javascript" src="MD5.js"></script>
		<script type="text/javascript" src="Crypto.js"></script>
	</head>
	<body>
	<h1>EasyVote - Voting TAN Management</h1>
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
			<tr><td>Enter here a random value for this list: </td><td><input type='text' name='RNDVal'></td></tr>
			<tr><td><input type='button' onclick='document.write(CreateVotingTANList(1, TANPassword.value, RNDVal.value));' value='Show the user TAN list'></td></tr>
			<tr><td><input type='submit' onclick='document.getElementById("Tans").value=CreateVotingTanListServer(1, TANPassword.value, RNDVal.value); TANPassword.value="";' value='Submit TAN verification data to server'></tr></td>
		</table>
	</form>
	<?php
		include_once("Database.php");
	
		// Now: lets see whether wee can update the databases
		$Result = $_POST["TANVerificationList"];
		//echo $Result;
		if ($Result)
		{	// We have a list - now extract the necesarry TAN-Hashes and TVN's for our database
			$TheList = explode(",", $Result);
			$L = count($TheList);
			$L = ($L - 1) / 3;
			
			//echo $L;
			
			for ($i = 0; $i < $L; $i++)
			{	// Now extract the information out of the array and store it in the database
				$TanIndex = mysql_real_escape_string($TheList[$i*3 + 0]); 
				$TanHash  = mysql_real_escape_string($TheList[$i*3 + 1]);
				$TanSum   = mysql_real_escape_string($TheList[$i*3 + 2]);
				
				mysql_query("INSERT INTO VotingTan (VotingTanID, VotingTanHash, VotingTanSum) VALUES ('$TanIndex', '$TanHash', '$TanSum')", $_SESSION["ConDB"]);
				echo mysql_error();
			}
						
			echo "<p>The Voting TAN data has been sucessfully added!</p>";
		}
	
	?>
	</body>
</html>