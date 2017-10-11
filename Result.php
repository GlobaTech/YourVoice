<?php session_start();?>
<html>
	<head><title>Elections result</title></head>
	<body>
		<h1>Elections result</h1>
		<p>Here you can see the result of the election, when the election has been counted</p>
		<table>
			<tr><td>Option</td><td>Result</td></tr>
			<?php
				include_once("Database.php");
				// Now let's count the votes submitted to Result
				for ($i = 1; $i <= 6; $i++)
				{
					$c = 0;
					$Result = mysql_query("SELECT * FROM Result WHERE Count='$i'", $_SESSION["ConDB"]);
					while ($Row = mysql_fetch_array($Result))
					{
						$c++;
					}
					echo "<tr><td>$i</td><td>$c</td></tr>";
				}
				
			?>
		</table>
	</body>
</html>