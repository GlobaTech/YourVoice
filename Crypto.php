<?php session_start();
	// Create the RSA - Server Keys here for signing the packet - insertion certificate
	
	function GenerateRSAKey($Seed)
	{	// Returns an array - Result["n"] = n, Result["d"] = e^-1 = 65537^-1
		
	}
		function ChoosePrime($Seed)
		{	// Try to choose a prime
			for ($i = 0; $i < 1000; $i++)
			{
				$Prime = "1" . md5($Seed . $i . "1") . md5($Seed . $i . "2");
				$Result = bcpowmod("2", bcsub($Prime, "1"), $Prime);
				if ($Result == 2)
					return $Prime;
				echo "<p><b>$Prime</b></p>";
				echo "<p>$Result</p>";
			}
			
		}
		
		echo ChoosePrime("a");
?>