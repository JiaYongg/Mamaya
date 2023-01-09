<?php

session_start();

$name = $_POST["name"];
$address = $_POST["address"];
$country = $_POST["country"];
$phone = $_POST["phone"];
$email = $_POST["email"];
$password = $_POST["password"];

// Include the PHP file that establishes the database connection handle: $conn
include_once("mysql_conn.php");

$qry = "INSERT INTO Shopper (Name, Address, Country, Phone, Email, Password) VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($qry);
$stmt->bind_param("ssssss", $name, $address, $country, $phone, $email, $password); // "ssssss" 6 string parameters

if ($stmt->execute()) // successfully executed
{
    // retrieve the Shopper ID assigned to the new shopper
    $qry = "SELECT LAST_INSERT_ID() AS ShopperID";
    $result = $conn->query($qry); // Execute the SQL and get the returned result
    while ($row = $result->fetch_array())
    {
        $_SESSION["ShopperID"] = $row["ShopperID"];
    }

    $message = "Registration successful! <br /> Your ShopperID is $_SESSION[ShopperID] <br />";
}
else // Error message
{
    $message = "<h3 style='color:red'>Error in inserting record</h3>";
}

// Release the resource allocated for prepared statement
$stmt->close();

// Close database connection
$conn->close();

// Display Page Layout header with updated session state and links
include("header.php");

// Display message
echo $message;

//Display Page Layout Footer
include("footer.php");
?>