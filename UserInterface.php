<!DOCTYPE html PUBLIC “-//W3C//DTD XHTML 1.0 Strict//EN”“http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd”>   
<html xmlns=”http://www.w3.org/1999/xhtml” xml:lang=”en” lang=”en”>         
     <head>                  
	 <title>Membership Form</title>             
	 <link rel=”stylesheet” type=”text/css” href=”common.css” />                
	 <style type=”text/css”>                                .error { background: #d33; color: white; padding: 0.2em; }               
    </style>    
	</head>         
	<body> 
<?php session_start();
	/***************************************************************************
 *                                YourVoice.php
 *                            -------------------
 *   Last Updated           : Aug 03, 2017 - 10:08 pm
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
	
	function ToggleButton($Name, $ButtonText)
	{
		echo "<form method='post'><input type='hidden' name='ToggleButton$Name' value='Clicked'><input type='submit' value='$ButtonText'></form>";
		
		if ($_POST["ToggleButton$Name"] == "Clicked")
		{	// Change the toogle buttons state
			if ($_SESSION["ToggleButton$Name"] == true)
			{
				$_SESSION["ToggleButton$Name"] = false;
			}else{
				$_SESSION["ToggleButton$Name"] = true;
			}
		}
		
		if ($_SESSION["ToggleButton$Name"] != true)
			return false;
		return true;
	}
	function SimpleButton($Name, $ButtonText)
	{	
		echo "<form method='post'><input type='hidden' name='SimpleButton$Name' value='Clicked'><input type='submit' value='$ButtonText'></form>";
		if ($_POST["SimpleButton$Name"] == "Clicked")
		{
			return true;
		}
		return false;
	}
	
	function TD($Text)
	{
		return "<td>" . $Text . "</td>";
	}
	function TextBox($Name)
	{
		return "<input type='text' name='$Name'>";
	}
	function Button($Text)
	{
		return "<input type='submit' name='Submit' value='$Text'>";
	}
	
	function ShowAdminUserTable()
	{	// Check if the user already logged in as admin
		if ($_SESSION["Admin"] != true)
			return;		// The user didn't login - exit
		
		if (GetAttribute("UsersFinished"))
		{	// The table cannot be changed anymore
			ShowUserTable();		// This is the no change user version
			return;
		}
		
		// Check, whether the user clicked the add button
		if ($_POST["Submit"] == "Add user")
		{	// He entered something, now add the user to the database
			$NewUserName = mysql_real_escape_string($_POST["UserName"]);
			$NewUserAddress = mysql_real_escape_string($_POST["UserAddress"]);
			$NewUserEmail = mysql_real_escape_string($_POST["UserEmail"]);
			if ($NewUserName == "FinishUsers")
			{
				AddAttribute("UsersFinished");
				return;
			}
			if (!mysql_query("INSERT INTO User (UserName, UserAddress, UserEmail) VALUES ('$NewUserName', '$NewUserAddress', '$NewUserEmail')", $_SESSION["ConDB"]))
				echo "<p>MySQL - Error (".mysql_error().")</p>";
			
		}
		
		// Show the user database
		echo "<table>\n";
			// Now show the table - header
			echo "<form method='post'>";
			echo "<tr>" .TD("UserID").TD("Username").TD("Address").TD("Email").TD(" ")."</tr>\n";
			echo "<tr>" .TD(" ").TD("<font color='0xff0000'>In order to finish editing the table, add  \"FinishUsers\" to the table</font>") . "</tr>\n";
			echo "<tr>" .TD(" ").TD("<font color='0xff0000'>You cannot change the table anymore afterwards!</font>")."</tr>\n";
			$UserData = mysql_query("SELECT UserID, UserName, UserAddress, UserEmail FROM User", $_SESSION["ConDB"]);
				while ($Row = mysql_fetch_array($UserData))
				{	// Now output the data
					if ($_POST["Submit"] == "Delete User (".$Row['UserID'].")")
					{
						mysql_query("DELETE FROM User WHERE UserID = '".$Row['UserID']."'", $_SESSION["ConDB"]);
					}else{
						echo "<tr>".TD($Row["UserID"]).TD($Row["UserName"]).TD($Row["UserAddress"]).TD($Row["UserEmail"]).TD(Button("Delete User (".$Row['UserID'].")"))."</tr>";	
					}
				}
			// Now show an add - entry
			echo "<tr>".TD("Auto ID").TD(TextBox("UserName")).TD(TextBox("UserAddress")).TD(TextBox("UserEmail"))."</tr>";
			echo "<tr>".TD(Button("Add user"))."</tr>";
			echo "</form>";
		echo "</table>\n";
	}
	function ShowAdminOptionsTable()
	{	// Shows election options table
		if ($_SESSION["Admin"] != true)
			return;
		
		if (GetAttribute("OptionsFinished"))
		{	// The table cannot be changed anymore
			ShowUserOptionsTable();		// This is the no change user version
			return;
		}
		
		// Check, whether the user clicked the add button
		if ($_POST["Submit"] == "Add option")
		{	// He entered something, now add the option to the database
			$NewOption = mysql_real_escape_string($_POST["OptionText"]);
			if ($NewOption == "FinishOptions")
			{
				AddAttribute("OptionsFinished");
				return;
			}
			
			if (!mysql_query("INSERT INTO Options (TextData) VALUES ('$NewOption')", $_SESSION["ConDB"]))
				echo "<p>MySQL - Error (".mysql_error().")</p>";
		}
		
		// Show the options database
		echo "<table>\n";
			// Now show the table - header
			echo "<form method='post'>";
			echo "<tr>" . TD("Option ID") . TD("Option").TD(" ")."</tr>\n";
			echo "<tr>" .TD(" ").TD("<font color='0xff0000'>In order to finish editing the table, add  \"FinishOptions\" to the table</font>") . "</tr>\n";
			echo "<tr>" .TD(" ").TD("<font color='0xff0000'>You cannot change the table anymore afterwards!</font>")."</tr>\n";
			$UserData = mysql_query("SELECT DataID, TextData FROM Options", $_SESSION["ConDB"]);
				while ($Row = mysql_fetch_array($UserData))
				{	// Now output the data
					if ($_POST["Submit"] == "Delete Option (".$Row['DataID'].")")
					{
						mysql_query("DELETE FROM Options WHERE DataID = '".$Row['DataID']."'", $_SESSION["ConDB"]);
					}else{
						echo "<tr>".TD($Row["DataID"]).TD($Row["TextData"]).TD(Button("Delete Option (".$Row['DataID'].")"))."</tr>";	
					}
				}
			// Now show an add - entry
			echo "<tr>".TD("Auto ID").TD(TextBox("OptionText"))."</tr>";
			echo "<tr>".TD(Button("Add option"))."</tr>";
			echo "</form>";
		echo "</table>\n";
		
	}
	
	function ShowUserOptionsTable()
	{
		// Show the options database
		echo "<table>\n";
			// Now show the table - header
			echo "<tr>" . TD("Option ID        ") . TD("Option").TD(" ")."</tr>\n";
			$UserData = mysql_query("SELECT DataID, TextData FROM Options", $_SESSION["ConDB"]);
				while ($Row = mysql_fetch_array($UserData))
				{	// Now output the data
					echo "<tr>".TD($Row["DataID"]).TD($Row["TextData"])."</tr>";	
				}
		echo "</table>\n";
	}
	
	function ShowUserTable()
	{	// The normal users still shall not see this table
		if ($_SESSION["Admin"] != true)
			return;
		// Show the user database
		echo "<table>\n";
			// Now show the table - header
			echo "<tr>" .TD("UserID").TD("Username").TD("Address").TD("Email").TD(" ")."</tr>\n";
			$UserData = mysql_query("SELECT UserID, UserName, UserAddress, UserEmail FROM User", $_SESSION["ConDB"]);
				while ($Row = mysql_fetch_array($UserData))
				{	// Now output the data
					echo "<tr>".TD($Row["UserID"]).TD($Row["UserName"]).TD($Row["UserAddress"]).TD($Row["UserEmail"])."</tr>";	
				}
		echo "</table>";
	}
	
	function ShowMixKeyTable()
	{	// Shows (all users) Mix Key Table
		echo "<table>\n";
			// Now show the table - header
			echo "<tr>" .TD("KeyID").TD("Responsible election workers").TD("Key (in Hexadecimal)")."</tr>\n";
			$UserData = mysql_query("SELECT DataID, MixKey, Name FROM MixingKey", $_SESSION["ConDB"]);
				while ($Row = mysql_fetch_array($UserData))
				{	// Now output the data
					echo "<tr>".TD($Row["DataID"]).TD($Row["Name"]).TD($Row["MixingKey"])."</tr>";	
				}
		echo "</table>";
	}
?>
</body> 

  </html> 