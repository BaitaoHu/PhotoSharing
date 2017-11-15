<!DOCTYPE html>
<html>

<head>
    <title>User Profile</title>
    <?php include_once 'scripts.php'; ?>
</head>

<body>
    <?php include_once 'header.php'; ?>
    <h1>User Profile</h1>
    <?php
$success = True;
$config = include('config.php');
$db_conn = OCILogon($config["db_username"], $config["db_password"], "dbhost.ugrad.cs.ubc.ca:1522/ug"); // Change this!
/**
 * Executes a plain SQL commands.
 * Adapted from the CPSC304 PHP tutorial.
 */
function executePlainSQL($cmdstr) { 
    global $db_conn, $success;
    $statement = OCIParse($db_conn, $cmdstr);

    if (!$statement) {
        echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
        $e = OCI_Error($db_conn);
        echo htmlentities($e['message']);
        $success = False;
    }

    $r = OCIExecute($statement, OCI_DEFAULT);
    if (!$r) {
        echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
        $e = oci_error($statement); // For OCIExecute errors pass the statementhandle
        echo htmlentities($e['message']);
        $success = False;
    }

    return $statement;
}

function executeBoundSQL($cmdstr, $list) {
    /* Sometimes the same statement will be executed for several times ... only
     the value of variables need to be changed.
     In this case, you don't need to create the statement several times; 
     using bind variables can make the statement be shared and just parsed once.
     This is also very useful in protecting against SQL injection.  
      See the sample code below for how this functions is used */

    global $db_conn, $success;
    $statement = OCIParse($db_conn, $cmdstr);

    if (!$statement) {
        echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
        $e = OCI_Error($db_conn);
        echo htmlentities($e['message']);
        $success = False;
    }

    foreach ($list as $tuple) {
        foreach ($tuple as $bind => $val) {
            //echo $val;
            //echo "<br>".$bind."<br>";
            OCIBindByName($statement, $bind, $val);
            unset ($val); //make sure you do not remove this. Otherwise $val will remain in an array object wrapper which will not be recognized by Oracle as a proper datatype

        }
        $r = OCIExecute($statement, OCI_DEFAULT);
        if (!$r) {
            echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
            $e = OCI_Error($statement); // For OCIExecute errors pass the statement handle
            echo htmlentities($e['message']);
            echo "<br>";
            $success = False;
        }
    }
 return $statement;
}



if ($db_conn) {
    // Fetches all posts by all users
      $userName = $_GET["name"];
    if(!$userName){
        echo "<h1>Unknown username</h1>";
        return;
    }

     $postparams = array(":username" => $userName);
    $result = executeBoundSQL("SELECT ProUser.*
    FROM ProUser
    WHERE ProUser.username = :username", array($postparams));

    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {

        echo "<h1>".$row["USERNAME"]."</h1>";
        echo "<a href='https://".$row["PROFILEURL"]."'>";
        echo "<p>".$row["PROFILEURL"]."</p>";
        echo "</a>";
        echo "<p>" . $row["SIGNATURE"] . "</p>";     
}

    //Commit to save changes...
    OCILogoff($db_conn);
} else {
    echo "cannot connect";
    $e = OCI_Error(); // For OCILogon errors pass no handle
    echo htmlentities($e['message']);
}
?>
</body>

</html>