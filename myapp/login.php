<?php
session_start(); // Start the session at the beginning of the script

$host = "localhost";
$user = "root";
$password = "";
$dbname = "sa";

try {
    // Establish database connection
    $conn = new PDO("mysql:host={$host};dbname={$dbname}", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];  // Get the email and password from POST data

    // Query to find the employee by their email and password
    $sql = "SELECT p.p_id, p.fname, p.lname, p.email, d.dept_name, pos.pos_name, p.pos_id
            FROM PERSON p
            JOIN department d ON p.dept_id = d.dept_id
            JOIN position pos ON p.pos_id = pos.pos_id
            WHERE p.email = :email AND p.password = :password ORDER BY p.p_id";

    // Prepare and bind parameters
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $password);
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        // Store the user's information in the session
        $_SESSION['fname'] = $row['fname'];
        $_SESSION['lname'] = $row['lname'];
        $_SESSION['dept_name'] = $row['dept_name'];
        $_SESSION['pos_name'] = $row['pos_name'];
        $_SESSION['email'] = $row['email'];
        $_SESSION['p_id'] = $row['p_id'];

        // Redirect based on the employee's position
        if ($row["pos_id"] == 3) { // Chief
            header("Location: chiefpage/index.php");
            exit();
        } else if ($row["pos_id"] == 2) { // Manager
            header("Location: userpage/index.php");
            exit();
        } else if ($row["pos_id"] == 1) { // Officer
            header("Location: adminpage/index.php");
            exit();
        }
    } else {
        // If the user is not found, alert the user
        echo '<script>alert("Email or password incorrect.");</script>';
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link rel="stylesheet" href="style_login.css">
  <script type="text/javascript" src="validation.js" defer></script>
</head>
<body>
  <div class="wrapper">
    <h1>Login</h1>
    <p id="error-message"></p>
    <form id="form" action="#" method="POST">
      <div>
        <label for="email-input">
          <span>@</span>
        </label>
        <input type="email" name="email" id="email-input" placeholder="Email">
      </div>
      <div>
        <label for="password-input">
          <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24"><path d="M240-80q-33 0-56.5-23.5T160-160v-400q0-33 23.5-56.5T240-640h40v-80q0-83 58.5-141.5T480-920q83 0 141.5 58.5T680-720v80h40q33 0 56.5 23.5T800-560v400q0 33-23.5 56.5T720-80H240Zm240-200q33 0 56.5-23.5T560-360q0-33-23.5-56.5T480-440q-33 0-56.5 23.5T400-360q0 33 23.5 56.5T480-280ZM360-640h240v-80q0-50-35-85t-85-35q-50 0-85 35t-35 85v80Z"/></svg>
        </label>
        <input type="password" name="password" id="password-input" placeholder="Password">
      </div>
      <button type="submit" value="login">Login</button>
    </form>
  </div>
</body>
</html>