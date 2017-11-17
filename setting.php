<!DOCTYPE html>
<form method="POST" action="setting.php">

<html>

<head>
    <title>Setting</title>
    <?php include_once 'scripts.php'; ?>
</head>
    <?php include_once 'utils.php'; ?>

<body>
    <?php include_once 'header.php'; ?>
    <h1>Change Password</h1>
<div>
    <label>oldPassword</label>
    <input type="password" name="oldPassword" size = "20">
</div>
<div>
    <label>newPassword</label>
    <input type="password" name = "newPassword" size = "20">
</div>
<div>
    <input type="submit" value="update111" name="updatePass"></p>
</div>
    <?php if ( $_SESSION["ispro"] == true ) : ?> 
<div>
    <p> Please be aware that only prousers can update the following information</p>
</div>
<div>
    <label>Signature</label>
    <input type="text" name="signature" size = "50" >
</div>
<div>
    <label>Type In Your URL</label>
    <input type="text" name="profileURL" size = "200">
</div>
<div>
    <button type="update">submit</button>
</div>
<?php endif; ?> 
       
<?php
if ($db_conn) { 
    if (array_key_exists('updatePass', $_POST)) {
        $tuple = array (
        ":bind1" => $_POST["oldPassword"],
        ":bind2" => $_POST["newPassword"],
    //    ":username" => $_SESSION['username'],           
        );
        $alltuples = array (
            $tuple
        );
        executeBoundSQL("update NormalUser set pass=:bind2 where pass=:bind1", $alltuples);
        OCICommit($db_conn);
        echo "you have successfully changed your password!";
    } 
    if (array_key_exists('submit', $_POST,$_SESSION)) {
        // Update tuple using data from user
        $tuple = array (
         ":bind3" => $_POST['signature'],
         ":bind4" => $_POST['profileURL'],
         ":username" => $_SESSION['username'],           
        );
        $alltuples = array (
            $tuple
        );
        executeBoundSQL("update ProUser set signature=:bind3 where username =:username ", $alltuples);
        executeBoundSQL("update ProUser set profileURL = :bind4 where username = :username", $alltuples);
                
        OCICommit($db_conn);
    }
}

?> 

</body>

</html>

