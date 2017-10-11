<?php session_start(); 
	include_once("Database.php");
?>
<html>
	<head>
		<title>EasyVote - Voting TAN</title>
		<script type="text/javascript" src="MD5.js"></script>
		<script type="text/javascript" src="Crypto.js"></script>
	</head>
	<body>
	<h1>EasyVote - Voting TAN Management / Vote Count</h1>
	<?php
		if ($_SESSION["Admin"] == false)
		{	// Exit here
			echo "<a href='Admin.php'>You have to login first! Click here to login!</a>";
			echo "</body></html>";
			exit();
		}
	?>
	<script type='text/javascript'>
		var ArrayTan = new Array();
		var ArrayIndex = new Array();
		var L;
		<?php
			// Here we include the -TAN - Array
			$Result = mysql_query("SELECT Data FROM Ballot WHERE MixingStep=0", $_SESSION["ConDB"]);
			echo "//".mysql_error()."\n";
			$i = 0;
			while ($Row = mysql_fetch_array($Result))
			{	// Include it in the result
				echo "ArrayTan[$i] = \"".$Row["Data"]."\";\n";
				$Result2 = mysql_query("SELECT VotingTanID FROM VotingTan WHERE VotingTanHash='".md5($Row["Data"])."'", $_SESSION["ConDB"]);
				echo "//".mysql_error()."\n";
				$Row2 = mysql_fetch_array($Result2);
				echo "ArrayIndex[$i] = \"".$Row2["VotingTanID"]."\";\n";
				$i++;
			}
			echo "L=$i;\n";
		?>
		
		function CountVotes()
		{
			document.getElementById("Tans").value=OpenTheVotingTans(ArrayTan, ArrayIndex, L, document.getElementById("p").value); 
			document.getElementById("p").value="";
		}	</script>
	<form method='post'>
		<input type='hidden' name='TANVerificationList' id="Tans">
		<table>
			<tr><td>Enter here the TAN - Password: </td><td><input type='text' name='TANPassword' id="p"></td></tr>
			<tr><td><input type='submit' onclick='CountVotes();' value='Submit TAN data to server'></tr></td>
		</table>
	</form>
	<?php
		include_once("Database.php");
	
		// Now: lets see whether wee can update the databases
		$Result = $_POST["TANVerificationList"];
		//echo $Result;
		if ($Result)
		{	// We have a list - - now extract the voting numbers
			$TheList = explode(",", $Result);
			$L = count($TheList);
			$L = ($L - 1);
			
			//echo $L;
			
			echo $Result;
			
			for ($i = 0; $i < $L; $i++)
			{	// Now extract the information out of the array and store it in the database
				$TanNumber = mysql_real_escape_string($TheList[$i]); 
				
				// We can now extract the candidate here and count the votes
				if (substr($TanNumber, strlen($TanNumber) - 1, 1) != "0")
				{	// We have a candidate number
					$C = substr($TanNumber, strlen($TanNumber) - 1, 1);
					mysql_query("INSERT INTO Result (Count) VALUES ('$C')", $_SESSION["ConDB"]);
				}
				
				mysql_query("INSERT INTO TanResult (VotingTan) VALUES ('$TanNumber')", $_SESSION["ConDB"]);
				echo mysql_error();
			}
						
			echo "<p>The Vote-Count has been completed!</p>";
		}
	
	?>
	<?php include("Banner.php");?>
	</body>
</html>