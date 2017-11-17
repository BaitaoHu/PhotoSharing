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

/**
 * Executes a bound SQL command.
 */
function executeBoundSQL($cmdstr, $list) {
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
?>


<?php
      session_start();
	  
   
        $msg = '';
           
        //   if (isset($_POST['login']) && !empty($_POST['username'])
         //      && !empty($_POST['password'])) {
        //         $result = executeBoundSQL('select nu.username, NVL2(pu.username, \'true\', \'false\') as IsPro, pu.membershipExpiryDate, pu.signature, pu.profileURL
        //         from NormalUser nu left join ProUser pu on nu.username = pu.username
        //         where nu.username=:username and nu.pass=:password;', array([":username"=>$_POST['username'], ":password"=>$_POST['pass']]));
        //         echo $result;
   
        //       if ($result.length == 1) {
                   $_SESSION["valid"] = true;
                   $_SESSION["username"] = "Katherina";
                   $_SESSION["ispro"] = true;
        //         
        //           echo 'You have entered valid use name and password';
        //       } else {
        //           $msg = 'Wrong username or password';
        //       }
        //    } 
          
        ?>
