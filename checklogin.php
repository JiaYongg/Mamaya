<?php
// Detect the current session
session_start();

// Include the PHP file that establishes the database connection handle: $conn
include_once("mysql_conn.php");

// Include the Page Layout header
include("header.php"); 

// Reading inputs entered in previous page
$email = $_POST["email"];
$pwd = $_POST["password"];

// SQL query to get the Shopper ID and Shopper’s name of a particular record 
// whereby the email address and passowrd in the Shopper table matches with the email address and password entered in the login page
$qry = "SELECT ShopperID, Name FROM Shopper WHERE Email = ? AND Password = ?";

// bind parameters to prevent sql injection 
$stmt = $conn->prepare($qry);
$stmt -> bind_param("ss", $email, $pwd);

// execute the query
$stmt ->execute();

// store the result back into $stmt
$stmt -> store_result();

// bind iterative variables($id, $name) to ShopperID and Name from Shopper table
$stmt -> bind_result($id, $name);
$results = $stmt->fetch(); // returns true if thr is a result and false if no result

$stmt->close();
// loop through each column in the record when found
if ($results == true)
{
	while ($results)
	{
		// Save user's info in session variables
		$_SESSION["ShopperName"] = $name;
		$_SESSION["ShopperID"] = $id;
	

		// To Do 2 (Practical 4): Get active shopping cart
		$qry = "SELECT sc.ShopCartID, COUNT(sci.ProductID) AS NumItems FROM ShopCart sc LEFT JOIN ShopCartItem sci ON sc.ShopCartID=sci.ShopCartID WHERE sc.ShopperID=? AND sc.OrderPlaced=0";

		$stmt = $conn->prepare($qry);
		$stmt -> bind_param("s", $_SESSION["ShopperID"]);

		$stmt->execute();

		$result = $stmt->get_result();
		$stmt->close();
		$conn->close();

		if ($result->num_rows > 0)
		{
			$row = $result->fetch_array();

			if ($row["NumItems"] > 0)
			{
				$_SESSION["Cart"] = $row["ShopCartID"];
				$_SESSION["NumCartItem"] = $row["NumItems"];
			}
		}

		// Redirect to home page
		header("Location: index.php");
		exit;
	}
}
else
{
	echo "<h3 style='color:red'>Invalid Login Credentials</h3>";
}


// To Do 1 (Practical 2): Validate login credentials with database

// if (($email == "ecader@np.edu.sg") && ($pwd == "password")) {
// 	// Save user's info in session variables
// 	$_SESSION["ShopperName"] = "Ecader";
// 	$_SESSION["ShopperID"] = 1;
	
	
// 	// Redirect to home page
// 	header("Location: index.php");
// 	exit;
// }
// else {
// 	echo "<h3 style='color:red'>Invalid Login Credentials</h3>";
// }
	
// Include the Page Layout footer
include("footer.php");
?>