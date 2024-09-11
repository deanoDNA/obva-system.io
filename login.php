<?php

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "obva_system";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['username']) && isset($_POST['password1'])) {
        $username = $_POST['username'];
        $password = $_POST['password1'];

        // Prepare SQL statement
        $sql = "SELECT id, password1 FROM users_table WHERE username = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->store_result();

            // Check if user exists
            if ($stmt->num_rows > 0) {
                $stmt->bind_result($id, $hashed_password);
                $stmt->fetch();

                // Verify password
                if (password_verify($password, $hashed_password)) {
                    $_SESSION['user_id'] = $id;
                    $_SESSION['username'] = $username;
                    $_SESSION['loggedin'] = true;
                    
                    // Redirect to a protected page or user dashboard
                    header("Location: \OBVA system\userboard\userboard.php");
                    exit();
                } else {
                    $message = "Invalid password.";
                }
            } else {
                $message = "No user found with that registration number.";
            }

        
        } else {
            echo "Error preparing statement: " . $conn->error;
        }
    } 
}

$conn->close();

if ($message) {
    echo "<script>
        window.onload = function() {
            const popup = document.getElementById('popup');
            const popupMessage = document.getElementById('popup-message');
            popupMessage.textContent = '$message';
            popup.classList.add('show');
            setTimeout(function() {
                popup.classList.remove('show');
            }, 3000);
        }
            
    </script>";
    include 'index.html';
}

?>