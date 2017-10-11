<?php session_start();
	// Database - Management Functions
		
	function OpenDatabase()
	{	// Opens the MySQL server and connects to the database
		$ConDB = mysql_connect("mysql4-e", "e229916admin", "EasyItsNotThatEa");	// TODO: Change username / password
		if (!$ConDB)
			{
				die ("<p>Could not connect to database (" . mysql_error() . ")</p>");
			}
		
			if (!mysql_select_db("e229916_EasyVote", $ConDB))
			{
				CreateDatabase($ConDB);
				die("Database should have no been generated! Restart by clicking the refresh button!");
			}
			
		// TODO SOURCEFORGE
		
		return $ConDB;
	}
	
	function CreateDatabase($ConDB)
	{
		// Now let's create the database
		if (mysql_query("CREATE DATABASE e229916_EasyVote", $ConDB))
			{
				echo "<p>The database has been successfully created.</p>";
			}
		else{
				echo "<p>The database cannot be created (" . mysql_error() . ")</p>"; 
				return false;
			}
		mysql_select_db("e229916_EasyVote", $ConDB);
		
		// Now create the tables of the voting database
		// User - Table
		if (!mysql_query("CREATE TABLE User 
		                  (UserID int NOT NULL AUTO_INCREMENT,
						   PRIMARY KEY (UserID),
   						   Pin  varchar(32),
						   HashSignIn varchar(40),
						   HashBallot varchar(40),
						   TanSignIn  varchar(40),
						   TanBallot  varchar(40),
						   TvnSignIn   varchar(40),
						   TvnBallot  varchar(40),
						   UserName    varchar(100),
						   UserAddress varchar(100),
						   UserEmail   varchar(100))						   
						   ", $ConDB))
			{
				echo "<p>The database table User cannot be created (" . mysql_error() . ")</p>";
				return false;
			}
		// SignIn - Table
		if (!mysql_query("CREATE TABLE SignIn 
		                  (DataID int NOT NULL AUTO_INCREMENT,
						   PRIMARY KEY (DataID),
   						   MixingStep int,
						   Data     varchar(2000),
						   Checksum varchar(40)
						   )", $ConDB))
			{
				echo "<p>The database table SignIn cannot be created (" . mysql_error() . ")</p>";
				return false;
			}
		// Ballot - Table
		if (!mysql_query("CREATE TABLE Ballot 
		                  (DataID int NOT NULL AUTO_INCREMENT,
						   PRIMARY KEY (DataID),
   						   MixingStep int,
						   Data     varchar(2000),
						   Checksum varchar(40)
						   )", $ConDB))
			{
				echo "<p>The database table Ballot cannot be created (" . mysql_error() . ")</p>";
				return false;
			}
		// TAN - Result
		if (!mysql_query("CREATE TABLE TanResult 
		                  (DataID int NOT NULL AUTO_INCREMENT,
						   PRIMARY KEY (DataID),
   						   VotingTAN varchar (40),
						   TanID     varchar(40)
						   )", $ConDB))
			{
				echo "<p>The database table TanResult cannot be created (" . mysql_error() . ")</p>";
				return false;
			}
		// Result
		if (!mysql_query("CREATE TABLE Result 
		                  (DataID int NOT NULL AUTO_INCREMENT,
						   PRIMARY KEY (DataID),
   						   Count int
						   )", $ConDB))
			{
				echo "<p>The database table Result cannot be created (" . mysql_error() . ")</p>";
				return false;
			}
		// VotingTanID
		if (!mysql_query("CREATE TABLE VotingTan 
		                  (DataID int NOT NULL AUTO_INCREMENT,
						   PRIMARY KEY (DataID),
   						   VotingTanID varchar(20),
						   VotingTanSum varchar (40),
						   VotingTanHash varchar (40)
						   )", $ConDB))
			{
				echo "<p>The database table TanResult cannot be created (" . mysql_error() . ")</p>";
				return false;
			}
		// Option
		if (!mysql_query("CREATE TABLE Options 
		                  (DataID int NOT NULL AUTO_INCREMENT,
						   PRIMARY KEY (DataID),
   						   TextData varchar(100)
						   )", $ConDB))
			{
				echo "<p>The database table Option cannot be created (" . mysql_error() . ")</p>";
				return false;
			}
		// Mixing-Key
		if (!mysql_query("CREATE TABLE MixingKey 
		                  (DataID int NOT NULL AUTO_INCREMENT,
						   PRIMARY KEY (DataID),
   						   MixKey varchar(500),
						   Name   varchar(100),
						   PrivateKey varchar(500)
						   )", $ConDB))
			{
				echo "<p>The database table MixingKey cannot be created (" . mysql_error() . ")</p>";
				return false;
			}
		// Settings
		if (!mysql_query("CREATE TABLE Settings 
		                  (DataID int NOT NULL AUTO_INCREMENT,
						   PRIMARY KEY (DataID),
   						   Setting varchar(40),
						   Value   varchar(40)
						   )", $ConDB))
			{
				echo "<p>The database table MixingKey cannot be created (" . mysql_error() . ")</p>";
				return false;
			}
			
		echo "<p>All databases have been created successfully</p>";
	}
	
	function AddAttribute($Attribute)
	{	// Sets a new attribute - a attribute cannot be deleted by the admin (so use carefully)
		if (GetAttribute($Attribute))
			return;
		$NewAttribute = mysql_real_escape_string($Attribute);
		mysql_query("INSERT INTO Settings (Setting) VALUES('$NewAttribute')", $_SESSION["ConDB"]);
	}
	function GetAttribute($Attribute)
	{	// Retrieves the attribute
		$AttributeName = mysql_real_escape_string($Attribute);
		$Result = mysql_query("SELECT Setting FROM Settings WHERE Setting='$AttributeName'", $_SESSION["ConDB"]);
		
		while ($Row = mysql_fetch_array($Result))
		{
			return true;
		}
		return false;
	}
	
	function SetValue($Name, $Value)
	{
		$NewName  = mysql_real_escape_string($Name);
		$NewValue = mysql_real_escape_string($Value);
		
		if (!mysql_query("UPDATE Settings SET Setting='NewValue' WHERE Setting='$NewName'", $_SESSION["ConDB"]))
			mysql_query("INSERT INTO Settings (Setting, Value) VALUES ('$NewName', '$NewValue')", $_SESSION["ConDB"]);
	}
	
	function GetValue($Name)
	{
		$NewName = mysql_real_escape_string($Name);
		
		$Result = mysql_query("SELECT Value FROM Settings WHERE Setting='$NewName'", $_SESSION["ConDB"]);
		while ($Row = mysql_fetch_array($Result))
		{
			return $Row["Value"];
		}
	
		return null;
	}

	$_SESSION["ConDB"] = OpenDatabase();


?>