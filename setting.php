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

<?php 
echo "<h3>".$_COOKIE['username']."</h3>";?>
<div>
    <label>New Password</label>
    <input type="password" name = "newPassword" size = "20">
</div>
<div>
    <input type="submit" value="update" name="updatePass"></p>
</div>
    <?php if ( $_COOKIE["ispro"] == true ) : ?>
<div>
    <label>Signature</label>
    <input type="text" name="signature" size = "50" >
</div>
<div>
    <label>Profile URL</label>
    <input type="text" name="profileURL" size = "200">
</div>
<div>
    <input type="submit" value="submit" name="submitURL"></p>
</div>
<?php endif; ?> 
       
<?php
if ($db_conn) {
    if (array_key_exists('updatePass', $_POST)) {
        $tuple = array (
            ":bind1" => $_COOKIE["username"],
            ":bind2" => $_POST["newPassword"]      
        );
        $alltuples = array (
            $tuple
        );
        executeBoundSQL("update NormalUser set pass=:bind2 where username=:bind1", $alltuples);
        OCICommit($db_conn);
        echo "you have successfully changed your password!";
    } 

    if (array_key_exists('submitURL', $_POST)) {
        // Update tuple using data from user
        $tuple = array (
            ":bind3" => $_POST['signature'],
            ":bind4" => $_POST['profileURL']
        );

        $alltuples = array (
            $tuple
        );

        if($_POST['signature']){
            executeBoundSQL("update ProUser set signature=:bind3 where username='".$_COOKIE['username']."'", $alltuples);
        }

        if($_POST['profileURL']){
            executeBoundSQL("update ProUser set profileURL = :bind4 where username='".$_COOKIE['username']."'", $alltuples);
        }
                
        OCICommit($db_conn);
        echo "you have successfully changed your signature or profile URL!";
    }
}

?> 

</body>

</html>

