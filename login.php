<!DOCTYPE html>
<html>

<head>
    <title>User Profile</title>
    <?php include_once 'scripts.php'; ?>
</head>
<?php include_once 'utils.php'; ?>
<body>
    <?php include_once 'header.php'; ?>
    <h1>Login</h1>

<form action="login.php" method="POST">
    <label><b>Username</b></label><br>
    <input type="text" placeholder="Username" name="username" required><br>
    <label><b>Password</b></label><br>
    <input type="password" placeholder="Password" name="password" required><br>

    <button type="submit">Login</button> 
</form>
<?php

if ($db_conn) {
    if (array_key_exists('username', $_POST)) {
		$tuple = array(
            ":username" => $_POST["username"],
            ":pass" => $_POST["password"]
        );

        /* Finds the username and membership expiry date of the user trying to log in.
        In addition, if membershipExpiryDate isn't null, then the user is a ProUser. */
        $result = executeBoundSQL("SELECT U.username, P.membershipExpiryDate FROM NormalUser U LEFT JOIN ProUser P ON U.username = P.username WHERE U.username = :username AND U.pass = :pass", array($tuple));
        
        $row = OCI_Fetch_Array($result, OCI_BOTH);
        if ($row) {
            // User exists!
            // Set these facts in cookies. They can obviously be changed, but it will do for this
            setcookie("username", $_POST["username"], time() + (86400 * 30), "/"); // 86400 = 1 day
            setcookie("ispro", array_key_exists("MEMBERSHIPEXPIRYDATE", $row), time() + (86400 * 30), "/");

            header("location: index.php");            
        } else {
            echo "Username and/or login is incorrect. Try again!";
        }
		OCICommit($db_conn);

    }
    OCILogoff($db_conn);
} else {
    echo "cannot connect";
    $e = OCI_Error(); // For OCILogon errors pass no handle
    echo htmlentities($e['message']);
}
?>
</body>

</html>
