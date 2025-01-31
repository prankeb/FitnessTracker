<?php

// Set up connection constants
define("SERVER_NAME", "localhost");
define("DBF_USER_NAME", "root");
define("DBF_PASSWORD", "mysql");
define("DATABASE_NAME", "fitnessTrackDB");

// Create connection object
$db = new mysqli(SERVER_NAME, DBF_USER_NAME, DBF_PASSWORD, DATABASE_NAME);

// Check connection
if ($db->connect_error) {
    die('Unable to connect to the database: ' . $db->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["login"])) {
    // Validate input data
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Prepare the SQL query with a parameterized statement
    $sql = "SELECT UserID, Username, Password FROM User WHERE Username = ?";

    // Set up a prepared statement
    if ($stmt = $db->prepare($sql)) {
        // Bind the parameter
        $stmt->bind_param("s", $username);

        // Execute the query
        $stmt->execute();

        // Bind the results
        $stmt->bind_result($user_id, $username, $hashed_password);

        // Check if a row is returned
        if ($stmt->fetch()) {
            // Verify the entered password with the hashed password
            if (password_verify($password, $hashed_password)) {
                //Starts a session to save that the user is logged in
                session_start();

                //Saves the user's id and their username 
                $_SESSION['user_id'] = $user_id;
                $_SESSION['username'] = $username;

                // Authentication successful
                //Redirects user to logged in version of homepage
                header("Location: index.php");
                exit();
            } else {
                // Authentication failed
                echo "Invalid username or password";
            }
        } else {
            // Authentication failed
            echo "Invalid username or password";
        }

        // Close the statement
        $stmt->close();
    } else {
        echo "Error preparing query: " . $db->error;
    }
}

// Close the database connection
$db->close();

?>


