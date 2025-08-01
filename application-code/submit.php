<?php
// Enable error reporting for debugging (optional)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection details
$servername = "";
$username = "admin";
$password = "password";  // Make sure the password is correct
$dbname = "info";

// Establish the database connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Sanitize user input to prevent SQL injection
$name = mysqli_real_escape_string($conn, $_POST["name"]);
$email = mysqli_real_escape_string($conn, $_POST["email"]);
$website = mysqli_real_escape_string($conn, $_POST["website"]);
$comment = mysqli_real_escape_string($conn, $_POST["comment"]);
$gender = mysqli_real_escape_string($conn, $_POST["gender"]);

// Display input values for debugging purposes (optional)
echo "Name: " . $name . "<br>";
echo "Email: " . $email . "<br>";
echo "Website: " . $website . "<br>";
echo "Comment: " . $comment . "<br>";
echo "Gender: " . $gender . "<br>";

// SQL query to insert data into the database
$sql = "INSERT INTO user (name, email, website, comment, gender)
        VALUES ('$name', '$email', '$website', '$comment', '$gender')";

// Execute the query and handle errors
if (mysqli_query($conn, $sql)) {
    echo "New record created successfully";
} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}

// Close the database connection
mysqli_close($conn);
?>
